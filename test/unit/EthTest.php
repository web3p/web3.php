<?php

namespace Test\Unit;

use RuntimeException;
use Test\TestCase;
use Web3\Providers\HttpProvider;
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
    public function setUp(): void
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
    }

    /**
     * testSetProvider
     * 
     * @return void
     */
    public function testSetProvider()
    {
        $eth = $this->eth;
        $eth->provider = new HttpProvider('http://localhost:8545');

        $this->assertEquals($eth->provider->host, 'http://localhost:8545');

        $eth->provider = null;

        $this->assertEquals($eth->provider->host, 'http://localhost:8545');
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