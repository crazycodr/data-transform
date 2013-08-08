<?php
include('../vendor/autoload.php');
use \CrazyCodr\Data\Transform as cdt;

//Utf-8 default support in case of bad configuration of server running the example
header('content-type: text/html; charset=utf-8');

//Setup sample data to work with
$data = array(
	array('make' => 'Yutsuma', 'model' => 'Smally', 'year' => 2011, 'properties' => 'a:3:{s:6:"engine";s:2:"v3";s:2:"hp";s:3:"160";s:10:"curbweight";i:1000;}'),
	array('make' => 'Yutsuma', 'model' => 'Ready', 'year' => 2012, 'properties' => 'a:3:{s:6:"engine";s:2:"v4";s:2:"hp";s:3:"210";s:10:"curbweight";i:1200;}'),
	array('make' => 'Yutsuma', 'model' => 'Biggy', 'year' => 2011, 'properties' => 'a:3:{s:6:"engine";s:2:"v6";s:2:"hp";s:3:"320";s:10:"curbweight";i:1400;}'),
	array('make' => 'Yutsuma', 'model' => 'Hugeyack', 'year' => 2013, 'properties' => 'a:3:{s:6:"engine";s:2:"v8";s:2:"hp";s:3:"425";s:10:"curbweight";i:1600;}'),
);

//Object to output items into
class MyApp_Models_Car
{
	public $make;
	public $model;
	public $year;
	public $properties;
}

//Setup a transforming iterator
$mytransformer = new cdt\TransformingIterator(new cdt\TransformerContainer(), $data);
$mytransformer->addTransformer(new cdt\ClosureTransformer(function($data, $key, $existingData){ 

	//Good stuff
	$a = new MyApp_Models_Car();
	$a->make = $data['make'];
	$a->model = $data['model'];
	$a->year = $data['year'];

	//Bad stuff, cause you assume in your models that the data will always be serialized so you'll have to unserialize it
	//Further more, it's serialized state comes from your provider, it then incumbs you to find a provider that give serialized data
	//if you need to change it, we'll look in example two how to fix this
	$a->properties = $data['properties'];

	//Return the new object, important
	return $a;

}));

//Transform the data by iterating it
foreach($mytransformer as $car)
{
	echo '<h1>Car found</h1>';
	echo '<ul>';
	echo '<li>Make: '.$car->make.'</li>';
	echo '<li>Model: '.$car->model.'</li>';
	echo '<li>Year: '.$car->year.'</li>';
	echo '<li>Properties: '.implode(', ', unserialize($car->properties)).'</li>';
	echo '</ul>';
	echo '<hr>';
}