<?php

namespace MAPHPReduce;

class MAPHPReduce 
{

  private $tasks = array();
  private $reducedTasks = array();
  private $numWorkers = 0;
  private $children = 0;

  // @Closure $map fn
  private $mapFn;
  private $howToSplitThem;

  public function __construct(array $tasks = array(), $numTasks = 4) 
  {
    $this->tasks = $tasks;
    $this->howToSplitThem = is_numeric($numTasks) ? $numTasks : 4;
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
    
    var_dump($this->children);
    if ($this->numWorkers == $this->howToSplitThem) {
      var_dump($this->children);
      call_user_func($reduce, $this->reducedTasks);
    }

  }

  private function splitTasks() 
  {

    $pidChild = pcntl_fork();
                                                                    
    switch ($pidChild) {
      case -1:
        throw new Exception("Error Forking process", 1);
        break;

      case 0: // Child's time

          $myTask = $this->giveMeMyTask($this->numWorkers);
          
          $this->numWorkers++;

          $this->reducedTasks = array_merge(
            $this->reducedTasks,
            $this->getReducedTask(call_user_func($this->mapFn, $myTask))
          );

          if ($this->numWorkers < $this->howToSplitThem) {
            $this->splitTasks();
          }  
          
        break;

      default:
          
          if (pcntl_waitpid($pidChild, $status) > 0 ) {
              $this->children--;
          }
    }
  }

  private function giveMeMyTask($numWorker) 
  {
    $taskLength = count($this->tasks) / $this->howToSplitThem;
    $offset = $numWorker * $taskLength;
    
    return array_slice($this->tasks, $offset, $taskLength); 
  }

  private function isThereAnyChild() {
    return count($this->pids) < $this->howToSplitThem;
  }

  /**
   * Prevent invalid returned values
   */
  private function getReducedTask($returnedTask = null) {
    if (! is_array($returnedTask)) {
      throw new \InvalidArgumentException("Returned value musht be array");
    }

    return $returnedTask;
  }

  /**
   * @array $tasks
   * @return MAPHPReduce
   */
  public function setTasks(array $tasks, $numTasks = 4)
  {
    $this->tasks = $tasks;
    $this->howToSplitThem = is_numeric($numTasks) ? $numTasks : 4;

    return $this;
  }

}