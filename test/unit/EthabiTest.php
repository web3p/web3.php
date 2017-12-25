<?php

namespace Test\Unit;

use InvalidArgumentException;
use Test\TestCase;
use Web3\Utils;
use Web3\Contracts\Ethabi;

class EthabiTest extends TestCase
{
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
     * abi
     * 
     * @var \Web3\Contracts\Ethabi
     */
    protected $abi;

    /**
     * setUp
     * 
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->abi = new Ethabi();
    }

    /**
     * testEncodeFunctionSignature
     * 
     * @return void
     */
    public function testEncodeFunctionSignature()
    {
        $abi = $this->abi;
        $str = $abi->encodeFunctionSignature('baz(uint32,bool)');

        $this->assertEquals($str, '0xcdcd77c0');

        $json = json_decode($this->testJsonMethodString);
        $methodString = Utils::jsonMethodToString($json);
        $str = $abi->encodeFunctionSignature($methodString);

        $this->assertEquals($str, '0x095ea7b3');

        $str = $abi->encodeFunctionSignature('bar(bytes3[2])');

        $this->assertEquals($str, '0xfce353f6');

        $str = $abi->encodeFunctionSignature('sam(bytes,bool,uint256[])');

        $this->assertEquals($str, '0xa5643bf2');
    }

    /**
     * testEncodeEventSignature
     * 
     * @return void
     */
    public function testEncodeEventSignature()
    {
        $abi = $this->abi;
        $str = $abi->encodeEventSignature('baz(uint32,bool)');

        $this->assertEquals($str, '0xcdcd77c0992ec5bbfc459984220f8c45084cc24d9b6efed1fae540db8de801d2');

        $json = json_decode($this->testJsonMethodString);
        $methodString = Utils::jsonMethodToString($json);
        $str = $abi->encodeEventSignature($methodString);

        $this->assertEquals($str, '0x095ea7b334ae44009aa867bfb386f5c3b4b443ac6f0ee573fa91c4608fbadfba');
    }
}