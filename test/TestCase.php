<?php

namespace Test;

use \PHPUnit\Framework\TestCase as BaseTestCase;
use Web3\Web3;

class TestCase extends BaseTestCase
{
    /**
     * web3
     * 
     * @var \Web3\Web3
     */
    protected $web3;

    /**
     * testRinkebyHost
     * 
     * @var string
     */
    protected $testRinkebyHost = 'https://rinkeby.infura.io/vuethexplore';

    /**
     * testHost
     * 
     * @var string
     */
    protected $testHost = 'http://localhost:8545';

    /**
     * coinbase
     * 
     * @var string
     */
    protected $coinbase;

    /**
     * setUp
     * 
     * @return void
     */
    public function setUp()
    {
        $web3 = new Web3($this->testHost);
        $this->web3 = $web3;

        $web3->eth->coinbase(function ($err, $coinbase) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->coinbase = $coinbase;
        });
    }

    /**
     * tearDown
     * 
     * @return void
     */
    public function tearDown() {}
}