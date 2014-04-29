<?php

use Forker\Forker;
use Forker\Storage\ArrayStorage;

class ForkerTest extends PHPUnit_Framework_TestCase
{

  private $Forker = null;
  private $tasks    = array();

  public function setUp()
  {
    $this->tasks = array(
      'some', 'tasks', 'to', 'perform'
    );

    $storageSystem    = new ArrayStorage;
    $numberOfSubTasks = 4;
    $this->Forker   = new Forker($storageSystem, $this->tasks, $numberOfSubTasks);
  }

  /**
   * @expectedException Forker\Exception\ForkingErrorException
   */
  public function testWeThrowExceptionIfForkingError()
  {
    $forkError = -1;

    $mockForker = $this->getMock(
      'Forker\Forker', 
      array('getChildProces', 'child', 'waitForMyChildren'),
      array(new ArrayStorage, array())
    );

    $mockForker->expects($this->exactly(1))
                 ->method('getChildProces')
                 ->will($this->returnValue($forkError));
    
    $mockForker->map(function($foo) {});
  }

 /**
  * Each child should receive his task :)
  */
  public function testWeCanSendSubTasksForEachChild()
  {
    $storageSystem     = new ArrayStorage;
    $numberOfSubTasks  = 4;

    $mockForker = $this->getMock(
      'Forker\Forker', 
      array('getChildProces', 'child', 'waitForMyChildren'),
      array($storageSystem, $this->tasks, $numberOfSubTasks)
    );


    $mockForker->expects($this->exactly(4))
                 ->method('child');

    list($key,$value) = each($this->tasks);

    $mockForker->expects($this->at(1))
                 ->method('child')
                 ->with(array($key=>$value));

    list($key,$value) = each($this->tasks);
    $mockForker->expects($this->at(3))
                 ->method('child')
                 ->with(array($key=>$value));

    list($key,$value) = each($this->tasks);
    $mockForker->expects($this->at(5))
                 ->method('child')
                 ->with(array($key=>$value));

    list($key,$value) = each($this->tasks);
    $mockForker->expects($this->at(7))
                 ->method('child')
                 ->with(array($key=>$value));

    $mockForker->map(function($foo) {});
  }

  public function testWeCreateANewProcessForEachSubTask()
  {
    $storageSystem     = new ArrayStorage;
    $childProcessValue = 7;
    $numberOfSubTasks  = 4;

    $mockForker = $this->getMock(
      'Forker\Forker', 
      array('getChildProces', 'child', 'waitForMyChildren'),
      array($storageSystem, $this->tasks, $numberOfSubTasks)
    );

    $mockForker->expects($this->exactly(4))
                 ->method('getChildProces')
                 ->will($this->returnValue($childProcessValue));
    $mockForker->map(function($foo) {});
  }

  public function testWeCanCalculateSafeNumberOfWorkers()
  {
    
    // we try to set here a non divisor as a number of subtasks
    $numTasks = 16;
    $numberOfSubTaks = 7;
    $expectedNumOfWorkers = 8;

    $this->assertEquals(
      $expectedNumOfWorkers,
      $this->Forker->calculateNumberOfWorkers($numTasks, $numberOfSubTaks)
    );

    // If the number of sub tasks is a divisor of the number of tasks, let it
    $numTasks = 6;
    $numberOfSubTaks = 3;
    $expectedNumOfWorkers = 3;

    $this->assertEquals(
      $expectedNumOfWorkers,
      $this->Forker->calculateNumberOfWorkers($numTasks, $numberOfSubTaks)
    );

    // If the number is greater, then we let the number of subtasks
    $numTasks = 6;
    $numberOfSubTaks = 12;
    $expectedNumOfWorkers = 6;

    $this->assertEquals(
      $expectedNumOfWorkers,
      $this->Forker->calculateNumberOfWorkers($numTasks, $numberOfSubTaks)
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
      $this->Forker->giveMeMyTask($indexTask, $numberOfSubTasks)
    );
  }
}
