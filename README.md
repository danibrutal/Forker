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

$mpr->map(function($myJob) {
  echo 'Retrieving weather in ' . key($myJob) . "\n";
  return file_get_contents(current($myJob));
});

$mpr->reduce(function($allmytasks) use(& $allCitiesWeather) {
  $allCitiesWeather = $allmytasks;
});

var_dump($allCitiesWeather);
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

1. Run "composer update" in your project folder.
3. See the `Usage` or `Code example` sections.
4. Enjoy!.

## API Reference

By the moment, there is just a few methods to work with:

```php
setTasks(array $tasks, $numSubTasks = 4)
map(Closure $fn)
reduce(Closure $fn)
```
## Creating your own StorageSystem:
We follow here a TDD aproach so is extremely easy to develop a new system:

1º Create your own storage system following the StorageSystem interface's signature:
```php
  /**
   * @param key
   * @param value
   * @return bool
   */
  public function store($key, $value);

  /**
   * @return array $tasks
   */
  public function getReducedTasks();

  /**
   * @return bool
   */
  public function cleanUp();
```
2º Creates a test
```php
<?php
use MAPHPReduce\Storage\ArrayStorage;
require_once 'BaseStorageTest.php';

class ArrayStorageTest extends BaseStorageTest
{
    protected function getSystemStorage()
    {        
        return new ArrayStorage();        
    }
}
```
Hard, huh?

3º Then, type phpunit so you can see 3 errors to solve:
```
There were 3 failures:

1) ArrayStorageTest::testWeCanSToreValues
Failed asserting that false is true.

2) ArrayStorageTest::testIcanGetAllMyReducedTasks
Failed asserting that a NULL is not empty.

3) ArrayStorageTest::testWeCanCleanUpAllPreviousTasks
Failed asserting that a NULL is not empty.
```
4º Just solve the errors. Create your implementation and you are done!
Easy and funny :)

## Contributors
Please, feel free to colaborate. Fork the project and check [issues][2].
We still have so much work ahead!.

## License

MIT
