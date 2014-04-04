<?php

namespace MAPHPReduce;

use MAPHPReduce\Storage\StorageInterface;
use MAPHPReduce\Exception\ForkingErrorException;

class MAPHPReduce 
{

  /**
   * @var MAPHPReduceStorage
   */
  private $storageSystem = null;

  private $numWorkers = 0;

  private $semKey = '123456';
  private $semResource = null;

  // @Closure $map fn
  private $mapFn;
  private $numberOfTasks;

  public function __construct($numTasks = 4) 
  {
    $this->numberOfTasks = is_numeric($numTasks) ? $numTasks : 4;
  }

  // we like Closures, don't we?
  public function map(\Closure $map) 
  {
    $this->mapFn = $map;
    $this->splitTasks();
    
    return $this;
  }

  public function reduce(\Closure $reduce) 
  {
    $this->waitForMyChildren();
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
          
          $this->lockIt();

          $key = $this->numWorkers - 1;
          $myTask = $this->storageSystem->giveMeMyTask($key);          
          $reducedTask = call_user_func($this->mapFn, $myTask);
          $this->storageSystem->store($key, $reducedTask);     
                 
          $this->unLock();

          $this->imDoneHere($this->numWorkers); 
          
        break;

      default: // i'm still the father
        
        if ($this->numWorkers < $this->numberOfTasks) {
          $this->splitTasks();
        } 
        
    }
  }

  public function setStoreSystem(StorageInterface $storeSystem) 
  {
    $this->storageSystem = $storeSystem;
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