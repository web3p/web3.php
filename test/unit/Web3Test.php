<?php

namespace Test\Unit;

use Test\TestCase;
use Web3\Web3;
use Web3\Eth;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\RequestManager;

class Web3Test extends TestCase
{
    /**
     * web3
     * 
     * @var \Web3\Web3
     */
    protected $web3;

    /**
     * testHex
     * 'hello world'
     * you can check by call pack('H*', $hex)
     * 
     * @var string
     */
    protected $testHex = '0x68656c6c6f20776f726c64';

    /**
     * testHash
     * 
     * @var string
     */
    protected $testHash = '0x47173285a8d7341e5e972fc677286384f802f8ef42a5ec5f03bbfa254cb01fad';

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
     * testInstance
     * 
     * @return void
     */
    public function testInstance()
    {
        $web3 = $this->web3;

        $this->assertTrue($web3->provider instanceof HttpProvider);
        $this->assertTrue($web3->provider->requestManager instanceof RequestManager);
        $this->assertTrue($web3->eth instanceof Eth);
    }

    /**
     * testSend
     * 
     * @return void
     */    
    public function testSend()
    {
        $web3 = $this->web3;

        $web3->clientVersion(function ($err, $version) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if (isset($version->result)) {
                $this->assertTrue(is_string($version->result));
            } else {
                $this->fail($version->error->message);
            }
        });

        $web3->sha3($this->testHex, function ($err, $hash) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if (isset($hash->result)) {
                $this->assertEquals($hash->result, $this->testHash);
            } else {
                $this->fail($hash->error->message);
            }
        });
    }

    /**
     * testBatch
     * 
     * @return void
     */
    public function testBatch()
    {
        $web3 = $this->web3;

        $web3->batch(true);
        $web3->clientVersion();
        $web3->sha3($this->testHex);

        $web3->provider->execute(function ($err, $data) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(is_string($data[0]->result));
            $this->assertEquals($data[1]->result, $this->testHash);
        });
    }
}