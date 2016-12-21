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

/**
 * Description of AbstractCommand
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
abstract class AbstractCommand implements CommandInterface {

    private $_arrayDotNotation;

    public function __construct(ArrayDotNotation $arrayDotNotation) {
        $this->setArrayDotNotation($arrayDotNotation);
        ;
    }

    /**
     * Calls the unshift function like a true function and not by reference
     * @since 1.3.0
     * @param array $keys array that will have a key appended to
     * @param string $key key to be added to the beginning of the array
     * @return array array of merged keys with key prepended to the beginning of the array
     */
    public function unshiftKeys($keys, $key) {
        $tempKeys = $keys;
        array_unshift($tempKeys, $key);
        return $tempKeys;
    }

    /**
     * @since 1.3.0
     * @return ArrayDotNotation
     */
    public function getArrayDotNotation() {
        return $this->_arrayDotNotation;
    }

    /**
     * 
     * @since 1.3.0
     * @param type $arrayDotNotation
     * @return $this
     */
    public function setArrayDotNotation(ArrayDotNotation $arrayDotNotation) {
        $this->_arrayDotNotation = $arrayDotNotation;
        return $this;
    }

    /**
     * consistently parses notation keys
     * @since 1.2.0
     * @param type $notation key path to a value in an array
     * @return array array of keys as delimited by the notation type
     */
    public function getKeys($notation) {
        return explode($this->getArrayDotNotation()->getNotationType(), $notation);
    }

}
