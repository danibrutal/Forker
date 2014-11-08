<?php
/**
 * Created by PhpStorm.
 * User: danibrutal
 * Date: 8/11/14
 * Time: 16:59
 */

namespace Forker;

/**
 * Class Semaphore
 * @package Forker
 */
class Semaphore implements ISemaphore
{

    private $semKey        = 0;
    private $semResource   = null;

    public function __construct()
    {
        $this->semKey = time();
    }

    public function lock()
    {
        $this->semResource = sem_get($this->semKey);
        sem_acquire($this->semResource);
    }

    public function unLock()
    {
        sem_release($this->semResource);
    }

} 