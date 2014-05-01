<?php

use Forker\Storage\FileStorage;

use org\bovigo\vfs\vfsStream;

require_once 'BaseStorageTest.php';

class FileStorageTest extends BaseStorageTest
{

  private $folder_to_store;

  public function setUp() 
  {
    $this->folder_to_store = vfsStream::setup('myTasksDir');
    parent::setUp();  
  } 

  protected function getSystemStorage()
  {        
      return new FileStorage(
          vfsStream::url('myTasksDir/')    
      );        
  }
  
}
