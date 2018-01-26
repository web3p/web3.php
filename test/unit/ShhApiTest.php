<?php

namespace Test\Unit;

use RuntimeException;
use Test\TestCase;

class ShhApiTest extends TestCase
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
     * testVersion
     * 
     * @return void
     */    
    public function testVersion()
    {
        $shh = $this->shh;

        $shh->version(function ($err, $version) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(is_string($version));
        });
    }

    /**
     * testNewIdentity
     * 
     * @return void
     */    
    public function testNewIdentity()
    {
        $shh = $this->shh;

        $shh->newIdentity(function ($err, $identity) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertEquals(mb_strlen($identity), 132);
        });
    }
}