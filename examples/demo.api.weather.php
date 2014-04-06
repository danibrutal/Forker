<?php
require 'vendor/autoload.php';

use MAPHPReduce\MAPHPReduce;
use MAPHPReduce\Storage\MemcacheStorage;

$allCitiesWeather = "";

$urlApiWeather = "http://api.openweathermap.org/data/2.5/weather?q=%s&mode=xml";

$myTasks = array(
  'madrid'    => sprintf($urlApiWeather, 'Madrid'),
  'london'    => sprintf($urlApiWeather, 'London'),
  'new-york'  => sprintf($urlApiWeather, 'NewYork')
);

// a way to keep our data
$storageSystem = new MemcacheStorage;

$numberOfSubTasks = 3;

$mpr = new MAPHPReduce($storageSystem, $myTasks, $numberOfSubTasks);

// This is called 3 times before doing reduce method
// myJob here looks like this :
// array(1) {
//    ["madrid"]=>"http://api.openweathermap.org/data/2.5/weather?q=Madrid&mode=xml"
// }

$mpr->map(function($myJob) {
  echo 'Retrieving weather in ' . key($myJob) . "\n";
  
  return file_get_contents(current($myJob));
});

$mpr->reduce(function($allmytasks) use(& $allCitiesWeather) {
  $allCitiesWeather = $allmytasks;
});

var_dump($allCitiesWeather);
