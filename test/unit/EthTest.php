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
    public function setUp()
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
        $eth = $this->eth;

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
     * testCoinbase
     * 
     * @return void
     */    
    public function testCoinbase()
    {
        $eth = $this->eth;

        $eth->coinbase(function ($err, $coinbase) {
            if ($err !== null) {
                // infura banned us to use coinbase
                return $this->assertTrue($err->getCode() === 405);
            }
            if (isset($coinbase->result)) {
                $this->assertTrue(is_string($coinbasse->result));
            } else {
                $this->fail($coinbase->error->message);
            }
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
            if (isset($mining->result)) {
                $this->assertTrue($mining->result !== null);
            } else {
                $this->fail($mining->error->message);
            }
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
            if (isset($hashrate->result)) {
                $this->assertTrue(is_string($hashrate->result));
            } else {
                $this->fail($hashrate->error->message);
            }
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
            if (isset($gasPrice->result)) {
                $this->assertTrue(is_string($gasPrice->result));
            } else {
                $this->fail($gasPrice->error->message);
            }
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
            if (isset($accounts->result)) {
                $this->assertTrue(is_array($accounts->result));
            } else {
                $this->fail($accounts->error->message);
            }
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
            if (isset($blockNumber->result)) {
                $this->assertTrue(is_string($blockNumber->result));
            } else {
                $this->fail($blockNumber->error->message);
            }
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
            if (isset($balance->result)) {
                $this->assertTrue(is_string($balance->result));
            } else {
                $this->fail($balance->error->message);
            }
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

        $eth->getStorageAt('0x407d73d8a49eeb85d32cf465507dd71d507100c1', '0x0', function ($err, $storage) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if (isset($storage->result)) {
                $this->assertTrue(is_string($storage->result));
            } else {
                $this->fail($storage->error->message);
            }
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

        $eth->getTransactionCount('0x407d73d8a49eeb85d32cf465507dd71d507100c1', function ($err, $transactionCount) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if (isset($transactionCount->result)) {
                $this->assertTrue(is_string($transactionCount->result));
            } else {
                $this->fail($transactionCount->error->message);
            }
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
                return $this->fail($err->getMessage());
            }
            if (isset($transactionCount->result)) {
                $this->assertTrue(is_string($transactionCount->result));
            } else {
                if (isset($transactionCount->error)) {
                    $this->fail($transactionCount->error->message);
                } else {
                    $this->assertTrue(true);
                }
            }
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
            if (isset($transactionCount->result)) {
                $this->assertTrue(is_string($transactionCount->result));
            } else {
                if (isset($transactionCount->error)) {
                    $this->fail($transactionCount->error->message);
                } else {
                    $this->assertTrue(true);
                }
            }
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
            if (isset($uncleCount->result)) {
                $this->assertTrue(is_string($uncleCount->result));
            } else {
                if (isset($uncleCount->error)) {
                    $this->fail($uncleCount->error->message);
                } else {
                    $this->assertTrue(true);
                }
            }
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
            if (isset($uncleCount->result)) {
                $this->assertTrue(is_string($uncleCount->result));
            } else {
                if (isset($uncleCount->error)) {
                    $this->fail($uncleCount->error->message);
                } else {
                    $this->assertTrue(true);
                }
            }
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
            if (isset($code->result)) {
                $this->assertTrue(is_string($code->result));
            } else {
                $this->fail($code->error->message);
            }
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
                // infura banned us to sign message
                return $this->assertTrue($err->getCode() === 405);
            }
            if (isset($sign->result)) {
                $this->assertTrue(is_string($sign->result));
            } else {
                $this->fail($sign->error->message);
            }
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
                // infura banned us to send transaction
                return $this->assertTrue($err->getCode() === 405);
            }
            if (isset($transaction->result)) {
                $this->assertTrue(is_string($transaction->result));
            } else {
                if (isset($transaction->error)) {
                    // it's just test hex.
                    $this->assertTrue(is_string($transaction->error->message));
                } else {
                    $this->assertTrue(true);
                }
            }
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
                return $this->fail($err->getMessage());
            }
            if (isset($transaction->result)) {
                $this->assertTrue(is_string($transaction->result));
            } else {
                if (isset($transaction->error)) {
                    // it's just test hex.
                    $this->assertTrue(is_string($transaction->error->message));
                } else {
                    $this->assertTrue(true);
                }
            }
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
            'from' => "0xb60e8dd61c5d32be8058bb8eb970870f07233155",
            'to' => "0xd46e8dd67c5d32be8058bb8eb970870f07244567",
            'gas' => "0x76c0",
            'gasPrice' => "0x9184e72a000",
            'value' => "0x9184e72a",
            'data' => "0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675"
        ], function ($err, $transaction) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if (isset($transaction->result)) {
                $this->assertTrue(is_string($transaction->result));
            } else {
                if (isset($transaction->error)) {
                    // it's just test hex.
                    $this->assertTrue(is_string($transaction->error->message));
                } else {
                    $this->assertTrue(true);
                }
            }
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
            if (isset($gas->result)) {
                $this->assertTrue(is_string($gas->result));
            } else {
                if (isset($gas->error)) {
                    // it's just test hex.
                    $this->assertTrue(is_string($gas->error->message));
                } else {
                    $this->assertTrue(true);
                }
            }
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
                return $this->fail($err->getMessage());
            }
            if (isset($block->result)) {
                $this->assertTrue(is_string($block->result));
            } else {
                if (isset($block->error)) {
                    $this->fail($block->error->message);
                } else {
                    $this->assertTrue(true);
                }
            }
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
            if (isset($block->result)) {
                // weired behavior, see https://github.com/sc0Vu/web3.php/issues/16
                $this->assertTrue($block->result !== null);
            } else {
                if (isset($block->error)) {
                    $this->fail($block->error->message);
                } else {
                    $this->assertTrue(true);
                }
            }
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
            if (isset($transaction->result)) {
                $this->assertTrue(is_string($transaction->result));
            } else {
                if (isset($transaction->error)) {
                    $this->fail($transaction->error->message);
                } else {
                    $this->assertTrue(true);
                }
            }
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
            if (isset($transaction->result)) {
                $this->assertTrue(is_string($transaction->result));
            } else {
                if (isset($transaction->error)) {
                    $this->fail($transaction->error->message);
                } else {
                    $this->assertTrue(true);
                }
            }
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
                return $this->fail($err->getMessage());
            }
            if (isset($transaction->result)) {
                $this->assertTrue(is_string($transaction->result));
            } else {
                if (isset($transaction->error)) {
                    $this->fail($transaction->error->message);
                } else {
                    $this->assertTrue(true);
                }
            }
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
            if (isset($transaction->result)) {
                $this->assertTrue(is_string($transaction->result));
            } else {
                if (isset($transaction->error)) {
                    $this->fail($transaction->error->message);
                } else {
                    $this->assertTrue(true);
                }
            }
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
            if (isset($uncle->result)) {
                $this->assertTrue(is_string($uncle->result));
            } else {
                if (isset($uncle->error)) {
                    $this->fail($uncle->error->message);
                } else {
                    $this->assertTrue(true);
                }
            }
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
            if (isset($uncle->result)) {
                $this->assertTrue(is_string($uncle->result));
            } else {
                if (isset($uncle->error)) {
                    $this->fail($uncle->error->message);
                } else {
                    $this->assertTrue(true);
                }
            }
        });
    }

    /**
     * testGetCompilers
     * 
     * @return void
     */    
    public function testGetCompilers()
    {
        $eth = $this->eth;

        $eth->getCompilers(function ($err, $compilers) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if (isset($compilers->result)) {
                $this->assertTrue(is_string($compilers->result));
            } else {
                // if (isset($compilers->error)) {
                    // infura banned getCompilers, $compilers->error->message
                    $this->assertTrue(true);
                // } else {
                //     $this->assertTrue(true);
                // }
            }
        });
    }

    /**
     * testCompileSolidity
     * 
     * @return void
     */    
    public function testCompileSolidity()
    {
        $eth = $this->eth;

        $eth->compileSolidity('contract test { function multiply(uint a) returns(uint d) {   return a * 7;   } }', function ($err, $compiled) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if (isset($compiled->result)) {
                $this->assertTrue(is_string($compiled->result));
            } else {
                // if (isset($compilers->error)) {
                    $this->assertTrue(true);
                // } else {
                //     $this->assertTrue(true);
                // }
            }
        });
    }

    /**
     * testCompileLLL
     * 
     * @return void
     */    
    public function testCompileLLL()
    {
        $eth = $this->eth;

        $eth->compileLLL('(returnlll (suicide (caller)))', function ($err, $compiled) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if (isset($compiled->result)) {
                $this->assertTrue(is_string($compiled->result));
            } else {
                // if (isset($compilers->error)) {
                    $this->assertTrue(true);
                // } else {
                //     $this->assertTrue(true);
                // }
            }
        });
    }

    /**
     * testCompileSerpent
     * 
     * @return void
     */    
    public function testCompileSerpent()
    {
        $eth = $this->eth;

        $eth->compileSerpent('\/* some serpent *\/', function ($err, $compiled) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if (isset($compiled->result)) {
                $this->assertTrue(is_string($compiled->result));
            } else {
                // if (isset($compilers->error)) {
                    $this->assertTrue(true);
                // } else {
                //     $this->assertTrue(true);
                // }
            }
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
                return $this->assertTrue($err->getCode() === 405);
            }
            if (isset($filter->result)) {
                $this->assertTrue(is_string($filter->result));
            } else {
                if (isset($filter->error)) {
                    $this->assertTrue(is_string($filter->error->message));
                } else {
                    $this->assertTrue(true);
                }
            }
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

        $eth->newBlockFilter('0x01', function ($err, $filter) {
            if ($err !== null) {
                // infura banned us to new block filter
                return $this->assertTrue($err->getCode() === 405);
            }
            if (isset($filter->result)) {
                $this->assertTrue(is_string($filter->result));
            } else {
                if (isset($filter->error)) {
                    $this->assertTrue(is_string($filter->error->message));
                } else {
                    $this->assertTrue(true);
                }
            }
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
                return $this->assertTrue($err->getCode() === 405);
            }
            if (isset($filter->result)) {
                $this->assertTrue(is_string($filter->result));
            } else {
                if (isset($filter->error)) {
                    $this->assertTrue(is_string($filter->error->message));
                } else {
                    $this->assertTrue(true);
                }
            }
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
                return $this->assertTrue($err->getCode() === 405);
            }
            if (isset($filter->result)) {
                $this->assertTrue(is_string($filter->result));
            } else {
                if (isset($filter->error)) {
                    $this->assertTrue(is_string($filter->error->message));
                } else {
                    $this->assertTrue(true);
                }
            }
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

        $eth->getFilterChanges('0x01', function ($err, $filter) {
            if ($err !== null) {
                // infura banned us to get filter changes
                return $this->assertTrue($err->getCode() === 405);
            }
            if (isset($filter->result)) {
                $this->assertTrue(is_string($filter->result));
            } else {
                if (isset($filter->error)) {
                    $this->assertTrue(is_string($filter->error->message));
                } else {
                    $this->assertTrue(true);
                }
            }
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
                // infura banned us to get filter logs
                return $this->assertTrue($err->getCode() === 405);
            }
            if (isset($logs->result)) {
                $this->assertTrue(is_array($logs->result));
            } else {
                if (isset($logs->error)) {
                    $this->assertTrue(is_string($logs->error->message));
                } else {
                    $this->assertTrue(true);
                }
            }
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
            if (isset($logs->result)) {
                $this->assertTrue(is_array($logs->result));
            } else {
                if (isset($logs->error)) {
                    $this->assertTrue(is_string($logs->error->message));
                } else {
                    $this->assertTrue(true);
                }
            }
        });
    }

    /**
     * testGetWork
     * 
     * @return void
     */    
    public function testGetWork()
    {
        $eth = $this->eth;

        $eth->getWork(function ($err, $work) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if (isset($work->result)) {
                $this->assertTrue(is_array($work->result));
            } else {
                if (isset($work->error)) {
                    // we cannot get work if coinbase isnot set.
                    $this->assertTrue(is_string($work->error->message));
                } else {
                    $this->assertTrue(true);
                }
            }
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
            if (isset($work->result)) {
                $this->assertTrue(is_bool($work->result));
            } else {
                if (isset($work->error)) {
                    $this->assertTrue(is_string($work->error->message));
                } else {
                    $this->assertTrue(true);
                }
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

        $eth = $this->eth;

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