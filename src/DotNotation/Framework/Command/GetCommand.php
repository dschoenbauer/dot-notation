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

namespace DSchoenbauer\DotNotation\Framework\Command;

use DSchoenbauer\DotNotation\ArrayDotNotation;
use DSchoenbauer\DotNotation\Exception\InvalidArgumentException;
use DSchoenbauer\DotNotation\Exception\PathNotFoundException;
use Exception;

/**
 * Description of Get
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class GetCommand extends AbstractCommand {

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

    private $_defaultValue;
    private $_getMode;

    public function __construct(ArrayDotNotation $arrayDotNotation, $getMode = self::MODE_RETURN_DEFAULT) {
        $this->setGetMode($getMode);
        parent::__construct($arrayDotNotation);
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
        return $this->recursiveGet($this->getArrayDotNotation()->getData(), $this->getKeys($dotNotation));
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
        if (is_array($data) && $key === $this->getArrayDotNotation()->getWildCardCharacter()) {
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
            } catch (Exception $exc) {
                if ($this->getGetMode() !== self::MODE_RETURN_FOUND) {
                    throw $exc;
                }
                //else do nothing
            }
        }
        return $output;
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
            throw new InvalidArgumentException('Not a supported mode. Please use on of:' . implode(', ', $modes));
        }
        $this->_getMode = $getMode;
        return $this;
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

}
