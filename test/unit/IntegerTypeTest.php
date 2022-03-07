<?php

namespace Test\Unit;

use InvalidArgumentException;
use Test\TestCase;
use Web3\Contracts\Types\Integer;

class IntegerTypeTest extends TestCase
{
    /**
     * testTypes
     * 
     * @var array
     */
    protected $testTypes = [
        [
            'value' => 'int',
            'result' => true
        ], [
            'value' => 'int[]',
            'result' => true
        ], [
            'value' => 'int[4]',
            'result' => true
        ], [
            'value' => 'int[][]',
            'result' => true
        ], [
            'value' => 'int[3][]',
            'result' => true
        ], [
            'value' => 'int[][6][]',
            'result' => true
        ], [
            'value' => 'int32',
            'result' => true
        ], [
            'value' => 'int64[4]',
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
    public function setUp(): void
    {
        parent::setUp();
        $this->solidityType = new Integer;
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