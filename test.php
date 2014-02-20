<?php
require 'vendor/autoload.php';

use MAPHPReduce\MAPHPReduce;

$mpr = new MAPHPReduce();
$myTasks = array(1,2,3,4,5,6);

$mpr->setTasks($myTasks, 3);

$mpr->map(function($myJob) {
  return array(
      array_sum($myJob)
    );
});

$mpr->reduce(function($allmytasks) {
  var_dump($allmytasks);
  var_dump(array_sum($allmytasks));

});
