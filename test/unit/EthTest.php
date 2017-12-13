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