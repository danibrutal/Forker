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


  public function store($key, $value)
  {
    $this->reducedTasks[$key] = $value;
  }

  public function getReducedTasks() 
  {
    return $this->reducedTasks;
  }
}