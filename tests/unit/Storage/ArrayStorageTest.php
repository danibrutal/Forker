<?php

use MAPHPReduce\Storage\ArrayStorage;

require 'BaseStorageTest.php';

class ArrayStorageTest extends BaseStorageTest
{

    protected function getSystemStorage()
    {        
        return new ArrayStorage();        
    }

}
