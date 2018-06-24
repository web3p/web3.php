<?php

namespace Test\Unit;

use InvalidArgumentException;
use Test\TestCase;
use Web3\Contracts\Types\DynamicBytes;

class DynamicBytesTypeTest extends TestCase
{
    /**
     * testTypes
     * 
     * @var array
     */
    protected $testTypes = [
        [
            'value' => 'bytes',
            'result' => true
        ], [
            'value' => 'bytes[]',
            'result' => true
        ], [
            'value' => 'bytes[4]',
            'result' => true
        ], [
            'value' => 'bytes[][]',
            'result' => true
        ], [
            'value' => 'bytes[3][]',
            'result' => true
        ], [
            'value' => 'bytes[][6][]',
            'result' => true
        ], [
            'value' => 'bytes32',
            'result' => false
        ], [
            'value' => 'bytes8[4]',
            'result' => false
        ],
    ];

    /**
     * solidityType
     * 
     * @var \Web3\Contracts\SolidityType
     */
    protected $solidityType;

    /**
     * setUp
     * 
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->solidityType = new DynamicBytes;
    }

    /**
     * testIsType
     * 
     * @return void
     */
    public function testIsType()
    {
        $solidityType = $this->solidityType;

        foreach ($this->testTypes as $type) {
            $this->assertEquals($solidityType->isType($type['value']), $type['result']);
        }
    }
}