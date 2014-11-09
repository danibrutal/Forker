<?php
/**
 * Created by PhpStorm.
 * User: danibrutal
 * Date: 1/11/14
 * Time: 11:59
 */

namespace Forker;

use Forker\Storage\StorageInterface;

/**
 * Class ChilProcess
 * @package Forker
 */
class ChilProcess
{
    /**
     * @var array
     */
    private $tasks;

    /**
     * @var StorageInterface $storageSystem
     */
    private $storageSystem = null;

    // Semaphore
    private $semaphore = null;

    /**
     * @param array $tasks
     * @param StorageInterface $storageSystem
     * @param Semaphore $sem
     */
    public function __construct(array $tasks, StorageInterface $storageSystem, Semaphore $sem)
    {
        $this->tasks = $tasks;

        $this->storageSystem = $storageSystem;

        $this->semaphore = $sem;
    }

    /**
     * @param callable $callback
     */
    public function run($callback = null)
    {
        foreach($this->tasks as $taskKey => $myTask) {
            $emited = array();

            call_user_func($callback, $taskKey, $myTask, function($key, $value) use(& $emited) {
                $emited[] = array($key, $value);
            });

            // todo: validate entry
            if (! empty($emited)) {

                foreach($emited as $processed) {
                    $this->storeChildTask($processed[0], $processed[1]);
                }
            }
        }
    }

    /**
     * @param $key
     * @param $value
     */
    private function storeChildTask($key, $value)
    {
        $this->semaphore->lock();

        $this->storageSystem->store($key, $value);

        $this->semaphore->unLock();
    }

    public function __destruct()
    {
        $this->imDoneHere();
    }

    private function imDoneHere()
    {
        exit(0);
    }

} 