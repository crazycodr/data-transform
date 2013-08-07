<?php

namespace CrazyCodr\Data\Transform;

/**
* This exception is raised when an operation tries to access a transformer that doesn't exist in a transformer container
*
* @uses     \OutOfRangeException
*
* @category Exceptions
* @package  CrazyCodr/Data-Transform
* @author   CrazyOne@CrazyCoders
* @license  MIT
* @link     crazycoders.net  
*/
class TransformerNotFoundException extends \OutOfRangeException
{

    /**
     * Builds a new exception
     * 
     * @param mixed $name Name of the intended transformer that failed
     *
     * @access public
     */
	public function __construct($name)
	{
		parent::__construct('Transformer named "'.htmlentities($name).'" not found in current transformer container');
	}

}