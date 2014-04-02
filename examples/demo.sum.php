<?php
/**************************************************
 * [MAPHPReduce]
 *
 * Example: Sum of n firsts numbers in parallel
 * Usage : php demo.sum.php 
 **************************************************/
require 'vendor/autoload.php';

use MAPHPReduce\MAPHPReduce;
use MAPHPReduce\Storage\MemcacheStorage;

$myResult = 0;

$myTasks = array(
  0 => array(1,2),
  1 => array(3,4),
  2 => array(5,6)
);

// a way to keep our data
$storageSystem = new MemcacheStorage($myTasks);

$numberOfSubTasks = 3;

$mpr = new MAPHPReduce($numberOfSubTasks);
$mpr->setStoreSystem($storageSystem);

// My job here is [1,2] , [3,4] , [5,6]
$mpr->map(function($myJob) {
  return array_sum($myJob);
});

$mpr->reduce(function($allmytasks) use(& $myResult) {
  $myResult = array_sum($allmytasks);
});

echo "Oh my! We could retrieve the sum : {$myResult} \n";
