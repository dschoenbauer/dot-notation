<?php

namespace DSchoenbauer\DotNotation;

use DSchoenbauer\DotNotation\Exception\PathNotArrayException;
use DSchoenbauer\DotNotation\Exception\PathNotFoundException;
use DSchoenbauer\DotNotation\Exception\UnexpectedValueException;
use DSchoenbauer\DotNotation\Framework\Command\GetCommand;
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

    public function testWith() {
        $this->assertInstanceOf(ArrayDotNotation::class, ArrayDotNotation::with());
    }

    public function testWithData() {
        $data = ['test' => 'value'];
        $this->assertEquals($data, ArrayDotNotation::with($data)->getData());
    }

    public function testWithNoData() {
        $this->assertEquals([], ArrayDotNotation::with()->getData());
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
        $this->assertEquals(['someValueB'], $this->_object->getGetCommand()->setGetMode(GetCommand::MODE_RETURN_FOUND)->getArrayDotNotation()->get('*.levelB'));
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
        $this->assertEquals(['a', 'b', 'c', 'd', 'e', 'f'], $this->_object->setData($data)->get('test.test.*.value'));
    }

    public function testGetWildCardNotFoundException() {
        $this->expectException(PathNotFoundException::class);
        $this->_object->getGetCommand()->setGetMode(GetCommand::MODE_THROW_EXCEPTION)->get('*.levelC', 'noValue');
    }

    public function testGetNoFindDefaultValue() {
        $this->assertEquals('noValue', $this->_object->get('levelA.levelB.levelC', 'noValue'));
    }

    public function testGetDefaultValue() {
        $this->assertEquals('noValue', $this->_object->get('levelA.levelC', 'noValue'));
    }

    public function testGetException() {
        $this->expectException(PathNotFoundException::class);
        $this->_object->getGetCommand()->setGetMode(GetCommand::MODE_THROW_EXCEPTION)->get('levelA.levelC', 'noValue');
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
        $this->assertEquals(['levelB' => 'someValueB', 'levelB2' => ['levelC2' => ['levelD2' => 'someValueD2']]], $this->_object->get('levelA'));
        $this->assertEquals('newValue', $this->_object->set('levelA', 'newValue')->get('levelA'));
        $this->assertEquals('someValue2', $this->_object->get('level1.level2'), "existing value compromised");
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
        $this->assertEquals($results, $this->_object->setData($data)->set('*.email', null)->getData());
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
        $this->assertEquals($results, $this->_object->setData($data)->set('level1.level2.level3.*.email', null)->getData());
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
        $this->assertEquals($data, $this->_object->remove('levelA.levelB')->getData());
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
        ];
        $this->assertEquals($results, $this->_object->setData($data)->remove('level1.level2.level3.*.email')->getData());
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
        $this->assertEquals($results, $this->_object->setData($data)->remove('level1.level2.level3.*.email')->getData());
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
        $this->_object->setData($data)->remove('level1.level2.level3.*.email')->getData();
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

    public function testChangeNotationType() {
        $this->assertEquals('someValueB', $this->_object->setNotationType('-')->get('levelA-levelB'));
    }

    public function testChangeNotationTypeNoFindDefaultValue() {
        $this->assertEquals('noValue', $this->_object->setNotationType('-')->get('levelA-levelB-levelC', 'noValue'));
    }

    public function testChangeNotationTypeSameLevelDefaultValue() {
        $this->assertEquals('noValue', $this->_object->setNotationType('-')->get('levelA-levelC', 'noValue'));
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

    public function testGetMode() {
        $this->assertEquals(GetCommand::MODE_RETURN_DEFAULT, $this->_object->getGetCommand()->setGetMode(GetCommand::MODE_RETURN_DEFAULT)->getGetMode());
        $this->assertEquals(GetCommand::MODE_RETURN_FOUND, $this->_object->getGetCommand()->setGetMode(GetCommand::MODE_RETURN_FOUND)->getGetMode());
        $this->assertEquals(GetCommand::MODE_THROW_EXCEPTION, $this->_object->getGetCommand()->setGetMode(GetCommand::MODE_THROW_EXCEPTION)->getGetMode());

        $this->expectException(Exception\InvalidArgumentException::class);
        $this->_object->getGetCommand()->setGetMode('not a mode');
    }

    public function testDefaultValue() {
        $this->assertEquals('test', $this->_object->getGetCommand()->setDefaultValue('test')->getDefaultValue(), 'Straight pass through');
        $this->assertEquals('test', $this->_object->getGetCommand()->setDefaultValue('test')->getDefaultValue('key'), 'Key Ignored due to mode');

        $this->assertEquals('test', $this->_object->getGetCommand()->setDefaultValue('test')->setGetMode(GetCommand::MODE_THROW_EXCEPTION)->getDefaultValue(), 'Mode ignored without key');
        $this->assertEquals('test', $this->_object->getGetCommand()->setDefaultValue('test')->setGetMode(GetCommand::MODE_RETURN_FOUND)->getDefaultValue(), 'Mode ignored without key');

        $this->expectException(PathNotFoundException::class);
        $this->_object->getGetCommand()->setDefaultValue('test')->setGetMode(GetCommand::MODE_THROW_EXCEPTION)->getDefaultValue('key');
    }

    public function testWildCardCharacter() {
        $this->assertEquals('t', $this->_object->setWildCardCharacter("t")->getWildCardCharacter(), 'Straight pass through');
        $this->assertTrue($this->_object->setWildCardCharacter("t")->has('t'));
    }

    public function testDefaultValueOnlyFound() {
        $this->expectException(PathNotFoundException::class);
        $this->_object->getGetCommand()->setDefaultValue('test')->setGetMode(GetCommand::MODE_RETURN_FOUND)->getDefaultValue('key');
    }

}
