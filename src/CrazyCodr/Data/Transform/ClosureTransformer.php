<?php

namespace CrazyCodr\Data\Transform;

/**
* This class represents a transformer based on a closure.
*
* The closure should accept three parameters: $data, $key and $transformedData, both not typed.
*
* The $data parameter is usually some kind of object or array of data while
* the $key is most oftenly a scalar string value representing the id of the item.
*
* The $transformedData parameter can and will be NULL on the first transformer call because nothing has been transformed yet
*
* @category Transformer organisation
* @package  CrazyCodr/Data-Transform
* @author   CrazyOne@CrazyCoders
* @license  MIT
* @link     crazycoders.net
*/
class ClosureTransformer implements TransformerInterface
{

    /**
     * Contains the closure represented by this transformer closure
     *
     * @var closure
     *
     * @access protected
     */
    protected $closure = NULL;

     /**
      * Builds a new ClosureTransformer by specifying the closure and options
      * 
      * @param \Closure $closure Closure to use while transforming data, must accept three parameters with no type hint, parameter1 $data, parameter2 $key, parameter3 $transformedData = NULL
      */
    public function __construct(\Closure $closure)
    {
        $this->setClosure($closure);
    }

    /**
     * Sets the closure used by this closure transformer when transforming data
     * Closure must accept three parameters with no type hint, parameter1 $data, parameter2 $key, parameter3 $transformedData = NULL
     * 
     * @param mixed \Closure Closure must accept three parameters with no type hint, parameter1 $data, parameter2 $key, parameter3 $transformedData = NULL
     *
     * @access public
     */
    public function setClosure(\Closure $closure)
    {
        $this->closure = $closure;
    }

    /**
     * Returns the current closure used to transform data
     *
     * @access public
     *
     * @return \Closure Closure used to transform data
     */
    public function getClosure()
    {
        return $this->closure;
    }

    /**
     * When called with data, a key and an optional current transformation state value, the function should attempt to resolve
     * some kind of transformation decision operation and return the newly changed item. If the item being passed in $transformedData
     * is an object and you think this object should not be modified, remember to clone the object.
     * 
     * @param mixed $data Data to be used in the filtering operation
     * @param mixed $key  Identification key to be used in the filtering operation
     * @param mixed $transformedData Current state of the transformation as already applied by other transformers
     * 
     * @return mixed New state of the transformed data
     */
    function transform($data, $key, $transformedData = NULL)
    {
        $closure = $this->getClosure();
        return $closure($data, $key, $transformedData);
    }

}