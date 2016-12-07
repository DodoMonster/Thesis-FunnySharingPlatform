<?php 
require('Search.php'); 
$s = new Search([ 'snippet_fields' => ['title', 'content'], 'field_weights' => ['title' => 20, 'content' => 10], ]); 
$s->setSortMode(SPH_SORT_EXTENDED, 'created desc,@weight desc'); 
//$s->setSortBy('created desc,@weight desc'); 
$words = $s->wordSplit("MySQL复制"); 
$res = $s->query($words, 0, 10, 'master'); 
var_dump($res);

