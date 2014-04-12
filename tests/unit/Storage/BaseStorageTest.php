<?php

use MAPHPReduce\Storage\ArrayStorage;

Abstract class BaseStorageTest extends PHPUnit_Framework_TestCase
{
    protected $storageSystem;
    protected $tasks = array(1, 2, 3, 4, 5, 6);

    public function setUp()
    {        
        $this->storageSystem = $this->getSystemStorage();
        $this->storeAllTasks();
    }

    public function tearDown()
    {
        $this->storageSystem->cleanUp();
    }

    // to override
    abstract protected function getSystemStorage();

    protected function storeAllTasks()
    {
        foreach ($this->tasks as $keyTask => $task) {
            $this->storageSystem->store($keyTask, $task);
        }
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
    public function testIcanGetAllMyReducedTasks()
    {

        $reducedTasks = $this->storageSystem->getReducedTasks();
        $expected = $this->tasks;
        
        $this->assertNotEmpty($reducedTasks);
        $this->assertTrue(is_array($reducedTasks));
        $this->assertEquals($expected, $reducedTasks);        
    }

    public function testWeCanCleanUpAllPreviousTasks()
    {
        $reducedTasks = $this->storageSystem->getReducedTasks();
        
        $this->assertNotEmpty($reducedTasks);
        $this->assertTrue($this->storageSystem->cleanUp());
        $this->assertEmpty($this->storageSystem->getReducedTasks());
    }

}
