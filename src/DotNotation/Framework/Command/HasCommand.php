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

/**
 * Description of HasCommand
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class HasCommand extends AbstractCommand {

    /**
     * Checks to see if a dot notation path is present in the data set.
     * 
     * @since 1.2.0
     * @param string $dotNotation dot notation representation of keys of where to remove a value
     * @return boolean returns true if the dot notation path exists in the data
     */
    public function has($dotNotation) {
        $keys = $this->getKeys($dotNotation);
        $data = $this->getArrayDotNotation()->getData();
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
        if (is_array($data) && $key === $this->getArrayDotNotation()->getWildCardCharacter()) {
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

}
