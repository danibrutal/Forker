<?php

namespace MAPHPReduce\Storage;

class ArrayStorage implements MAPHPReduceStorage
{

  private $tasks_db = array();

  public function store($key, $value)
  {
    $this->tasks_db[] = array($key, $value);
  }

  public function getReducedTasks() 
  {
    return $this->tasks_db;
  }
}