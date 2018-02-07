<?php

namespace Test\Unit;

use RuntimeException;
use Test\TestCase;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\RequestManager;
use Web3\RequestManagers\HttpRequestManager;
use Web3\Shh;

class ShhTest extends TestCase
{
    /**
     * shh
     * 
     * @var Web3\Shh
     */
    protected $shh;

    /**
     * setUp
     * 
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->shh = $this->web3->shh;
    }

    /**
     * testInstance
     * 
     * @return void
     */
    public function testInstance()
    {
        $shh = new Shh($this->testHost);

        $this->assertTrue($shh->provider instanceof HttpProvider);
        $this->assertTrue($shh->provider->requestManager instanceof RequestManager);
    }

    /**
     * testSetProvider
     * 
     * @return void
     */
    public function testSetProvider()
    {
        $shh = $this->shh;
        $requestManager = new HttpRequestManager('http://localhost:8545');
        $shh->provider = new HttpProvider($requestManager);

        $this->assertEquals($shh->provider->requestManager->host, 'http://localhost:8545');

        $shh->provider = null;

        $this->assertEquals($shh->provider->requestManager->host, 'http://localhost:8545');
    }

    /**
     * testCallThrowRuntimeException
     * 
     * @return void
     */
    public function testCallThrowRuntimeException()
    {
        $this->expectException(RuntimeException::class);

        $shh = new Shh(null);
        $shh->post([]);
    }
}