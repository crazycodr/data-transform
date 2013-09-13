[![Build Status](https://travis-ci.org/crazycodr/data-transform.png?branch=master)](https://travis-ci.org/crazycodr/data-transform)

CrazyCodr/Data/Transformer
==========================

This package contains facilities to easily transform data from any enumerable source.

This class features a single transforming iterator accompagnied by transformer container and a closure transformer adapter that you can use to transform out data as you iterate it. It's goal is to cleanup your code that gets bloaty when importing or transforming data that doesn't come from an existing data partnership such as CSV files.

Why should i use it?
--------------------

1. Every class is designed to be extended to create concrete transformation classes which makes for better TDD
2. The iterator can be changed live (add/remove transformers) to change the behavior of the iterator
3. You can use your transformers outside the scope of an iteration using $transformer->transform() which could help in other situations, not just the iteration/input transformation

How to use
----------

1. Create a datasource that can be iterated (array, iterator, etc)
2. Create ClosureTransformer objects that will execute what you want to transform
3. Create a TransformerContainer and add all transformers to it
4. Create a TransformingIterator and add the datasource and TransformerContainer to it
5. Iterate, rinse, repeat...

Whats next for you?
-------------------

1. Download the package through composer "crazycodr/data-transformer" and then look in the documentation directory of the package to know more about it, it's actually quite simple
2. Look at the examples
3. Try and build some tests using real life data such as CSV files
4. Use in production

A few quick examples
--------------------

**Basic setup for all examples below**

This code here will be used in all examples, paste it in front of each example, it will save use some display space.

```PHP
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
```

**Basic usage**

The fun aspect of this class is that you can prepare transformation closures in advance, they could be simple closures in another file that you test using unit tests. In this case, we prepare those closures in the example and add them to the transformation process.

```PHP
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
```
Results in
```HTML
<h1>Car found</h1><ul><li>Make: Yutsuma</li><li>Model: Smally</li><li>Year: 2011</li><li>Properties: v3, 160, 1000</li></ul><hr><h1>Car found</h1><ul><li>Make: Yutsuma</li><li>Model: Ready</li><li>Year: 2012</li><li>Properties: v4, 210, 1200</li></ul><hr><h1>Car found</h1><ul><li>Make: Yutsuma</li><li>Model: Biggy</li><li>Year: 2011</li><li>Properties: v6, 320, 1400</li></ul><hr><h1>Car found</h1><ul><li>Make: Yutsuma</li><li>Model: Hugeyack</li><li>Year: 2013</li><li>Properties: v8, 425, 1600</li></ul><hr>
```

**Support for multiple transformers in a chain**

By default, the TransformingIterator uses a simple TransformerClosure but you can use many in a chain if you want. The principle is that the first transformer will create the initial transform and all other transformers will work on "existing data" to provide a more refined version of the transformed result. The reason to adopt this method is to segment your transformation process into small units that don't necessarily work together. It makes for better testing and better code segmentation.

```PHP
//Setup a transforming iterator
$mytransformer = new cdt\TransformingIterator(new cdt\TransformerContainer(), $data);
$mytransformer->addTransformer(new cdt\ClosureTransformer(function($data, $key, $existingData){ 

	//Good stuff
	$a = new MyApp_Models_Car();
	$a->make = $data['make'];
	$a->model = $data['model'];
	$a->year = $data['year'];

	//Return the new object, important
	return $a;

}));
$mytransformer->addTransformer(new cdt\ClosureTransformer(function($data, $key, $existingData){

	//Setup the properties that should exist
	$existingData->properties = array(
		'engine' => NULL,
		'horsepower' => NULL,
		'curbweight' => NULL,
	);

	//Good stuff, now we extract the data from the properties, prepare the properties in advance and copy them in a proprietary format
	//If the input format changes, i don't have to be scared, i just change my transformer and my code stays the same
	$existingData->properties = array_merge($existingData->properties, unserialize($data['properties']));

	//Return the new object, important
	return $existingData;

}));

//Transform the data by iterating it
foreach($mytransformer as $car)
{
	echo '<h1>Car found</h1>';
	echo '<ul>';
	echo '<li>Make: '.$car->make.'</li>';
	echo '<li>Model: '.$car->model.'</li>';
	echo '<li>Year: '.$car->year.'</li>';
	echo '<li>Properties: <ul>';
	echo '<li>Engine cylinders: '.$car->properties['engine'].'</li>';
	echo '<li>Horsepower deployed: '.$car->properties['horsepower'].' HP</li>';
	echo '<li>Curb weight: '.$car->properties['curbweight'].' kg</li>';
	echo '</ul></li>';
	echo '</ul>';
	echo '<hr>';
}
```
Results in
```HTML
<h1>Car found</h1><ul><li>Make: Yutsuma</li><li>Model: Smally</li><li>Year: 2011</li><li>Properties: <ul><li>Engine cylinders: v3</li><li>Horsepower deployed:  HP</li><li>Curb weight: 1000 kg</li></ul></li></ul><hr><h1>Car found</h1><ul><li>Make: Yutsuma</li><li>Model: Ready</li><li>Year: 2012</li><li>Properties: <ul><li>Engine cylinders: v4</li><li>Horsepower deployed:  HP</li><li>Curb weight: 1200 kg</li></ul></li></ul><hr><h1>Car found</h1><ul><li>Make: Yutsuma</li><li>Model: Biggy</li><li>Year: 2011</li><li>Properties: <ul><li>Engine cylinders: v6</li><li>Horsepower deployed:  HP</li><li>Curb weight: 1400 kg</li></ul></li></ul><hr><h1>Car found</h1><ul><li>Make: Yutsuma</li><li>Model: Hugeyack</li><li>Year: 2013</li><li>Properties: <ul><li>Engine cylinders: v8</li><li>Horsepower deployed:  HP</li><li>Curb weight: 1600 kg</li></ul></li></ul><hr><br />
<b>Warning</b>:  array_merge() [<a href='function.array-merge'>function.array-merge</a>]: Argument #2 is not an array in <b>/home/crazycod/subdomains/labs.crazycoders.net/data-transformer/examples/example2.php</b> on line <b>50</b><br />
```