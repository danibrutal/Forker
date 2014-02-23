<?php

namespace MAPHPReduce\Storage;

interface MAPHPReduceStorage
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