<?php

namespace Test\Unit;

use InvalidArgumentException;
use stdClass;
use Test\TestCase;
use phpseclib\Math\BigInteger as BigNumber;
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
      "type": "function",
      "test": {
        "name": "testObject"
      }
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
        $this->assertEquals($this->testHex, Utils::toHex('hello world'));
        $this->assertEquals('0x' . $this->testHex, Utils::toHex('hello world', true));

        $this->assertEquals('0x927c0', Utils::toHex(0x0927c0, true));
        $this->assertEquals('0x927c0', Utils::toHex('600000', true));
        $this->assertEquals('0x927c0', Utils::toHex(600000, true));
        $this->assertEquals('0x927c0', Utils::toHex(new BigNumber(600000), true));
        
        $this->assertEquals('0xea60', Utils::toHex(0x0ea60, true));
        $this->assertEquals('0xea60', Utils::toHex('60000', true));
        $this->assertEquals('0xea60', Utils::toHex(60000, true));
        $this->assertEquals('0xea60', Utils::toHex(new BigNumber(60000), true));

        $this->assertEquals('0x', Utils::toHex(0x00, true));
        $this->assertEquals('0x', Utils::toHex('0', true));
        $this->assertEquals('0x', Utils::toHex(0, true));
        $this->assertEquals('0x', Utils::toHex(new BigNumber(0), true));

        $this->assertEquals('0x30', Utils::toHex(48, true));
        $this->assertEquals('0x30', Utils::toHex('48', true));
        $this->assertEquals('30', Utils::toHex(48));
        $this->assertEquals('30', Utils::toHex('48'));

        $this->assertEquals('0x30', Utils::toHex(new BigNumber(48), true));
        $this->assertEquals('0x30', Utils::toHex(new BigNumber('48'), true));
        $this->assertEquals('30', Utils::toHex(new BigNumber(48)));
        $this->assertEquals('30', Utils::toHex(new BigNumber('48')));

        $this->expectException(InvalidArgumentException::class);
        $hex = Utils::toHex(new stdClass);
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

        $this->expectException(InvalidArgumentException::class);
        $str = Utils::hexToBin(new stdClass);
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

        $this->expectException(InvalidArgumentException::class);
        $isPrefixed = Utils::isZeroPrefixed(new stdClass);
    }

    /**
     * testIsAddress
     * 
     * @return void
     */
    public function testIsAddress()
    {
        $isAddress = Utils::isAddress('ca35b7d915458ef540ade6068dfe2f44e8fa733c');
        $this->assertEquals($isAddress, true);

        $isAddress = Utils::isAddress('0xca35b7d915458ef540ade6068dfe2f44e8fa733c');
        $this->assertEquals($isAddress, true);

        $isAddress = Utils::isAddress('0Xca35b7d915458ef540ade6068dfe2f44e8fa733c');
        $this->assertEquals($isAddress, true);

        $isAddress = Utils::isAddress('0XCA35B7D915458EF540ADE6068DFE2F44E8FA733C');
        $this->assertEquals($isAddress, true);

        $isAddress = Utils::isAddress('0xCA35B7D915458EF540ADE6068DFE2F44E8FA733C');
        $this->assertEquals($isAddress, true);

        $isAddress = Utils::isAddress('0xCA35B7D915458EF540ADE6068DFE2F44E8FA73cc');
        $this->assertEquals($isAddress, false);

        $this->expectException(InvalidArgumentException::class);
        $isAddress = Utils::isAddress(new stdClass);
    }

    /**
     * testIsAddressChecksum
     * 
     * @return void
     */
    public function testIsAddressChecksum()
    {
        $isAddressChecksum = Utils::isAddressChecksum('0x52908400098527886E0F7030069857D2E4169EE7');
        $this->assertEquals($isAddressChecksum, true);

        $isAddressChecksum = Utils::isAddressChecksum('0x8617E340B3D01FA5F11F306F4090FD50E238070D');
        $this->assertEquals($isAddressChecksum, true);

        $isAddressChecksum = Utils::isAddressChecksum('0xde709f2102306220921060314715629080e2fb77');
        $this->assertEquals($isAddressChecksum, true);

        $isAddressChecksum = Utils::isAddressChecksum('0x27b1fdb04752bbc536007a920d24acb045561c26');
        $this->assertEquals($isAddressChecksum, true);

        $isAddressChecksum = Utils::isAddressChecksum('0x5aAeb6053F3E94C9b9A09f33669435E7Ef1BeAed');
        $this->assertEquals($isAddressChecksum, true);

        $isAddressChecksum = Utils::isAddressChecksum('0x5aAeb6053F3E94C9b9A09f33669435E7Ef1BeAed');
        $this->assertEquals($isAddressChecksum, true);

        $isAddressChecksum = Utils::isAddressChecksum('0xfB6916095ca1df60bB79Ce92cE3Ea74c37c5d359');
        $this->assertEquals($isAddressChecksum, true);

        $isAddressChecksum = Utils::isAddressChecksum('0xdbF03B407c01E7cD3CBea99509d93f8DDDC8C6FB');
        $this->assertEquals($isAddressChecksum, true);

        $isAddressChecksum = Utils::isAddressChecksum('0xD1220A0cf47c7B9Be7A2E6BA89F429762e7b9aDb');
        $this->assertEquals($isAddressChecksum, true);

        $isAddressChecksum = Utils::isAddressChecksum('0XD1220A0CF47C7B9BE7A2E6BA89F429762E7B9ADB');
        $this->assertEquals($isAddressChecksum, false);

        $isAddressChecksum = Utils::isAddressChecksum('0xd1220a0cf47c7b9be7a2e6ba89f429762e7b9adb');
        $this->assertEquals($isAddressChecksum, false);

        $this->expectException(InvalidArgumentException::class);
        $isAddressChecksum = Utils::isAddressChecksum(new stdClass);
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

        $this->expectException(InvalidArgumentException::class);
        $str = Utils::sha3(new stdClass);
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

        $bn = Utils::toWei('0.1', 'ether');
        $this->assertEquals($bn->toString(), '100000000000000000');

        $bn = Utils::toWei('1.69', 'ether');
        $this->assertEquals($bn->toString(), '1690000000000000000');

        $bn = Utils::toWei(0.1, 'ether');
        $this->assertEquals($bn->toString(), '100000000000000000');

        $bn = Utils::toWei(1.69, 'ether');
        $this->assertEquals($bn->toString(), '1690000000000000000');

        $bn = Utils::toWei('-0.1', 'ether');
        $this->assertEquals($bn->toString(), '-100000000000000000');

        $bn = Utils::toWei('-1.69', 'ether');
        $this->assertEquals($bn->toString(), '-1690000000000000000');

        $bn = Utils::toWei(-0.1, 'ether');
        $this->assertEquals($bn->toString(), '-100000000000000000');

        $bn = Utils::toWei(-1.69, 'ether');
        $this->assertEquals($bn->toString(), '-1690000000000000000');

        $bn = Utils::toWei('', 'ether');
        $this->assertEquals($bn->toString(), '0');

        $bn = Utils::toWei(-1.697, 'kwei');
        $this->assertEquals($bn->toString(), '-1697');

        try {
            $bn = Utils::toWei('0x5218', new stdClass);
        } catch (InvalidArgumentException $e) {
            $this->assertTrue($e !== null);
        }

        try {
            $bn = Utils::toWei('0x5218', 'test');
        } catch (InvalidArgumentException $e) {
            $this->assertTrue($e !== null);
        }

        try {
            // out of limit
            $bn = Utils::toWei(-1.6977, 'kwei');
        } catch (InvalidArgumentException $e) {
            $this->assertTrue($e !== null);
        }
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

        list($bnq, $bnr) = Utils::toEther('0x5218', 'ether');

        $this->assertEquals($bnq->toString(), '21016');
        $this->assertEquals($bnr->toString(), '0');
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

        try {
            list($bnq, $bnr) = Utils::fromWei('0x5218', new stdClass);
        } catch (InvalidArgumentException $e) {
            $this->assertTrue($e !== null);
        }

        try {
            list($bnq, $bnr) = Utils::fromWei('0x5218', 'test');
        } catch (InvalidArgumentException $e) {
            $this->assertTrue($e !== null);
        }
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

        $methodString = Utils::jsonMethodToString([
            'name' => 'approve(address,uint256)'
        ]);
        $this->assertEquals($methodString, 'approve(address,uint256)');

        $this->expectException(InvalidArgumentException::class);
        $methodString = Utils::jsonMethodToString('test');
    }

    /**
     * testJsonToArray
     * 
     * @return void
     */
    public function testJsonToArray()
    {
        $json = json_decode($this->testJsonMethodString);
        $jsonArrayDepth1 = Utils::jsonToArray($json);

        $this->assertEquals($jsonArrayDepth1, (array) $json);

        $jsonAssoc = json_decode($this->testJsonMethodString, true);
        $jsonArrayDepth2 = Utils::jsonToArray($json, 2);

        $this->assertEquals($jsonArrayDepth2, $jsonAssoc);

        $jsonArrayDepth2 = Utils::jsonToArray($jsonArrayDepth1, 2);
        $this->assertEquals($jsonArrayDepth2, $jsonAssoc);

        $jsonArray = Utils::jsonToArray($this->testJsonMethodString);
        $this->assertEquals($jsonArray, $jsonAssoc);

        try {
            $jsonArray = Utils::jsonToArray($json, 0);
        } catch (InvalidArgumentException $e) {
            $this->assertTrue($e !== null);
        }

        try {
            $jsonArray = Utils::jsonToArray(mb_substr($this->testJsonMethodString, 0, 50), 1);
        } catch (InvalidArgumentException $e) {
            $this->assertTrue($e !== null);
        }

        try {
            $jsonArray = Utils::jsonToArray(0, 1);
        } catch (InvalidArgumentException $e) {
            $this->assertTrue($e !== null);
        }
    }

    /**
     * testIsHex
     * 
     * @return void
     */
    public function testIsHex()
    {
        $isHex = Utils::isHex($this->testHex);

        $this->assertTrue($isHex);

        $isHex = Utils::isHex('0x' . $this->testHex);

        $this->assertTrue($isHex);

        $isHex = Utils::isHex('hello world');

        $this->assertFalse($isHex);
    }

    /**
     * testIsNegative
     * 
     * @return void
     */
    public function testIsNegative()
    {
        $isNegative = Utils::isNegative('-1');
        $this->assertTrue($isNegative);

        $isNegative = Utils::isNegative('1');
        $this->assertFalse($isNegative);
    }

    /**
     * testToBn
     * 
     * @return void
     */
    public function testToBn()
    {
        $bn = Utils::toBn('');
        $this->assertEquals($bn->toString(), '0');

        $bn = Utils::toBn(11);
        $this->assertEquals($bn->toString(), '11');

        $bn = Utils::toBn('0x12');
        $this->assertEquals($bn->toString(), '18');

        $bn = Utils::toBn('-0x12');
        $this->assertEquals($bn->toString(), '-18');

        $bn = Utils::toBn(0x12);
        $this->assertEquals($bn->toString(), '18');

        $bn = Utils::toBn('ae');
        $this->assertEquals($bn->toString(), '174');

        $bn = Utils::toBn('-ae');
        $this->assertEquals($bn->toString(), '-174');

        $bn = Utils::toBn('-1');
        $this->assertEquals($bn->toString(), '-1');

        $bn = Utils::toBn('-0.1');
        $this->assertEquals(count($bn), 3);
        $this->assertEquals($bn[0]->toString(), '0');
        $this->assertEquals($bn[1]->toString(), '1');
        $this->assertEquals($bn[2]->toString(), '-1');

        $bn = Utils::toBn(-0.1);
        $this->assertEquals(count($bn), 3);
        $this->assertEquals($bn[0]->toString(), '0');
        $this->assertEquals($bn[1]->toString(), '1');
        $this->assertEquals($bn[2]->toString(), '-1');

        $bn = Utils::toBn('0.1');
        $this->assertEquals(count($bn), 3);
        $this->assertEquals($bn[0]->toString(), '0');
        $this->assertEquals($bn[1]->toString(), '1');
        $this->assertEquals($bn[2], false);

        $bn = Utils::toBn('-1.69');
        $this->assertEquals(count($bn), 3);
        $this->assertEquals($bn[0]->toString(), '1');
        $this->assertEquals($bn[1]->toString(), '69');
        $this->assertEquals($bn[2]->toString(), '-1');

        $bn = Utils::toBn(-1.69);
        $this->assertEquals($bn[0]->toString(), '1');
        $this->assertEquals($bn[1]->toString(), '69');
        $this->assertEquals($bn[2]->toString(), '-1');

        $bn = Utils::toBn('1.69');
        $this->assertEquals(count($bn), 3);
        $this->assertEquals($bn[0]->toString(), '1');
        $this->assertEquals($bn[1]->toString(), '69');
        $this->assertEquals($bn[2], false);

        $bn = Utils::toBn(new BigNumber(1));
        $this->assertEquals($bn->toString(), '1');

        $this->expectException(InvalidArgumentException::class);
        $bn = Utils::toBn(new stdClass);
    }
}