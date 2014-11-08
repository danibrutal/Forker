<?php
/****************************************************************
 * [Forker]
 *
 * Example: MapReduce example counts the appearance of each
word in a set of documents

 * Usage  : php examples/MapReduce/demo.map-reduce.php > test-mp
 * Storage: FileStorage
 ****************************************************************/
require 'vendor/autoload.php';

use Forker\Forker;
use Forker\Storage\FileStorage;

$myResult = 0;
$myTasks = array(
    'quijote-1.txt',
    'quijote-2.txt',
    'quijote-3.txt',
);

$numberOfSubTasks = 3;

$forker = new Forker(new FileStorage, $myTasks, $numberOfSubTasks);
$path   = dirname(__FILE__);

// MAP
$forker->fork(function($key, $fileName, $emit) use($path){

    $file_to_get = "$path/$fileName";
    $content     = file_get_contents($file_to_get);

    foreach(getUTF8Words($content) as $word) {
        $emit($word, 1);
    }

});

// REDUCE
$mapped = $forker->fetch();

// We dont set here the number of sub tasks, 
// since we don't know the total number
$forker = new Forker(new FileStorage('/tmp/reduced-words'), $mapped);

$forker->fork(function($word, $counts, $emit) {
    $emit($word, is_array($counts) ? count($counts) : 1);
});

$allWords = $forker->fetch();

arsort($allWords, SORT_NUMERIC);

// First 10 words most used :)
$cont = 10;

foreach($allWords as $word => $counts) {
    echo $word . " (". $counts .")\n";
    if (! --$cont) break;
}

//////////////////////////////////////////////////////////
function getUTF8Words($text)
{
    $match_arr = array();

    //http://stackoverflow.com/questions/10684183/extract-words-from-string-with-preg-match-all
    if(preg_match_all('/([a-zA-Z]|\xC3[\x80-\x96\x98-\xB6\xB8-\xBF]|\xC5[\x92\x93\xA0\xA1\xB8\xBD\xBE]){3,}/', $text, $match_arr)) {
        return $match_arr[0];
    }
    return array();
}