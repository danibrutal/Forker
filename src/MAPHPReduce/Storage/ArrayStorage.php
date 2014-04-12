<?php

namespace MAPHPReduce\Storage;

/**
 * For testing purposes
 * It actually doesn't work
 */
class ArrayStorage implements StorageInterface
{

  private $tasks_db = array();
  private $reducedTasks = array();

  /**
   * @param key
   * @param value
   * @return bool
   */
  public function store($key, $value)
  {
    $this->reducedTasks[$key] = $value;

    return array_key_exists($key, $this->reducedTasks);
  }

  /**
   * @return array $tasks
   */
  public function getReducedTasks() 
  {
    return $this->reducedTasks;
  }

  /**
   * @return bool
   */
  public function cleanUp()
  {
    $this->reducedTasks = array();

    return count($this->reducedTasks) === 0; 
  }
}