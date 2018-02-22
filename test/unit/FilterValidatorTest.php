<?php

namespace Test\Unit;

use Test\TestCase;
use Web3\Validators\FilterValidator;

class FilterValidatorTest extends TestCase
{
    /**
     * validator
     * 
     * @var \Web3\Validators\FilterValidator
     */
    protected $validator;

    /**
     * setUp
     * 
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->validator = new FilterValidator;
    }

    /**
     * testValidate
     * 
     * @return void
     */
    public function testValidate()
    {
        $validator = $this->validator;

        $this->assertEquals(false, $validator->validate('hello web3.php'));
        $this->assertEquals(false, $validator->validate([
            'fromBlock' => 'hello',
        ]));
        $this->assertEquals(false, $validator->validate([
            'toBlock' => 'hello',
        ]));
        $this->assertEquals(false, $validator->validate([
            'address' => '0xzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz',
        ]));
        $this->assertEquals(false, $validator->validate([
            'topics' => [
                '0xzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz',
            ]
        ]));
        $this->assertEquals(true, $validator->validate([]));
        $this->assertEquals(true, $validator->validate([
            'fromBlock' => 'earliest',
            'toBlock' => 'latest',
            'address' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
            'topics' => [
                '0xd46e8dd67c5d32be8058bb8eb970870f07244567', '0xd46e8dd67c5d32be8058bb8eb970870f07244567'
            ]
        ]));
        $this->assertEquals(true, $validator->validate([
            'fromBlock' => 'earliest',
            'toBlock' => 'latest',
            'address' => [
                '0xd46e8dd67c5d32be8058bb8eb970870f07244567', '0xd46e8dd67c5d32be8058bb8eb970870f07244567'
            ],
            'topics' => [
                '0xd46e8dd67c5d32be8058bb8eb970870f07244567', '0xd46e8dd67c5d32be8058bb8eb970870f07244567'
            ]
        ]));
    }
}