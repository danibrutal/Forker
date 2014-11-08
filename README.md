Forker
------------
[![Build Status](https://travis-ci.org/danibrutal/Forker.svg?branch=master)](https://travis-ci.org/danibrutal/Forker)
[![Latest Stable Version](https://poser.pugx.org/danibrutal/forker/v/stable.png)](https://packagist.org/packages/danibrutal/forker)

## Synopsis

A structured , safe, and easiest way to perform tasks parallely in PHP.

## Code Example

```php
<?php
/**************************************************
 * Example: Retrieving the city-weather using external api
 * Usage  : php examples/demo.api.weather.php 
 * Storage: File
 **************************************************/
require 'vendor/autoload.php';

use Forker\Forker;
use Forker\Storage\FileStorage;

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
$storageSystem = new FileStorage;
$numberOfSubTasks = 6;

$forker = new Forker($storageSystem, $myTasks, $numberOfSubTasks);

$forker->fork(function($city, $url,  $emit) {
  echo "Retrieving weather in $city\n";
  
  $contents = file_get_contents($url);
  $emit($city, $contents);
});

$allCitiesWeather = $forker->fetch();

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

## Install

Using composer:
```
  "require": {
        "danibrutal/forker": "dev-master"
    }
```


## API Reference

You can check the API out [here](http://testdouble.es/Forker/API/);
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
   * @param key
   * @return value | false
   */
  public function get($key);

  /**
   * @return array $tasks
   */
  public function getStoredTasks();

  /**
   * @return bool
   */
  public function cleanUp();
```
2º Creates a test
```php
<?php
use Forker\Storage\ArrayStorage;
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
There were 4 failures:

1) ArrayStorageTest::testWeCanGetASimpleStoredValue
Failed asserting that null matches expected 'value'.

2) ArrayStorageTest::testWeCanSToreValues
Failed asserting that null is true.

3) ArrayStorageTest::testIcanGetAllMyStoredTasks
Failed asserting that a NULL is not empty.

4) ArrayStorageTest::testWeCanCleanUpAllPreviousTasks
Failed asserting that a NULL is not empty.

```
4º Just solve the errors. Create your implementation and you are done!
Easy and funny :)

## Contributors
Please, feel free to colaborate. Fork the project and check [issues][2].
We still have so much work ahead!.

## License

MIT
