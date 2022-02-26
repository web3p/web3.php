<?php

namespace Test\Unit;

use Test\TestCase;
use Web3\Validators\StringValidator;

class StringValidatorTest extends TestCase
{
    /**
     * validator
     * 
     * @var \Web3\Validators\StringValidator
     */
    protected $validator;

    /**
     * setUp
     * 
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->validator = new StringValidator;
    }

    /**
     * testValidate
     * 
     * @return void
     */
    public function testValidate()
    {
        $validator = $this->validator;

        $this->assertEquals(true, $validator->validate('0Xca35b7d915458ef540ade6068dfe2f44e8fa733c'));
        $this->assertEquals(false, $validator->validate(1234));
        $this->assertEquals(false, $validator->validate(0xCA35B7D915458EF540ADE6068DFE2F44E8FA733C));
    }
}