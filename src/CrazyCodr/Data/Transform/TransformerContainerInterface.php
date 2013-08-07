<?php

namespace CrazyCodr\Data\Transform;

/**
* This interface dictates what a transformer container should be able to do naturaly
*
* @category Interfaces
* @package  CrazyCodr/Data-Transform
* @author   CrazyOne@CrazyCoders
* @license  MIT
* @link     crazycoders.net
*/
interface TransformerContainerInterface extends TransformerInterface
{

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
    function addTransformer(TransformerInterface $transformer, $name = NULL);

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
    function setTransformer(TransformerInterface $transformer, $name);

    /**
     * Finds if a transformer exists in this collection
     *
     * @param String $name Name of the transformer you want to find
     *
     * @return bool Does the transformer exist in the collection
     */
    function hasTransformer($name);

    /**
     * Removes a transformer with $name
     *
     * @param String $name Name of the transformer you want to find
     *
     * @throws TransformerNotFoundException Thrown if the $name cannot be found
     */
    function removeTransformer($name);

    /**
     * Clears the collection of transformers
     */
    function clearTransformers();

    /**
     * Returns a specific transformer from the collection
     *
     * @param String $name Name of the transformer you want to find
     *
     * @throws TransformerNotFoundException Thrown if the $name cannot be found
     *
     * @return TransformerInterface Transformer requested
     */
    function getTransformer($name);

    /**
     * Returns the collection of transformers
     *
     * @return Array Collection of all transformers in the group
     */
    function getTransformers();

}