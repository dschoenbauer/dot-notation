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

use DSchoenbauer\DotNotation\Exception\PathNotArrayException;

/**
 * Description of SetCommand
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class SetCommand extends AbstractCommand {

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
        $data = $this->getArrayDotNotation()->getData();
        $this->recursiveSet($data, $this->getKeys($dotNotation), $value);
        $this->getArrayDotNotation()->setData($data);
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
            if (is_array($data) && $key === $this->getArrayDotNotation()->getWildCardCharacter()) {
                $this->wildCardSet($keys, $data, $value);
            } elseif (!array_key_exists($key, $data)) {
                $data[$key] = [];
            } elseif (!is_array($data[$key])) {
                throw new PathNotArrayException($key);
            }
            if ($key !== $this->getArrayDotNotation()->getWildCardCharacter()) {
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

}
