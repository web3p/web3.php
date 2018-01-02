<?php

namespace Test\Unit;

use Test\TestCase;
use Web3\Contract;

class ContractTest extends TestCase
{
    /**
     * contract
     *
     * @var \Web3\Contract
     */
    protected $contract;

    /**
     * testAbi
     * GameToken abi from https://github.com/sc0Vu/GameToken
     *
     * @var \Web3\Contract
     */
    protected $testAbi = '[
        {
          "constant": true,
          "inputs": [],
          "name": "name",
          "outputs": [
            {
              "name": "",
              "type": "string"
            }
          ],
          "payable": false,
          "stateMutability": "view",
          "type": "function"
        },
        {
          "constant": false,
          "inputs": [
            {
              "name": "_spender",
              "type": "address"
            },
            {
              "name": "_value",
              "type": "uint256"
            }
          ],
          "name": "approve",
          "outputs": [
            {
              "name": "success",
              "type": "bool"
            }
          ],
          "payable": false,
          "stateMutability": "nonpayable",
          "type": "function"
        },
        {
          "constant": true,
          "inputs": [],
          "name": "totalSupply",
          "outputs": [
            {
              "name": "",
              "type": "uint256"
            }
          ],
          "payable": false,
          "stateMutability": "view",
          "type": "function"
        },
        {
          "constant": false,
          "inputs": [
            {
              "name": "_from",
              "type": "address"
            },
            {
              "name": "_to",
              "type": "address"
            },
            {
              "name": "_value",
              "type": "uint256"
            }
          ],
          "name": "transferFrom",
          "outputs": [
            {
              "name": "success",
              "type": "bool"
            }
          ],
          "payable": false,
          "stateMutability": "nonpayable",
          "type": "function"
        },
        {
          "constant": true,
          "inputs": [],
          "name": "decimals",
          "outputs": [
            {
              "name": "",
              "type": "uint8"
            }
          ],
          "payable": false,
          "stateMutability": "view",
          "type": "function"
        },
        {
          "constant": true,
          "inputs": [],
          "name": "standard",
          "outputs": [
            {
              "name": "",
              "type": "string"
            }
          ],
          "payable": false,
          "stateMutability": "view",
          "type": "function"
        },
        {
          "constant": true,
          "inputs": [
            {
              "name": "",
              "type": "address"
            }
          ],
          "name": "balanceOf",
          "outputs": [
            {
              "name": "",
              "type": "uint256"
            }
          ],
          "payable": false,
          "stateMutability": "view",
          "type": "function"
        },
        {
          "constant": true,
          "inputs": [],
          "name": "symbol",
          "outputs": [
            {
              "name": "",
              "type": "string"
            }
          ],
          "payable": false,
          "stateMutability": "view",
          "type": "function"
        },
        {
          "constant": false,
          "inputs": [
            {
              "name": "_to",
              "type": "address"
            },
            {
              "name": "_value",
              "type": "uint256"
            }
          ],
          "name": "transfer",
          "outputs": [],
          "payable": false,
          "stateMutability": "nonpayable",
          "type": "function"
        },
        {
          "constant": true,
          "inputs": [
            {
              "name": "",
              "type": "address"
            },
            {
              "name": "",
              "type": "address"
            }
          ],
          "name": "allowance",
          "outputs": [
            {
              "name": "",
              "type": "uint256"
            }
          ],
          "payable": false,
          "stateMutability": "view",
          "type": "function"
        },
        {
          "inputs": [
            {
              "name": "initialSupply",
              "type": "uint256"
            },
            {
              "name": "tokenName",
              "type": "string"
            },
            {
              "name": "decimalUnits",
              "type": "uint8"
            },
            {
              "name": "tokenSymbol",
              "type": "string"
            }
          ],
          "payable": false,
          "stateMutability": "nonpayable",
          "type": "constructor"
        },
        {
          "anonymous": false,
          "inputs": [
            {
              "indexed": true,
              "name": "from",
              "type": "address"
            },
            {
              "indexed": true,
              "name": "to",
              "type": "address"
            },
            {
              "indexed": false,
              "name": "value",
              "type": "uint256"
            }
          ],
          "name": "Transfer",
          "type": "event"
        },
        {
          "anonymous": false,
          "inputs": [
            {
              "indexed": true,
              "name": "_owner",
              "type": "address"
            },
            {
              "indexed": true,
              "name": "_spender",
              "type": "address"
            },
            {
              "indexed": false,
              "name": "_value",
              "type": "uint256"
            }
          ],
          "name": "Approval",
          "type": "event"
        }
    ]';

    /**
     * accounts
     * 
     * @var array
     */
    protected $accounts;

    /**
     * contractAddress
     * 
     * @var string
     */
    protected $contractAddress;

    /**
     * setUp
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->contract = new Contract('http://localhost:8545', $this->testAbi);
        // $this->contract = new Contract($this->web3->provider, $this->testAbi);
        $this->contract->eth->accounts(function ($err, $accounts) {
            if ($err === null) {
                if (isset($accounts->result)) {
                    $this->accounts = $accounts->result;
                    return;
                }
            }
        });
    }

    /**
     * testDeploy
     * 
     * @return void
     */
    public function testDeploy()
    {
        $contract = $this->contract;

        if (!isset($this->accounts[0])) {
            $account = '0x407d73d8a49eeb85d32cf465507dd71d507100c1';
        } else {
            $account = $this->accounts[0];
        }
        $contract->new(10000, 'Game Token', 1, 'GT', [
            'from' => $account,
            'gas' => '0x200b20'
        ], function ($err, $result) use ($contract) {
            if ($err !== null) {
                // infura api gg
                return $this->assertTrue($err !== null);
            }
            if ($result->result) {
                echo "\nTransaction has made:) id: " . $result->result . "\n";
            }
            $transactionId = $result->result;
            $this->assertTrue((preg_match('/^0x[a-f0-9]{64}$/', $transactionId) === 1));

            $contract->eth->getTransactionReceipt($transactionId, function ($err, $transaction) {
                if ($err !== null) {
                    return $this->fail($err);
                }
                if ($transaction->result) {
                    $this->contractAddress = $transaction->result->contractAddress;
                    echo "\nTransaction has mind:) block number: " . $transaction->result->blockNumber . "\n";
                }
            });
        });
    }

    /**
     * testSend
     * 
     * @return void
     */
    public function testSend()
    {
        $contract = $this->contract;

        if (!isset($this->accounts[0])) {
            $fromAccount = '0x407d73d8a49eeb85d32cf465507dd71d507100c1';
        } else {
            $fromAccount = $this->accounts[0];
        }
        if (!isset($this->accounts[1])) {
            $toAccount = '0x407d73d8a49eeb85d32cf465507dd71d507100c2';
        } else {
            $toAccount = $this->accounts[1];
        }
        $contract->new(10000, 'Game Token', 1, 'GT', [
            'from' => $fromAccount,
            'gas' => '0x200b20'
        ], function ($err, $result) use ($contract) {
            if ($err !== null) {
                // infura api gg
                return $this->assertTrue($err !== null);
            }
            if ($result->result) {
                echo "\nTransaction has made:) id: " . $result->result . "\n";
            }
            $transactionId = $result->result;
            $this->assertTrue((preg_match('/^0x[a-f0-9]{64}$/', $transactionId) === 1));

            $contract->eth->getTransactionReceipt($transactionId, function ($err, $transaction) {
                if ($err !== null) {
                    return $this->fail($err);
                }
                if ($transaction->result) {
                    $this->contractAddress = $transaction->result->contractAddress;
                    echo "\nTransaction has mind:) block number: " . $transaction->result->blockNumber . "\n";
                }
            });
        });
        $contract->at($this->contractAddress)->send('transfer', $toAccount, 100, [
            'from' => $fromAccount
        ], function ($err, $result) use ($contract) {
            if ($err !== null) {
                // infura api gg
                return $this->assertTrue($err !== null);
            }
            if ($result->result) {
                echo "\nTransaction has made:) id: " . $result->result . "\n";
            }
            $transactionId = $result->result;
            $this->assertTrue((preg_match('/^0x[a-f0-9]{64}$/', $transactionId) === 1));

            $contract->eth->getTransactionReceipt($transactionId, function ($err, $transaction) {
                if ($err !== null) {
                    return $this->fail($err);
                }
                if ($transaction->result) {
                    echo "\nTransaction has mind:) block number: " . $transaction->result->blockNumber . "\n";
                }
            });
        });
    }
}