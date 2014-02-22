<?php

namespace MAPHPReduce;

use MAPHPReduce\Storage\MAPHPReduceStorage;

class MAPHPReduce 
{

  private $storeSystem = null;

  private $tasks = array();
  private $reducedTasks = array();
  private $numWorkers = 0;

  // @Closure $map fn
  private $mapFn;
  private $numberOfTasks;

  public function setStore(MAPHPReduceStorage $storeSystem) 
  {
    $this->storeSystem = $storeSystem;
  }

  public function __construct(array $tasks = array(), $numTasks = 4) 
  {
    $this->tasks = $tasks;
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
    if ($this->numWorkers == $this->numberOfTasks) {
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

          if ($this->numWorkers < $this->numberOfTasks) {
            $this->splitTasks();
          }  
          
        break;

      default:
          
          if (pcntl_waitpid($pidChild, $status) > 0 ) {
              //$this->children--;
          }
    }
  }

  private function giveMeMyTask($numWorker) 
  {
    $taskLength = count($this->tasks) / $this->numberOfTasks;
    $offset = $numWorker * $taskLength;
    
    return array_slice($this->tasks, $offset, $taskLength); 
  }

  private function isThereAnyChild() {
    return count($this->pids) < $this->numberOfTasks;
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
    $this->numberOfTasks = is_numeric($numTasks) ? $numTasks : 4;

    return $this;
  }

}