<?php

namespace Test\Unit;

use InvalidArgumentException;
use Test\TestCase;
use Web3\Contracts\Types\Uinteger;

class UintegerTypeTest extends TestCase
{
    /**
     * testTypes
     * 
     * @var array
     */
    protected $testTypes = [
        [
            'value' => 'uint',
            'result' => true
        ], [
            'value' => 'uint[]',
            'result' => true
        ], [
            'value' => 'uint[4]',
            'result' => true
        ], [
            'value' => 'uint[][]',
            'result' => true
        ], [
            'value' => 'uint[3][]',
            'result' => true
        ], [
            'value' => 'uint[][6][]',
            'result' => true
        ], [
            'value' => 'uint32',
            'result' => true
        ], [
            'value' => 'uint64[4]',
            'result' => true
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
        $this->solidityType = new Uinteger;
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