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
use DSchoenbauerTest\DotNotation\Framework\Command\CommandTestHelper;

/**
 * Description of RemoveCommandTest
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class RemoveCommandTest extends CommandTestHelper {
    protected $_object;
    
    protected function setUp() {
        $this->_object = new RemoveCommand($this->getArrayDotNotation($this->getDefaultData()));
    }
    
    
    public function testRemove() {
        $data = [
            'levelA' => [
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
        $this->assertEquals($data, $this->_object->remove('levelA.levelB')->getArrayDotNotation()->getData());
    }

    public function testRemoveWildCard() {
        $data = ["level1" => ["level2" => ["level3" => [
                            ["id" => 1, "email" => null, "name" => "one"],
                            ["id" => 2, "email" => null, "name" => "two"],
                            ["id" => 3, "email" => null, "name" => "three"],
                            ["id" => 4, "email" => null, "name" => "four"],
                            ["id" => 5, "email" => null, "name" => "five"],
                    ]]]
        ];
        $results = ["level1" => ["level2" => ["level3" => [
                            ["id" => 1, "name" => "one"],
                            ["id" => 2, "name" => "two"],
                            ["id" => 3, "name" => "three"],
                            ["id" => 4, "name" => "four"],
                            ["id" => 5, "name" => "five"],
                    ]]]
        ];$this->_object->getArrayDotNotation()->setData($data);

        $this->assertEquals($results, $this->_object->remove('level1.level2.level3.*.email')->getArrayDotNotation()->getData());
    }

    public function testRemoveWildCardNoException() {
        $data = ["level1" => ["level2" => ["level3" => [
                            ["id" => 1, "email" => null, "name" => "one"],
                            ["id" => 2, "email" => null, "name" => "two"],
                            ["id" => 3, "email" => null, "name" => "three"],
                            ["id" => 4, "email" => null, "name" => "four"],
                            ["id" => 5, "name" => "five"],
                    ]]]
        ];
        $results = ["level1" => ["level2" => ["level3" => [
                            ["id" => 1, "name" => "one"],
                            ["id" => 2, "name" => "two"],
                            ["id" => 3, "name" => "three"],
                            ["id" => 4, "name" => "four"],
                            ["id" => 5, "name" => "five"],
                    ]]]
        ];
        $this->assertEquals($results, $this->_object->getArrayDotNotation()->setData($data)->remove('level1.level2.level3.*.email')->getData());
    }

    public function testRemoveWildCardException() {
        $data = ["level1" => ["level2" => ["level3" => [
                            ["id" => 1, "name" => "one"],
                            ["id" => 2, "name" => "two"],
                            ["id" => 3, "name" => "three"],
                            ["id" => 4, "name" => "four"],
                            ["id" => 5, "name" => "five"],
                    ]]]
        ];
        $this->expectException(PathNotFoundException::class);
        $this->_object->getArrayDotNotation()->setData($data);
        $this->_object->remove('level1.level2.level3.*.email');
    }

    public function testRemovePathNotFound() {
        $this->expectException(PathNotFoundException::class);
        $this->_object->remove('levelA.levelC');
    }

    public function testRemovePathNotFoundShort() {
        $this->expectException(PathNotFoundException::class);
        $this->_object->remove('level0');
    }

    public function testRemovePathNotArray() {
        $this->expectException(PathNotArrayException::class);
        $this->_object->remove('levelA.levelB.levelD');
    }

}
