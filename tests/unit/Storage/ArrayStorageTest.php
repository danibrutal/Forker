<?php

use MAPHPReduce\Storage\ArrayStorage;

class ArrayStorageTest extends PHPUnit_Framework_TestCase
{

    private $arrStorageSystem;
    private $tasks;

    public function setUp()
    {
        $this->tasks = array(1,2,3,4,5,6);
        $this->arrStorageSystem = new ArrayStorage($this->tasks);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidKeyThrowsException()
    {
        $this->arrStorageSystem->giveMeMyTask(7);
    }

    /**
     * We should be able to retrieve some task from the task's key
     * ArrayStorage::giveMeMyTask(key)
     */
    public function testIcanGetAllMyTasks()
    {
        foreach ($this->tasks as $key => $task) {

            $this->assertEquals(
                $task, $this->arrStorageSystem->giveMeMyTask($key)
            );
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

        $this->arrStorageSystem->store(1, 'val');
        $this->arrStorageSystem->store(2, 'val2');

        $reducedTasks = $this->arrStorageSystem->getReducedTasks();

        $this->assertTrue(is_array($reducedTasks));
        $this->assertTrue(2 == count($reducedTasks));

        $this->assertEquals($reducedTasks[1] , 'val');
        $this->assertEquals($reducedTasks[2] , 'val2');        

    }

}
