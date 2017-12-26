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
     * testJsonMethodString
     * from GameToken approve function
     * 
     * @var string
     */
    protected $testJsonMethodString = '{
      "constant": false,
      "inputs": [
        {
          "name": "_spender",
          "type": "address"
        },
        {
          "name": "_value",
          "type": "uint256"
        }
      ],
      "name": "approve",
      "outputs": [
        {
          "name": "success",
          "type": "bool"
        }
      ],
      "payable": false,
      "stateMutability": "nonpayable",
      "type": "function"
    }';

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
     * testIsZeroPrefixed
     * 
     * @return void
     */
    public function testIsZeroPrefixed()
    {
        $isPrefixed = Utils::isZeroPrefixed($this->testHex);

        $this->assertEquals($isPrefixed, false);

        $isPrefixed = Utils::isZeroPrefixed('0x' . $this->testHex);

        $this->assertEquals($isPrefixed, true);
    }

    /**
     * testStripZero
     * 
     * @return void
     */
    public function testStripZero()
    {
        $str = Utils::stripZero($this->testHex);

        $this->assertEquals($str, $this->testHex);

        $str = Utils::stripZero('0x' . $this->testHex);

        $this->assertEquals($str, $this->testHex);
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

    /**
     * testToWei
     * 
     * @return void
     */
    public function testToWei()
    {
        $bn = Utils::toWei('0x1', 'wei');

        $this->assertEquals($bn->toString(), '1');

        $bn = Utils::toWei('18', 'wei');

        $this->assertEquals($bn->toString(), '18');

        $bn = Utils::toWei(1, 'wei');

        $this->assertEquals($bn->toString(), '1');

        $bn = Utils::toWei(0x11, 'wei');

        $this->assertEquals($bn->toString(), '17');

        $bn = Utils::toWei('1', 'ether');

        $this->assertEquals($bn->toString(), '1000000000000000000');

        $bn = Utils::toWei('0x5218', 'wei');

        $this->assertEquals($bn->toString(), '21016');
    }

    /**
     * testToEther
     * 
     * @return void
     */
    public function testToEther()
    {
        list($bnq, $bnr) = Utils::toEther('0x1', 'wei');

        $this->assertEquals($bnq->toString(), '0');
        $this->assertEquals($bnr->toString(), '1');

        list($bnq, $bnr) = Utils::toEther('18', 'wei');

        $this->assertEquals($bnq->toString(), '0');
        $this->assertEquals($bnr->toString(), '18');

        list($bnq, $bnr) = Utils::toEther(1, 'wei');

        $this->assertEquals($bnq->toString(), '0');
        $this->assertEquals($bnr->toString(), '1');

        list($bnq, $bnr) = Utils::toEther(0x11, 'wei');

        $this->assertEquals($bnq->toString(), '0');
        $this->assertEquals($bnr->toString(), '17');

        list($bnq, $bnr) = Utils::toEther('1', 'kether');

        $this->assertEquals($bnq->toString(), '1000');
        $this->assertEquals($bnr->toString(), '0');

        list($bnq, $bnr) = Utils::toEther('0x5218', 'wei');

        $this->assertEquals($bnq->toString(), '0');
        $this->assertEquals($bnr->toString(), '21016');
    }

    /**
     * testFromWei
     * 
     * @return void
     */
    public function testFromWei()
    {
        list($bnq, $bnr) = Utils::fromWei('1000000000000000000', 'ether');

        $this->assertEquals($bnq->toString(), '1');
        $this->assertEquals($bnr->toString(), '0');

        list($bnq, $bnr) = Utils::fromWei('18', 'wei');

        $this->assertEquals($bnq->toString(), '18');
        $this->assertEquals($bnr->toString(), '0');

        list($bnq, $bnr) = Utils::fromWei(1, 'femtoether');

        $this->assertEquals($bnq->toString(), '0');
        $this->assertEquals($bnr->toString(), '1');

        list($bnq, $bnr) = Utils::fromWei(0x11, 'nano');

        $this->assertEquals($bnq->toString(), '0');
        $this->assertEquals($bnr->toString(), '17');

        list($bnq, $bnr) = Utils::fromWei('0x5218', 'kwei');

        $this->assertEquals($bnq->toString(), '21');
        $this->assertEquals($bnr->toString(), '16');
    }

    /**
     * testJsonMethodToString
     * 
     * @return void
     */
    public function testJsonMethodToString()
    {
        $json = json_decode($this->testJsonMethodString);
        $methodString = Utils::jsonMethodToString($json);

        $this->assertEquals($methodString, 'approve(address,uint256)');

        $json = json_decode($this->testJsonMethodString, true);
        $methodString = Utils::jsonMethodToString($json);

        $this->assertEquals($methodString, 'approve(address,uint256)');
    }

    /**
     * testJsonToArray
     * 
     * @return void
     */
    public function testJsonToArray()
    {
        $json = json_decode($this->testJsonMethodString);
        $jsonArray = Utils::jsonToArray($json);

        $this->assertEquals($jsonArray, (array) $json);

        $jsonAssoc = json_decode($this->testJsonMethodString, true);
        $jsonArray = Utils::jsonToArray($json, 2);

        $this->assertEquals($jsonArray, $jsonAssoc);
    }
}