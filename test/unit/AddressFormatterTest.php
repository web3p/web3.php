<?php

namespace Test\Unit;

use Test\TestCase;
use Web3\Formatters\AddressFormatter;

class AddressFormatterTest extends TestCase
{
    /**
     * formatter
     * 
     * @var \Web3\Formatters\AddressFormatter
     */
    protected $formatter;

    /**
     * setUp
     * 
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->formatter = new AddressFormatter;
    }

    /**
     * testFormat
     * 
     * @return void
     */
    public function testFormat()
    {
        $formatter = $this->formatter;

        $address = $formatter->format('0Xca35b7d915458ef540ade6068dfe2f44e8fa733c');
        $this->assertEquals($address, '0xca35b7d915458ef540ade6068dfe2f44e8fa733c');

        $address = $formatter->format('0XCA35B7D915458EF540ADE6068DFE2F44E8FA733C');
        $this->assertEquals($address, '0xca35b7d915458ef540ade6068dfe2f44e8fa733c');

        $address = $formatter->format('0xCA35B7D915458EF540ADE6068DFE2F44E8FA733C');
        $this->assertEquals($address, '0xca35b7d915458ef540ade6068dfe2f44e8fa733c');

        $address = $formatter->format('CA35B7D915458EF540ADE6068DFE2F44E8FA733C');
        $this->assertEquals($address, '0xca35b7d915458ef540ade6068dfe2f44e8fa733c');

        $address = $formatter->format('1234');
        $this->assertEquals($address, '0x00000000000000000000000000000000000004d2');

        $address = $formatter->format('abcd');
        $this->assertEquals($address, '0x000000000000000000000000000000000000abcd');
    }
}