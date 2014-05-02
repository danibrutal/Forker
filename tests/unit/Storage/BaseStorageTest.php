<?php

use Forker\Storage\ArrayStorage;

Abstract class BaseStorageTest extends PHPUnit_Framework_TestCase
{
    protected $storageSystem;
    protected $tasks = array(1, 2, 3, 4, 5, 6);

    public function setUp()
    {        
        $this->storageSystem = $this->getSystemStorage();              
    }

    public function tearDown()
    {
        $this->storageSystem->cleanUp();
    }

    // to override
    abstract protected function getSystemStorage();

    public function testWeCanGetASimpleStoredValue()
    {
        $expectedValue  = 'value';
        $nonExistingKey = 'uhh';

        $this->storageSystem->store('foo', $expectedValue);
        $this->assertEquals($expectedValue, $this->storageSystem->get('foo'));

        $this->assertFalse($this->storageSystem->get($nonExistingKey));
    }

    /**
     * Let's return a boolean value
     * To make it easier
     */
    public function testWeCanSToreValues()
    {
        $this->assertTrue(
            $this->storageSystem->store('foo', 'some-value')
        );
    }

    /**
     * It should be a way to retrieve all our stored tasks
     */
    public function testIcanGetAllMyStoredTasks()
    {

        $this->storeAllTasks();  
        $reducedTasks = $this->storageSystem->getStoredTasks();
        $expected = $this->tasks;
        
        $this->assertNotEmpty($reducedTasks);
        $this->assertTrue(is_array($reducedTasks));
        $this->assertEquals($expected, $reducedTasks);        
    }

    public function testWeCanCleanUpAllPreviousTasks()
    {
        $this->storeAllTasks();  
        $reducedTasks = $this->storageSystem->getStoredTasks();
        
        $this->assertNotEmpty($reducedTasks);
        $this->assertTrue($this->storageSystem->cleanUp());
        $this->assertEmpty($this->storageSystem->getStoredTasks());
    }

    protected function storeAllTasks()
    {
        foreach ($this->tasks as $keyTask => $task) {
            $this->storageSystem->store($keyTask, $task);
        }
    }
}
