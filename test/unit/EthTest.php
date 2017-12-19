<?php

namespace Test\Unit;

use RuntimeException;
use Test\TestCase;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\RequestManager;

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
        $eth = $this->eth;

        $this->assertTrue($eth->provider instanceof HttpProvider);
        $this->assertTrue($eth->provider->requestManager instanceof RequestManager);
    }
}