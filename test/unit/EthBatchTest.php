<?php

namespace Test\Unit;

use RuntimeException;
use Test\TestCase;
use phpseclib\Math\BigInteger as BigNumber;

class EthBatchTest extends TestCase
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
     * testBatch
     * 
     * @return void
     */
    public function testBatch()
    {
        $eth = $this->eth;

        $eth->batch(true);
        $eth->protocolVersion();
        $eth->syncing();

        $eth->provider->execute(function ($err, $data) {
            if ($err !== null) {
                return $this->fail('Got error!');
            }
            $this->assertTrue($data[0] instanceof BigNumber);
            $this->assertTrue($data[1] !== null);
        });
    }
}