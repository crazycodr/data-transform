<?php

namespace CrazyCodr\Data\Transform;

/**
* This class represents a group of transformers and is the base and probably unique 
* and only class you will ever need to process groups of transformers.
*
* @category Transformer organisation
* @package  CrazyCodr/Data-Transform
* @author   CrazyOne@CrazyCoders
* @license  MIT
* @link     crazycoders.net
*/
class TransformerContainer implements TransformerInterface, TransformerContainerInterface
{

    /**
     * Contains the transformers to apply
     *
     * @var array
     *
     * @access protected
     */
    protected $transformers = array();

    /**
     * When called with data, a key and an optional current transformation state value, the function should attempt to resolve
     * some kind of transformation decision operation and return the newly changed item. If the item being passed in $transformedData
     * is an object and you think this object should not be modified, remember to clone the object.
     * 
     * @param mixed $data Data to be used in the transforming operation
     * @param mixed $key  Identification key to be used in the transforming operation
     * @param mixed $transformedData Current state of the transformation as already applied by other transformers
     * 
     * @return mixed New state of the transformed data
     */
    function transform($data, $key, $transformedData = NULL)
    {
        foreach($this->getTransformers() as $transformer)
        {
            $transformedData = $transformer->transform($data, $key, $transformedData);
        }
        return $transformedData;
    }

    /**
     * Adds a transformer with $name to the container
     * If the $name is not set, a new automatic index is used
     *
     * @param TransformerInterface $transformer Transformer to add to the container for later processing
     * @param String $name If null (Default) will simply add the transformer with a new key, else tries to add the current transformer with the new transformer
     *
     * @throws TransformerAlreadyExistsException Thrown when a transformer already exists with that $name
     *
     * @return string Index/Name of the added transformer
     */
    function addTransformer(TransformerInterface $transformer, $name = NULL)
    {

        //Generate an appropriate name if none
        while($name == NULL)
        {
            $name = 'autotransformer_'.rand(1111, 9999);
            if($this->hasTransformer($name))
            {
                $name = NULL;
            }
        }

        //Check if the name exists
        if($this->hasTransformer($name))
        {
            throw new TransformerAlreadyExistsException($name);
        }

        //Add the transformer to the container
        $this->transformers[$name] = $transformer;

        //Return the name/new name
        return $name;

    }

    /**
     * Replaces a transformer with $name
     *
     * @param TransformerInterface $transformer Transformer to add to the container for later processing
     * @param String $name Name of the transformer to replace
     *
     * @throws TransformerNotFoundException Thrown when the requested transformer name was not found in the collection
     *
     * @return string Index/Name of the added/set transformer
     */
    function setTransformer(TransformerInterface $transformer, $name)
    {

        //Check if the name exists
        if($this->hasTransformer($name) == false)
        {
            throw new TransformerNotFoundException($name);
        }

        //Add the transformer to the container
        $this->transformers[$name] = $transformer;

        //Return the name/new name
        return $name;

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
        return array_key_exists($name, $this->transformers);
    }

    /**
     * Removes a transformer with $name
     *
     * @param String $name Name of the transformer you want to find
     *
     * @throws TransformerNotFoundException Thrown when the requested transformer name was not found in the collection
     */
    function removeTransformer($name)
    {

        //Check if the name exists
        if($this->hasTransformer($name) == false)
        {
            throw new TransformerNotFoundException($name);
        }

        //Add the transformer to the container
        unset($this->transformers[$name]);

    }

    /**
     * Clears the collection of transformers
     */
    function clearTransformers()
    {
        $this->transformers = array();
    }

    /**
     * Returns a specific transformer from the collection
     *
     * @param String $name Name of the transformer you want to find
     *
     * @throws TransformerNotFoundException Thrown when the requested transformer name was not found in the collection
     *
     * @return TransformerInterface Transformer requested
     */
    function getTransformer($name)
    {

        //Check if the name exists
        if($this->hasTransformer($name) == false)
        {
            throw new TransformerNotFoundException($name);
        }

        //Return the transformer
        return $this->transformers[$name];

    }

    /**
     * Returns the collection of transformers
     *
     * @return Array Collection of all transformers in the group
     */
    function getTransformers()
    {
        return $this->transformers;
    }

}