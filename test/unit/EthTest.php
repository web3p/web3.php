<?php

namespace Test\Unit;

use RuntimeException;
use Test\TestCase;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\RequestManager;
use Web3\RequestManagers\HttpRequestManager;
use Web3\Eth;

class EthTest extends TestCase
{
    /**
     * eth
     * 
     * @var \Web3\Eth
     */
    protected $eth;

    /**
     * setUp
     * 
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->eth = $this->web3->eth;
    }

    /**
     * testInstance
     * 
     * @return void
     */
    public function testInstance()
    {
        $eth = new Eth($this->testHost);

        $this->assertTrue($eth->provider instanceof HttpProvider);
        $this->assertTrue($eth->provider->requestManager instanceof RequestManager);
    }

    /**
     * testSetProvider
     * 
     * @return void
     */
    public function testSetProvider()
    {
        $eth = $this->eth;
        $requestManager = new HttpRequestManager('http://localhost:8545');
        $eth->provider = new HttpProvider($requestManager);

        $this->assertEquals($eth->provider->requestManager->host, 'http://localhost:8545');

        $eth->provider = null;

        $this->assertEquals($eth->provider->requestManager->host, 'http://localhost:8545');
    }

    /**
     * testCallThrowRuntimeException
     * 
     * @return void
     */
    public function testCallThrowRuntimeException()
    {
        $this->expectException(RuntimeException::class);

        $eth = new Eth(null);
        $eth->protocolVersion();
    }
}