<?php

namespace MAPHPReduce;

use MAPHPReduce\Storage\MAPHPReduceStorage;
use MAPHPReduce\Exception\ForkingErrorException;

class MAPHPReduce 
{

  /**
   * @var MAPHPReduceStorage
   */
  private $storageSystem = null;

  private $reducedTasks = array();
  private $numWorkers = 0;
  private $lock = '/tmp/foo';

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

    call_user_func(
      $reduce, 
      $this->storageSystem->getReducedTasks()
    );

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
          
          while(is_file($this->lock)) {
            clearstatcache();
            usleep(150 * $this->numWorkers);
          }

          touch($this->lock);

          $key = $this->numWorkers - 1;
          $myTask = $this->storageSystem->giveMeMyTask($key);          

          $reducedTask = call_user_func($this->mapFn, $myTask);

          $this->storageSystem->store(
            $key,
            $reducedTask              
          );            

          unlink($this->lock);

          $this->imDoneHere($this->numWorkers); 
          
        break;

      default: // i'm still the father
        
        if ($this->numWorkers < $this->numberOfTasks) {
          $this->splitTasks();
        } 
        
    }
  }

  public function setStoreSystem(MAPHPReduceStorage $storeSystem) 
  {
    $this->storageSystem = $storeSystem;
  }

  private function waitForMyChildren() 
  {
    while (pcntl_waitpid(0, $status) != -1) {
      $status = pcntl_wexitstatus($status);
      echo "Child $status completed\n";
    }
  }

  /**
   *
   **/
  private function imDoneHere($key = 'some key') 
  {
    usleep(5);
    exit($key);
  }

}