MAPHPReduce
------------

[![Build Status](https://travis-ci.org/danibrutal/MAPHPReduce.svg?branch=master)](https://travis-ci.org/danibrutal/MAPHPReduce)


## Synopsis

A PHP implementation of [Map Reduce framework](http://en.wikipedia.org/wiki/MapReduce).

## Code Example

```php
<?php
/**************************************************
 * Example: Retrieving the city-weather using external api
 * Usage  : php examples/demo.api.weather.php 
 **************************************************/
require 'vendor/autoload.php';

use MAPHPReduce\MAPHPReduce;
use MAPHPReduce\Storage\MemcacheStorage;

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

$mpr = new MAPHPReduce($storageSystem, $myTasks, $numberOfSubTasks);

// This is called 3 times before doing reduce method
// myJob here looks like this :
// array(1) {
//    ["madrid"]=>"http://api.openweathermap.org/data/2.5/weather?q=Madrid&mode=xml"
// }

$time_start = microtime(true);

$mpr->map(function($myJob) {
  echo 'Retrieving weather in ' . key($myJob) . "\n";
  return file_get_contents(current($myJob));
});

$mpr->reduce(function($allmytasks) use(& $allCitiesWeather) {
  $allCitiesWeather = $allmytasks;
});

$time_end = microtime(true);
$time = $time_end - $time_start;

echo "it took {$time} seconds in paralel \n";

$time_start = microtime(true);

foreach($myTasks as $city => $url) {
  echo 'Retrieving weather in ' . $city . "\n";
  $allCitiesWeather[] = file_get_contents($url);
}

$time_end = microtime(true);
$time = $time_end - $time_start;

echo "it took {$time} seconds secuencially \n";

// Retrieving weather in madrid
Retrieving weather in london
Retrieving weather in new-york
Retrieving weather in barcelona
Retrieving weather in lisboa
Retrieving weather in iasi
it took 0.34020018577576 seconds in paralel 
Retrieving weather in madrid
Retrieving weather in london
Retrieving weather in new-york
Retrieving weather in barcelona
Retrieving weather in lisboa
Retrieving weather in iasi
it took 2.1834211349487 seconds secuencially
```

## Motivation

Sometimes we have to work with a huge amount of data. 
[Lately](http://en.wikipedia.org/wiki/Big_data), more and more, and it's just no possible to work sequentially these times. 

For example, we have two cooks in a kitchen and a very big carrot.
Well, we want our two workers not to be waiting for each other. 
So why we don't split the carrot in two parts so they can work each one with a part ?

This way both cooks can work together, at the same time, and we will have our dinner soon ! Great huh ?

So, the intention is to make an agile and encapsulated way to split a task in several child subtasks in parallel.

## Installation (Composer)

1. Create composer.json file the following content:
    {
        "autoload": {
            "psr-0": {"MAPHPReduce": "src/"}
        }
    }
2. Run "composer update" in your project folder.
3. See the `Usage` or `Code example` sections.
4. Enjoy!.

## API Reference

By the moment, there is just a few methods to work with:

```php
setTasks(array $tasks, $numSubTasks = 4)
map(Closure $fn)
reduce(Closure $fn)
```

But in order to keep it agile, this won't change very much.

## Tests

Well, there is no tests yet !! It's a thing one can contribute.
See more about this [here](http://kpayne.me/2012/01/17/how-to-unit-test-fork/)

## Contributors
Please, feel free to colaborate. Fork the project and check [issues][2].
We still have so much work ahead!.

## License

MIT
