<?php

namespace Test\Unit;

use Test\TestCase;
use Web3\Validators\CallValidator;

class CallValidatorTest extends TestCase
{
    /**
     * validator
     * 
     * @var \Web3\Validators\CallValidator
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
        $this->validator = new CallValidator;
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
            'to' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
        ]));
        $this->assertEquals(false, $validator->validate([
            'to' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
            'gas' => '',
        ]));
        $this->assertEquals(false, $validator->validate([
            'to' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
            'gasPrice' => '',
        ]));
        $this->assertEquals(false, $validator->validate([
            'to' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
            'value' => '',
        ]));
        $this->assertEquals(false, $validator->validate([
            'to' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
            'data' => '',
        ]));
        $this->assertEquals(false, $validator->validate([
            'to' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
            'nonce' => '',
        ]));
        $this->assertEquals(true, $validator->validate([
            'to' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
        ]));
        $this->assertEquals(true, $validator->validate([
            'to' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
            'gas' => '0x76c0',
            'gasPrice' => '0x9184e72a000',
            'value' => '0x9184e72a',
            'data' => '0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675',
            'nonce' => '0x9184e72a',
        ]));
    }
}