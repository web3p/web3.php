<?php

namespace Test\Unit;

use RuntimeException;
use Test\TestCase;
use Web3\Web3;
use Web3\Eth;
use Web3\Net;
use Web3\Personal;
use Web3\Shh;
use Web3\Utils;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\RequestManager;
use Web3\RequestManagers\HttpRequestManager;

class Web3Test extends TestCase
{
    /**
     * testHex
     * 'hello world'
     * you can check by call pack('H*', $hex)
     * 
     * @var string
     */
    protected $testHex = '0x68656c6c6f20776f726c64';

    /**
     * testHash
     * 
     * @var string
     */
    protected $testHash = '0x47173285a8d7341e5e972fc677286384f802f8ef42a5ec5f03bbfa254cb01fad';

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
     * testInstance
     * 
     * @return void
     */
    public function testInstance()
    {
        $requestManager = new HttpRequestManager('http://localhost:8545');
        $web3 = new Web3(new HttpProvider($requestManager));

        $this->assertTrue($web3->provider instanceof HttpProvider);
        $this->assertTrue($web3->provider->requestManager instanceof RequestManager);
        $this->assertTrue($web3->eth instanceof Eth);
        $this->assertTrue($web3->net instanceof Net);
        $this->assertTrue($web3->personal instanceof Personal);
        $this->assertTrue($web3->shh instanceof Shh);
        $this->assertTrue($web3->utils instanceof Utils);
    }

    /**
     * testSetProvider
     * 
     * @return void
     */
    public function testSetProvider()
    {
        $web3 = $this->web3;
        $requestManager = new HttpRequestManager('http://localhost:8545');
        $web3->provider = new HttpProvider($requestManager);

        $this->assertEquals($web3->provider->requestManager->host, 'http://localhost:8545');

        $web3->provider = null;
        $this->assertEquals($web3->provider->requestManager->host, 'http://localhost:8545');
    }

    /**
     * testCallThrowRuntimeException
     * 
     * @return void
     */
    public function testCallThrowRuntimeException()
    {
        $this->expectException(RuntimeException::class);

        $web3 = new Web3(null);
        $web3->sha3('hello world');
    }
}