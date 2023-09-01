<?php

namespace Test\Unit;

use RuntimeException;
use InvalidArgumentException;
use Test\TestCase;
use phpseclib\Math\BigInteger as BigNumber;

class NetApiTest extends TestCase
{
    /**
     * net
     * 
     * @var Web3\Net
     */
    protected $net;

    /**
     * setUp
     * 
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->net = $this->web3->net;
    }

    /**
     * testVersion
     * 
     * @return void
     */    
    public function testVersion()
    {
        $net = $this->net;

        $net->version(function ($err, $version) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(is_string($version));
        });
    }

    /**
     * testPeerCount
     * 
     * @return void
     */    
    public function testPeerCount()
    {
        $net = $this->net;

        $net->peerCount(function ($err, $count) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue($count instanceof BigNumber);
        });
    }

    /**
     * testListening
     * 
     * @return void
     */    
    public function testListening()
    {
        $net = $this->net;

        $net->listening(function ($err, $net) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(is_bool($net));
        });
    }

    /**
     * testPeerCountAsync
     * 
     * @return void
     */    
    public function testPeerCountAsync()
    {
        $net = $this->net;
        $net->provider = $this->asyncHttpProvider;

        // should return reactphp promise
        $promise = $net->peerCount(function ($err, $count) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue($count instanceof BigNumber);
        });
        $this->assertTrue($promise instanceof \React\Promise\PromiseInterface);
        \React\Async\await($promise);
    }

    /**
     * testUnallowedMethod
     * 
     * @return void
     */
    public function testUnallowedMethod()
    {
        $this->expectException(RuntimeException::class);

        $net = $this->net;

        $net->hello(function ($err, $hello) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(true);
        });
    }

    /**
     * testWrongCallback
     * 
     * @return void
     */
    public function testWrongCallback()
    {
        $this->expectException(InvalidArgumentException::class);

        $net = $this->net;

        $net->version();
    }
}