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
 * Description of SetCommandTest
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class SetCommandTest extends CommandTestHelper {

    private $_object;

    protected function setUp() {
        $this->_object = new SetCommand($this->getArrayDotNotation($this->getDefaultData()));
    }

    public function testSetInitialLevelNewValue() {
        $this->assertEquals('noValue', $this->_object->getArrayDotNotation()->get('levelD', 'noValue'));
        $this->assertEquals('newValue', $this->_object->set('levelD', 'newValue')->getArrayDotNotation()->get('levelD'));
    }

    public function testSetDeepLevelNewValue() {
        $this->assertEquals('noValue', $this->_object->getArrayDotNotation()->get('LevelA.LevelB.LevelC.levelD', 'noValue'));
        $this->assertEquals('newValue', $this->_object->set('LevelA.LevelB.LevelC.levelD', 'newValue')->getArrayDotNotation()->get('LevelA.LevelB.LevelC.levelD'));
        $this->assertEquals('someValue2', $this->_object->getArrayDotNotation()->get('level1.level2'), "existing value compromised");
    }

    /**
     * @expectedException UnexpectedValueException
     * @expectedExceptionMessage Array dot notation path key 'c' is not an array
     */
    public function testSetNotExistArrayButString() {
        $data = ['a' => ['b' => ['c' => 'cValue']]];
        $newData = ['e' => 'dValue'];
        $this->_object->getArrayDotNotation()->setData($data);
        $this->_object->set('a.b.c.d', $newData);
    }

    public function testSetInitialLevelExistingValue() {
        $this->assertEquals('levelB', $this->_object->getArrayDotNotation()->get('levelB'));
        $this->assertEquals('newValue', $this->_object->set('levelB', 'newValue')->getArrayDotNotation()->get('levelB'));
        $this->assertEquals('someValue2', $this->_object->getArrayDotNotation()->get('level1.level2'), "existing value compromised");
    }

    public function testSetDeepLevelExistingValue() {
        $this->assertEquals('someValueB', $this->_object->getArrayDotNotation()->get('levelA.levelB'));
        $this->assertEquals('newValue', $this->_object->set('levelA.levelB', 'newValue')->getArrayDotNotation()->get('levelA.levelB'));
        $this->assertEquals('someValue2', $this->_object->getArrayDotNotation()->get('level1.level2'), "existing value compromised");
    }

    public function testSetDataTypeConversion() {
        $this->assertEquals(['levelB' => 'someValueB', 'levelB2' => ['levelC2' => ['levelD2' => 'someValueD2']]], $this->_object->getArrayDotNotation()->get('levelA'));
        $this->assertEquals('newValue', $this->_object->set('levelA', 'newValue')->getArrayDotNotation()->get('levelA'));
        $this->assertEquals('someValue2', $this->_object->getArrayDotNotation()->get('level1.level2'), "existing value compromised");
    }

    public function testSetWildCardForShallowPath() {
        $data = [
                ["id" => 1, "name" => "one"],
                ["id" => 2, "name" => "two"],
                ["id" => 3, "name" => "three"],
                ["id" => 4, "name" => "four"],
                ["id" => 5, "name" => "five"],
        ];
        $results = [
                ["id" => 1, "email" => null, "name" => "one"],
                ["id" => 2, "email" => null, "name" => "two"],
                ["id" => 3, "email" => null, "name" => "three"],
                ["id" => 4, "email" => null, "name" => "four"],
                ["id" => 5, "email" => null, "name" => "five"],
        ];
        $this->_object->getArrayDotNotation()->setData($data);
        $this->assertEquals($results, $this->_object->set('*.email', null)->getArrayDotNotation()->getData());
    }

    public function testSetWildCardForADeepPath() {
        $data = ["level1" => ["level2" => ["level3" => [
                            ["id" => 1, "name" => "one"],
                            ["id" => 2, "name" => "two"],
                            ["id" => 3, "name" => "three"],
                            ["id" => 4, "name" => "four"],
                            ["id" => 5, "name" => "five"],
                    ]]]
        ];
        $results = ["level1" => ["level2" => ["level3" => [
                            ["id" => 1, "email" => null, "name" => "one"],
                            ["id" => 2, "email" => null, "name" => "two"],
                            ["id" => 3, "email" => null, "name" => "three"],
                            ["id" => 4, "email" => null, "name" => "four"],
                            ["id" => 5, "email" => null, "name" => "five"],
                    ]]]
        ];
        $this->_object->getArrayDotNotation()->setData($data);
        $this->assertEquals($results, $this->_object->set('level1.level2.level3.*.email', null)->getArrayDotNotation()->getData());
    }

}
