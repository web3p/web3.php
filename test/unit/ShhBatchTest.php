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
    public function setUp()
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
                return $this->fail('Got error!');
            }
            $this->assertTrue(is_string($data[0]));
            $this->assertTrue(is_string($data[1]));
        });
    }

    /**
     * testWrongParam
     * 
     * @return void
     */
    // public function testWrongParam()
    // {
    //     $this->expectException(RuntimeException::class);

    //     $shh = $this->shh;

    //     $shh->batch(true);
    //     $shh->version();
    //     $shh->hasIdentity('0');

    //     $shh->provider->execute(function ($err, $data) {
    //         if ($err !== null) {
    //             return $this->fail('Got error!');
    //         }
    //         $this->assertTrue(is_string($data[0]));
    //         $this->assertFalse($data[1]);
    //     });
    // }
}