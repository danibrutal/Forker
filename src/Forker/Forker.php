<?php
namespace Forker;

use Forker\Storage\StorageInterface;
use Forker\Exception\ForkingErrorException;

class Forker 
{

  private $tasks = array();

  private $storageSystem = null;
  private $numWorkers    = 0;

  private $semKey      = '123456';
  private $semResource = null;

  // @Closure $map fn
  private $mapFn;
  private $numberOfTasks = 3;

  /**
   * @param StorageInterface $storeSystem
   * @param array $tasks 
   * @param int $numberOfSubTasks 
   */
  public function __construct(StorageInterface $storeSystem, array $tasks, $numberOfSubTasks = 3) 
  {
    $this->storageSystem = $storeSystem;
    $this->tasks = $tasks;
    
    $this->numberOfTasks = $this->calculateNumberOfWorkers(
      count($this->tasks), 
      $numberOfSubTasks
    );
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
    $reduce( $this->storageSystem->getStoredTasks() );
  }

  private function splitTasks() 
  {

    $this->numWorkers++;
                           
    switch ($this->getChildProces()) {
      case -1:
      
        throw new ForkingErrorException("Error Forking process", 1);
        break;

      case 0: // Child's time
          $this->child($this->numWorkers - 1);
        break;        
    }

    if ($this->numWorkers < $this->numberOfTasks) {
      $this->splitTasks();
    } 
  }

  /**
   * @return int
   */
  protected function getChildProces()
  {
    return pcntl_fork();
  }

  /**
   * @param int $indexTask
   */
  protected function child($indexTask)
  {
    $myTask = $this->giveMeMyTask($indexTask, $this->numberOfTasks);          
    $reducedTask = call_user_func($this->mapFn, $myTask);

    $this->lockIt();

    $this->storageSystem->store(key($myTask), $reducedTask);            
    
    $this->unLock();

    $this->imDoneHere($this->numWorkers); 
  }

  /**
   * We calculate here the next divisor
   *
   */
  public function calculateNumberOfWorkers($numTasks, $numberOfSubTaks)
  {
    $n = $numberOfSubTaks;
    
    if ($numberOfSubTaks > $numTasks) {
      $n = $numTasks;
    }elseif (($numTasks % $numberOfSubTaks) !== 0) {
      $n = $this->calculateNumberOfWorkers($numTasks, $numberOfSubTaks + 1);
    }

    return $n;
  }

  public function giveMeMyTask($indexTask, $numberOfTasks) 
  {
    $taskLength = floor(count($this->tasks) / $numberOfTasks);
    $offset     = $indexTask * $taskLength;
    
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