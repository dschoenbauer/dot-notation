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

namespace DSchoenbauer\DotNotation\Exception;

use DSchoenbauer\DotNotation\Enum\ExceptionMessage;
use PHPUnit_Framework_TestCase;

/**
 * Description of PathNotFoundExceptionTest
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class TargetNotArrayExceptionTest extends PHPUnit_Framework_TestCase {

    private $_object;

    protected function setUp() {
        $this->_object = new TargetNotArrayException();
    }

    public function testHasExceptionInterface() {
        $this->assertInstanceOf(ExceptionInterface::class, $this->_object);
    }

    public function testIsUnexpectedValue() {
        $this->assertInstanceOf(UnexpectedValueException::class, $this->_object);
    }

    public function testKeyMessage() {
        $exc = new TargetNotArrayException("Key");
        $message = sprintf(ExceptionMessage::TARGET_NOT_ARRAY, "Key");
        $this->assertEquals($message, $exc->getMessage());
    }

    public function testCustomMessage() {
        $exc = new PathNotFoundException("","Full Message");
        $this->assertEquals("Full Message", $exc->getMessage());
    }

}
