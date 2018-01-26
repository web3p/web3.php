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

    /**
     * testHasIdentity
     * 
     * @return void
     */    
    public function testHasIdentity()
    {
        $shh = $this->shh;
        $newIdentity = '0x' . implode('', array_fill(0, 120, '0'));

        $shh->hasIdentity($newIdentity, function ($err, $hasIdentity) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertFalse($hasIdentity);
        });

        $shh->newIdentity(function ($err, $identity) use (&$newIdentity) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $newIdentity = $identity;

            $this->assertEquals(mb_strlen($identity), 132);
        });

        $shh->hasIdentity($newIdentity, function ($err, $hasIdentity) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue($hasIdentity);
        });
    }

    /**
     * testWrongParam
     * We transform data and throw invalid argument exception
     * instead of runtime exception.
     * 
     * @return void
     */
    public function testWrongParam()
    {
        $this->expectException(RuntimeException::class);

        $shh = $this->shh;

        $shh->hasIdentity('0', function ($err, $hasIdentity) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(true);
        });
    }
}