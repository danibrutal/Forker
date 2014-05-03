<?php

namespace Forker\Storage;

/**
 * A very simple implementation.
 * Just to pass all the tests, as TDD's rule points out.
 *
 * IT will generate a file for each task, so be sure if it fits 
 * for your project
 */
class FileStorage implements StorageInterface
{
  const VALUE_SEPARATOR = "||";

  private $hash_folder  = "";
  private $tasks_path   = "/tmp/";

  /**
   * @throws \Exception
   */
  public function __construct($tasks_path = "/tmp/")
  {
    $this->tasks_path = $tasks_path;
    $this->hash_folder = sha1(time());

    if (! file_exists($this->tasks_path . $this->hash_folder) 
    AND ! mkdir($this->tasks_path . $this->hash_folder)) {
      throw new \Exception("Error creating folder in {$this->tasks_path}");
    }

  }

  /**
   * @param key
   * @param value
   * @return bool
   */
  public function store($key, $value)
  {
    $filename = "{$this->tasks_path}{$this->hash_folder}/" . $this->generateFilenameFromKey($key);
    
    if (is_file($filename)) {      
      return file_put_contents($filename,self::VALUE_SEPARATOR . $value , FILE_APPEND) !== FALSE;
    }
      
    return file_put_contents($filename, $value , FILE_APPEND) !== FALSE;
  }

  /**
   * @param key
   * @return value
   */
  public function get($key)
  {
    $filename = "{$this->tasks_path}{$this->hash_folder}/" . $this->generateFilenameFromKey($key);

    if (is_file($filename)) {
      $contents =  file_get_contents($filename);

      // If there are more values, we return array
      if (strpos($contents, self::VALUE_SEPARATOR) !== false) {
        $contents = explode(self::VALUE_SEPARATOR, $contents);
      }

      return $contents;
    }

    return false;
  }

  /**
   * @return array $tasks
   */
  public function getStoredTasks()
  {
    $reducedTasks = array();
   
    foreach ($this->getStoredTasksFiles() as $storedTaskFile) {
      $key = $this->getKeyFromFilename($storedTaskFile);
      $reducedTasks[$key] = $this->get($key);
    }

    return $reducedTasks;
  }

  /**
   * @return bool
   */
  public function cleanUp()
  {
    $ret = false;
    
    foreach ($this->getStoredTasksFiles() as $storedTaskFile) {
      unlink($storedTaskFile);
    }
    
    if (is_dir($folderToClean = "{$this->tasks_path}{$this->hash_folder}")) {
      $ret = rmdir($folderToClean);
    }

    return $ret;
  }

  /**
   * wrapper to retrieve tasks file so we can read/delete
   * them safely
   * @return array
   */
  private function getStoredTasksFiles()
  {
    $storedFiles = array();

    if (is_dir($folderToSearch = "{$this->tasks_path}{$this->hash_folder}")) {

      foreach (scandir($folderToSearch) as $storedTaskFile) {
        if ($storedTaskFile=='.' OR $storedTaskFile=='..') continue;

        $storedFiles[] = "{$folderToSearch}/$storedTaskFile";
      }

    }
    
    return $storedFiles;
  }

  private function generateFilenameFromKey($key)
  {
    return sha1($key) . "_" . $key;
  }

  private function getKeyFromFilename($filename)
  {
    $tmp = explode("_", $filename);
    return end($tmp);
  }
}