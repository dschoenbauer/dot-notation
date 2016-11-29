<?php

namespace DSchoenbauer\DotNotation;

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
     * @see \DSchoenbauer\DotNotation\ArrayDotNotation::setData()
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
     */
    protected function recursiveSet(array &$data, array $keys, $value) {
        $key = array_shift($keys);
        if ($key && count($keys) == 0) { //Last Key
            $data[$key] = $value;
        } else {
            if (!array_key_exists($key, $data)) {
                $data[$key] = [];
            }
            $this->recursiveSet($data[$key], $keys, $value);
        }
    }

}
