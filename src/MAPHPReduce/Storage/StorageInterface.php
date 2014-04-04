<?php

namespace MAPHPReduce\Storage;

interface StorageInterface
{

  /**
   * @param mixed-whatever tasks
   */
  public function __construct($tasks);

  /**
   * @param key
   * @param value
   * @return bool
   */
  public function store($key, $value);

  /**
   * Should be called from each child to get their task
   * @param mixed $key
   * @return array task
   */
  public function giveMeMyTask($key);

  /**
   * @return array $tasks
   */
  public function getReducedTasks();
}