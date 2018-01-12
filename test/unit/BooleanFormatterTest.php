<?php

namespace Test\Unit;

use Test\TestCase;
use Web3\Formatters\BooleanFormatter;

class BooleanFormatterTest extends TestCase
{
    /**
     * formatter
     * 
     * @var \Web3\Formatters\Boolean
     */
    protected $formatter;

    /**
     * setUp
     * 
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->formatter = new BooleanFormatter;
    }

    /**
     * testFormat
     * 
     * @return void
     */
    public function testFormat()
    {
        $formatter = $this->formatter;

        $boolean = $formatter->format(true);

        $this->assertEquals($boolean, 1);

        $boolean = $formatter->format(false);

        $this->assertEquals($boolean, 0);
    }
}