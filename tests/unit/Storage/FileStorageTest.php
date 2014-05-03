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

  /**
   * Useful if we want to store several values for each key
   */
  public function testWeCanAddValuesToAStoredKey()
  {
      $value1  = 'value1';
      $value2  = 'value2';
      $value3  = 'value3';

      $expectedValue = array($value1, $value2, $value3);
      
      $this->storageSystem->store('foo', $value1);
      $this->storageSystem->store('foo', $value2);
      $this->storageSystem->store('foo', $value3);

      $this->assertEquals($expectedValue,  $this->storageSystem->get('foo') );
  }
  
}
