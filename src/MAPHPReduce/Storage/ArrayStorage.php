<?php

namespace MAPHPReduce\Storage;

class ArrayStorage implements StorageInterface
{

  private $tasks_db = array();
  private $reducedTasks = array();

  /**
   * @array $tasks
   */
  public function __construct($tasks)
  {
    $this->tasks_db = $tasks;
  }

  public function giveMeMyTask($taskKey) 
  {
    if (! isset($this->tasks_db[$taskKey])) {

      throw new \InvalidArgumentException(
        "{$taskKey} has not been defined in " . __CLASS__ . " tasks"
      );
    }

    return $this->tasks_db[$taskKey]; 
  }

  public function store($key, $value)
  {
    $this->reducedTasks[$key] = $value;
  }

  public function getReducedTasks() 
  {
    return $this->reducedTasks;
  }
}