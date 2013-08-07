<?php

namespace CrazyCodr\Data\Transform;

/**
* This interface dictates what a transformer should be able to do naturaly
*
* @category Interfaces
* @package  CrazyCodr/Data-Transform
* @author   CrazyOne@CrazyCoders
* @license  MIT
* @link     crazycoders.net
*/
interface TransformerInterface
{

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
	function transform($data, $key, $transformedData = NULL);

}