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
use DSchoenbauer\DotNotation\Exception\PathNotFoundException;
use DSchoenbauer\DotNotation\Exception\UnexpectedValueException;

/**
 * Description of RemoveCommand
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class RemoveCommand extends AbstractCommand {

    /**
     * Removes data from the array
     * 
     * @since 1.1.0
     * @param string $dotNotation dot notation representation of keys of where to remove a value
     * @return $this
     */
    public function remove($dotNotation) {
        $data = $this->getArrayDotNotation()->getData();
        $this->recursiveRemove($data, $this->getKeys($dotNotation));
        $this->getArrayDotNotation()->setData($data);
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
        if (is_array($data) && $key === $this->getArrayDotNotation()->getWildCardCharacter()) {
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
                $keysNotFound[] = implode($this->getArrayDotNotation()->getNotationType(), $this->unshiftKeys($keys, $key));
            }
        }
        if (count($keysNotFound) === count($data)) {
            throw new PathNotFoundException(implode(', ', $keysNotFound));
        }
    }

}
