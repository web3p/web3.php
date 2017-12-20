<?php

namespace Test\Unit;

use InvalidArgumentException;
use Test\TestCase;
use Web3\Utils;

class UtilsTest extends TestCase
{
    /**
     * testHex
     * 'hello world'
     * you can check by call pack('H*', $hex)
     * 
     * @var string
     */
    protected $testHex = '68656c6c6f20776f726c64';

    /**
     * setUp
     * 
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * testToHex
     * 
     * @return void
     */
    public function testToHex()
    {
        $hex = Utils::toHex('hello world');

        $this->assertEquals($hex, $this->testHex);

        $hexPrefixed = Utils::toHex('hello world', true);

        $this->assertEquals($hexPrefixed, '0x' . $this->testHex);
    }

    /**
     * testHexToBin
     * 
     * @return void
     */
    public function testHexToBin()
    {
        $str = Utils::hexToBin($this->testHex);

        $this->assertEquals($str, 'hello world');

        $str = Utils::hexToBin('0x' . $this->testHex);

        $this->assertEquals($str, 'hello world');

        $str = Utils::hexToBin('0xe4b883e5bda9e7a59ee4bb99e9b1bc');

        $this->assertEquals($str, '七彩神仙鱼');
    }

    /**
     * testSha3
     * 
     * @return void
     */
    public function testSha3()
    {
        $str = Utils::sha3('');

        $this->assertNull($str);

        $str = Utils::sha3('baz(uint32,bool)');

        $this->assertEquals(mb_substr($str, 0, 10), '0xcdcd77c0');
    }
}