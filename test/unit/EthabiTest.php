<?php

namespace Test\Unit;

use InvalidArgumentException;
use Test\TestCase;
use Web3\Utils;
use Web3\Contracts\Ethabi;
use Web3\Contracts\Types\Address;
use Web3\Contracts\Types\Boolean;
use Web3\Contracts\Types\Bytes;
use Web3\Contracts\Types\Integer;
use Web3\Contracts\Types\Str;
use Web3\Contracts\Types\Uinteger;

class EthabiTest extends TestCase
{
    /**
     * abi
     * 
     * @var \Web3\Contracts\Ethabi
     */
    protected $abi;

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
     * tests
     * from web3 eth.abi.encodeParameters test
     * and web3 eth.abi.encodeParameter test
     * 
     * @param array
     */
    protected $tests = [
        [
            'params' => [['uint256','string'], ['2345675643', 'Hello!%']],
            'result' => '0x000000000000000000000000000000000000000000000000000000008bd02b7b0000000000000000000000000000000000000000000000000000000000000040000000000000000000000000000000000000000000000000000000000000000748656c6c6f212500000000000000000000000000000000000000000000000000'
        ], [
            'params' => [['uint8[]','bytes32'], [['34','434'], '0x324567dfff']],
            'result' => '0x0000000000000000000000000000000000000000000000000000000000000040324567dfff0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000002000000000000000000000000000000000000000000000000000000000000002200000000000000000000000000000000000000000000000000000000000001b2'
        ], [
            'params' => [['address','address','address', 'address'], ['0x90f8bf6a479f320ead074411a4b0e7944ea8c9c1','','0x0', null]],
            'result' => '0x00000000000000000000000090f8bf6a479f320ead074411a4b0e7944ea8c9c1000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000'
        ], [
            'params' => [['bool[2]', 'bool[3]'], [[true, false], [false, false, true]]],
            'result' => '0x00000000000000000000000000000000000000000000000000000000000000010000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000001'
        ], [
            'params' => [['int'], [1]],
            'result' => '0x0000000000000000000000000000000000000000000000000000000000000001'
        ], [
            'params' => [['int'], [16]],
            'result' => '0x0000000000000000000000000000000000000000000000000000000000000010'
        ], [
            'params' => [['int'], [-1]],
            'result' => '0xffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff'
        ], [
            'params' => [['int256'], [1]],
            'result' => '0x0000000000000000000000000000000000000000000000000000000000000001'
        ], [
            'params' => [['int256'], [16]],
            'result' => '0x0000000000000000000000000000000000000000000000000000000000000010'
        ], [
            'params' => [['int256'], [-1]],
            'result' => '0xffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff'
        ], [
            'params' => [['int[]'], [[3]]],
            'result' => '0x000000000000000000000000000000000000000000000000000000000000002000000000000000000000000000000000000000000000000000000000000000010000000000000000000000000000000000000000000000000000000000000003'
        ], [
            'params' => [['int256[]'], [[3]]],
            'result' => '0x000000000000000000000000000000000000000000000000000000000000002000000000000000000000000000000000000000000000000000000000000000010000000000000000000000000000000000000000000000000000000000000003'
        ], [
            'params' => [['int256[]'], [[1,2,3]]],
            'result' => '0x00000000000000000000000000000000000000000000000000000000000000200000000000000000000000000000000000000000000000000000000000000003000000000000000000000000000000000000000000000000000000000000000100000000000000000000000000000000000000000000000000000000000000020000000000000000000000000000000000000000000000000000000000000003'
        ], [
            'params' => [['int[]','int[]'], [[1,2],[3,4]]],
            'result' => '0x000000000000000000000000000000000000000000000000000000000000004000000000000000000000000000000000000000000000000000000000000000a0000000000000000000000000000000000000000000000000000000000000000200000000000000000000000000000000000000000000000000000000000000010000000000000000000000000000000000000000000000000000000000000002000000000000000000000000000000000000000000000000000000000000000200000000000000000000000000000000000000000000000000000000000000030000000000000000000000000000000000000000000000000000000000000004'
        ]
    ];

    /**
     * setUp
     * 
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        // Error: Using $this when not in object context
        // $this->abi = new Ethabi([
        //     'address' => Address::class,
        //     'bool' => Boolean::class,
        //     'bytes' => Bytes::class,
        //     'int' => Integer::class,
        //     'string' => Str::class,
        //     'uint' => Uinteger::class,
        // ]);

        $this->abi = new Ethabi([
            'address' => new Address,
            'bool' => new Boolean,
            'bytes' => new Bytes,
            'int' => new Integer,
            'string' => new Str,
            'uint' => new Uinteger,
        ]);
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

    /**
     * testEncodeParameter
     * 
     * @return void
     */
    public function testEncodeParameter()
    {
        $abi = $this->abi;

        $this->assertEquals('0xffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $abi->encodeParameter('int256', '-1'));
    }

    /**
     * testEncodeParameters
     * 
     * @return void
     */
    public function testEncodeParameters()
    {
        $abi = $this->abi;

        foreach ($this->tests as $test) {
            $this->assertEquals($test['result'], $abi->encodeParameters($test['params'][0], $test['params'][1]));
        }
    }
}