<?php

namespace Test\Unit;

use RuntimeException;
use Test\TestCase;

class ShhBatchTest extends TestCase
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
    public function setUp(): void
    {
        parent::setUp();

        $this->shh = $this->web3->shh;
    }

    /**
     * testBatch
     * 
     * @return void
     */
    public function testBatch()
    {
        $shh = $this->shh;

        $shh->batch(true);
        $shh->version();
        $shh->version();

        $shh->provider->execute(function ($err, $data) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(is_string($data[0]));
            $this->assertTrue(is_string($data[1]));
            $this->assertEquals($data[0], $data[1]);
        });
    }

    /**
     * testBatchAsync
     * 
     * @return void
     */
    public function testBatchAsync()
    {
        $shh = $this->shh;
        $shh->provider = $this->asyncHttpProvider;

        $shh->batch(true);
        $shh->version();
        $shh->version();

        // should return reactphp promise
        $promise = $shh->provider->execute(function ($err, $data) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(is_string($data[0]));
            $this->assertTrue(is_string($data[1]));
            $this->assertEquals($data[0], $data[1]);
        });
        $this->assertTrue($promise instanceof \React\Promise\PromiseInterface);
        \React\Async\await($promise);
    }
}