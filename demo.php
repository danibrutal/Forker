<?php
/**
 * Example : 
 * Summation of a list of numbers
 */
require 'vendor/autoload.php';

use MAPHPReduce\MAPHPReduce;
use MAPHPReduce\Storage\ArrayStorage;

$mpr = new MAPHPReduce();

$mpr->setStoreSystem(new ArrayStorage);

$myTasks = array(1,2,3,4,5,6);

$numberOfSubTasks = 3;

$mpr->setTasks($myTasks, $numberOfSubTasks);

// My job here is [1,2] , [3,4] , [5,6]
// depends on child
$mpr->map(function($myJob, $emit) {
  $emit->store(1, array_sum($myJob));
});

$mpr->reduce(function($allmytasks) {
  $total = 0;

  foreach($allmytasks as $subTask) {
    $total += getValueFromTask($subTask);
  }

  return $total;
});
