<?php
namespace Forker;

use Forker\Storage\StorageInterface;
use Forker\Exception\ForkingErrorException;

use Forker\ChilProcess as ChildProcess;

/**
 * Class Forker
 * @package Forker
 */
class Forker 
{

  const FORKING_ERROR = -1;
  const CHILD_PROCESS = 0;

  /**
   * @var StorageInterface $storageSystem
   */
  private $storageSystem = null;

  private $tasks = array();
  private $numWorkers    = 0;

  // Semaphores
  private $semKey        = '123456';
  private $semResource   = null;

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

  /**
   * @param \Clousure $map
   * @return Forker $this
   */
  public function fork(\Closure $map)
  {
    $this->mapFn = $map;
    $this->splitTasks();
    
    $this->waitForMyChildren();
    return $this;
  }

  /**
   * @return array
   */
  public function fetch() 
  {
    return $this->storageSystem->getStoredTasks();
  }

  /**
   * Copy the process recursively for each sub-task
   *
   */
  private function splitTasks() 
  {

    $this->numWorkers++;
                           
    switch ($this->getChildProces()) {
      
      case self::FORKING_ERROR:
        throw new ForkingErrorException("Error Forking process", 1);
        break;

      case self::CHILD_PROCESS:
          $childTask = $this->giveMeMyTask($this->numWorkers - 1, $this->numberOfTasks);

          $childProcess = new ChildProcess($childTask);
          $childProcess->run( $this->mapFn );

          $this->child($childTask, $this->mapFn);
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
   * @param array $myTask
   * @param \Closure $map
   */
  protected function child(array $myTasks, \Closure $map)
  {          

    foreach($myTasks as $taskKey => $myTask) {
      $emited = array();

      call_user_func($map, $taskKey, $myTask, function($key, $value) use(& $emited) {
        $emited[] = array($key, $value);
      });

      // todo: validate entry
      if (! empty($emited)) {
        
        foreach($emited as $processed) {          
          $this->storeChildTask($processed[0], $processed[1]);        
        }        
      }

    }

    $this->imDoneHere($this->numWorkers); 
  }

  private function storeChildTask($key, $value)
  {
    $this->lockIt();

    $this->storageSystem->store($key, $value);            
    
    $this->unLock();
  }

  /**
   * We calculate here the next divisor
   * @return int number of workers o subprocess
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

  /**
   * @param int $indexTask
   * @param int $numberOfTasks
   * @return array $task
   */
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