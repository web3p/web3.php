<?php

namespace Test\Unit;

use RuntimeException;
use InvalidArgumentException;
use Test\TestCase;
use phpseclib\Math\BigInteger as BigNumber;

class EthApiTest extends TestCase
{
    /**
     * eth
     * 
     * @var \Web3\Eth
     */
    protected $eth;

    /**
     * setUp
     * 
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->eth = $this->web3->eth;
    }

    /**
     * testProtocolVersion
     * 
     * @return void
     */    
    public function testProtocolVersion()
    {
        $eth = $this->eth;

        $eth->protocolVersion(function ($err, $version) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue($version instanceof BigNumber);
        });
    }

    /**
     * testSyncing
     * 
     * @return void
     */    
    public function testSyncing()
    {
        $eth = $this->eth;

        $eth->syncing(function ($err, $syncing) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            // due to the result might be object or bool, only test is null
            $this->assertTrue($syncing !== null);
        });
    }

    /**
     * testCoinbase
     * 
     * @return void
     */    
    public function testCoinbase()
    {
        $eth = $this->eth;

        $eth->coinbase(function ($err, $coinbase) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertEquals($coinbase, $this->coinbase);
        });
    }

    /**
     * testMining
     * 
     * @return void
     */    
    public function testMining()
    {
        $eth = $this->eth;

        $eth->mining(function ($err, $mining) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue($mining);
        });
    }

    /**
     * testHashrate
     * 
     * @return void
     */    
    public function testHashrate()
    {
        $eth = $this->eth;

        $eth->hashrate(function ($err, $hashrate) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertEquals($hashrate->toString(), '0');
        });
    }

    /**
     * testGasPrice
     * 
     * @return void
     */    
    public function testGasPrice()
    {
        $eth = $this->eth;

        $eth->gasPrice(function ($err, $gasPrice) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(is_numeric($gasPrice->toString()));
        });
    }

    /**
     * testAccounts
     * 
     * @return void
     */    
    public function testAccounts()
    {
        $eth = $this->eth;

        $eth->accounts(function ($err, $accounts) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(is_array($accounts));
        });
    }

    /**
     * testBlockNumber
     * 
     * @return void
     */    
    public function testBlockNumber()
    {
        $eth = $this->eth;

        $eth->blockNumber(function ($err, $blockNumber) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(is_numeric($blockNumber->toString()));
        });
    }

    /**
     * testGetBalance
     * 
     * @return void
     */    
    public function testGetBalance()
    {
        $eth = $this->eth;

        $eth->getBalance('0x407d73d8a49eeb85d32cf465507dd71d507100c1', function ($err, $balance) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(is_numeric($balance->toString()));
        });
    }

    /**
     * testGetStorageAt
     * 
     * @return void
     */    
    public function testGetStorageAt()
    {
        $eth = $this->eth;

        $eth->getStorageAt('0x561a2aa10f9a8589c93665554c871106342f70af', '0x0', function ($err, $storage) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(is_string($storage));
        });
    }

    /**
     * testGetTransactionCount
     * 
     * @return void
     */    
    public function testGetTransactionCount()
    {
        $eth = $this->eth;

        $eth->getTransactionCount('0x561a2aa10f9a8589c93665554c871106342f70af', function ($err, $transactionCount) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(is_numeric($transactionCount->toString()));
        });
    }

    /**
     * testGetBlockTransactionCountByHash
     * 
     * @return void
     */    
    public function testGetBlockTransactionCountByHash()
    {
        $eth = $this->eth;

        $eth->getBlockTransactionCountByHash('0xb903239f8543d04b5dc1ba6579132b143087c68db1b2168786408fcbce568238', function ($err, $transactionCount) {
            if ($err !== null) {
                return $this->assertEquals('Key not found in database', $err->getMessage());
            }
            $this->assertTrue(is_numeric($transactionCount->toString()));
        });
    }

    /**
     * testGetBlockTransactionCountByNumber
     * 
     * @return void
     */    
    public function testGetBlockTransactionCountByNumber()
    {
        $eth = $this->eth;

        $eth->getBlockTransactionCountByNumber('0x0', function ($err, $transactionCount) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(is_numeric($transactionCount->toString()));
        });
    }

    /**
     * testGetUncleCountByBlockHash
     * 
     * @return void
     */    
    public function testGetUncleCountByBlockHash()
    {
        $eth = $this->eth;

        $eth->getUncleCountByBlockHash('0xb903239f8543d04b5dc1ba6579132b143087c68db1b2168786408fcbce568238', function ($err, $uncleCount) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(is_numeric($uncleCount->toString()));
        });
    }

    /**
     * testGetUncleCountByBlockNumber
     * 
     * @return void
     */    
    public function testGetUncleCountByBlockNumber()
    {
        $eth = $this->eth;

        $eth->getUncleCountByBlockNumber('0x0', function ($err, $uncleCount) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(is_numeric($uncleCount->toString()));
        });
    }

    /**
     * testGetCode
     * 
     * @return void
     */    
    public function testGetCode()
    {
        $eth = $this->eth;

        $eth->getCode('0x407d73d8a49eeb85d32cf465507dd71d507100c1', function ($err, $code) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(is_string($code));
        });
    }

    /**
     * testSign
     * 
     * @return void
     */    
    public function testSign()
    {
        $eth = $this->eth;

        $eth->sign('0x407d73d8a49eeb85d32cf465507dd71d507100c1', '0xdeadbeaf', function ($err, $sign) {
            if ($err !== null) {
                return $this->assertEquals('cannot sign data; no private key', $err->getMessage());
            }
            $this->assertTrue(is_string($sign));
        });
    }

    /**
     * testSendTransaction
     * 
     * @return void
     */    
    public function testSendTransaction()
    {
        $eth = $this->eth;

        $eth->sendTransaction([
            'from' => "0xb60e8dd61c5d32be8058bb8eb970870f07233155",
            'to' => "0xd46e8dd67c5d32be8058bb8eb970870f07244567",
            'gas' => "0x76c0",
            'gasPrice' => "0x9184e72a000",
            'value' => "0x9184e72a",
            'data' => "0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675"
        ], function ($err, $transaction) {
            if ($err !== null) {
                return $this->assertEquals('sender account not recognized', $err->getMessage());
            }
            $this->assertTrue(is_string($transaction));
        });
    }

    /**
     * testSendRawTransaction
     * 
     * @return void
     */    
    public function testSendRawTransaction()
    {
        $eth = $this->eth;

        $eth->sendRawTransaction('0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675', function ($err, $transaction) {
            if ($err !== null) {
                return $this->assertStringContainsString('Could not ', $err->getMessage());
            }
            $this->assertTrue(is_string($transaction));
        });
    }

    /**
     * testCall
     * 
     * @return void
     */    
    public function testCall()
    {
        $eth = $this->eth;

        $eth->call([
            // 'from' => "0xb60e8dd61c5d32be8058bb8eb970870f07233155",
            'to' => "0xd46e8dd67c5d32be8058bb8eb970870f07244567",
            'gas' => "0x76c0",
            'gasPrice' => "0x9184e72a000",
            'value' => "0x9184e72a",
            'data' => "0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675"
        ], function ($err, $transaction) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(is_string($transaction));
        });
    }

    /**
     * testEstimateGas
     * 
     * @return void
     */    
    public function testEstimateGas()
    {
        $eth = $this->eth;

        $eth->estimateGas([
            'from' => "0xb60e8dd61c5d32be8058bb8eb970870f07233155",
            'to' => "0xd46e8dd67c5d32be8058bb8eb970870f07244567",
            'gas' => "0x76c0",
            'gasPrice' => "0x9184e72a000",
            'value' => "0x9184e72a",
            'data' => "0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675"
        ], function ($err, $gas) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(is_numeric($gas->toString()));
        });
    }

    /**
     * testGetBlockByHash
     * 
     * @return void
     */    
    public function testGetBlockByHash()
    {
        $eth = $this->eth;

        $eth->getBlockByHash('0xb903239f8543d04b5dc1ba6579132b143087c68db1b2168786408fcbce568238', false, function ($err, $block) {
            if ($err !== null) {
                return $this->assertEquals('Key not found in database', $err->getMessage());
            }
            $this->assertTrue($block === null);
        });
    }

    /**
     * testGetBlockByNumber
     * 
     * @return void
     */    
    public function testGetBlockByNumber()
    {
        $eth = $this->eth;

        $eth->getBlockByNumber('latest', false, function ($err, $block) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            // weird behavior, see https://github.com/web3p/web3.php/issues/16
            $this->assertTrue($block !== null);
        });
    }

    /**
     * testGetTransactionByHash
     * 
     * @return void
     */    
    public function testGetTransactionByHash()
    {
        $eth = $this->eth;

        $eth->getTransactionByHash('0xb903239f8543d04b5dc1ba6579132b143087c68db1b2168786408fcbce568238', function ($err, $transaction) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue($transaction == null);
        });
    }

    /**
     * testGetTransactionByBlockHashAndIndex
     * 
     * @return void
     */    
    public function testGetTransactionByBlockHashAndIndex()
    {
        $eth = $this->eth;

        $eth->getTransactionByBlockHashAndIndex('0xb903239f8543d04b5dc1ba6579132b143087c68db1b2168786408fcbce568238', '0x0', function ($err, $transaction) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue($transaction == null);
        });
    }

    /**
     * testGetTransactionByBlockNumberAndIndex
     * 
     * @return void
     */    
    public function testGetTransactionByBlockNumberAndIndex()
    {
        $eth = $this->eth;

        $eth->getTransactionByBlockNumberAndIndex('0xe8', '0x0', function ($err, $transaction) {
            if ($err !== null) {
                return $this->assertStringStartsWith('LevelUpArrayAdapter named \'blocks\' index out of range', $err->getMessage());
            }
            $this->assertTrue($transaction === null);
        });
    }

    /**
     * testGetTransactionReceipt
     * 
     * @return void
     */    
    public function testGetTransactionReceipt()
    {
        $eth = $this->eth;

        $eth->getTransactionReceipt('0xb903239f8543d04b5dc1ba6579132b143087c68db1b2168786408fcbce568238', function ($err, $transaction) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue($transaction == null);
        });
    }

    /**
     * testGetUncleByBlockHashAndIndex
     * 
     * @return void
     */    
    public function testGetUncleByBlockHashAndIndex()
    {
        $eth = $this->eth;

        $eth->getUncleByBlockHashAndIndex('0xb903239f8543d04b5dc1ba6579132b143087c68db1b2168786408fcbce568238', '0x0', function ($err, $uncle) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue($uncle === null);
        });
    }

    /**
     * testGetUncleByBlockNumberAndIndex
     * 
     * @return void
     */    
    public function testGetUncleByBlockNumberAndIndex()
    {
        $eth = $this->eth;

        $eth->getUncleByBlockNumberAndIndex('0xe8', '0x0', function ($err, $uncle) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue($uncle === null);
        });
    }

    /**
     * testNewFilter
     * 
     * @return void
     */    
    public function testNewFilter()
    {
        $eth = $this->eth;

        $eth->newFilter([
            'fromBlock' => '0x1',
            'toBlock' => '0x2',
            'address' => '0x8888f1f195afa192cfee860698584c030f4c9db1',
            'topics' => ['0x000000000000000000000000a94f5374fce5edbc8e2a8697c15331677e6ebf0b', null, ['0x000000000000000000000000a94f5374fce5edbc8e2a8697c15331677e6ebf0b', '0x0000000000000000000000000aff3454fce5edbc8cca8697c15331677e6ebccc']]
        ], function ($err, $filter) {
            if ($err !== null) {
                // infura banned us to new filter
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(is_string($filter));
        });
    }

    /**
     * testNewBlockFilter
     * 
     * @return void
     */    
    public function testNewBlockFilter()
    {
        $eth = $this->eth;

        $eth->newBlockFilter(function ($err, $filter) {
            if ($err !== null) {
                // infura banned us to new block filter
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(is_string($filter));
        });
    }

    /**
     * testNewPendingTransactionFilter
     * 
     * @return void
     */    
    public function testNewPendingTransactionFilter()
    {
        $eth = $this->eth;

        $eth->newPendingTransactionFilter(function ($err, $filter) {
            if ($err !== null) {
                // infura banned us to new pending transaction filter
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(is_string($filter));
        });
    }

    /**
     * testUninstallFilter
     * 
     * @return void
     */    
    public function testUninstallFilter()
    {
        $eth = $this->eth;

        $eth->uninstallFilter('0x01', function ($err, $filter) {
            if ($err !== null) {
                // infura banned us to uninstall filter
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(is_bool($filter));
        });
    }

    /**
     * testGetFilterChanges
     * 
     * @return void
     */    
    public function testGetFilterChanges()
    {
        $eth = $this->eth;

        $eth->getFilterChanges('0x01', function ($err, $changes) {
            if ($err !== null) {
                return $this->assertEquals('filter not found', $err->getMessage());
            }
            $this->assertTrue(is_array($changes));
        });
    }

    /**
     * testGetFilterLogs
     * 
     * @return void
     */    
    public function testGetFilterLogs()
    {
        $eth = $this->eth;

        $eth->getFilterLogs('0x01', function ($err, $logs) {
            if ($err !== null) {
                return $this->assertEquals('filter not found', $err->getMessage());
            }
            $this->assertTrue(is_array($logs));
        });
    }

    /**
     * testGetLogs
     * 
     * @return void
     */    
    public function testGetLogs()
    {
        $eth = $this->eth;

        $eth->getLogs([
            'fromBlock' => '0x1',
            'toBlock' => '0x2',
            'address' => '0x8888f1f195afa192cfee860698584c030f4c9db1',
            'topics' => ['0x000000000000000000000000a94f5374fce5edbc8e2a8697c15331677e6ebf0b', null, ['0x000000000000000000000000a94f5374fce5edbc8e2a8697c15331677e6ebf0b', '0x0000000000000000000000000aff3454fce5edbc8cca8697c15331677e6ebccc']]
        ], function ($err, $logs) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(is_array($logs));
        });
    }

    /**
     * testSubmitWork
     * 
     * @return void
     */    
    public function testSubmitWork()
    {
        $eth = $this->eth;

        $eth->submitWork(
            '0x0000000000000001',
            '0x1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdef',
            '0xD1FE5700000000000000000000000000D1FE5700000000000000000000000000'
        , function ($err, $work) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(is_bool($work));
        });
    }

    /**
     * testSubmitHashrate
     * 
     * @return void
     */    
    public function testSubmitHashrate()
    {
        $eth = $this->eth;

        $eth->submitHashrate(
            '0x1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdef',
            '0xD1FE5700000000000000000000000000D1FE5700000000000000000000000000'
        , function ($err, $work) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(is_bool($work));
        });
    }

    /**
     * testFeeHistory
     * 
     * @return void
     */    
    public function testFeeHistory()
    {
        $eth = $this->eth;

        $eth->feeHistory(1, 'latest', [ 1, 40, 50 ], function ($err, $feeHistory) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue($feeHistory->oldestBlock !== null);
            $this->assertTrue($feeHistory->baseFeePerGas !== null);
            $this->assertTrue($feeHistory->gasUsedRatio !== null);
            $this->assertEquals(count($feeHistory->gasUsedRatio), 1);
            $this->assertTrue($feeHistory->reward !== null);
            $this->assertEquals(count($feeHistory->reward), 1);
            $this->assertEquals(count($feeHistory->reward[0]), 3);
        });
    }

    /**
     * testGetBlockByNumberAsync
     * 
     * @return void
     */    
    public function testGetBlockByNumberAsync()
    {
        $eth = $this->eth;
        $eth->provider = $this->asyncHttpProvider;

        // should return reactphp promise
        $promise = $eth->getBlockByNumber('latest', false, function ($err, $block) {
            if ($err !== null) {
                return $this->assertTrue($err !== null);
            }
            // weird behavior, see https://github.com/web3p/web3.php/issues/16
            $this->assertTrue($block !== null);
        });
        $this->assertTrue($promise instanceof \React\Promise\PromiseInterface);
        \React\Async\await($promise);
    }

    /**
     * testUnallowedMethod
     * 
     * @return void
     */
    public function testUnallowedMethod()
    {
        $this->expectException(RuntimeException::class);

        $eth = $this->eth;

        $eth->hello(function ($err, $hello) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(true);
        });
    }

    /**
     * testWrongCallback
     * 
     * @return void
     */
    public function testWrongCallback()
    {
        $this->expectException(InvalidArgumentException::class);

        $eth = $this->eth;

        $eth->protocolVersion();
    }
}