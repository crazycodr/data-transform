<?php
class TransformerContainerTest extends PHPUnit_Framework_TestCase
{

    public function testTransformerContainerInterface()
    {
        $a = new \CrazyCodr\Data\Transform\TransformerContainer();
        $this->assertInstanceOf('\CrazyCodr\Data\Transform\TransformerContainerInterface', $a);
    }

    public function testGetTransformers()
    {
        $a = new \CrazyCodr\Data\Transform\TransformerContainer();
        $this->assertCount(0, $a->getTransformers());
    }

    public function testAddTransformerWithoutName()
    {
        $a = new \CrazyCodr\Data\Transform\TransformerContainer();
        $a->addTransformer(new \CrazyCodr\Data\Transform\ClosureTransformer(function($a){ return true; }));
        $this->assertCount(1, $a->getTransformers());
    }

    public function testAddTransformerWithName()
    {
        $a = new \CrazyCodr\Data\Transform\TransformerContainer();
        $newName = $a->addTransformer(new \CrazyCodr\Data\Transform\ClosureTransformer(function($a){ return true; }), 'testTransformer');
        $this->assertEquals($newName, 'testTransformer');
    }

    public function testAddTransformers()
    {
        $a = new \CrazyCodr\Data\Transform\TransformerContainer();
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
        $a = new \CrazyCodr\Data\Transform\TransformerContainer();
        $c = new \CrazyCodr\Data\Transform\ClosureTransformer(function($a){ return true; });
        $a->setTransformer($c, 'testTransformer');
    }

    public function testSetExistantTransform()
    {
        $a = new \CrazyCodr\Data\Transform\TransformerContainer();
        $c = new \CrazyCodr\Data\Transform\ClosureTransformer(function($a){ return true; });
        $a->addTransformer($c, 'testTransformer');
        $this->assertEquals($a->setTransformer($c, 'testTransformer'), 'testTransformer');
    }

    public function testHasTransformer()
    {
        $a = new \CrazyCodr\Data\Transform\TransformerContainer();
        $c = new \CrazyCodr\Data\Transform\ClosureTransformer(function($a){ return true; });
        $a->addTransformer($c, 'testTransformer');
        $this->assertTrue($a->hasTransformer('testTransformer'));
    }

    public function testHasTransformerNotFound()
    {
        $a = new \CrazyCodr\Data\Transform\TransformerContainer();
        $c = new \CrazyCodr\Data\Transform\ClosureTransformer(function($a){ return true; });
        $this->assertFalse($a->hasTransformer('testTransformer'));
    }

    public function testRemoveTransformer()
    {
        $a = new \CrazyCodr\Data\Transform\TransformerContainer();
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
        $a = new \CrazyCodr\Data\Transform\TransformerContainer();
        $c = new \CrazyCodr\Data\Transform\ClosureTransformer(function($a){ return true; });
        $a->removeTransformer('testTransformer');
    }

    public function testClearTransformers()
    {
        $a = new \CrazyCodr\Data\Transform\TransformerContainer();
        $a->addTransformer(new \CrazyCodr\Data\Transform\ClosureTransformer(function($a){ return true; }));
        $a->addTransformer(new \CrazyCodr\Data\Transform\ClosureTransformer(function($a){ return true; }));
        $a->addTransformer(new \CrazyCodr\Data\Transform\ClosureTransformer(function($a){ return true; }));
        $a->addTransformer(new \CrazyCodr\Data\Transform\ClosureTransformer(function($a){ return true; }));
        $a->clearTransformers();
        $this->assertCount(0, $a->getTransformers());
    }

    public function testTransform()
    {
        $a = new \CrazyCodr\Data\Transform\TransformerContainer();
        $a->addTransformer(new \CrazyCodr\Data\Transform\ClosureTransformer(function($a, $b, $c){ $c['a'] = $a['a']; return $c; }));
        $a->addTransformer(new \CrazyCodr\Data\Transform\ClosureTransformer(function($a, $b, $c){ $c['b'] = $a['b']; return $c; }));
        $a->addTransformer(new \CrazyCodr\Data\Transform\ClosureTransformer(function($a, $b, $c){ $c['c'] = $a['c']; return $c; }));
        $a->addTransformer(new \CrazyCodr\Data\Transform\ClosureTransformer(function($a, $b, $c){ $c['d'] = $a['d']; return $c; }));
        $this->assertEquals($a->transform(array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4), 'bah', NULL), array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4));
        $this->assertEquals($a->transform(array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4), 'bah', array('z' => 26)), array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'z' => 26));
    }

}