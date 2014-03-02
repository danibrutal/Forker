MAPHPReduce
------------

## Synopsis

A PHP implementation of [Map Reduce framework](http://en.wikipedia.org/wiki/MapReduce).

## Code Example

```php
<?php
/**
 * Example : 
 * Summation of a list of numbers
 */
use MAPHPReduce\MAPHPReduce;
use MAPHPReduce\Storage\ArrayStorage;

$myTasks = array(
  '0'=>array(1,2),
  '1'=>array(3,4),
  '2'=>array(5,6)
);

// a way to keep our data
$storageSystem = new ArrayStorage($myTasks);

$numberOfSubTasks = 3;

$mpr = new MAPHPReduce($numberOfSubTasks);
$mpr->setStoreSystem($storageSystem);

// My job here is [1,2] , [3,4] , [5,6]
$mpr->map(function($myJob) {
  var_dump($myJob);
  return array(
    array_sum($myJob)
  );
});

$mpr->reduce(function($allmytasks) {
  var_dump($allmytasks);
  var_dump(array_sum($allmytasks));
});
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
