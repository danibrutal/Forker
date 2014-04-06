<?php

namespace MAPHPReduce\Storage;

interface StorageInterface
{

  /**
   * @param key
   * @param value
   * @return bool
   */
  public function store($key, $value);


  /**
   * @return array $tasks
   */
  public function getReducedTasks();
}