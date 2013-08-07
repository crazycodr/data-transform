<?php

namespace CrazyCodr\Data\Transform;

/**
* This exception is raised when an operation tries to add a transformer that with a name that already exists in a transformer container
*
* @uses     \OutOfRangeException
*
* @category Exceptions
* @package  CrazyCodr/Data-Transform
* @author   CrazyOne@CrazyCoders
* @license  MIT
* @link     crazycoders.net  
*/
class TransformerAlreadyExistsException extends \OutOfRangeException
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
		parent::__construct('Transformer named "'.htmlentities($name).'" is already present in current transformer container');
	}

}