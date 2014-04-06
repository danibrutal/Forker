<?php
require 'vendor/autoload.php';

use MAPHPReduce\MAPHPReduce;
use MAPHPReduce\Storage\MemcacheStorage;

$allCitiesWeather = "";

$urlApiWeather = "http://api.openweathermap.org/data/2.5/weather?q=%s&mode=xml";

$myTasks = array(
  0 => sprintf($urlApiWeather, 'Madrid'),
  1 => sprintf($urlApiWeather, 'London'),
  2 => sprintf($urlApiWeather, 'NewYork')
);

// a way to keep our data
$storageSystem = new MemcacheStorage;

$numberOfSubTasks = 3;

$mpr = new MAPHPReduce($storageSystem, $myTasks, $numberOfSubTasks);

// This is called 3 times before doing reduce method
$mpr->map(function($myJob) {
  echo $myJob . "\n"; 

  return file_get_contents($myJob);
});

$mpr->reduce(function($allmytasks) use(& $allCitiesWeather) {
  $allCitiesWeather = $allmytasks;
});

var_dump($allCitiesWeather);
