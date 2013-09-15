[![Latest Stable Version](https://poser.pugx.org/crazycodr/data-transform/version.png)](https://packagist.org/packages/crazycodr/data-transform) [![Total Downloads](https://poser.pugx.org/crazycodr/data-transform/downloads.png)](https://packagist.org/packages/crazycodr/data-transform) [![Build Status](https://travis-ci.org/crazycodr/data-transform.png?branch=master)](https://travis-ci.org/crazycodr/data-transform)
CrazyCodr/Data/Transform
======================
This package contains facilities to easily transform live iterated data from any enumerable source.

This class features a transforming iterator accompagnied by different classes that you can use to transform incoming data from any iteratable data-source.

Table of contents
-----------------
1. [Installation](#installation)
2. [Creating a basic transforming iterator](#creating-a-basic-transforming-iterator)
3. [Supporting many transformers at once](#supporting-many-transformers-at-once)
4. [Using components outside of the iterator context](#using-components-outside-of-the-iterator-context)
5. [Creating your own testable classes](#creating-your-own-testable-classes)

Installation
------------

To install it, just include this requirement into your composer.json

```json
{
    "require": {
        "crazycodr/data-transform": "2.*"
    }
}
``` 

And then run composer install/update as necessary.

Creating a basic transforming iterator
--------------------------------------

Creating a transforming iterator requires at least three items:

1. A TransformerContainer used to contain the different transformers for your iterator
2. A TransformingIterator used to iterate your data and provide transforming features
3. A Transformer used to transform the current data into something new

(Note: This code assumes that you have an array based datasource with columns: name, type, sex and birthdate)

(Note: This code assumes that you have a class called Employe that mimics the previous datasource + a new property called age)

```PHP
//Create an employee transformer
$employeeTransformer = new ClosureTransformer(function($data, $key, $previous){ 
	$result = new Employee();
	$result->setName($data['name']);
	$result->setType($data['type']);
	$result->setSex($data['sex']);
	$result->setBirthdate($data['birthdate']);
	return $result;
});
$iterator = new TransformingIterator(new TransformerContainer(), $data);
$iterator->addTransformer($employeeTransformer);

//Iterate our data source and automatically only get males
foreach($iterator as $employee)
{
	echo 'Employee: '.$employee->getName().'<br>';
}
```

Supporting many transformers at once
------------------------------------

You can add many transformers at once to the transformer container. The goal being to separate the different transformation aspects.

This example demonstrate that the first transformer creates the employee but the second initializes some other property manually. (Age vs Birthdate) Just note that the second transformer must use the $previous variable to obtain the current state of the transformed object, you don't want to recreate the object again.

```PHP
//Create an employee transformer
$employeeTransformer = new ClosureTransformer(function($data, $key, $previous){ 
	$result = new Employee();
	$result->setName($data['name']);
	$result->setType($data['type']);
	$result->setSex($data['sex']);
	$result->setBirthdate($data['birthdate']);
	return $result;
});
$ageCalculatorTransformer = new ClosureTransformer(function($data, $key, $previous){ 
	$birthday = new DateTime($previous->getBirthday());
	$interval = $birthday->diff(new DateTime());
	$previous->setAge($interval->y);
	return $previous;
});
$iterator = new TransformingIterator(new TransformerContainer(), $data);
$iterator->addTransformer($employeeTransformer);
$iterator->addTransformer($ageCalculatorTransformer);

//Iterate our data source and automatically only get males
foreach($iterator as $employee)
{
	echo 'Employee: '.$employee->getName().' is '.$employee->getAge().' years old<br>';
}
```

Using components outside of the iterator context
------------------------------------------------

You don't need to use a transforming iterator... The ClosureTransformer and TransformerContainer can be used outside of a loop. Build transformers normally using concrete/non-concrete classes and call "transform" with some data.

```PHP
//See code in previous snipet
$container = new TransformerContainer();
$container->addTransformer($employeeTransformer);
$container->addTransformer($ageCalculatorTransformer);

$result = $container->transform($data, null);
```

Creating your own testable classes
----------------------------------

The point of this library is not to have to create the iterators and they sub-components each time and be able
to test the lot easily. To this end, simply create concrete extensions of your iterators and sub-components and 
then test them.

```PHP
class EmployeeTransformer extends ClosureTransformer
{
	public function __construct()
	{
		parent::__construct(function($data, $key, $previous){ 
			$result = new Employee();
			$result->setName($data['name']);
			$result->setType($data['type']);
			$result->setSex($data['sex']);
			$result->setBirthdate($data['birthdate']);
			return $result;
		});
	}
}
```

```PHP
class EmployeeTransformingIterator extends TransformingIterator
{
	public function __construct($data)
	{
		parent::__construct(new TransformerContainer(), $data);
		$this->addTransformer(new EmployeeTransformer());
	}
}
```

It might look extreme but this way you are creating a concrete functional class that can be reused and tested.
Note that DataProviders are a great way to test your components but it will look strange to use a 
DataProvider when testing the iterators.

```PHP
class EmployeeTransformerTest extends PHPUnit_Framework_TestCase
{

	/**
	* @dataProvider employeeTransformerDataProvider
	*/
	public function testTransform($data)
	{
		$transformer = new EmployeeTransformer();
		$this->assertEquals($data['expected'], $transformer->transform($data['testdata'], null));
	}
	
	public function employeeTransformerDataProvider()
	{
		//To contain the results returned by the data provider
		$result = array();
		
		//Item 1
		$emp = new Employee();
		$emp->setName('John doe');
		$emp->setBirthdate('1768-01-01');
		$emp->setSex('male');
		$result[] = array(
			'expected' => $emp,
			'testdata' => array('name' => 'John doe', 'birthdate' => '1768-01-01', 'sex' => 'male'),
		);
		
		//Item 2
		$emp = new Employee();
		$emp->setName('Jane doe');
		$emp->setBirthdate('1768-01-01');
		$emp->setSex('female');
		$result[] = array(
			'expected' => $emp,
			'testdata' => array('name' => 'Jane doe', 'birthdate' => '1768-01-01', 'sex' => 'female'),
		);
		
		return $results;
		
	}
	
}
```
