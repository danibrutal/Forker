<?php

use MAPHPReduce\MAPHPReduce;
use MAPHPReduce\Storage\ArrayStorage;

class MAPHPReduceTest extends PHPUnit_Framework_TestCase
{

  private $splitter = null;
  private $tasks    = array();

  public function setUp()
  {
    $this->tasks = array(
      'some', 'tasks', 'to', 'perform'
    );

    $storageSystem    = new ArrayStorage;
    $numberOfSubTasks = 4;
    $this->splitter   = new MAPHPReduce($storageSystem, $this->tasks, $numberOfSubTasks);
  }

  public function testWeCanSendSubTasksForEachChild()
  {
    $storageSystem     = new ArrayStorage;
    $numberOfSubTasks  = 4;

    $mockSplitter = $this->getMock(
      'MAPHPReduce\MAPHPReduce', 
      array('getChildProces', 'child', 'waitForMyChildren'),
      array($storageSystem, $this->tasks, $numberOfSubTasks)
    );

    $mockSplitter->expects($this->exactly(4))
                 ->method('child');

    $mockSplitter->map(function($foo) {});
  }

  public function testWeCreateANewProcessForEachSubTask()
  {
    $storageSystem     = new ArrayStorage;
    $childProcessValue = 7;
    $numberOfSubTasks  = 4;

    $mockSplitter = $this->getMock(
      'MAPHPReduce\MAPHPReduce', 
      array('getChildProces', 'child', 'waitForMyChildren'),
      array($storageSystem, $this->tasks, $numberOfSubTasks)
    );

    $mockSplitter->expects($this->exactly(4))
                 ->method('getChildProces')
                 ->will($this->returnValue($childProcessValue));
    $mockSplitter->map(function($foo) {});
  }

  public function testWeCanCalculateSafeNumberOfWorkers()
  {
    
    // we try to set here a non divisor as a number of subtasks
    $numTasks = 16;
    $numberOfSubTaks = 7;
    $expectedNumOfWorkers = 8;

    $this->assertEquals(
      $expectedNumOfWorkers,
      $this->splitter->calculateNumberOfWorkers($numTasks, $numberOfSubTaks)
    );

    // If the number of sub tasks is a divisor of the number of tasks, let it
    $numTasks = 6;
    $numberOfSubTaks = 3;
    $expectedNumOfWorkers = 3;

    $this->assertEquals(
      $expectedNumOfWorkers,
      $this->splitter->calculateNumberOfWorkers($numTasks, $numberOfSubTaks)
    );

    // If the number is greater, then we let the number of subtasks
    $numTasks = 6;
    $numberOfSubTaks = 12;
    $expectedNumOfWorkers = 6;

    $this->assertEquals(
      $expectedNumOfWorkers,
      $this->splitter->calculateNumberOfWorkers($numTasks, $numberOfSubTaks)
    );
  }

  public function testWeCanSplitTaks()
  {
    
    // half
    $expectedTask = array(2=>'to', 3=>'perform');
    $indexTask    =1;
    $numberOfSubTasks = 2;

    $this->assertEquals(
      $expectedTask,
      $this->splitter->giveMeMyTask($indexTask, $numberOfSubTasks)
    );
  }
}
