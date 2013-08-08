<?php
class TransformingIteratorTest extends PHPUnit_Framework_TestCase
{

    public function testTransformerContainerInterface()
    {
        $a = new \CrazyCodr\Data\Transform\TransformingIterator(new \CrazyCodr\Data\Transform\TransformerContainer());
        $this->assertInstanceOf('\CrazyCodr\Data\Transform\TransformerContainerInterface', $a);
    }

    public function testGetTransformers()
    {
        $a = new \CrazyCodr\Data\Transform\TransformingIterator(new \CrazyCodr\Data\Transform\TransformerContainer());
        $this->assertCount(0, $a->getTransformers());
    }

    public function testAddTransformerWithoutName()
    {
        $a = new \CrazyCodr\Data\Transform\TransformingIterator(new \CrazyCodr\Data\Transform\TransformerContainer());
        $a->addTransformer(new \CrazyCodr\Data\Transform\ClosureTransformer(function($a){ return true; }));
        $this->assertCount(1, $a->getTransformers());
    }

    public function testAddTransformerWithName()
    {
        $a = new \CrazyCodr\Data\Transform\TransformingIterator(new \CrazyCodr\Data\Transform\TransformerContainer());
        $newName = $a->addTransformer(new \CrazyCodr\Data\Transform\ClosureTransformer(function($a){ return true; }), 'testTransformer');
        $this->assertEquals($newName, 'testTransformer');
    }

    public function testAddTransformers()
    {
        $a = new \CrazyCodr\Data\Transform\TransformingIterator(new \CrazyCodr\Data\Transform\TransformerContainer());
        $a->addTransformer(new \CrazyCodr\Data\Transform\ClosureTransformer(function($a){ return true; }));
        $a->addTransformer(new \CrazyCodr\Data\Transform\ClosureTransformer(function($a){ return true; }));
        $a->addTransformer(new \CrazyCodr\Data\Transform\ClosureTransformer(function($a){ return true; }));
        $a->addTransformer(new \CrazyCodr\Data\Transform\ClosureTransformer(function($a){ return true; }));
        $this->assertCount(4, $a->getTransformers());
    }

    /**
     * @expectedException \CrazyCodr\Data\Transform\TransformerNotFoundException
     */
    public function testSetInexistantTransform()
    {
        $a = new \CrazyCodr\Data\Transform\TransformingIterator(new \CrazyCodr\Data\Transform\TransformerContainer());
        $c = new \CrazyCodr\Data\Transform\ClosureTransformer(function($a){ return true; });
        $a->setTransformer($c, 'testTransformer');
    }

    public function testSetExistantTransform()
    {
        $a = new \CrazyCodr\Data\Transform\TransformingIterator(new \CrazyCodr\Data\Transform\TransformerContainer());
        $c = new \CrazyCodr\Data\Transform\ClosureTransformer(function($a){ return true; });
        $a->addTransformer($c, 'testTransformer');
        $this->assertEquals($a->setTransformer($c, 'testTransformer'), 'testTransformer');
    }

    public function testHasTransformer()
    {
        $a = new \CrazyCodr\Data\Transform\TransformingIterator(new \CrazyCodr\Data\Transform\TransformerContainer());
        $c = new \CrazyCodr\Data\Transform\ClosureTransformer(function($a){ return true; });
        $a->addTransformer($c, 'testTransformer');
        $this->assertTrue($a->hasTransformer('testTransformer'));
    }

    public function testHasTransformerNotFound()
    {
        $a = new \CrazyCodr\Data\Transform\TransformingIterator(new \CrazyCodr\Data\Transform\TransformerContainer());
        $c = new \CrazyCodr\Data\Transform\ClosureTransformer(function($a){ return true; });
        $this->assertFalse($a->hasTransformer('testTransformer'));
    }

    public function testRemoveTransformer()
    {
        $a = new \CrazyCodr\Data\Transform\TransformingIterator(new \CrazyCodr\Data\Transform\TransformerContainer());
        $c = new \CrazyCodr\Data\Transform\ClosureTransformer(function($a){ return true; });
        $a->addTransformer($c, 'testTransformer');
        $a->removeTransformer('testTransformer');
        $this->assertFalse($a->hasTransformer('testTransformer'));
    }

    /**
     * @expectedException \CrazyCodr\Data\Transform\TransformerNotFoundException
     */
    public function testRemoveTransformerNotFound()
    {
        $a = new \CrazyCodr\Data\Transform\TransformingIterator(new \CrazyCodr\Data\Transform\TransformerContainer());
        $c = new \CrazyCodr\Data\Transform\ClosureTransformer(function($a){ return true; });
        $a->removeTransformer('testTransformer');
    }

    public function testClearTransformers()
    {
        $a = new \CrazyCodr\Data\Transform\TransformingIterator(new \CrazyCodr\Data\Transform\TransformerContainer());
        $a->addTransformer(new \CrazyCodr\Data\Transform\ClosureTransformer(function($a){ return true; }));
        $a->addTransformer(new \CrazyCodr\Data\Transform\ClosureTransformer(function($a){ return true; }));
        $a->addTransformer(new \CrazyCodr\Data\Transform\ClosureTransformer(function($a){ return true; }));
        $a->addTransformer(new \CrazyCodr\Data\Transform\ClosureTransformer(function($a){ return true; }));
        $a->clearTransformers();
        $this->assertCount(0, $a->getTransformers());
    }

    public function testTransform()
    {

        //Setup the transformer
        $a = new \CrazyCodr\Data\Transform\TransformingIterator(new \CrazyCodr\Data\Transform\TransformerContainer());
        $a->addTransformer(new \CrazyCodr\Data\Transform\ClosureTransformer(function($a, $b, $c){ return $a; }));
        $a->addTransformer(new \CrazyCodr\Data\Transform\ClosureTransformer(function($a, $b, $c){ $c['age'] += 10; return $c; }));
        $a->addTransformer(new \CrazyCodr\Data\Transform\ClosureTransformer(function($a, $b, $c){ $c['name'] = strtolower($c['name']); return $c; }));

        //Setup test data
        $input = array(
            array('name' => 'Mathieu', 'type' => 'programmer', 'sex' => 'male', 'age' => 30),
            array('name' => 'David', 'type' => 'manager', 'sex' => 'male', 'age' => 35),
            array('name' => 'Jean-Michel', 'type' => 'manager', 'sex' => 'male', 'age' => 30),
            array('name' => 'Frédéric', 'type' => 'integrator', 'sex' => 'male', 'age' => 25),
            array('name' => 'Eric', 'type' => 'integrator', 'sex' => 'male', 'age' => 30),
            array('name' => 'Philippe', 'type' => 'designer', 'sex' => 'male', 'age' => 30),
            array('name' => 'Caroline', 'type' => 'project manager', 'sex' => 'female', 'age' => 30),
            array('name' => 'Joelle', 'type' => 'project manager', 'sex' => 'female', 'age' => 30),
            array('name' => 'Jocelyne', 'type' => 'project manager', 'sex' => 'female', 'age' => 45),
            array('name' => 'Capucine', 'type' => 'sales', 'sex' => 'female', 'age' => 30),
            array('name' => 'Françis', 'type' => 'sales', 'sex' => 'male', 'age' => 30),
            array('name' => 'Manon', 'type' => 'manager', 'sex' => 'female', 'age' => 45),
        );
        $output = array(
            array('name' => 'mathieu', 'type' => 'programmer', 'sex' => 'male', 'age' => 40),
            array('name' => 'david', 'type' => 'manager', 'sex' => 'male', 'age' => 45),
            array('name' => 'jean-michel', 'type' => 'manager', 'sex' => 'male', 'age' => 40),
            array('name' => 'frédéric', 'type' => 'integrator', 'sex' => 'male', 'age' => 35),
            array('name' => 'eric', 'type' => 'integrator', 'sex' => 'male', 'age' => 40),
            array('name' => 'philippe', 'type' => 'designer', 'sex' => 'male', 'age' => 40),
            array('name' => 'caroline', 'type' => 'project manager', 'sex' => 'female', 'age' => 40),
            array('name' => 'joelle', 'type' => 'project manager', 'sex' => 'female', 'age' => 40),
            array('name' => 'jocelyne', 'type' => 'project manager', 'sex' => 'female', 'age' => 55),
            array('name' => 'capucine', 'type' => 'sales', 'sex' => 'female', 'age' => 40),
            array('name' => 'françis', 'type' => 'sales', 'sex' => 'male', 'age' => 40),
            array('name' => 'manon', 'type' => 'manager', 'sex' => 'female', 'age' => 55),
        );
        $a->setDatasource($input);
        
        //Assert
        foreach($a as $k => $outputItem)
        {
            $this->assertEquals($outputItem, $output[$k]);
        }

    }

}