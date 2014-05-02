<?php
/**************************************************
 * [Forker]
 *
 * Example: Sum of 10 firsts numbers in parallel
 * Usage : php demo.sum.php 
 * Storage: Memcache
 **************************************************/
require 'vendor/autoload.php';

use Forker\Forker;
use Forker\Storage\MemcacheStorage;

$myResult = 0;

$myTasks = array(
  0 => array(1,2),
  1 => array(3,4),
  2 => array(5,6),
  3 => array(7,8),
  4 => array(9,10),
  5 => array(11,12),
);

// a way to keep our data
$storageSystem = new MemcacheStorage;

$numberOfSubTasks = 3;

$forker = new Forker($storageSystem, $myTasks, $numberOfSubTasks);

// My job here is [[1,2] , [3,4]] ,[[5,6],[7,8]]...not precisely in this order
$forker->map(function($myJobs) {
  $total = 0;

  foreach($myJobs as $job) {
    $total += array_sum($job);
  }

  return $total;
});

$myResult = array_sum($forker->fetch());

$n = 12;
$expected = ($n * ($n+1)) / 2;

var_dump($myResult===$expected);
echo "Oh my! We could retrieve the sum : {$myResult} \n";

