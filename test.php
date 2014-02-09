<?php
require 'vendor/autoload.php';

use MAPHPReduce\MAPHPReduce;

$mpr = new MAPHPReduce();
$myTasks = array(1,2,3,4,5,6);

$mpr->setTasks($myTasks, 6);

$mpr->map(function($myJob) {

  //echo "hola desde childs\n";
  var_dump($myJob);
  return array('2');
});

$mpr->reduce(function($allmytasks) {
  //var_dump($allmytasks);
  echo 'reducing';
});
