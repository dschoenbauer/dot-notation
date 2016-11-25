<?php

namespace DSchoenbauer\DotNotation;

use PHPUnit_Framework_TestCase;

/**
 * Description of ArrayDotNotationTest
 *
 * @author David
 */
class ArrayDotNotationTest extends PHPUnit_Framework_TestCase {

    private $_object;

    protected function setUp() {
        $data = [
            'levelA' => [
                'levelB' => 'someValueB'
            ],
            'levelB' => 'levelB',
            'level1' => [
                'level2' => 'someValue2'
            ]
        ];
        $this->_object = new ArrayDotNotation($data);
    }

    public function testData() {
        $data = ['test' => 'value'];
        $this->assertEquals($data, $this->_object->setData($data)->getData());
    }

    public function testDataConstructor() {
        $data = ['test' => 'value'];
        $object = new ArrayDotNotation($data);
        $this->assertEquals($data, $object->getData());
    }

    public function testGet() {
        $this->assertEquals('someValueB', $this->_object->get('levelA.levelB'));
    }

    public function testGetNoFindDefaultValue() {
        $this->assertEquals('noValue', $this->_object->get('levelA.levelB.levelC', 'noValue'));
    }

    public function testGetSameLevelDefaultValue() {
        $this->assertEquals('noValue', $this->_object->get('levelA.levelC', 'noValue'));
    }

    public function testSetInitialLevelNewValue() {
        $this->assertEquals('noValue', $this->_object->get('levelD', 'noValue'));
        $this->assertEquals('newValue', $this->_object->set('levelD', 'newValue')->get('levelD'));
    }

    public function testSetDeepLevelNewValue() {
        $this->assertEquals('noValue', $this->_object->get('LevelA.LevelB.LevelC.levelD', 'noValue'));
        $this->assertEquals('newValue', $this->_object->set('LevelA.LevelB.LevelC.levelD', 'newValue')->get('LevelA.LevelB.LevelC.levelD'));
        $this->assertEquals('someValue2', $this->_object->get('level1.level2'), "existing value compromised");
    }

    public function testSetInitialLevelExistingValue() {
        $this->assertEquals('levelB', $this->_object->get('levelB'));
        $this->assertEquals('newValue', $this->_object->set('levelB', 'newValue')->get('levelB'));
        $this->assertEquals('someValue2', $this->_object->get('level1.level2'), "existing value compromised");
    }

    public function testSetDeepLevelExistingValue() {
        $this->assertEquals('someValueB', $this->_object->get('levelA.levelB'));
        $this->assertEquals('newValue', $this->_object->set('levelA.levelB', 'newValue')->get('levelA.levelB'));
        $this->assertEquals('someValue2', $this->_object->get('level1.level2'), "existing value compromised");
    }

    public function testSetDataTypeConversion() {
        $this->assertEquals(['levelB' => 'someValueB'], $this->_object->get('levelA'));
        $this->assertEquals('newValue', $this->_object->set('levelA', 'newValue')->get('levelA'));
        $this->assertEquals('someValue2', $this->_object->get('level1.level2'), "existing value compromised");
    }

}
