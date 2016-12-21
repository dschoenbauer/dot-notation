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
 * Description of MergeCommandTest
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class MergeCommandTest extends CommandTestHelper {

    private $_object;

    protected function setUp() {
        $this->_object = new MergeCommand($this->getArrayDotNotation($this->getDefaultData()));
    }

    public function testMergeSimpleArray() {
        $data = ['a' => ['b' => ['c' => 'cValue']]];
        $merge = ['d' => 'dValue'];
        $this->_object->getArrayDotNotation()->setData($data);
        $this->assertEquals('dValue', $this->_object->merge('a.b', $merge)->getArrayDotNotation()->get('a.b.d'));
        $this->assertEquals('cValue', $this->_object->getArrayDotNotation()->get('a.b.c'));
    }

    /**
     * @expectedException UnexpectedValueException
     * @expectedExceptionMessage Array dot notation target key 'a.b.c' value is not an array.
     */
    public function testMergeNotAnArray() {
        $data = ['a' => ['b' => ['c' => 'cValue']]];
        $merge = ['d' => 'dValue'];
        $this->_object->getArrayDotNotation()->setData($data);
        $this->_object->merge('a.b.c', $merge);
    }

    /**
     * @expectedException UnexpectedValueException
     * @expectedExceptionMessage Array dot notation path key 'c' is not an array
     */
    public function testMergeNotPresentKeyString() {
        $data = ['a' => ['b' => ['c' => 'cValue']]];
        $merge = ['e' => 'dValue'];
        $this->_object->getArrayDotNotation()->setData($data);
        $this->_object->merge('a.b.c.d', $merge)->getArrayDotNotation()->getData();
    }

    public function testMergeNotPresentKey() {
        $data = ['a' => ['b' => ['c' => []]]];
        $merge = ['e' => 'dValue'];
        $result = ['a' => ['b' => ['c' => ['d' => ['e' => 'dValue']]]]];
        $this->_object->getArrayDotNotation()->setData($data);
        $this->assertEquals($result, $this->_object->merge('a.b.c.d', $merge)->getArrayDotNotation()->getData());
    }

}
