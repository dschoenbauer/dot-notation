<?php

namespace DSchoenbauer\DotNotation\Exception;

use DSchoenbauer\DotNotation\Exception\ExceptionInterface;
use DSchoenbauer\DotNotation\Exception\InvalidArgumentException;
use InvalidArgumentException as SystemInvalidArgumentException;
use PHPUnit_Framework_TestCase;

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

/**
 * Description of InvalidArgumentExceptionTest
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class InvalidArgumentExceptionTest extends PHPUnit_Framework_TestCase {

    private $_object;

    protected function setUp() {
        $this->_object = new InvalidArgumentException();
    }

    public function testHasInterface() {
        $this->assertInstanceOf(ExceptionInterface::class, $this->_object);
    }

    public function testIsInvalidArgument() {
        $this->assertInstanceOf(SystemInvalidArgumentException::class, $this->_object);
    }

}
