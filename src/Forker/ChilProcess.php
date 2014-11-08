<?php
/**
 * Created by PhpStorm.
 * User: danibrutal
 * Date: 1/11/14
 * Time: 11:59
 */

namespace Forker;

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
     * @var callable
     */
    private $onRunTask;

    /**
     * @param array $tasks
     */
    public function __construct(array $tasks)
    {
        $this->tasks = $tasks;
    }

    /**
     * @param null $callback
     */
    public function run($callback = null)
    {

    }

} 