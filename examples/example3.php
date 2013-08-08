<?php
include('../vendor/autoload.php');
use \CrazyCodr\Data\Grouper as cdg;

//Utf-8 default support in case of bad configuration
header('content-type: text/html; charset=utf-8');

//Setup sample data to work with
$data = array(
	array('name' => 'Mathieu', 'type' => 'programmer', 'sex' => 'male', 'age' => 30),
	array('name' => 'David', 'type' => 'manager', 'sex' => 'male', 'age' => 35),
	array('name' => 'Jean-Michel', 'type' => 'manager', 'sex' => 'male', 'age' => 30),
	array('name' => 'Frédéric', 'type' => 'integrator', 'sex' => 'male', 'age' => 25),
	array('name' => 'Éric', 'type' => 'integrator', 'sex' => 'male', 'age' => 30),
	array('name' => 'Philippe', 'type' => 'designer', 'sex' => 'male', 'age' => 30),
	array('name' => 'Caroline', 'type' => 'project manager', 'sex' => 'female', 'age' => 30),
	array('name' => 'Joelle', 'type' => 'project manager', 'sex' => 'female', 'age' => 30),
	array('name' => 'Jocelyne', 'type' => 'project manager', 'sex' => 'female', 'age' => 45),
	array('name' => 'Capucine', 'type' => 'sales', 'sex' => 'female', 'age' => 30),
	array('name' => 'Françis', 'type' => 'sales', 'sex' => 'male', 'age' => 30),
	array('name' => 'Manon', 'type' => 'manager', 'sex' => 'female', 'age' => 45),
);

//Setup a grouping iterator that will group employees by type and count them
$mygrouper = new cdg\GroupingIterator(new cdg\GroupResult(), $data);
$mygrouper->addGrouper(new cdg\ClosureGrouper(function($a){ return $a['type']; }));
$mygrouper->addAggregator(new cdg\MaxClosureAggregator(function($a){ return $a['age']; }, 'oldest'));

//Display the data by iterating the group
//Note that on "rewind", first operation of the foreach, all data is precompiled, this is the only possible way
//to do it, thus, large or slow data-sources may end up slowing your iteration repetitive calls to foreach
$mygrouper->rewind();
foreach($mygrouper->getGroups() as $group)
{
	echo '<h1>Group '.$group->getGroupValue().'</h1>';
	echo 'Oldest employee is '.$group->getAggregationValue('oldest').' years old<br>';
	echo '<hr>';
}