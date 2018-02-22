<?php

namespace Test\Unit;

use Test\TestCase;
use Web3\Validators\NonceValidator;

class NonceValidatorTest extends TestCase
{
    /**
     * validator
     * 
     * @var \Web3\Validators\NonceValidator
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
        $this->validator = new NonceValidator;
    }

    /**
     * testValidate
     * 
     * @return void
     */
    public function testValidate()
    {
        $validator = $this->validator;

        $this->assertEquals(false, $validator->validate('0Xca35b7d915458ef540ade6068dfe2f44e8fa733c'));
        $this->assertEquals(false, $validator->validate('0XCA35B7D915458EF540ADE6068DFE2F44E8FA733C'));
        $this->assertEquals(false, $validator->validate('0xcA35b7D915458eF540ade6068Dfe2f44e8fA733ccA35b7D915458eF540ade6068Dfe2f44e8fA733c'));
        $this->assertEquals(false, $validator->validate('CA35B7D915458EF540ADE6068DFE2F44E8FA733C'));
        $this->assertEquals(false, $validator->validate('1234'));
        $this->assertEquals(false, $validator->validate('abcd'));
        $this->assertEquals(false, $validator->validate(0xCA35B7D915458EF540ADE6068DFE2F44E8FA733C));
        $this->assertEquals(true, $validator->validate('0xCA35B7D915458EF5'));
        $this->assertEquals(true, $validator->validate('0xeb0b54d62ec3f561'));
        $this->assertEquals(true, $validator->validate('0xeb0b54D62ec3f561'));
    }
}