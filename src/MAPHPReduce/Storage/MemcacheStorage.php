<?php

namespace MAPHPReduce\Storage;


use MAPHPReduce\Exception\StorageException;

class MemcacheStorage implements MAPHPReduceStorage
{

  const CACHE_KEY               = "hola_";
  const CACHE_REDUCED_KEY       = __CLASS__;
  const MEM_HOST                = 'localhost';
  const CACHE_EXPIRATION_TIME   = '600';

  private $cache = NULL;
  private $reducedTasks = array();

  /**
   * @param array $tasks
   * @throws Exception
   */
  public function __construct($tasks)
  {
    $this->tasks_db = $tasks;
    $this->cache = new \Memcache;

    if (! $this->cache->addServer(self::MEM_HOST, 11211, false)) {
      throw new \Exception("Error in ". __CLASS__ ." trying to connect to " . self::MEM_HOST, 1);
    }

    $this->cleanTasksCache();

    // We set here the tasks set
    $stored = $this->cache->set(
      self::CACHE_KEY,
      $tasks,
      0,
      self::CACHE_EXPIRATION_TIME
    );

    if ($stored === FALSE) {
      throw new StorageException("It was not possible to store all tasks in " . __CLASS__);
    }

  }

  public function cleanTasksCache()
  {
    $this->cache->delete(self::CACHE_KEY);
    $this->cache->delete(self::CACHE_REDUCED_KEY);
  }

  /**
   * @param key
   * @param value
   * @return bool
   */
  public function store($key, $value)
  {

    $this->cache->addServer(self::MEM_HOST, 11211, false);

    $hash_key = $key + 1;

    // we set here the task itself indexed by key
    if ($this->cache->set($hash_key, $value, 0, self::CACHE_EXPIRATION_TIME) ) {
      
      // And we add the key to the queue
      $reduced = $this->getReducedTasksFromCache();
      $reduced[] = $hash_key;
      $this->cache->set(self::CACHE_REDUCED_KEY, $reduced);  
      
    }

  }

  /**
   * @param mixed $key
   * @return array task
   */
  public function giveMeMyTask($taskKey) 
  {

    $this->cache->addServer(self::MEM_HOST, 11211, false);

    $allTasks = $this->cache->get(self::CACHE_KEY);

    return $allTasks[$taskKey];
  }

  public function getReducedTasks() 
  {
    $reduced = $this->getReducedTasksFromCache();

    return $this->cache->get($reduced);
  }

  /**
   * @return array 
   */
  private function getReducedTasksFromCache()
  {
    $r = $this->cache->get(self::CACHE_REDUCED_KEY);  

    return is_array($r) ? $r : array();
  }

}