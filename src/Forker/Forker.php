<?php
namespace Forker;

use Forker\Storage\StorageInterface;
use Forker\Exception\ForkingErrorException;

use Forker\ChilProcess as ChildProcess;

/**
 * Class Forker
 * @package Forker
 */
class Forker
{

    const FORKING_ERROR = -1;
    const CHILD_PROCESS = 0;

    /**
     * @var StorageInterface $storageSystem
     */
    private $storageSystem = null;

    private $childs = array();

    private $tasks = array();
    private $numWorkers    = 0;

    // Semaphore
    private $semaphore = null;

    // @Closure $map fn
    private $mapFn;
    private $numberOfTasks = 3;

    private $timeout = 0;


    /**
     * @param StorageInterface $storeSystem
     * @param array $tasks
     * @param int $numberOfSubTasks
     */
    public function __construct(StorageInterface $storageSystem, array $tasks, $numberOfSubTasks = 3)
    {
        $this->storageSystem = $storageSystem;
        $this->tasks = $tasks;

        $this->numberOfTasks = $this->calculateNumberOfWorkers(
            count($this->tasks),
            $numberOfSubTasks
        );

        // inject dependency
        $this->semaphore = new Semaphore();
    }

    /**
     * Sets a timeout for all child process
     * @param $seconds
     * @return $this
     */
    public function timeOut($seconds)
    {
        $this->timeout = $seconds;

        if ($this->timeout > 0 ) {
            declare(ticks = 1);
            new Alarm($this->timeout, array($this, 'wakeUpLazyChildren'));
        }

        return $this;
    }

    /**
     * @param $signo
     */
    public function wakeUpLazyChildren($signo)
    {
        foreach($this->childs as $pid => $forked) {
            posix_kill($pid, SIGTERM);
        }
    }

    /**
     * @param \Clousure $onTask
     * @return Forker $this
     */
    public function fork(\Closure $onTask)
    {
        $this->mapFn = $onTask;
        $this->splitTasks();

        $this->waitForMyChildren();
        return $this;
    }

    /**
     * @return array
     */
    public function fetch()
    {
        return $this->storageSystem->getStoredTasks();
    }

    /**
     * Copy the process recursively for each sub-task
     *
     */
    private function splitTasks()
    {

        $this->numWorkers++;

        switch ($pid = $this->getChildProces()) {

            case self::FORKING_ERROR:
                throw new ForkingErrorException("Error Forking process", 1);
                break;

            case self::CHILD_PROCESS:
                $childTask = $this->giveMeMyTask($this->numWorkers - 1, $this->numberOfTasks);

                $childProcess = new ChildProcess($childTask, $this->storageSystem, $this->semaphore);
                $childProcess->run( $this->mapFn );
                unset($childProcess);
                break;
            default: // parent time

                $this->childs[$pid] = true;
                break;
        }

        if ($this->numWorkers < $this->numberOfTasks) {
            $this->splitTasks();
        }

    }

    /**
     * @return int
     */
    protected function getChildProces()
    {
        return pcntl_fork();
    }

    /**
     * We calculate here the next divisor
     * @return int number of workers o subprocess
     */
    public function calculateNumberOfWorkers($numTasks, $numberOfSubTaks)
    {
        $n = $numberOfSubTaks;

        if ($numberOfSubTaks > $numTasks) {
            $n = $numTasks;
        }elseif (($numTasks % $numberOfSubTaks) !== 0) {
            $n = $this->calculateNumberOfWorkers($numTasks, $numberOfSubTaks + 1);
        }

        return $n;
    }

    /**
     * @param int $indexTask
     * @param int $numberOfTasks
     * @return array $task
     */
    public function giveMeMyTask($indexTask, $numberOfTasks)
    {
        $taskLength = floor(count($this->tasks) / $numberOfTasks);
        $offset     = $indexTask * $taskLength;

        return array_slice($this->tasks, $offset, $taskLength, true);
    }

    /**
     * waits for all forked processes
     */
    private function waitForMyChildren()
    {
        while (($pid = pcntl_wait($status)) !== -1) {
            unset($this->childs[$pid]);
        }
    }

}