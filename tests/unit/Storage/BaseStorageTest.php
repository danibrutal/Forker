<?php

use MAPHPReduce\Storage\ArrayStorage;

Abstract class BaseStorageTest extends PHPUnit_Framework_TestCase
{

    protected $storageSystem;
    protected $tasks = array(1,2,3,4,5,6);

    public function setUp()
    {        
        $this->storageSystem = $this->getSystemStorage();
        $this->storeAllTasks();
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
     * By the moment, this is the behaviour we want for
     * the array storage System.
     * We store in array a list of pairs (key, value) via 
     * 
     * ArrayStorage::store(key,value)
     */
    public function testIcanGetAllMyReducedTasks()
    {

        $reducedTasks = $this->storageSystem->getReducedTasks();
        $expected = $this->tasks;
        
        $this->assertNotEmpty($reducedTasks);
        $this->assertTrue(is_array($reducedTasks));
        $this->assertEquals($expected, $reducedTasks);        
    }

}
