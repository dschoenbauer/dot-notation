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
use DSchoenbauer\DotNotation\Exception\PathNotFoundException;
use DSchoenbauer\DotNotation\Exception\TargetNotArrayException;
use DSchoenbauer\DotNotation\Exception\UnexpectedValueException;

/**
 * An easier way to deal with complex PHP arrays
 * 
 * @author David Schoenbauer
 * @version 1.0.1
 */
class ArrayDotNotation {

    /**
     * Property that houses the data that the dot notation should access
     * @var array
     */
    private $_data = [];

    /**
     * An alias for setData 
     * 
     * @see ArrayDotNotation::setData()
     * @since 1.0.0
     * @param array $data Array of data that will be accessed via dot notation.
     */
    public function __construct(array $data = []) {
        $this->setData($data);
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
        return $this->recursiveGet($this->getData(), explode('.', $dotNotation), $defaultValue);
    }

    /**
     * Recursively works though an array level by level until the final key is found. Once key is found the keys value is returned.
     * @since 1.0.0
     * @param array $data array that has value to be retrieved
     * @param array $keys array of keys for each level of the array
     * @param mixed $defaultValue value to return when a key is not found
     * @return mixed value that the keys find in the data array
     */
    protected function recursiveGet($data, $keys, $defaultValue) {
        $key = array_shift($keys);
        if (is_array($data) && $key && count($keys) == 0) { //Last Key
            return array_key_exists($key, $data) ? $data[$key] : $defaultValue;
        } elseif (is_array($data) && array_key_exists($key, $data)) {
            return $this->recursiveGet($data[$key], $keys, $defaultValue);
        }
        return $defaultValue;
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
        $this->recursiveSet($this->_data, explode('.', $dotNotation), $value);
        return $this;
    }

    /**
     * Transverses the keys array until it reaches the appropriate key in the data array and sets that key to the value.
     * If the keys don't exist they are created.
     * 
     * @since 1.0.0
     * @param array $data data to be traversed
     * @param array $keys the remaining keys of focus for the data array
     * @param mixed $value the value to be placed at the final key
     * @throws PathNotArrayException if a value in the dot notation path is not an array
     */
    protected function recursiveSet(array &$data, array $keys, $value) {
        $key = array_shift($keys);
        if ($key && count($keys) == 0) { //Last Key
            $data[$key] = $value;
        } else {
            if (!array_key_exists($key, $data)) {
                $data[$key] = [];
            } elseif (!is_array($data[$key])) {
                throw new Exception\PathNotArrayException($key);
            }
            $this->recursiveSet($data[$key], $keys, $value);
        }
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
        $target = $this->get($dotNotation, []);
        if (!is_array($target)) {
            throw new Exception\TargetNotArrayException($dotNotation);
        }
        $this->set($dotNotation, array_merge_recursive($target, $value));
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
        $this->recursiveRemove($this->_data, explode('.', $dotNotation), $dotNotation);
        return $this;
    }

    /**
     * Transverses the keys array until it reaches the appropriate key in the data array and then deletes the value from the key.
     * 
     * @since 1.1.0
     * @param array $data data to be traversed
     * @param array $keys the remaining keys of focus for the data array
     * @throws UnexpectedValueException if a value in the dot notation path is not an array
     */
    protected function recursiveRemove(array &$data, array $keys) {
        $key = array_shift($keys);
        if (!array_key_exists($key, $data)) {
            throw new PathNotFoundException($key);
        } elseif ($key && count($keys) == 0) { //Last Key
            unset($data[$key]);
        } elseif (!is_array($data[$key])) {
            throw new PathNotArrayException($key);
        } else {
            $this->recursiveRemove($data[$key], $keys);
        }
    }

}
