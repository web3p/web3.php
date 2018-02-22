<?php

namespace Test\Unit;

use Test\TestCase;
use Web3\Validators\ShhFilterValidator;

class ShhFilterValidatorTest extends TestCase
{
    /**
     * validator
     * 
     * @var \Web3\Validators\ShhFilterValidator
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
        $this->validator = new ShhFilterValidator;
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
            'to' => 'hello',
        ]));
        $this->assertEquals(false, $validator->validate([
            'to' => '0xeb0b54D62ec3f561C2eebdaebd92432126F0817579c102b062d1a6c1f2ed83e8121233',
        ]));
        $this->assertEquals(false, $validator->validate([
            'to' => '0xeb0b54D62ec3f561C2eebdaebd92432126F0817579c102b062d1a6c1f2ed83e8121233',
            'topics' => [
                '0xeb0b54D62ec3f561C2eebdaebd9243212', [
                    '0xeb0b54D62ec3f561C2eebdaebd9243212', '0xzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz'
                ]
            ]
        ]));
        $this->assertEquals(true, $validator->validate([
            'to' => '0xeb0b54D62ec3f561C2eebdaebd92432126F0817579c102b062d1a6c1f2ed83e8121233',
            'topics' => [
                '0xeb0b54D62ec3f561C2eebdaebd9243212', [
                    '0xeb0b54D62ec3f561C2eebdaebd9243212', '0xeb0b54D62ec3f561C2eebdaebd9243212'
                ]
            ]
        ]));
        $this->assertEquals(true, $validator->validate([
            'topics' => [
                '0xeb0b54D62ec3f561C2eebdaebd9243212', [
                    '0xeb0b54D62ec3f561C2eebdaebd9243212', '0xeb0b54D62ec3f561C2eebdaebd9243212'
                ]
            ]
        ]));
    }
}