<?php

namespace Test\Unit;

use Test\TestCase;
use Web3\Formatters\HexFormatter;

class HexFormatterTest extends TestCase
{
    /**
     * formatter
     * 
     * @var \Web3\Formatters\HexFormatter
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
        $this->formatter = new HexFormatter;
    }

    /**
     * testFormat
     * 
     * @return void
     */
    public function testFormat()
    {
        $formatter = $this->formatter;

        $hex = $formatter->format('ae');
        $this->assertEquals($hex, '0x6165');

        $hex = $formatter->format('0xabce');
        $this->assertEquals($hex, '0xabce');

        $hex = $formatter->format('123');
        $this->assertEquals($hex, '0x7b');

        $hex = $formatter->format(12);
        $this->assertEquals($hex, '0xc');
    }
}