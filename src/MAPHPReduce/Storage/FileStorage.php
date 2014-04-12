<?php

namespace MAPHPReduce\Storage;

/**
 * A very simple implementation.
 * Just to pass all the tests, as TDD's rule points out.
 *
 * IT will generate a file for each task, so be sure if it fits 
 * for your project
 */
class FileStorage implements StorageInterface
{

  private $hash_folder = "";
  private $tasks_path = "/tmp/";

  /**
   * @throws \Exception
   */
  public function __construct()
  {
    $this->hash_folder = sha1(time());

    if (! is_dir($this->tasks_path . $this->hash_folder) 
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
    $filename = "{$this->tasks_path}{$this->hash_folder}/" . sha1($key) . '_' . $key;
    return file_put_contents($filename, $value) !== FALSE;
  }


  /**
   * @return array $tasks
   */
  public function getReducedTasks()
  {
    $reducedTasks = array();
   
    foreach ($this->getStoredTasksFiles() as $storedTaskFile) {
      $key = end(explode("_", $storedTaskFile));
      $reducedTasks[$key] = file_get_contents($storedTaskFile);
    }

    return $reducedTasks;
  }

  /**
   * @return bool
   */
  public function cleanUp()
  {
    foreach ($this->getStoredTasksFiles() as $storedTaskFile) {
      unlink($storedTaskFile);
    }
    
    if (is_dir($folderToClean = "{$this->tasks_path}{$this->hash_folder}")) {
      return rmdir($folderToClean);
    }

    return true;
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
}