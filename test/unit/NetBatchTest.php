<?php

namespace Test\Unit;

use RuntimeException;
use Test\TestCase;
use phpseclib\Math\BigInteger as BigNumber;

class NetBatchTest extends TestCase
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
     * testBatch
     * 
     * @return void
     */
    public function testBatch()
    {
        $net = $this->net;

        $net->batch(true);
        $net->version();
        $net->listening();
        $net->peerCount();

        $net->provider->execute(function ($err, $data) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(is_string($data[0]));
            $this->assertTrue(is_bool($data[1]));
            $this->assertTrue($data[2] instanceof BigNumber);
        });
    }

    /**
     * testBatchAsync
     * 
     * @return void
     */
    public function testBatchAsync()
    {
        $net = $this->net;
        $net->provider = $this->asyncHttpProvider;

        $net->batch(true);
        $net->version();
        $net->listening();
        $net->peerCount();

        // should return reactphp promise
        $promise = $net->provider->execute(function ($err, $data) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(is_string($data[0]));
            $this->assertTrue(is_bool($data[1]));
            $this->assertTrue($data[2] instanceof BigNumber);
        });
        $this->assertTrue($promise instanceof \React\Promise\PromiseInterface);
        \React\Async\await($promise);
    }
}