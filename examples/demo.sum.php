<?php
/**************************************************
 * [MAPHPReduce]
 *
 * Example: Sum of 10 firsts numbers in parallel
 * Usage : php demo.sum.php 
 **************************************************/
require 'vendor/autoload.php';

use MAPHPReduce\MAPHPReduce;
use MAPHPReduce\Storage\MemcacheStorage;

$myResult = 0;

$myTasks = array(
  0 => array(1,2),
  1 => array(3,4),
  2 => array(5,6),
  3 => array(7,8),
  4 => array(9,10),
);

// a way to keep our data
$storageSystem = new MemcacheStorage;

$numberOfSubTasks = 5;

$mpr = new MAPHPReduce($storageSystem, $myTasks, $numberOfSubTasks);

// My job here is [1,2] , [3,4] , [5,6]
$mpr->map(function($myJob, $key) {
  return array_sum($myJob);
});

$mpr->reduce(function($allmytasks) use(& $myResult) {
  $myResult = array_sum($allmytasks);
});

$n = 10;
$expected = ($n * ($n+1)) / 2;

var_dump($myResult===$expected);
echo "Oh my! We could retrieve the sum : {$myResult} \n";

