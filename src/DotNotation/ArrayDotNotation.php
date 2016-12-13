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
 * @version 1.3.0
 */
class ArrayDotNotation {

    const WILD_CARD_CHARACTER = "*";

    /**
     * Returns only the values that match the dot notation path. Used only with wild cards.
     */
    const MODE_RETURN_FOUND = 'found';

    /**
     * Returns a default value if the dot notation path is not found.
     */
    const MODE_RETURN_DEFAULT = 'default';

    /**
     * Throws a DSchoenbauer\DotNotation\Exception\PathNotFoundException if the path is not found in the data.
     */
    const MODE_THROW_EXCEPTION = 'exception';

    /**
     * Property that houses the data that the dot notation should access
     * @var array
     */
    private $_data = [];
    private $_notationType = ".";
    private $_defaultValue;
    private $_getMode;

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
    public function __construct(array $data = []) {
        $this->setData($data)->setGetMode(self::MODE_RETURN_DEFAULT);
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
        $this->setDefaultValue($defaultValue);
        return $this->recursiveGet($this->getData(), $this->getKeys($dotNotation));
    }

    /**
     * Recursively works though an array level by level until the final key is found. Once key is found the keys value is returned.
     * @since 1.0.0
     * @param array $data array that has value to be retrieved
     * @param array $keys array of keys for each level of the array
     * @param mixed $defaultValue value to return when a key is not found
     * @return mixed value that the keys find in the data array
     */
    protected function recursiveGet($data, $keys) {
        $key = array_shift($keys);
        if (is_array($data) && $key === static::WILD_CARD_CHARACTER) {
            return $this->wildCardGet($keys, $data);
        } elseif (is_array($data) && $key && count($keys) == 0) { //Last Key
            return array_key_exists($key, $data) ? $data[$key] : $this->getDefaultValue($key);
        } elseif (is_array($data) && array_key_exists($key, $data)) {
            return $this->recursiveGet($data[$key], $keys);
        }
        return $this->getDefaultValue($key);
    }

    protected function wildCardGet(array $keys, $data) {
        $output = [];
        foreach (array_keys($data) as $key) {
            try {
                $output[] = $this->recursiveGet($data, $this->unshiftKeys($keys, $key));
            } catch (\Exception $exc) {
                if ($this->getGetMode() !== self::MODE_RETURN_FOUND) {
                    throw $exc;
                }
                //else do nothing
            }
        }
        return $output;
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
        $this->recursiveSet($this->_data, $this->getKeys($dotNotation), $value);
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
            if (is_array($data) && $key === static::WILD_CARD_CHARACTER) {
                $this->wildCardSet($keys, $data, $value);
            } elseif (!array_key_exists($key, $data)) {
                $data[$key] = [];
            } elseif (!is_array($data[$key])) {
                throw new Exception\PathNotArrayException($key);
            }
            if ($key !== static::WILD_CARD_CHARACTER) {
                $this->recursiveSet($data[$key], $keys, $value);
            }
        }
    }

    /**
     * Parses each key when a wild card  key is met
     * @since 1.3.0
     * @param array $keys the remaining keys of focus for the data array
     * @param array $data data to be traversed
     * @param mixed $value the value to be placed at the final key
     */
    protected function wildCardSet(array $keys, &$data, $value) {
        foreach (array_keys($data) as $key) {
            $this->recursiveSet($data, $this->unshiftKeys($keys, $key), $value);
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
        $this->recursiveRemove($this->_data, $this->getKeys($dotNotation));
        return $this;
    }

    /**
     * Transverses the keys array until it reaches the appropriate key in the 
     * data array and then deletes the value from the key.
     * 
     * @since 1.1.0
     * @param array $data data to be traversed
     * @param array $keys the remaining keys of focus for the data array
     * @throws UnexpectedValueException if a value in the dot notation path is 
     * not an array
     */
    protected function recursiveRemove(array &$data, array $keys) {
        $key = array_shift($keys);
        if (is_array($data) && $key === static::WILD_CARD_CHARACTER) {
            $this->wildCardRemove($keys, $data);
        } elseif (!array_key_exists($key, $data)) {
            throw new PathNotFoundException($key);
        } elseif ($key && count($keys) == 0) { //Last Key
            unset($data[$key]);
        } elseif (!is_array($data[$key])) {
            throw new PathNotArrayException($key);
        } else {
            $this->recursiveRemove($data[$key], $keys);
        }
    }

    /**
     * Parses each key when a wild card  key is met
     * @since 1.3.0
     * @param array $keys the remaining keys of focus for the data array
     * @param array $data data to be traversed
     */
    protected function wildCardRemove(array $keys, &$data) {
        $keysNotFound = [];
        foreach (array_keys($data) as $key) {
            try {
                $this->recursiveRemove($data, $this->unshiftKeys($keys, $key));
            } catch (PathNotFoundException $exc) {
                $keysNotFound[] = implode($this->getNotationType(), $this->unshiftKeys($keys, $key));
            }
        }
        if(count($keysNotFound) === count($data)){
            throw new PathNotFoundException(implode(', ', $keysNotFound));
        }
    }

    /**
     * consistently parses notation keys
     * @since 1.2.0
     * @param type $notation key path to a value in an array
     * @return array array of keys as delimited by the notation type
     */
    protected function getKeys($notation) {
        return explode($this->getNotationType(), $notation);
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
        $keys = $this->getKeys($dotNotation);
        $data = $this->_data;
        return $this->recursiveHas($keys, $data);
    }

    /**
     * Recursively checks for the dot notation path in the data array
     * 
     * @since 1.3.0
     * @param array $keys array of keys that still need to be review for the dot notation path
     * @param array $data data to be checked for the dot notation path
     * @return boolean
     */
    protected function recursiveHas(array $keys, $data) {
        $key = array_shift($keys);
        if (is_array($data) && $key === static::WILD_CARD_CHARACTER) {
            return $this->wildCardHas($keys, $data);
        } elseif (!is_array($data) || !array_key_exists($key, $data)) {
            return false;
        } elseif ($key && count($keys) == 0) { //Last Key
            return true;
        }
        return $this->recursiveHas($keys, $data[$key]);
    }

    /**
     * A process to enumerate through the has function with a wild card
     * @since 1.3.0
     * @param array $keys 
     * @param array $data
     * @return boolean
     */
    protected function wildCardHas(array $keys, array $data) {
        foreach (array_keys($data) as $key) {
            if ($this->recursiveHas($this->unshiftKeys($keys, $key), $data)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns the current default value.
     * @note should be a protected method
     * @since 1.3.0
     * @param type $key
     * @return mixed value to be used instead of a real value is not found
     * @throws PathNotFoundException when the key 
     */
    public function getDefaultValue($key = null) {
        if ($key !== null && in_array($this->getGetMode(), [self::MODE_THROW_EXCEPTION, self::MODE_RETURN_FOUND])) {
            throw new PathNotFoundException($key);
        }
        return $this->_defaultValue;
    }

    /**
     * The default value that get will return. Do not set the value with this method. Set the default value in the get method.
     * @note should be a protected method
     * @since 1.3.0
     * @param mixed $defaultValue value to be used instead of a real value is not found
     * @return $this
     */
    public function setDefaultValue($defaultValue) {
        $this->_defaultValue = $defaultValue;
        return $this;
    }

    /**
     * Returns the behavior defined for how get will return a value.
     * @since 1.3.0
     * @return enum values are constants of this class MODE_RETURN_DEFAULT, MODE_RETURN_FOUND, MODE_THROW_EXCEPTION
     */
    public function getGetMode() {
        return $this->_getMode;
    }

    /**
     * Defines the behavior on how get returns a value.
     * @since 1.3.0
     * @param enum $getMode values are constants of this class MODE_RETURN_DEFAULT, MODE_RETURN_FOUND, MODE_THROW_EXCEPTION
     * @return $this
     */
    public function setGetMode($getMode) {
        $modes = [self::MODE_RETURN_DEFAULT, self::MODE_RETURN_FOUND, self::MODE_THROW_EXCEPTION];
        if (!in_array($getMode, $modes)) {
            throw new Exception\InvalidArgumentException('Not a supported mode. Please use on of:' . implode(', ', $modes));
        }
        $this->_getMode = $getMode;
        return $this;
    }

    /**
     * Calls the unshift function like a true function and not by reference
     * @param array $keys array that will have a key appended to
     * @param string $key key to be added to the beginning of the array
     * @return array array of merged keys with key prepended to the beginning of the array
     */
    private function unshiftKeys($keys, $key) {
        $tempKeys = $keys;
        array_unshift($tempKeys, $key);
        return $tempKeys;
    }

}
