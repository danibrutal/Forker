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

          $myTask = $this->storageSystem->giveMeMyTask($this->numWorkers - 1);          

          $this->reducedTasks = array_merge(
            $this->reducedTasks,            
            call_user_func($this->mapFn, $myTask)
          );

          $this->imDoneHere($this->numWorkers); 
          
        break;

      default: // i'm still the father
        
        if ($this->numWorkers < $this->numberOfTasks) {
          $this->splitTasks();
        } 

        $this->waitForMyChildren();
        
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
    exit($key);
  }

}