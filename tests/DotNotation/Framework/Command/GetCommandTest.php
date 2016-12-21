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

use DSchoenbauer\DotNotation\Exception\InvalidArgumentException;
use DSchoenbauer\DotNotation\Exception\PathNotFoundException;
use DSchoenbauerTest\DotNotation\Framework\Command\CommandTestHelper;

/**
 * Description of GetCommandTest
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class GetCommandTest extends CommandTestHelper {

    protected $_object;

    protected function setUp() {
        $data = [
            'levelA' => [
                'levelB' => 'someValueB',
                'levelB2' => [
                    'levelC2' => [
                        'levelD2' => 'someValueD2'
                    ]
                ]
            ],
            'levelB' => 'levelB',
            'level1' => [
                'level2' => 'someValue2'
            ]
        ];

        $this->_object = new GetCommand($this->getArrayDotNotation($data));
    }

    public function testGet() {
        $this->assertEquals('someValueB', $this->_object->get('levelA.levelB'));
    }

    public function testGetWildCardEndWithWild() {
        $this->assertEquals(['someValue2'], $this->_object->get('level1.*'));
    }

    public function testGetWildCardDefault() {
        $this->assertEquals(['someValueB', null, null], $this->_object->get('*.levelB'));
    }

    public function testGetWildCardOnlyFound() {
        $this->assertEquals(['someValueB'], $this->_object->setGetMode(GetCommand::MODE_RETURN_FOUND)->get('*.levelB'));
    }

    public function testGetWildTable() {
        $data = [
            'test' => [
                'test' => [
                        ['value' => 'a', 'ontme' => 1],
                        ['value' => 'b', 'ontme' => 1],
                        ['value' => 'c', 'ontme' => 1],
                        ['value' => 'd', 'ontme' => 1],
                        ['value' => 'e', 'ontme' => 1],
                        ['value' => 'f', 'ontme' => 1],
                ]
            ]
        ];
        $this->_object->setArrayDotNotation($this->getArrayDotNotation($data));
        $this->assertEquals(['a', 'b', 'c', 'd', 'e', 'f'], $this->_object->get('test.test.*.value'));
    }

    public function testGetWildCardNotFoundException() {
        $this->expectException(PathNotFoundException::class);
        $this->_object->setGetMode(GetCommand::MODE_THROW_EXCEPTION)->get('*.levelC', 'noValue');
    }

    public function testGetNoFindDefaultValue() {
        $this->assertEquals('noValue', $this->_object->get('levelA.levelB.levelC', 'noValue'));
    }

    public function testGetDefaultValue() {
        $this->assertEquals('noValue', $this->_object->get('levelA.levelC', 'noValue'));
    }

    public function testGetException() {
        $this->expectException(PathNotFoundException::class);
        $this->_object->setGetMode(GetCommand::MODE_THROW_EXCEPTION)->get('levelA.levelC', 'noValue');
    }

    public function testSetGetModeInvalidValue(){
        $this->expectException(InvalidArgumentException::class);
        $this->_object->setGetMode('Not Valid');
    }
}
