<?php

use MAPHPReduce\Storage\ArrayStorage;

require_once 'BaseStorageTest.php';

class ArrayStorageTest extends BaseStorageTest
{

    protected function getSystemStorage()
    {        
        return new ArrayStorage();        
    }

}
