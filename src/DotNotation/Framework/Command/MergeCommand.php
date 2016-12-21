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

use DSchoenbauer\DotNotation\Exception\TargetNotArrayException;
use UnexpectedValueException;

/**
 * Description of MergeCommand
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class MergeCommand extends AbstractCommand {

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
        $target = $this->getArrayDotNotation()->get($dotNotation, []);
        if (!is_array($target)) {
            throw new TargetNotArrayException($dotNotation);
        }
        $this->getArrayDotNotation()->set($dotNotation, array_merge_recursive($target, $value));
        return $this;
    }

}
