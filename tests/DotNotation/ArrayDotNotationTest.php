<?php

namespace DSchoenbauer\DotNotation;

use DSchoenbauer\DotNotation\Exception\UnexpectedValueException;
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
    
    public function testWith(){
        $this->assertInstanceOf(ArrayDotNotation::class, ArrayDotNotation::with());
    }

    public function testWithData(){
        $data = ['test'=>'value'];
        $this->assertEquals($data, ArrayDotNotation::with($data)->getData());
    }
    
    public function testWithNoData(){
        $this->assertEquals([], ArrayDotNotation::with()->getData());
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

    /**
     * @expectedException UnexpectedValueException
     * @expectedExceptionMessage Array dot notation path key 'c' is not an array
     */
    public function testSetNotExistArrayButString() {
        $data = ['a' => ['b' => ['c' => 'cValue']]];
        $newData = ['e' => 'dValue'];
        $this->_object->setData($data)->set('a.b.c.d', $newData)->getData();
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

    public function testMergeSimpleArray() {
        $data = ['a' => ['b' => ['c' => 'cValue']]];
        $merge = ['d' => 'dValue'];
        $this->assertEquals('dValue', $this->_object->setData($data)->merge('a.b', $merge)->get('a.b.d'));
        $this->assertEquals('cValue', $this->_object->get('a.b.c'));
    }

    /**
     * @expectedException UnexpectedValueException
     * @expectedExceptionMessage Array dot notation target key 'a.b.c' value is not an array.
     */
    public function testMergeNotAnArray() {
        $data = ['a' => ['b' => ['c' => 'cValue']]];
        $merge = ['d' => 'dValue'];
        $this->_object->setData($data)->merge('a.b.c', $merge);
    }

    /**
     * @expectedException UnexpectedValueException
     * @expectedExceptionMessage Array dot notation path key 'c' is not an array
     */
    public function testMergeNotPresentKeyString() {
        $data = ['a' => ['b' => ['c' => 'cValue']]];
        $merge = ['e' => 'dValue'];
        $this->_object->setData($data)->merge('a.b.c.d', $merge)->getData();
    }

    public function testMergeNotPresentKey() {
        $data = ['a' => ['b' => ['c' => []]]];
        $merge = ['e' => 'dValue'];
        $result = ['a' => ['b' => ['c' => ['d' => ['e' => 'dValue']]]]];
        $this->assertEquals($result, $this->_object->setData($data)->merge('a.b.c.d', $merge)->getData());
    }

    public function testRemove() {
        $data = [
            'levelA' => [],
            'levelB' => 'levelB',
            'level1' => [
                'level2' => 'someValue2'
            ]
        ];
        $this->assertEquals($data, $this->_object->remove('levelA.levelB')->getData());
    }

    /**
     * @expectedException \DSchoenbauer\DotNotation\Exception\PathNotFoundException
     */
    public function testRemovePathNotFound() {
        $this->_object->remove('levelA.levelC');
    }
    
    /**
     * @expectedException \DSchoenbauer\DotNotation\Exception\PathNotFoundException
     */
    public function testRemovePathNotFoundShort() {
        $this->_object->remove('level0');
    }
    /**
     * @expectedException \DSchoenbauer\DotNotation\Exception\PathNotArrayException
     */
    public function testRemovePathNotArray() {
        $this->_object->remove('levelA.levelB.levelD');
    }

    public function testChangeNotationType(){
        $this->assertEquals('someValueB', $this->_object->setNotationType('-')->get('levelA-levelB'));
    }

    public function testChangeNotationTypeNoFindDefaultValue() {
        $this->assertEquals('noValue', $this->_object->setNotationType('-')->get('levelA-levelB-levelC', 'noValue'));
    }

    public function testChangeNotationTypeSameLevelDefaultValue() {
        $this->assertEquals('noValue', $this->_object->setNotationType('-')->get('levelA-levelC', 'noValue'));
    }


}
