<?php
class ClosureTransformerTest extends PHPUnit_Framework_TestCase
{

    public function testFilterInterface()
    {
        $a = new \CrazyCodr\Data\Transform\ClosureTransformer(function($data, $key){ return true; });
        $this->assertInstanceOf('\CrazyCodr\Data\Transform\TransformerInterface', $a);
    }

    public function testClosureResult()
    {
        $a = new \CrazyCodr\Data\Transform\ClosureTransformer(function($data, $key, $existing = NULL){ return array_merge(($existing == NULL ? array() : $existing), array('a' => $data[0], 'b' => $data[1])); });
        $this->assertEquals($a->transform(array('bah', 'sup'), 1, null), array('a' => 'bah', 'b' => 'sup'));
        $this->assertEquals($a->transform(array('bah', 'sup'), 1, array('e' => 7)), array('a' => 'bah', 'b' => 'sup', 'e' => 7));
    }

    public function testGetSetClosure()
    {
        $b = function($data, $key){ return false; };
        $c = function($data, $key){ return true; };
        $a = new \CrazyCodr\Data\Transform\ClosureTransformer($b);
        $this->assertEquals($a->getClosure(), $b);
        $a->setClosure($c);
        $this->assertEquals($a->getClosure(), $c);
    }

}