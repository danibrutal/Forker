<?php
require 'vendor/autoload.php';

use MAPHPReduce\MAPHPReduce;
use MAPHPReduce\Storage\ArrayStorage;

$mpr = new MAPHPReduce();

$mpr->setStoreSystem(new ArrayStorage);

$myTasks = array(1,2,3,4,5,6);

$mpr->setTasks($myTasks, 3);

$mpr->map(function($myJob, $emit) {
  $emit->store(1, array_sum($myJob));
});

$mpr->reduce(function($allmytasks) {
  var_dump($allmytasks);
  var_dump(array_sum($allmytasks));

});
