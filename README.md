
MAPHPReduce
------------

## Synopsis

A PHP implementation of [Map Reduce framework](http://en.wikipedia.org/wiki/MapReduce).

## Code Example

    <?php
    require 'vendor/autoload.php';

    use MAPHPReduce\MAPHPReduce;

    $mpr = new MAPHPReduce();
    $myTasks = array(1,2,3,4,5,6);

    $mpr->setTasks($myTasks);

    // We like closures, don't we ?
    $mpr->map(function($myJob) {
      return doMyjob($myJob); // set a reduced task
    });
    
    // So we have now all the reduced tasks in $allMyTasks
    $mpr->reduce(function($allmytasks) {
      reduceIt($allmytasks);
    });

## Motivation

Sometimes we have to work with a huge amount of data. 
[Lately](http://en.wikipedia.org/wiki/Big_data), more and more, and it's just no possible to work sequentially these times. 

So, the intention is to make an agile and encapsulated way to split a task in several child subtasks in parallel.

## Installation (Composer)

1. Clone the repo.
2. Create composer.json file the following content:
    {
        "autoload": {
            "psr-0": {"MAPHPReduce": "src/"}
        }
    }
3. Run "composer update" in your project folder.
4. See the `Usage` or `Code example` sections.
5. Enjoy!.

## API Reference

By the moment, there is just a few methods to work with:

    setTasks(array $tasks)
    map(Closure $fn)
    reduce(Closure $fn)

But in order to keep it agile, this won't change very much.

## Tests

Well, there is no tests yet !! It's a thing one can contribute.
See more about this [here](http://kpayne.me/2012/01/17/how-to-unit-test-fork/)

## Contributors
Please, feel free to colaborate. Fork the project and check [issues][2].
We still have so much work ahead!.

## License

MIT
