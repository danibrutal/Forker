<?php
require 'vendor/autoload.php';

use MAPHPReduce\MAPHPReduce;

$mpr = new MAPHPReduce();
$myTasks = array(1,2,3,4,5,6);

$mpr->setTasks($myTasks);

$mpr->map(function($myJob) {
  //doMyjob($myJob)
  //var_dump($myJob);
  echo "hola desde childs\n";
  return array('2');
});

$mpr->reduce(function($allmytasks) {
  //var_dump($allmytasks);
});
