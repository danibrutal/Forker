<?php
/**
 * Created by PhpStorm.
 * User: danibrutal
 * Date: 9/11/14
 * Time: 19:20
 */
namespace Forker;

/**
 * Class Alarm
 * @package Forker
 */
class Alarm
{
    /**
     * @var int
     */
    private $seconds;

    /**
     * @var callable
     */
    private $onFire;

    /**
     * @param int $seconds
     * @param callable $callback
     */
    public function __construct($seconds, $callback)
    {

        $this->seconds = $seconds;
        $this->onFire = $callback;

        if (! is_numeric($seconds)) {
            throw new \InvalidArgumentException("Invalid argument seconds in " . __CLASS__ . __FUNCTION__);
        }


        $this->setAlarm();

    }

    private function setAlarm()
    {
        pcntl_signal(SIGALRM, $this->onFire);
        pcntl_alarm($this->seconds);
    }

} 