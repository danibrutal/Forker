<?php
/**************************************************
 * [Forker]
 *
 * Example: Retrieving the city-weather using external api
 *          IT Shows how the timeout system works
 *
 * Usage : php examples/demo.timeout.php
 * Storage: Memcache
 **************************************************/

require 'vendor/autoload.php';

use Forker\Forker;
use Forker\Storage\MemcacheStorage;

$allCitiesWeather = "";

$urlApiWeather = "http://api.openweathermap.org/data/2.5/weather?q=%s&mode=xml";

$myTasks = array(
    'madrid'    => sprintf($urlApiWeather, 'Madrid'),
    'london'    => sprintf($urlApiWeather, 'London'),
    'new-york'  => sprintf($urlApiWeather, 'NewYork'),
    'barcelona' => sprintf($urlApiWeather, 'barcelona'),
    'lisboa'    => sprintf($urlApiWeather, 'lisboa'),
    'iasi'      => sprintf($urlApiWeather, 'iasi'),
);

// a way to keep our data
$storageSystem = new MemcacheStorage;
$numberOfSubTasks = 6;
$timeout = 2;

$forker = new Forker($storageSystem, $myTasks, $numberOfSubTasks);

$time_start = microtime(true);

$forker->timeOut($timeout)->fork(function($city, $url, $emit) {

    if ($city == 'madrid') {
        echo "I'll stay in {$city} for a while..";
        sleep(10);
    }
    echo "Retrieving weather in $city\n";

    $contents = file_get_contents($url);
    $emit($city, $contents);
});

$allCitiesWeather = $forker->fetch();

$time_end = microtime(true);
$time = $time_end - $time_start;

echo "it took {$time} seconds in parallel \n";


