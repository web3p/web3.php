<?php

namespace Test\Unit;

use InvalidArgumentException;
use Test\TestCase;
use Web3\Contracts\SolidityType;

class SolidityTypeTest extends TestCase
{
    /**
     * type
     * 
     * @var \Web3\Contracts\SolidityType
     */
    protected $type;

    /**
     * setUp
     * 
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->type = new SolidityType();
    }

    /**
     * testNestedTypes
     * 
     * @return void
     */
    public function testNestedTypes()
    {
        $type = $this->type;

        $this->assertEquals($type->nestedTypes('int[2][3][4]'), ['[2]', '[3]', '[4]']);
        $this->assertEquals($type->nestedTypes('int[2][3][]'), ['[2]', '[3]', '[]']);
        $this->assertEquals($type->nestedTypes('int[2][3]'), ['[2]', '[3]']);
        $this->assertEquals($type->nestedTypes('int[2][]'), ['[2]', '[]']);
        $this->assertEquals($type->nestedTypes('int[2]'), ['[2]']);
        $this->assertEquals($type->nestedTypes('int[]'), ['[]']);
        $this->assertEquals($type->nestedTypes('int'), false);

    }

    /**
     * testNestedName
     * 
     * @return void
     */
    public function testNestedName()
    {
        $type = $this->type;

        $this->assertEquals($type->nestedName('int[2][3][4]'), 'int[2][3]');
        $this->assertEquals($type->nestedName('int[2][3][]'), 'int[2][3]');
        $this->assertEquals($type->nestedName('int[2][3]'), 'int[2]');
        $this->assertEquals($type->nestedName('int[2][]'), 'int[2]');
        $this->assertEquals($type->nestedName('int[2]'), 'int');
        $this->assertEquals($type->nestedName('int[]'), 'int');
        $this->assertEquals($type->nestedName('int'), 'int');
    }

    /**
     * testIsDynamicArray
     * 
     * @return void
     */
    public function testIsDynamicArray()
    {
        $type = $this->type;

        $this->assertFalse($type->isDynamicArray('int[2][3][4]'));
        $this->assertTrue($type->isDynamicArray('int[2][3][]'));
        $this->assertFalse($type->isDynamicArray('int[2][3]'));
        $this->assertTrue($type->isDynamicArray('int[2][]'));
        $this->assertFalse($type->isDynamicArray('int[2]'));
        $this->assertTrue($type->isDynamicArray('int[]'));
        $this->assertFalse($type->isDynamicArray('int'));
    }

    /**
     * testIsStaticArray
     * 
     * @return void
     */
    public function testIsStaticArray()
    {
        $type = $this->type;

        $this->assertTrue($type->isStaticArray('int[2][3][4]'));
        $this->assertFalse($type->isStaticArray('int[2][3][]'));
        $this->assertTrue($type->isStaticArray('int[2][3]'));
        $this->assertFalse($type->isStaticArray('int[2][]'));
        $this->assertTrue($type->isStaticArray('int[2]'));
        $this->assertFalse($type->isStaticArray('int[]'));
        $this->assertFalse($type->isStaticArray('int'));
    }

    /**
     * testStaticArrayLength
     * 
     * @return void
     */
    public function testStaticArrayLength()
    {
        $type = $this->type;

        $this->assertEquals($type->staticArrayLength('int[2][3][4]'), 4);
        $this->assertEquals($type->staticArrayLength('int[2][3][]'), 1);
        $this->assertEquals($type->staticArrayLength('int[2][3]'), 3);
        $this->assertEquals($type->staticArrayLength('int[2][]'), 1);
        $this->assertEquals($type->staticArrayLength('int[2]'), 2);
        $this->assertEquals($type->staticArrayLength('int[]'), 1);
        $this->assertEquals($type->staticArrayLength('int'), 1);

    }

    /**
     * testEncode
     * 
     * @return void
     */
    // public function testEncode()
    // {
    //     $type = $this->type;
    //     $this->assertTrue(true);
    // }

    /**
     * testDecode
     * 
     * @return void
     */
    // public function testDecode()
    // {
    //     $type = $this->type;
    //     $this->assertTrue(true);
    // }
}