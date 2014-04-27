<?php

use Forker\Storage\FileStorage;

require_once 'BaseStorageTest.php';

class FileStorageTest extends BaseStorageTest
{

    protected function getSystemStorage()
    {        
        return new FileStorage();        
    }

}
