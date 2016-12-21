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

use DSchoenbauerTest\DotNotation\Framework\Command\CommandTestHelper;

/**
 * Description of HasCommandTest
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class HasCommandTest extends CommandTestHelper {

    protected $_object;


    protected function setUp() {
        $this->_object = new HasCommand($this->getArrayDotNotation($this->getDefaultData()));
    }

    public function testHas() {
        $this->assertTrue($this->_object->has('levelA'));
        $this->assertTrue($this->_object->has('levelA.levelB'));
        $this->assertTrue($this->_object->has('levelB'));
        $this->assertTrue($this->_object->has('level1'));
        $this->assertTrue($this->_object->has('level1.level2'));
        $this->assertFalse($this->_object->has('level1.level2.level3'));
        $this->assertFalse($this->_object->has('level2'));
    }

    public function testHasWildCard() {
        $this->assertTrue($this->_object->has('*'));
        $this->assertTrue($this->_object->has('*.levelB'));
        $this->assertTrue($this->_object->has('*.*.*.*'));
        $this->assertTrue($this->_object->has('levelB'));
        $this->assertTrue($this->_object->has('level1'));
        $this->assertTrue($this->_object->has('level1.*'));
        $this->assertFalse($this->_object->has('*.*.*.*.*'));
        $this->assertFalse($this->_object->has('level1.level2.*'));
        $this->assertFalse($this->_object->has('level1.level2.*'));
    }

}
