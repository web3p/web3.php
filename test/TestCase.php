<?php

namespace Test;

use \PHPUnit\Framework\TestCase as BaseTestCase;
use Web3\Web3;
use Web3\RequestManagers\HttpAsyncRequestManager;
use Web3\Providers\HttpProvider;

class TestCase extends BaseTestCase
{
    /**
     * web3
     * 
     * @var \Web3\Web3
     */
    protected $web3;

    /**
     * testHost2
     * 
     * @var string
     */
    protected $testHost2 = 'https://eth-mainnet.g.alchemy.com/v2/notavalidkey';

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
     * asyncHttpProvider
     * 
     * @var \Web3\Providers\HttpProvider
     */
    protected $asyncHttpProvider;

    /**
     * setUp
     */
    public function setUp(): void
    {
        $web3 = new Web3($this->testHost);
        $this->web3 = $web3;

        $asyncRequestManager = new HttpAsyncRequestManager($this->testHost);
        $asyncHttpProvider = new HttpProvider($asyncRequestManager);
        $this->asyncHttpProvider = $asyncHttpProvider;

        $web3->eth->coinbase(function ($err, $coinbase) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->coinbase = $coinbase;
        });
    }

    /**
     * tearDown
     */
    public function tearDown(): void {}
}
