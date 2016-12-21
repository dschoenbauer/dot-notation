<?php

/*
 * The MIT License
 *
 * Copyright 2016 David Schoenbauer <dschoenbauer@gmail.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace DSchoenbauer\DotNotation;

use DSchoenbauer\DotNotation\Exception\PathNotArrayException;
use DSchoenbauer\DotNotation\Exception\TargetNotArrayException;
use DSchoenbauer\DotNotation\Exception\UnexpectedValueException;
use DSchoenbauer\DotNotation\Framework\Command\GetCommand;
use DSchoenbauer\DotNotation\Framework\Command\HasCommand;
use DSchoenbauer\DotNotation\Framework\Command\MergeCommand;
use DSchoenbauer\DotNotation\Framework\Command\RemoveCommand;
use DSchoenbauer\DotNotation\Framework\Command\SetCommand;

/**
 * An easier way to deal with complex PHP arrays
 * 
 * @author David Schoenbauer
 * @version 1.3.0
 */
class ArrayDotNotation {

    /**
     * Property that houses the data that the dot notation should access
     * @var array
     */
    private $_data = [];
    private $_notationType = ".";
    private $_wildCardCharacter = "*";
    private $_getCommand;
    private $_setCommand;
    private $_removeCommand;
    private $_hasCommand;
    private $_mergeCommand;

    /**
     * Sets the data to parse in a chain
     * @param array $data optional  Array of data that will be accessed via dot notation.
     * @author John Smart
     * @author David Schoenbauer
     * @return \static
     */
    public static function with(array $data = []) {
        return new static($data);
    }

    /**
     * An alias for setData 
     * 
     * @see ArrayDotNotation::setData()
     * @since 1.0.0
     * @param array $data optional Array of data that will be accessed via dot notation.
     */
    public function __construct(array $data = [], GetCommand $getCommand = null, SetCommand $setCommand = null, HasCommand $hasCommand = null, MergeCommand $mergeCommand = null, RemoveCommand $removeCommand = null) {
        $this->setData($data);
        $this->setGetCommand($getCommand ? $getCommand->setArrayDotNotation($this) : new GetCommand($this));
        $this->setSetCommand($setCommand ? $setCommand->setArrayDotNotation($this) : new SetCommand($this));
        $this->setHasCommand($hasCommand ? $hasCommand->setArrayDotNotation($this) : new HasCommand($this));
        $this->setMergeCommand($mergeCommand ? $mergeCommand->setArrayDotNotation($this) : new MergeCommand($this));
        $this->setRemoveCommand($removeCommand ? $removeCommand->setArrayDotNotation($this) : new RemoveCommand($this));
    }

    /**
     * returns the array
     * 
     * returns the array that the dot notation has been used on.
     * 
     * @since 1.0.0
     * @return array Array of data that will be accessed via dot notation.
     */
    public function getData() {
        return $this->_data;
    }

    /**
     * sets the array that the dot notation will be used on.
     * 
     * @since 1.0.0
     * @param array $data Array of data that will be accessed via dot notation.
     * @return $this
     */
    public function setData(array $data) {
        $this->_data = $data;
        return $this;
    }

    /**
     * Retrieves a value from an array structure as defined by a dot notation string
     * 
     * Returns a value from an array. 
     * 
     * @since 1.0.0
     * @param string $dotNotation a string of keys concatenated together by a dot that represent different levels of an array
     * @param mixed $defaultValue value to return if the dot notation does not find a valid key
     * @return mixed value found via dot notation in the array of data
     */
    public function get($dotNotation, $defaultValue = null) {
        return $this->getGetCommand()->get($dotNotation, $defaultValue);
    }

    /**
     * sets a value into a complicated array data structure
     * 
     * Places a value into a tiered array. A dot notation of "one.two.three" 
     * would place the value at ['one' => ['two' => ['three' => 'valueHere' ]]]
     * If the array does not exist it will be added. Indexed arrays can be used,
     * and referenced by number. i.e. "one.0" would return the first item of the 
     * one array.
     * 
     * @since 1.0.0
     * @param string $dotNotation dot notation representation of keys of where to set a value
     * @param mixed $value any value to be stored with in a key structure of dot notation
     * @return $this
     * @throws PathNotArrayException if a value in the dot notation path is not an array
     */
    public function set($dotNotation, $value) {
        $this->getSetCommand()->set($dotNotation, $value);
        return $this;
    }

    /**
     * Merges two arrays together over writing existing values with new values, while adding new array structure to the data 
     * 
     * @since 1.1.0
     * @param string $dotNotation dot notation representation of keys of where to set a value
     * @param array $value array to be merged with an existing array
     * @return $this
     * @throws UnexpectedValueException if a value in the dot notation path is not an array
     * @throws TargetNotArrayException if the value in the dot notation target is not an array
     */
    public function merge($dotNotation, array $value) {
        $this->getMergeCommand()->merge($dotNotation, $value);
        return $this;
    }

    /**
     * Removes data from the array
     * 
     * @since 1.1.0
     * @param string $dotNotation dot notation representation of keys of where to remove a value
     * @return $this
     */
    public function remove($dotNotation) {
        $this->getRemoveCommand()->remove($dotNotation);
        return $this;
    }

    /**
     * Returns the current notation type that delimits the notation path. 
     * Default: "."
     * @since 1.2.0
     * @return string current notation character delimiting the notation path
     */
    public function getNotationType() {
        return $this->_notationType;
    }

    /**
     * Sets the current notation type used to delimit the notation path.
     * @since 1.2.0
     * @param string $notationType
     * @return $this
     */
    public function setNotationType($notationType = ".") {
        $this->_notationType = $notationType;
        return $this;
    }

    /**
     * Checks to see if a dot notation path is present in the data set.
     * 
     * @since 1.2.0
     * @param string $dotNotation dot notation representation of keys of where to remove a value
     * @return boolean returns true if the dot notation path exists in the data
     */
    public function has($dotNotation) {
        return $this->getHasCommand()->has($dotNotation);
    }

    /**
     * @return GetCommand
     */
    public function getGetCommand() {
        return $this->_getCommand;
    }

    /**
     * 
     * @param type $getCommand
     * @return $this
     */
    public function setGetCommand(GetCommand $getCommand) {
        $this->_getCommand = $getCommand;
        return $this;
    }

    /**
     * 
     * @since 1.3.0
     * @return SetCommand
     */
    public function getSetCommand() {
        return $this->_setCommand;
    }

    /**
     * 
     * @since 1.3.0
     * @param SetCommand $setCommand
     * @return $this
     */
    public function setSetCommand(SetCommand $setCommand) {
        $this->_setCommand = $setCommand;
        return $this;
    }

    /**
     * 
     * @since 1.3.0
     * @return HasCommand
     */
    public function getHasCommand() {
        return $this->_hasCommand;
    }

    /**
     * 
     * @since 1.3.0
     * @param HasCommand $hasCommand
     * @return $this
     */
    public function setHasCommand(HasCommand $hasCommand) {
        $this->_hasCommand = $hasCommand;
        return $this;
    }

    /**
     * 
     * @since 1.3.0
     * @param RemoveCommand $removeCommand
     * @return $this
     */
    public function setRemoveCommand(RemoveCommand $removeCommand) {
        $this->_removeCommand = $removeCommand;
        return $this;
    }

    /**
     * 
     * @since 1.3.0
     * @return RemoveCommand
     */
    public function getRemoveCommand() {
        return $this->_removeCommand;
    }

    /**
     * 
     * @since 1.3.0
     * @param MergeCommand $mergeCommand
     * @return $this
     */
    public function setMergeCommand(MergeCommand $mergeCommand) {
        $this->_mergeCommand = $mergeCommand;
        return $this;
    }

    /**
     * 
     * @since 1.3.0
     * @return MergeCommand
     */
    public function getMergeCommand() {
        return $this->_mergeCommand;
    }

    public function getWildCardCharacter() {
        return $this->_wildCardCharacter;
    }

    public function setWildCardCharacter($wildCardCharacter = "*") {
        $this->_wildCardCharacter = $wildCardCharacter;
        return $this;
    }

}
