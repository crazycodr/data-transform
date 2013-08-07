<?php

namespace CrazyCodr\Data\Transform;

/**
* The transform iterator class is an iterator over an iterator that features 
* facilities to easily transform content of an enumerable using anonymous functions/closures
* or other types of transformInterfaces.
*
* Important note, the transform iterator transforms data from it's datasource in a live fashion
* it doesn't preprocess everything. Therefore, you can add/remove transforms as you read data and
* change the way the transform iterator operates.
*
* @uses     iterator
*
* @abstract
*
* @category Base class
* @package  CrazyCodr/Data-Transform
* @author   CrazyOne@CrazyCoders
* @license  MIT
* @link     crazycoders.net
*/
class TransformingIterator implements \iterator, TransformerContainerInterface
{

    /**
     * Contains the datasource that will be iterated over applying transformation functions to it
     *
     * @var \Traversable
     *
     * @access protected
     */
    protected $datasource = NULL;

    /**
     * Contains the transformer container used to contain the transformers for this transformer iterator
     *
     * @var TransformerContainerInterface
     *
     * @access protected
     */
    protected $container = NULL;

    /**
     * Contains the transformed data as of now after the next move has been done
     *
     * @var mixed
     *
     * @access protected
     */
    protected $transformedData = NULL;

    /**
     * Builds the TransformerIterator using a specific datasource
     *
     * @param TransformerContainerInterface Transformer container that will be proxied to store the different transformers
     * @param \Traversable $datasource Contains the datasource that will be iterated and transformed
     *
     * @throws \InvalidArgumentException Thrown if $datasource is not an array or traversable
     *
     * @access public
     */
    public function __construct(TransformerContainerInterface $transformerContainer, $datasource = NULL)
    {

        //Set the datasource
        $this->setDatasource($datasource);

        //Set the transformer container
        $this->setTransformerContainer($transformerContainer);

    }

    /**
     * Sets the datasource to be used in the iteration context
     * 
     * @param \Traversable $datasource Datasource to be used in iteration context
     *
     * @throws \InvalidArgumentException Thrown if $datasource is not an array or traversable
     *
     * @access public
     */
    public function setDatasource($datasource = NULL)
    {

        //If there are no datasource, set it to an empty array
        if($datasource === NULL)
        {
            $datasource = array();
        }

        //Validate
        if(!is_array($datasource) && !($datasource instanceof \Traversable))
        {
            throw new \InvalidArgumentException('Datasource must be either an array or \\Traversable');
        }

        //Save the datasource
        $this->datasource = $datasource;
        
    }

    /**
     * Returns the current datasource used in the iteration context
     * 
     * @access public
     *
     * @return \Traversable Datasource used in the iteration context
     */
    public function getDatasource()
    {
        return $this->datasource;
    }

    /**
     * Sets the TransformerContainer to be used in the transformer storage context
     * 
     * @param TransformerContainerInterface $transformerContainer TransformerContainer to use in this iterator
     *
     * @access public
     */
    public function setTransformerContainer(TransformerContainerInterface $transformerContainer = NULL)
    {
        $this->transformerContainer = $transformerContainer;
    }

    /**
     * Returns the current TransformerContainer used in the transformer storage context
     * 
     * @access public
     *
     * @return TransformerContainerInterface TransformerContainer used in the transformer storage context
     */
    public function getTransformerContainer()
    {
        return $this->transformerContainer;
    }

    /**
     * Implentation of the Iterator SPL class for Current(), 
     * returns the current element of the data source
     * Returns null if nothing found
     * 
     * @access public
     *
     * @return mixed Current value of the iterator
     */
	public function current()
	{
		return $this->transformedData;
	}

    /**
     * Implentation of the Iterator SPL class for Key(), 
     * returns the current element's identification of the data source
     * Returns null if nothing found
     * 
     * @access public
     *
     * @return mixed Value.
     */
	public function key()
	{
		return key($this->datasource);
	}

    /**
     * Implentation of the Iterator SPL class for Next(), 
     * prepares the next record in line to be read and return by Current() and Key()
     * 
     * @access public
     */
	public function next()
	{

        //Move to the next element
        next($this->datasource);

        //Process the transformers
        $this->transformedData = $this->getTransformerContainer()->transform(current($this->datasource), key($this->datasource));

	}

    /**
     * Implentation of the Iterator SPL class for Rewind(), 
     * prepares the whole datasource for an entirely new iterator operation
     * 
     * @access public
     */
    public function rewind()
    {
        reset($this->datasource);
    }

    /**
     * Implentation of the Iterator SPL class for Valid(), 
     * Checks if the current item is a valid item for processing
     * NULL keys represent an invalid item
     * 
     * @access public
     */
    public function valid()
    {
        return key($this->datasource) !== NULL;
    }

    /**
     * Adds a transformer with $name to the container
     * If the $name is not set, a new automatic numeric index is used
     *
     * @param TransformerInterface $transformer Transformer to add to the container for later processing
     * @param String $name If null (Default) will simply add the Transformer with a new key, else tries to add the current Transformer with the new Transformer
     *
     * @throws TransformerAlreadyExistsException Thrown if the $name already exists
     *
     * @return string Index/Name of the added Transformer
     */
    function addTransformer(TransformerInterface $transformer, $name = NULL)
    {
        return $this->getTransformerContainer()->addTransformer($transformer, $name);
    }

    /**
     * Replaces a transformer with $name
     *
     * @param TransformerInterface $transformer Transformer to add to the container for later processing
     * @param String $name Name of the Transformer to replace
     *
     * @throws TransformerNotFoundException Thrown if the $name cannot be found
     *
     * @return string Index/Name of the added/set transformer
     */
    function setTransformer(TransformerInterface $transformer, $name)
    {
        return $this->getTransformerContainer()->setTransformer($transformer, $name);
    }

    /**
     * Finds if a transformer exists in this collection
     *
     * @param String $name Name of the transformer you want to find
     *
     * @return bool Does the transformer exist in the collection
     */
    function hasTransformer($name)
    {
        return $this->getTransformerContainer()->hasTransformer($name);
    }

    /**
     * Removes a transformer with $name
     *
     * @param String $name Name of the transformer you want to find
     *
     * @throws TransformerNotFoundException Thrown if the $name cannot be found
     */
    function removeTransformer($name)
    {
        $this->getTransformerContainer()->removeTransformer($name);
    }

    /**
     * Clears the collection of transformers
     */
    function clearTransformers()
    {
        $this->getTransformerContainer()->clearTransformers();
    }

    /**
     * Returns a specific transformer from the collection
     *
     * @param String $name Name of the transformer you want to find
     *
     * @throws TransformerNotFoundException Thrown if the $name cannot be found
     *
     * @return TransformerInterface Transformer requested
     */
    function getTransformer($name)
    {
        return $this->getTransformerContainer()->getTransformer($name);
    }

    /**
     * Returns the collection of transformers
     *
     * @return Array Collection of all transformers in the group
     */
    function getTransformers()
    {
        return $this->getTransformerContainer()->getTransformers();
    }

}