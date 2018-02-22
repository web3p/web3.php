<?php

namespace Test\Unit;

use Test\TestCase;
use Web3\Validators\PostValidator;

class PostValidatorTest extends TestCase
{
    /**
     * validator
     * 
     * @var \Web3\Validators\PostValidator
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
        $this->validator = new PostValidator;
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
            'from' => 'hello',
        ]));
        $this->assertEquals(false, $validator->validate([
            'to' => 'hello',
        ]));
        $this->assertEquals(false, $validator->validate([
            'from' => '0xeb0b54D62ec3f561C2eebdaebd92432126F0817579c102b062d1a6c1f2ed83e8121233',
            'to' => '0xeb0b54D62ec3f561C2eebdaebd92432126F0817579c102b062d1a6c1f2ed83e8121233',
        ]));
        $this->assertEquals(false, $validator->validate([
            'topics' => [
                '0xzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz',
            ]
        ]));
        $this->assertEquals(false, $validator->validate([
            'topics' => [
                '0xzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz',
            ],
            'payload' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
        ]));
        $this->assertEquals(false, $validator->validate([
            'topics' => [
                '0xzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz',
            ],
            'payload' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
            'priority' => '0x1',
        ]));
        $this->assertEquals(true, $validator->validate([
            'topics' => [
                '0xd46e8dd67c5d32be8058bb8eb970870f07244567', '0xd46e8dd67c5d32be8058bb8eb970870f07244567'
            ],
            'payload' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
            'priority' => '0x1',
            'ttl' => '0x1',
        ]));
        $this->assertEquals(true, $validator->validate([
            'from' => '0xeb0b54D62ec3f561C2eebdaebd92432126F0817579c102b062d1a6c1f2ed83e8121233',
            'to' => '0xeb0b54D62ec3f561C2eebdaebd92432126F0817579c102b062d1a6c1f2ed83e8121233',
            'topics' => [
                '0xd46e8dd67c5d32be8058bb8eb970870f07244567', '0xd46e8dd67c5d32be8058bb8eb970870f07244567'
            ],
            'payload' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
            'priority' => '0x1',
            'ttl' => '0x1',
        ]));
    }
}