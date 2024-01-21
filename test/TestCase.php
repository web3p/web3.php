<?php

namespace Test;

use \PHPUnit\Framework\TestCase as BaseTestCase;
use Web3\Web3;
use Web3\Providers\HttpAsyncProvider;
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
     * testWsHost
     * 
     * @var string
     */
    protected $testWsHost = 'ws://localhost:8545';

    /**
     * coinbase
     * 
     * @var string
     */
    protected $coinbase;

    /**
     * asyncHttpProvider
     * 
     * @var \Web3\Providers\HttpAsyncProvider
     */
    protected $asyncHttpProvider;

    /**
     * EMPTY_ADDRESS
     * 
     * @var string
     */
    protected $EMPTY_ADDRESS = '0x0000000000000000000000000000000000000000';

    /**
     * test fixtures
     * 
     * TODO: add more fixtures
     * @var array
     */
    protected $testFixtures = [];

    /**
     * setUp
     */
    public function setUp(): void
    {
        $web3 = new Web3($this->testHost);
        $this->web3 = $web3;

        $asyncHttpProvider = new HttpAsyncProvider($this->testHost);
        $this->asyncHttpProvider = $asyncHttpProvider;

        $web3->eth->coinbase(function ($err, $coinbase) use ($web3) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            // if ($coinbase === $this->EMPTY_ADDRESS) {
            //     $web3->eth->accounts(function ($err, $accounts) {
            //         if ($err !== null) {
            //             return $this->fail($err->getMessage());
            //         }
            //         $this->coinbase = $accounts[rand(0, count($accounts) - 1)];
            //     });
            // } else {
            $this->coinbase = $coinbase;
            // }
        });

        // load test fixtures
        $fixtureFileName = __DIR__ . '/fixtures/typed-data.json';
        $json = \file_get_contents($fixtureFileName);
        if (false === $json) {
            throw new \RuntimeException("Unable to load file {$fixtureFileName}");
        }

        $data = \json_decode($json, true);
        $this->testFixtures = [
            'typed-data' => $data
        ];
    }

    /**
     * tearDown
     */
    public function tearDown(): void {}
}
