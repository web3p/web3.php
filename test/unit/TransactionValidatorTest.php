<?php

namespace Test\Unit;

use Test\TestCase;
use Web3\Validators\TransactionValidator;

class TransactionValidatorTest extends TestCase
{
    /**
     * validator
     * 
     * @var \Web3\Validators\TransactionValidator
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
        $this->validator = new TransactionValidator;
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
        $this->assertEquals(false, $validator->validate([]));
        $this->assertEquals(false, $validator->validate([
            'from' => '',
        ]));
        $this->assertEquals(false, $validator->validate([
            'from' => '0xb60e8dd61c5d32be8058bb8eb970870f07233155',
            'data' => ''
        ]));
        $this->assertEquals(true, $validator->validate([
            'from' => '0xb60e8dd61c5d32be8058bb8eb970870f07233155',
            'data' => '0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675'
        ]));
        $this->assertEquals(true, $validator->validate([
            'from' => '0xb60e8dd61c5d32be8058bb8eb970870f07233155',
            'to' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
            'gas' => '0x76c0',
            'gasPrice' => '0x9184e72a000',
            'value' => '0x9184e72a',
            'data' => '0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675'
        ]));
    }
}