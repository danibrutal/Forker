<?php

use MAPHPReduce\Storage\ArrayStorage;

class ArrayStorageTest extends PHPUnit_Framework_TestCase
{

    private $arrStoreSystem;

    public function setUp()
    {
        $this->arrStoreSystem = new ArrayStorage();
    }

    /**
     * By the moment, this is the behaviour we want for
     * the array storage System.
     * We store in array a list of pairs (key, value) via 
     * 
     * ArrayStorage::store(key,value)
     */
    public function testEmitStorage()
    {
        $this->arrStoreSystem->store(1, 'val');
        $this->arrStoreSystem->store(2, 'val2');

        $reducedTasks = $this->arrStoreSystem->getReducedTasks();

        $this->assertTrue(2 == count($reducedTasks));

        $this->assertContains(
            array(1 , 'val') ,
            $reducedTasks
        );
        
        $this->assertContains(
            array(2 , 'val2') ,
            $reducedTasks
        ); 
    }

}
