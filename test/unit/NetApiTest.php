<?php

namespace Test\Unit;

use RuntimeException;
use Test\TestCase;

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
    public function setUp()
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
            if (isset($version->result)) {
                $this->assertTrue(is_string($version->result));
            } else {
                $this->fail($version->error->message);
            }
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
            if (isset($count->result)) {
                $this->assertTrue(is_string($count->result));
            } else {
                $this->fail($count->error->message);
            }
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
            if (isset($net->result)) {
                $this->assertTrue(is_bool($net->result));
            } else {
                $this->fail($net->error->message);
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

        $net = $this->net;

        $net->hello(function ($err, $hello) {
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