<?php
require 'vendor/autoload.php';

use MAPHPReduce\MAPHPReduce;
use MAPHPReduce\Storage\ArrayStorage;

$myTasks = array(
  '0'=>array(1,2),
  '1'=>array(3,4),
  '2'=>array(5,6)
);

// a way to keep our data
$storageSystem = new ArrayStorage($myTasks);

$numberOfSubTasks = 3;

$mpr = new MAPHPReduce($numberOfSubTasks);
$mpr->setStoreSystem($storageSystem);

// My job here is [1,2] , [3,4] , [5,6]
$mpr->map(function($myJob) {
  var_dump($myJob);
  return array(
    array_sum($myJob)
  );
});

$mpr->reduce(function($allmytasks) {
  var_dump($allmytasks);
  var_dump(array_sum($allmytasks));
});
