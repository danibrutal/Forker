<?php
/**************************************************
 * [Forker]
 *
 * Example: Sum of 10 firsts numbers in parallel
 * Usage : php demo.sum.php 
 * Storage: Memcache
 **************************************************/
require 'vendor/autoload.php';

use Forker\Forker;
use Forker\Storage\FileStorage;

function getUTF8Words($text)
{
  $match_arr = array();
  
  //http://stackoverflow.com/questions/10684183/extract-words-from-string-with-preg-match-all
  if(preg_match_all('/([a-zA-Z]|\xC3[\x80-\x96\x98-\xB6\xB8-\xBF]|\xC5[\x92\x93\xA0\xA1\xB8\xBD\xBE]){4,}/', $text, $match_arr)) {
    return $match_arr[0];
  }

  return array();
}

$myResult = 0;

$myTasks = array(
  'quijote-1.txt',
  'quijote-2.txt',
  'quijote-3.txt',
);

$numberOfSubTasks = 3;

$forker = new Forker(new FileStorage(), $myTasks, $numberOfSubTasks);
$path   = dirname(__FILE__); 

$forker->map(function(array $fileName, $emit) use($path){
  
  $file_to_get = $path ."/". current($fileName);
  $content     = file_get_contents($file_to_get);
  
  foreach(getUTF8Words($content) as $word) {
    $emit($word, 1);
  }

});

//$all = $forker->fetch();

//var_dump($all);
