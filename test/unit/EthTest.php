<?php

namespace Test\Unit;

use RuntimeException;
use Test\TestCase;
use Web3\Web3;
use Web3\Eth;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\RequestManager;

class EthTest extends TestCase
{
    /**
     * web3
     * 
     * @var \Web3\Web3
     */
    protected $web3;

    /**
     * setUp
     * 
     * @return void
     */
    public function setUp()
    {
        $web3 = new Web3('https://rinkeby.infura.io/vuethexplore');
        $this->web3 = $web3;
    }

    /**
     * testProtocolVersion
     * 
     * @return void
     */    
    public function testProtocolVersion()
    {
        $eth = $this->web3->eth;

        $eth->protocolVersion(function ($err, $version) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if (isset($version->result)) {
                $this->assertTrue(is_string($version->result));
            } else {
                $this->fail($version->error->message);
            }
        });
    }

    /**
     * testSyncing
     * 
     * @return void
     */    
    public function testSyncing()
    {
        $eth = $this->web3->eth;

        $eth->syncing(function ($err, $syncing) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if (isset($syncing->result)) {
                // due to the result might be object or bool, only test is null
                $this->assertTrue($syncing->result !== null);
            } else {
                $this->fail($syncing->error->message);
            }
        });
    }

    /**
     * testCoinbase
     * 
     * @return void
     */    
    public function testCoinbase()
    {
        $eth = $this->web3->eth;

        $eth->coinbase(function ($err, $coinbase) {
            if ($err !== null) {
                // infura banned us to use coinbase
                return $this->assertTrue($err->getCode() === 405);
            }
            if (isset($coinbase->result)) {
                $this->assertTrue(is_string($coinbasse->result));
            } else {
                $this->fail($coinbase->error->message);
            }
        });
    }

    /**
     * testMining
     * 
     * @return void
     */    
    public function testMining()
    {
        $eth = $this->web3->eth;

        $eth->mining(function ($err, $mining) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if (isset($mining->result)) {
                $this->assertTrue($mining->result !== null);
            } else {
                $this->fail($mining->error->message);
            }
        });
    }

    /**
     * testHashrate
     * 
     * @return void
     */    
    public function testHashrate()
    {
        $eth = $this->web3->eth;

        $eth->hashrate(function ($err, $hashrate) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if (isset($hashrate->result)) {
                $this->assertTrue(is_string($hashrate->result));
            } else {
                $this->fail($hashrate->error->message);
            }
        });
    }

    /**
     * testGasPrice
     * 
     * @return void
     */    
    public function testGasPrice()
    {
        $eth = $this->web3->eth;

        $eth->gasPrice(function ($err, $gasPrice) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if (isset($gasPrice->result)) {
                $this->assertTrue(is_string($gasPrice->result));
            } else {
                $this->fail($gasPrice->error->message);
            }
        });
    }

    /**
     * testUnallowedMethod
     * 
     * @return void
     */
    public function testUnallowedMethod()
    {
        $this->expectException(RuntimeException::class);

        $eth = $this->web3->eth;

        $eth->hello(function ($err, $hello) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if (isset($hello->result)) {
                $this->assertTrue(true);
            } else {
                $this->fail($hello->error->message);
            }
        });
    }
}