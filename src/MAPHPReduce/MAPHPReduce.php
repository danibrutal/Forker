<?php
namespace MAPHPReduce;

use MAPHPReduce\Storage\StorageInterface;
use MAPHPReduce\Exception\ForkingErrorException;

class MAPHPReduce 
{

  private $tasks = array();

  private $storageSystem = null;
  private $numWorkers = 0;

  private $semKey = '123456';
  private $semResource = null;

  // @Closure $map fn
  private $mapFn;
  private $numberOfTasks = 3;

  public function __construct(StorageInterface $storeSystem, array $tasks, $numTasks = 3) 
  {
    $this->storageSystem = $storeSystem;
    $this->tasks = $tasks;
    $this->numberOfTasks = is_numeric($numTasks) ? $numTasks : $this->numberOfTasks;
  }

  // we like Closures, don't we?
  public function map(\Closure $map) 
  {
    $this->mapFn = $map;
    $this->splitTasks();
    
    $this->waitForMyChildren();
    return $this;
  }

  public function reduce(\Closure $reduce) 
  {
    $reduce( $this->storageSystem->getReducedTasks() );
  }

  private function splitTasks() 
  {

    $pidChild = pcntl_fork();
    $this->numWorkers++;
                                                                
    switch ($pidChild) {
      case -1:
        throw new ForkingErrorException("Error Forking process", 1);
        break;

      case 0: // Child's time
          
          $myTask = $this->giveMeMyTask($this->numWorkers - 1);          
          $reducedTask = call_user_func($this->mapFn, $myTask);

          $this->lockIt();

          $this->storageSystem->store(key($myTask), $reducedTask);            
          
          $this->unLock();

          $this->imDoneHere($this->numWorkers); 
        break;
      default: // i'm still the father
        
        if ($this->numWorkers < $this->numberOfTasks) {
          $this->splitTasks();
        } 
        
    }
  }

  private function giveMeMyTask($numWorker) 
  {
    $taskLength = count($this->tasks) / $this->numberOfTasks;
    $offset = $numWorker * $taskLength;
    
    return array_slice($this->tasks, $offset, $taskLength, true); 
  }

  private function lockIt()
  {
    $this->semResource = sem_get($this->semKey);
    sem_acquire($this->semResource);
  }

  private function unLock()
  {
    sem_release($this->semResource); 
  }

  private function waitForMyChildren() 
  {
    while (pcntl_waitpid(0, $status) != -1);
  }

  private function imDoneHere() 
  {
    exit;
  }

}