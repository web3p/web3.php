<?php

namespace Test\Unit;

use Test\TestCase;
use Web3\Validators\ArrayNumberValidator;

class ArrayNumberValidatorTest extends TestCase
{
    /**
     * validator
     * 
     * @var \Web3\Validators\ArrayNumberValidator
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
        $this->validator = new ArrayNumberValidator;
    }

    /**
     * testValidate
     * 
     * @return void
     */
    public function testValidate()
    {
        $validator = $this->validator;

        $this->assertEquals(false, $validator->validate(1));
        $this->assertEquals(false, $validator->validate(0.1));
        $this->assertEquals(false, $validator->validate('test'));
        $this->assertEquals(false, $validator->validate([1, 0.1, 'test']));
        $this->assertEquals(true, $validator->validate([1, 0.1]));
    }
}