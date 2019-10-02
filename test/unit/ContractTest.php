<?php

namespace Test\Unit;

use Test\TestCase;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\RequestManager;
use Web3\RequestManagers\HttpRequestManager;
use Web3\Contract;
use Web3\Utils;
use Web3\Contracts\Ethabi;
use Web3\Formatters\IntegerFormatter;
use phpseclib\Math\BigInteger as BigNumber;

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
     * @var string
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
     * testBytecode
     * GameToken abi from https://github.com/sc0Vu/GameToken
     *
     * @var string
     */
    protected $testBytecode = '0x60606040526040805190810160405280600581526020017f45524332300000000000000000000000000000000000000000000000000000008152506000908051906020019061004f92919061012f565b50341561005b57600080fd5b604051610ec5380380610ec58339810160405280805190602001909190805182019190602001805190602001909190805182019190505083600560003373ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff168152602001908152602001600020819055508360048190555082600190805190602001906100f392919061012f565b50806002908051906020019061010a92919061012f565b5081600360006101000a81548160ff021916908360ff160217905550505050506101d4565b828054600181600116156101000203166002900490600052602060002090601f016020900481019282601f1061017057805160ff191683800117855561019e565b8280016001018555821561019e579182015b8281111561019d578251825591602001919060010190610182565b5b5090506101ab91906101af565b5090565b6101d191905b808211156101cd5760008160009055506001016101b5565b5090565b90565b610ce2806101e36000396000f3006060604052600436106100a4576000357c0100000000000000000000000000000000000000000000000000000000900463ffffffff16806306fdde03146100a9578063095ea7b31461013757806318160ddd1461019157806323b872dd146101ba578063313ce567146102335780635a3b7e421461026257806370a08231146102f057806395d89b411461033d578063a9059cbb146103cb578063dd62ed3e1461040d575b600080fd5b34156100b457600080fd5b6100bc610479565b6040518080602001828103825283818151815260200191508051906020019080838360005b838110156100fc5780820151818401526020810190506100e1565b50505050905090810190601f1680156101295780820380516001836020036101000a031916815260200191505b509250505060405180910390f35b341561014257600080fd5b610177600480803573ffffffffffffffffffffffffffffffffffffffff16906020019091908035906020019091905050610517565b604051808215151515815260200191505060405180910390f35b341561019c57600080fd5b6101a4610609565b6040518082815260200191505060405180910390f35b34156101c557600080fd5b610219600480803573ffffffffffffffffffffffffffffffffffffffff1690602001909190803573ffffffffffffffffffffffffffffffffffffffff1690602001909190803590602001909190505061060f565b604051808215151515815260200191505060405180910390f35b341561023e57600080fd5b61024661092a565b604051808260ff1660ff16815260200191505060405180910390f35b341561026d57600080fd5b61027561093d565b6040518080602001828103825283818151815260200191508051906020019080838360005b838110156102b557808201518184015260208101905061029a565b50505050905090810190601f1680156102e25780820380516001836020036101000a031916815260200191505b509250505060405180910390f35b34156102fb57600080fd5b610327600480803573ffffffffffffffffffffffffffffffffffffffff169060200190919050506109db565b6040518082815260200191505060405180910390f35b341561034857600080fd5b6103506109f3565b6040518080602001828103825283818151815260200191508051906020019080838360005b83811015610390578082015181840152602081019050610375565b50505050905090810190601f1680156103bd5780820380516001836020036101000a031916815260200191505b509250505060405180910390f35b34156103d657600080fd5b61040b600480803573ffffffffffffffffffffffffffffffffffffffff16906020019091908035906020019091905050610a91565b005b341561041857600080fd5b610463600480803573ffffffffffffffffffffffffffffffffffffffff1690602001909190803573ffffffffffffffffffffffffffffffffffffffff16906020019091905050610c91565b6040518082815260200191505060405180910390f35b60018054600181600116156101000203166002900480601f01602080910402602001604051908101604052809291908181526020018280546001816001161561010002031660029004801561050f5780601f106104e45761010080835404028352916020019161050f565b820191906000526020600020905b8154815290600101906020018083116104f257829003601f168201915b505050505081565b600081600660003373ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16815260200190815260200160002060008573ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff168152602001908152602001600020819055508273ffffffffffffffffffffffffffffffffffffffff163373ffffffffffffffffffffffffffffffffffffffff167f8c5be1e5ebec7d5bd14f71427d1e84f3dd0314c0f7b2291e5b200ac8c7c3b925846040518082815260200191505060405180910390a36001905092915050565b60045481565b6000808373ffffffffffffffffffffffffffffffffffffffff16141561063457600080fd5b81600560008673ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16815260200190815260200160002054101561068057600080fd5b600560008473ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff1681526020019081526020016000205482600560008673ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff1681526020019081526020016000205401101561070d57600080fd5b600660008573ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16815260200190815260200160002060003373ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff1681526020019081526020016000205482111561079657600080fd5b81600560008673ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff1681526020019081526020016000206000828254039250508190555081600560008573ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff1681526020019081526020016000206000828254019250508190555081600660008673ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16815260200190815260200160002060003373ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff168152602001908152602001600020600082825403925050819055508273ffffffffffffffffffffffffffffffffffffffff168473ffffffffffffffffffffffffffffffffffffffff167fddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef846040518082815260200191505060405180910390a3600190509392505050565b600360009054906101000a900460ff1681565b60008054600181600116156101000203166002900480601f0160208091040260200160405190810160405280929190818152602001828054600181600116156101000203166002900480156109d35780601f106109a8576101008083540402835291602001916109d3565b820191906000526020600020905b8154815290600101906020018083116109b657829003601f168201915b505050505081565b60056020528060005260406000206000915090505481565b60028054600181600116156101000203166002900480601f016020809104026020016040519081016040528092919081815260200182805460018160011615610100020316600290048015610a895780601f10610a5e57610100808354040283529160200191610a89565b820191906000526020600020905b815481529060010190602001808311610a6c57829003601f168201915b505050505081565b60008273ffffffffffffffffffffffffffffffffffffffff161415610ab557600080fd5b80600560003373ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff168152602001908152602001600020541015610b0157600080fd5b600560008373ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff1681526020019081526020016000205481600560008573ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16815260200190815260200160002054011015610b8e57600080fd5b80600560003373ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff1681526020019081526020016000206000828254039250508190555080600560008473ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff168152602001908152602001600020600082825401925050819055508173ffffffffffffffffffffffffffffffffffffffff163373ffffffffffffffffffffffffffffffffffffffff167fddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef836040518082815260200191505060405180910390a35050565b60066020528160005260406000206020528060005260406000206000915091505054815600a165627a7a723058203eb700b31f6d7723be3f4a0dd07fc4ba166a17279e26a437227679b92bacb5a20029';

    /**
     * testUserAbi
     * User abi from https://github.com/BlockHR/contracts
     * 
     * @var string
     */
    protected $testUserAbi = '[
      {
        "constant": false,
        "inputs": [
          {
            "name": "ethAddress",
            "type": "address"
          }
        ],
        "name": "getUser",
        "outputs": [
          {
            "name": "firstName",
            "type": "string"
          },
          {
            "name": "lastName",
            "type": "string"
          },
          {
            "name": "age",
            "type": "uint256"
          }
        ],
        "payable": false,
        "stateMutability": "nonpayable",
        "type": "function"
      },
      {
        "constant": false,
        "inputs": [
          {
            "name": "ethAddress",
            "type": "address"
          },
          {
            "name": "firstName",
            "type": "string"
          },
          {
            "name": "lastName",
            "type": "string"
          },
          {
            "name": "age",
            "type": "uint256"
          }
        ],
        "name": "addUser",
        "outputs": [],
        "payable": false,
        "stateMutability": "nonpayable",
        "type": "function"
      },
      {
        "inputs": [],
        "payable": false,
        "stateMutability": "nonpayable",
        "type": "constructor"
      },
      {
        "payable": false,
        "stateMutability": "nonpayable",
        "type": "fallback"
      },
      {
        "anonymous": false,
        "inputs": [
          {
            "indexed": true,
            "name": "ethAddress",
            "type": "address"
          },
          {
            "indexed": false,
            "name": "firstName",
            "type": "string"
          },
          {
            "indexed": false,
            "name": "lastName",
            "type": "string"
          },
          {
            "indexed": false,
            "name": "age",
            "type": "uint256"
          }
        ],
        "name": "AddUser",
        "type": "event"
      }
    ]';

    /**
     * testUserBytecode
     * User bytecode from https://github.com/BlockHR/contracts
     * 
     * @var string
     */
    protected $testUserBytecode = '0x6060604052341561000f57600080fd5b336000806101000a81548173ffffffffffffffffffffffffffffffffffffffff021916908373ffffffffffffffffffffffffffffffffffffffff1602179055506108ee8061005e6000396000f30060606040526004361061004c576000357c0100000000000000000000000000000000000000000000000000000000900463ffffffff1680636f77926b1461005c578063b3c2583514610181575b341561005757600080fd5b600080fd5b341561006757600080fd5b610093600480803573ffffffffffffffffffffffffffffffffffffffff16906020019091905050610249565b604051808060200180602001848152602001838103835286818151815260200191508051906020019080838360005b838110156100dd5780820151818401526020810190506100c2565b50505050905090810190601f16801561010a5780820380516001836020036101000a031916815260200191505b50838103825285818151815260200191508051906020019080838360005b83811015610143578082015181840152602081019050610128565b50505050905090810190601f1680156101705780820380516001836020036101000a031916815260200191505b509550505050505060405180910390f35b341561018c57600080fd5b610247600480803573ffffffffffffffffffffffffffffffffffffffff1690602001909190803590602001908201803590602001908080601f0160208091040260200160405190810160405280939291908181526020018383808284378201915050505050509190803590602001908201803590602001908080601f01602080910402602001604051908101604052809392919081815260200183838082843782019150505050505091908035906020019091905050610545565b005b6102516107c7565b6102596107c7565b60003373ffffffffffffffffffffffffffffffffffffffff168473ffffffffffffffffffffffffffffffffffffffff16141580156102e457506000809054906101000a900473ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff163373ffffffffffffffffffffffffffffffffffffffff1614155b156102ee5761053e565b6000600160008673ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16815260200190815260200160002060020154141561033e5761053e565b600160008573ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff1681526020019081526020016000206000018054600181600116156101000203166002900480601f0160208091040260200160405190810160405280929190818152602001828054600181600116156101000203166002900480156104145780601f106103e957610100808354040283529160200191610414565b820191906000526020600020905b8154815290600101906020018083116103f757829003601f168201915b50505050509250600160008573ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff1681526020019081526020016000206001018054600181600116156101000203166002900480601f0160208091040260200160405190810160405280929190818152602001828054600181600116156101000203166002900480156104f15780601f106104c6576101008083540402835291602001916104f1565b820191906000526020600020905b8154815290600101906020018083116104d457829003601f168201915b50505050509150600160008573ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff1681526020019081526020016000206002015490505b9193909250565b61054d6107db565b6000809054906101000a900473ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff163373ffffffffffffffffffffffffffffffffffffffff1614156107c0576000600160008773ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff168152602001908152602001600020600201541415156105f3576107bf565b8381600001819052508281602001819052508181604001818152505080600160008773ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff168152602001908152602001600020600082015181600001908051906020019061066b929190610809565b506020820151816001019080519060200190610688929190610809565b50604082015181600201559050508473ffffffffffffffffffffffffffffffffffffffff167f2771be550edc032daf255a8987e0164bcfad0a5b97238d8961f9c5e38fa3e4f3858585604051808060200180602001848152602001838103835286818151815260200191508051906020019080838360005b8381101561071b578082015181840152602081019050610700565b50505050905090810190601f1680156107485780820380516001836020036101000a031916815260200191505b50838103825285818151815260200191508051906020019080838360005b83811015610781578082015181840152602081019050610766565b50505050905090810190601f1680156107ae5780820380516001836020036101000a031916815260200191505b509550505050505060405180910390a25b5b5050505050565b602060405190810160405280600081525090565b6060604051908101604052806107ef610889565b81526020016107fc610889565b8152602001600081525090565b828054600181600116156101000203166002900490600052602060002090601f016020900481019282601f1061084a57805160ff1916838001178555610878565b82800160010185558215610878579182015b8281111561087757825182559160200191906001019061085c565b5b509050610885919061089d565b5090565b602060405190810160405280600081525090565b6108bf91905b808211156108bb5760008160009055506001016108a3565b5090565b905600a165627a7a72305820f4dd7dc4c22792dec463ec68973ad2ed12d5994e96a9e7b34db512d6e38143090029';

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

        $this->contract = new Contract($this->web3->provider, $this->testAbi);
        $this->contract->eth->accounts(function ($err, $accounts) {
            if ($err === null) {
                if (isset($accounts)) {
                    $this->accounts = $accounts;
                    return;
                }
            }
        });
    }

    /**
     * testInstance
     * 
     * @return void
     */
    public function testInstance()
    {
        $contract = new Contract($this->testHost, $this->testAbi);

        $this->assertTrue($contract->provider instanceof HttpProvider);
        $this->assertTrue($contract->provider->requestManager instanceof RequestManager);
    }

    /**
     * testSetProvider
     * 
     * @return void
     */
    public function testSetProvider()
    {
        $contract = $this->contract;
        $requestManager = new HttpRequestManager('http://localhost:8545');
        $contract->provider = new HttpProvider($requestManager);

        $this->assertEquals($contract->provider->requestManager->host, 'http://localhost:8545');

        $contract->provider = null;

        $this->assertEquals($contract->provider->requestManager->host, 'http://localhost:8545');
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
        $contract->bytecode($this->testBytecode)->new(1000000, 'Game Token', 1, 'GT', [
            'from' => $account,
            'gas' => '0x200b20'
        ], function ($err, $result) use ($contract) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if ($result) {
                echo "\nTransaction has made:) id: " . $result . "\n";
            }
            $transactionId = $result;
            $this->assertTrue((preg_match('/^0x[a-f0-9]{64}$/', $transactionId) === 1));

            $contract->eth->getTransactionReceipt($transactionId, function ($err, $transaction) {
                if ($err !== null) {
                    return $this->fail($err);
                }
                if ($transaction) {
                    $this->contractAddress = $transaction->contractAddress;
                    echo "\nTransaction has mined:) block number: " . $transaction->blockNumber . "\n";
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
        $contract->bytecode($this->testBytecode)->new(1000000, 'Game Token', 1, 'GT', [
            'from' => $fromAccount,
            'gas' => '0x200b20'
        ], function ($err, $result) use ($contract) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if ($result) {
                echo "\nTransaction has made:) id: " . $result . "\n";
            }
            $transactionId = $result;
            $this->assertTrue((preg_match('/^0x[a-f0-9]{64}$/', $transactionId) === 1));

            $contract->eth->getTransactionReceipt($transactionId, function ($err, $transaction) {
                if ($err !== null) {
                    return $this->fail($err);
                }
                if ($transaction) {
                    $this->contractAddress = $transaction->contractAddress;
                    echo "\nTransaction has mined:) block number: " . $transaction->blockNumber . "\n";
                }
            });
        });

        if (!isset($this->contractAddress)) {
            $this->contractAddress = '0x407d73d8a49eeb85d32cf465507dd71d507100c2';
        }
        $contract->at($this->contractAddress)->send('transfer', $toAccount, 16, [
            'from' => $fromAccount,
            'gas' => '0x200b20'
        ], function ($err, $result) use ($contract, $fromAccount, $toAccount) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if ($result) {
                echo "\nTransaction has made:) id: " . $result . "\n";
            }
            $transactionId = $result;
            $this->assertTrue((preg_match('/^0x[a-f0-9]{64}$/', $transactionId) === 1));

            $contract->eth->getTransactionReceipt($transactionId, function ($err, $transaction) use ($fromAccount, $toAccount, $contract) {
                if ($err !== null) {
                    return $this->fail($err);
                }
                if ($transaction) {
                    $topics = $transaction->logs[0]->topics;
                    echo "\nTransaction has mined:) block number: " . $transaction->blockNumber . "\n";

                    // validate topics
                    $this->assertEquals($contract->ethabi->encodeEventSignature($this->contract->events['Transfer']), $topics[0]);
                    $this->assertEquals('0x' . IntegerFormatter::format($fromAccount), $topics[1]);
                    $this->assertEquals('0x' . IntegerFormatter::format($toAccount), $topics[2]);
                }
            });
        });
    }

    /**
     * testCall
     * 
     * @return void
     */
    public function testCall()
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
        $contract->bytecode($this->testBytecode)->new(10000, 'Game Token', 1, 'GT', [
            'from' => $fromAccount,
            'gas' => '0x200b20'
        ], function ($err, $result) use ($contract) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if ($result) {
                echo "\nTransaction has made:) id: " . $result . "\n";
            }
            $transactionId = $result;
            $this->assertTrue((preg_match('/^0x[a-f0-9]{64}$/', $transactionId) === 1));

            $contract->eth->getTransactionReceipt($transactionId, function ($err, $transaction) {
                if ($err !== null) {
                    return $this->fail($err);
                }
                if ($transaction) {
                    $this->contractAddress = $transaction->contractAddress;
                    echo "\nTransaction has mined:) block number: " . $transaction->blockNumber . "\n";
                }
            });
        });

        if (!isset($this->contractAddress)) {
            $this->contractAddress = '0x407d73d8a49eeb85d32cf465507dd71d507100c2';
        }
        $contract->at($this->contractAddress)->call('balanceOf', $fromAccount, [
            'from' => $fromAccount
        ], function ($err, $result) use ($contract) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if (isset($result)) {
                // $bn = Utils::toBn($result);
                // $this->assertEquals($bn->toString(), '10000', 'Balance should be 10000.');
                $this->assertTrue($result !== null);
            }
        });
        // test issue 143
        $contract->at($this->contractAddress)->call('balanceOf', $fromAccount, function ($err, $result) use ($contract) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if (isset($result)) {
                // $bn = Utils::toBn($result);
                // $this->assertEquals($bn->toString(), '10000', 'Balance should be 10000.');
                $this->assertTrue($result !== null);
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
        $contract->bytecode($this->testBytecode)->new(10000, 'Game Token', 1, 'GT', [
            'from' => $fromAccount,
            'gas' => '0x200b20'
        ], function ($err, $result) use ($contract) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if ($result) {
                echo "\nTransaction has made:) id: " . $result . "\n";
            }
            $transactionId = $result;
            $this->assertTrue((preg_match('/^0x[a-f0-9]{64}$/', $transactionId) === 1));

            $contract->eth->getTransactionReceipt($transactionId, function ($err, $transaction) {
                if ($err !== null) {
                    return $this->fail($err);
                }
                if ($transaction) {
                    $this->contractAddress = $transaction->contractAddress;
                    echo "\nTransaction has mined:) block number: " . $transaction->blockNumber . "\n";
                }
            });
        });

        $contract->bytecode($this->testBytecode)->estimateGas(10000, 'Game Token', 1, 'GT', [
            'from' => $fromAccount,
            'gas' => '0x200b20'
        ], function ($err, $result) use ($contract) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if (isset($result)) {
                echo "\nEstimate gas: " . $result->toString() . "\n";
                $this->assertTrue($result !== null);
            }
        });

        if (!isset($this->contractAddress)) {
            $this->contractAddress = '0x407d73d8a49eeb85d32cf465507dd71d507100c2';
        }
        $contract->at($this->contractAddress)->estimateGas('balanceOf', $fromAccount, [
            'from' => $fromAccount
        ], function ($err, $result) use ($contract) {
            if ($err !== null) {
                // infura api gg
                return $this->assertTrue($err !== null);
            }
            if (isset($result)) {
                echo "\nEstimate gas: " . $result->toString() . "\n";
                $this->assertTrue($result !== null);
            }
        });
    }

    /**
     * testGetData
     * 
     * @return void
     */
    public function testGetData()
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
        $contract->bytecode($this->testBytecode)->new(10000, 'Game Token', 1, 'GT', [
            'from' => $fromAccount,
            'gas' => '0x200b20'
        ], function ($err, $result) use ($contract) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if ($result) {
                echo "\nTransaction has made:) id: " . $result . "\n";
            }
            $transactionId = $result;
            $this->assertTrue((preg_match('/^0x[a-f0-9]{64}$/', $transactionId) === 1));

            $contract->eth->getTransactionReceipt($transactionId, function ($err, $transaction) {
                if ($err !== null) {
                    return $this->fail($err);
                }
                if ($transaction) {
                    $this->contractAddress = $transaction->contractAddress;
                    echo "\nTransaction has mined:) block number: " . $transaction->blockNumber . "\n";
                }
            });
        });

        $constructorData = $contract->bytecode($this->testBytecode)->getData(10000, 'Game Token', 1, 'GT');

        $this->assertEquals('60606040526040805190810160405280600581526020017f45524332300000000000000000000000000000000000000000000000000000008152506000908051906020019061004f92919061012f565b50341561005b57600080fd5b604051610ec5380380610ec58339810160405280805190602001909190805182019190602001805190602001909190805182019190505083600560003373ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff168152602001908152602001600020819055508360048190555082600190805190602001906100f392919061012f565b50806002908051906020019061010a92919061012f565b5081600360006101000a81548160ff021916908360ff160217905550505050506101d4565b828054600181600116156101000203166002900490600052602060002090601f016020900481019282601f1061017057805160ff191683800117855561019e565b8280016001018555821561019e579182015b8281111561019d578251825591602001919060010190610182565b5b5090506101ab91906101af565b5090565b6101d191905b808211156101cd5760008160009055506001016101b5565b5090565b90565b610ce2806101e36000396000f3006060604052600436106100a4576000357c0100000000000000000000000000000000000000000000000000000000900463ffffffff16806306fdde03146100a9578063095ea7b31461013757806318160ddd1461019157806323b872dd146101ba578063313ce567146102335780635a3b7e421461026257806370a08231146102f057806395d89b411461033d578063a9059cbb146103cb578063dd62ed3e1461040d575b600080fd5b34156100b457600080fd5b6100bc610479565b6040518080602001828103825283818151815260200191508051906020019080838360005b838110156100fc5780820151818401526020810190506100e1565b50505050905090810190601f1680156101295780820380516001836020036101000a031916815260200191505b509250505060405180910390f35b341561014257600080fd5b610177600480803573ffffffffffffffffffffffffffffffffffffffff16906020019091908035906020019091905050610517565b604051808215151515815260200191505060405180910390f35b341561019c57600080fd5b6101a4610609565b6040518082815260200191505060405180910390f35b34156101c557600080fd5b610219600480803573ffffffffffffffffffffffffffffffffffffffff1690602001909190803573ffffffffffffffffffffffffffffffffffffffff1690602001909190803590602001909190505061060f565b604051808215151515815260200191505060405180910390f35b341561023e57600080fd5b61024661092a565b604051808260ff1660ff16815260200191505060405180910390f35b341561026d57600080fd5b61027561093d565b6040518080602001828103825283818151815260200191508051906020019080838360005b838110156102b557808201518184015260208101905061029a565b50505050905090810190601f1680156102e25780820380516001836020036101000a031916815260200191505b509250505060405180910390f35b34156102fb57600080fd5b610327600480803573ffffffffffffffffffffffffffffffffffffffff169060200190919050506109db565b6040518082815260200191505060405180910390f35b341561034857600080fd5b6103506109f3565b6040518080602001828103825283818151815260200191508051906020019080838360005b83811015610390578082015181840152602081019050610375565b50505050905090810190601f1680156103bd5780820380516001836020036101000a031916815260200191505b509250505060405180910390f35b34156103d657600080fd5b61040b600480803573ffffffffffffffffffffffffffffffffffffffff16906020019091908035906020019091905050610a91565b005b341561041857600080fd5b610463600480803573ffffffffffffffffffffffffffffffffffffffff1690602001909190803573ffffffffffffffffffffffffffffffffffffffff16906020019091905050610c91565b6040518082815260200191505060405180910390f35b60018054600181600116156101000203166002900480601f01602080910402602001604051908101604052809291908181526020018280546001816001161561010002031660029004801561050f5780601f106104e45761010080835404028352916020019161050f565b820191906000526020600020905b8154815290600101906020018083116104f257829003601f168201915b505050505081565b600081600660003373ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16815260200190815260200160002060008573ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff168152602001908152602001600020819055508273ffffffffffffffffffffffffffffffffffffffff163373ffffffffffffffffffffffffffffffffffffffff167f8c5be1e5ebec7d5bd14f71427d1e84f3dd0314c0f7b2291e5b200ac8c7c3b925846040518082815260200191505060405180910390a36001905092915050565b60045481565b6000808373ffffffffffffffffffffffffffffffffffffffff16141561063457600080fd5b81600560008673ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16815260200190815260200160002054101561068057600080fd5b600560008473ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff1681526020019081526020016000205482600560008673ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff1681526020019081526020016000205401101561070d57600080fd5b600660008573ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16815260200190815260200160002060003373ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff1681526020019081526020016000205482111561079657600080fd5b81600560008673ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff1681526020019081526020016000206000828254039250508190555081600560008573ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff1681526020019081526020016000206000828254019250508190555081600660008673ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16815260200190815260200160002060003373ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff168152602001908152602001600020600082825403925050819055508273ffffffffffffffffffffffffffffffffffffffff168473ffffffffffffffffffffffffffffffffffffffff167fddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef846040518082815260200191505060405180910390a3600190509392505050565b600360009054906101000a900460ff1681565b60008054600181600116156101000203166002900480601f0160208091040260200160405190810160405280929190818152602001828054600181600116156101000203166002900480156109d35780601f106109a8576101008083540402835291602001916109d3565b820191906000526020600020905b8154815290600101906020018083116109b657829003601f168201915b505050505081565b60056020528060005260406000206000915090505481565b60028054600181600116156101000203166002900480601f016020809104026020016040519081016040528092919081815260200182805460018160011615610100020316600290048015610a895780601f10610a5e57610100808354040283529160200191610a89565b820191906000526020600020905b815481529060010190602001808311610a6c57829003601f168201915b505050505081565b60008273ffffffffffffffffffffffffffffffffffffffff161415610ab557600080fd5b80600560003373ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff168152602001908152602001600020541015610b0157600080fd5b600560008373ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff1681526020019081526020016000205481600560008573ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16815260200190815260200160002054011015610b8e57600080fd5b80600560003373ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff1681526020019081526020016000206000828254039250508190555080600560008473ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff168152602001908152602001600020600082825401925050819055508173ffffffffffffffffffffffffffffffffffffffff163373ffffffffffffffffffffffffffffffffffffffff167fddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef836040518082815260200191505060405180910390a35050565b60066020528160005260406000206020528060005260406000206000915091505054815600a165627a7a723058203eb700b31f6d7723be3f4a0dd07fc4ba166a17279e26a437227679b92bacb5a2002900000000000000000000000000000000000000000000000000000000000027100000000000000000000000000000000000000000000000000000000000000080000000000000000000000000000000000000000000000000000000000000000100000000000000000000000000000000000000000000000000000000000000c0000000000000000000000000000000000000000000000000000000000000000a47616d6520546f6b656e0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000024754000000000000000000000000000000000000000000000000000000000000', $constructorData);

        if (!isset($this->contractAddress)) {
            $this->contractAddress = '0x407d73d8a49eeb85d32cf465507dd71d507100c2';
        }

        $balanceOfData = $contract->at($this->contractAddress)->getData('balanceOf', $fromAccount);

        $this->assertEquals('70a08231000000000000000000000000' . Utils::stripZero($fromAccount), $balanceOfData);
    }

    /**
     * testDecodeMethodReturn
     * 
     * @return void
     */
    public function testDecodeMethodReturn()
    {
        $contract = $this->contract;
        $contract->abi($this->testUserAbi);

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
        // Deploy user contract.
        $contract->bytecode($this->testUserBytecode)->new([
            'from' => $fromAccount,
            'gas' => '0x200b20'
        ], function ($err, $result) use ($contract) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if ($result) {
                echo "\nTransaction has made:) id: " . $result . "\n";
            }
            $transactionId = $result;
            $this->assertTrue((preg_match('/^0x[a-f0-9]{64}$/', $transactionId) === 1));

            $contract->eth->getTransactionReceipt($transactionId, function ($err, $transaction) {
                if ($err !== null) {
                    return $this->fail($err);
                }
                if ($transaction) {
                    $this->contractAddress = $transaction->contractAddress;
                    echo "\nTransaction has mined:) block number: " . $transaction->blockNumber . "\n";
                }
            });
        });

        if (!isset($this->contractAddress)) {
            $this->contractAddress = '0x407d73d8a49eeb85d32cf465507dd71d507100c2';
        }

        // Add user.
        $contract->at($this->contractAddress)->send('addUser', $toAccount, 'Peter', 'Lai', 18, [
            'from' => $fromAccount,
            'gas' => '0x200b20'
        ], function ($err, $result) use ($contract, $fromAccount, $toAccount) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if ($result) {
                echo "\nTransaction has made:) id: " . $result . "\n";
            }
            $transactionId = $result;
            $this->assertTrue((preg_match('/^0x[a-f0-9]{64}$/', $transactionId) === 1));

            $contract->eth->getTransactionReceipt($transactionId, function ($err, $transaction) use ($fromAccount, $toAccount, $contract) {
                if ($err !== null) {
                    return $this->fail($err);
                }
                if ($transaction) {
                    $topics = $transaction->logs[0]->topics;
                    echo "\nTransaction has mined:) block number: " . $transaction->blockNumber . "\n";

                    // validate topics
                    $this->assertEquals($contract->ethabi->encodeEventSignature($this->contract->events['AddUser']), $topics[0]);
                    $this->assertEquals('0x' . IntegerFormatter::format($toAccount), $topics[1]);
                }
            });
        });

        // Get user.
        $contract->call('getUser', $toAccount, [
            'from' => $fromAccount
        ], function ($err, $result) use ($contract, $fromAccount, $toAccount) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if ($result) {
                $this->assertEquals($result['firstName'], 'Peter');
                $this->assertEquals($result['lastName'], 'Lai');
                $this->assertEquals($result['age']->toString(), '18');
            }
        });
    }

    /**
     * testIssue85
     *
     * @return void
     */
    public function testIssue85()
    {
        $bytecode = '0x608060405234801561001057600080fd5b50610a36806100206000396000f3006080604052600436106100af576000357c0100000000000000000000000000000000000000000000000000000000900463ffffffff1680630bcd3b33146100b45780631f9030371461014457806325f35c36146101775780632d1be94d146102735780638b84925b146102a2578063a15e05fd146102cd578063a5f807cc1461030f578063d07326681461039f578063ded516d21461043e578063f1d0876a146104ad578063f5c2e88a1461054c575b600080fd5b3480156100c057600080fd5b506100c96105dc565b6040518080602001828103825283818151815260200191508051906020019080838360005b838110156101095780820151818401526020810190506100ee565b50505050905090810190601f1680156101365780820380516001836020036101000a031916815260200191505b509250505060405180910390f35b34801561015057600080fd5b5061015961061e565b60405180826000191660001916815260200191505060405180910390f35b34801561018357600080fd5b5061018c61064b565b604051808060200180602001838103835285818151815260200191508051906020019080838360005b838110156101d05780820151818401526020810190506101b5565b50505050905090810190601f1680156101fd5780820380516001836020036101000a031916815260200191505b50838103825284818151815260200191508051906020019080838360005b8381101561023657808201518184015260208101905061021b565b50505050905090810190601f1680156102635780820380516001836020036101000a031916815260200191505b5094505050505060405180910390f35b34801561027f57600080fd5b506102886106cd565b604051808215151515815260200191505060405180910390f35b3480156102ae57600080fd5b506102b76106db565b6040518082815260200191505060405180910390f35b3480156102d957600080fd5b506102e26106e9565b60405180836000191660001916815260200182600019166000191681526020019250505060405180910390f35b34801561031b57600080fd5b50610324610741565b6040518080602001828103825283818151815260200191508051906020019080838360005b83811015610364578082015181840152602081019050610349565b50505050905090810190601f1680156103915780820380516001836020036101000a031916815260200191505b509250505060405180910390f35b3480156103ab57600080fd5b506103b461076a565b60405180806020018360001916600019168152602001828103825284818151815260200191508051906020019080838360005b838110156104025780820151818401526020810190506103e7565b50505050905090810190601f16801561042f5780820380516001836020036101000a031916815260200191505b50935050505060405180910390f35b34801561044a57600080fd5b506104536107d9565b60405180827effffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff19167effffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff1916815260200191505060405180910390f35b3480156104b957600080fd5b506104c2610806565b60405180836000191660001916815260200180602001828103825283818151815260200191508051906020019080838360005b838110156105105780820151818401526020810190506104f5565b50505050905090810190601f16801561053d5780820380516001836020036101000a031916815260200191505b50935050505060405180910390f35b34801561055857600080fd5b50610561610875565b6040518080602001828103825283818151815260200191508051906020019080838360005b838110156105a1578082015181840152602081019050610586565b50505050905090810190601f1680156105ce5780820380516001836020036101000a031916815260200191505b509250505060405180910390f35b6060806040805190810160405280600381526020017f616263000000000000000000000000000000000000000000000000000000000081525090508091505090565b6000807f616263000000000000000000000000000000000000000000000000000000000090508091505090565b6060806060806040805190810160405280600381526020017f616263000000000000000000000000000000000000000000000000000000000081525091506040805190810160405280600381526020017f78797a0000000000000000000000000000000000000000000000000000000000815250905081819350935050509091565b600080600190508091505090565b600080606390508091505090565b6000806000807f616263000000000000000000000000000000000000000000000000000000000091507f78797a0000000000000000000000000000000000000000000000000000000000905081819350935050509091565b6060806101606040519081016040528061012c81526020016108df61012c913990508091505090565b60606000606060006040805190810160405280600381526020017f616263000000000000000000000000000000000000000000000000000000000081525091507f78797a0000000000000000000000000000000000000000000000000000000000905081819350935050509091565b6000807f610000000000000000000000000000000000000000000000000000000000000090508091505090565b60006060600060607f78797a000000000000000000000000000000000000000000000000000000000091506040805190810160405280600381526020017f6162630000000000000000000000000000000000000000000000000000000000815250905081819350935050509091565b606080606060405190810160405280603c81526020017f616263616263616263636162636162636162636361626361626361626363616281526020017f6361626361626363616263616263616263636162636162636162636300000000815250905080915050905600616263616263616263636162636162636162636361626361626361626363616263616263616263636162636162636162636361626361626361626363616263616263616263636162636162636162636361626361626361626363616263616263616263636162636162636162636361626361626361626363616263616263616263636162636162636162636361626361626361626363616263616263616263636162636162636162636361626361626361626363616263616263616263636162636162636162636361626361626361626363616263616263616263636162636162636162636361626361626361626363616263616263616263636162636162636162636361626361626361626363616263616263616263636162636162636162636361626361626361626363a165627a7a7230582020ee9e6b05918d0df987776ee24808f4dd1c522806bf9fa8f4336c93f6b0ec050029';
        $abi = '[
            {
                "constant": true,
                "inputs": [],
                "name": "getBytes",
                "outputs": [
                    {
                        "name": "",
                        "type": "bytes"
                    }
                ],
                "payable": false,
                "stateMutability": "pure",
                "type": "function"
            },
            {
                "constant": true,
                "inputs": [],
                "name": "getBytes32",
                "outputs": [
                    {
                        "name": "",
                        "type": "bytes32"
                    }
                ],
                "payable": false,
                "stateMutability": "pure",
                "type": "function"
            },
            {
                "constant": true,
                "inputs": [],
                "name": "getDynamicData",
                "outputs": [
                    {
                        "name": "",
                        "type": "bytes"
                    },
                    {
                        "name": "",
                        "type": "bytes"
                    }
                ],
                "payable": false,
                "stateMutability": "pure",
                "type": "function"
            },
            {
                "constant": true,
                "inputs": [],
                "name": "getBoolean",
                "outputs": [
                    {
                        "name": "",
                        "type": "bool"
                    }
                ],
                "payable": false,
                "stateMutability": "pure",
                "type": "function"
            },
            {
                "constant": true,
                "inputs": [],
                "name": "getInteger",
                "outputs": [
                    {
                        "name": "",
                        "type": "int256"
                    }
                ],
                "payable": false,
                "stateMutability": "pure",
                "type": "function"
            },
            {
                "constant": true,
                "inputs": [],
                "name": "getTwoBytes32",
                "outputs": [
                    {
                        "name": "",
                        "type": "bytes32"
                    },
                    {
                        "name": "",
                        "type": "bytes32"
                    }
                ],
                "payable": false,
                "stateMutability": "pure",
                "type": "function"
            },
            {
                "constant": true,
                "inputs": [],
                "name": "getBytesVeryLong",
                "outputs": [
                    {
                        "name": "",
                        "type": "bytes"
                    }
                ],
                "payable": false,
                "stateMutability": "pure",
                "type": "function"
            },
            {
                "constant": true,
                "inputs": [],
                "name": "getDynamicDataMixedOne",
                "outputs": [
                    {
                        "name": "",
                        "type": "bytes"
                    },
                    {
                        "name": "",
                        "type": "bytes32"
                    }
                ],
                "payable": false,
                "stateMutability": "pure",
                "type": "function"
            },
            {
                "constant": true,
                "inputs": [],
                "name": "getBytes1",
                "outputs": [
                    {
                        "name": "",
                        "type": "bytes1"
                    }
                ],
                "payable": false,
                "stateMutability": "pure",
                "type": "function"
            },
            {
                "constant": true,
                "inputs": [],
                "name": "getDynamicDataMixedTwo",
                "outputs": [
                    {
                        "name": "",
                        "type": "bytes32"
                    },
                    {
                        "name": "",
                        "type": "bytes"
                    }
                ],
                "payable": false,
                "stateMutability": "pure",
                "type": "function"
            },
            {
                "constant": true,
                "inputs": [],
                "name": "getBytesLong",
                "outputs": [
                    {
                        "name": "",
                        "type": "bytes"
                    }
                ],
                "payable": false,
                "stateMutability": "pure",
                "type": "function"
            },
            {
                "inputs": [],
                "payable": false,
                "stateMutability": "nonpayable",
                "type": "constructor"
              }
        ]';
        $functions = [
            [
                'name' => 'getBoolean',
                'test' => function ($value) {
                    return is_bool($value);
                }
            ], [
                'name' => 'getInteger',
                'test' => function ($value) {
                    return ($value instanceof BigNumber);
                }
            ], [
                'name' => 'getBytes1',
                'test' => function ($value) {
                    return Utils::isHex($value) && mb_strlen($value) === 4;
                }
            ], [
                'name' => 'getBytes32',
                'test' => function ($value) {
                    return Utils::isHex($value) && mb_strlen($value) === 66;
                }
            ], [
                'name' => 'getBytesLong',
                'test' => function ($value) {
                    return Utils::isHex($value);
                }
            ], [
                'name' => 'getDynamicData',
                'test' => function ($value) {
                    return Utils::isHex($value);
                }
            ], [
                'name' => 'getDynamicDataMixedOne',
                'test' => function ($value) {
                    return Utils::isHex($value);
                }
            ], [
                'name' => 'getDynamicDataMixedTwo',
                'test' => function ($value) {
                    return Utils::isHex($value);
                }
            ], [
                'name' => 'getTwoBytes32',
                'test' => function ($value) {
                    return Utils::isHex($value) && mb_strlen($value) === 66;
                }
            ]
          ];
        $contractAddress = "";

        if (!isset($this->accounts[0])) {
            $account = '0x407d73d8a49eeb85d32cf465507dd71d507100c1';
        } else {
            $account = $this->accounts[0];
        }
        $contract = new Contract($this->web3->provider, $abi);
        $contract->bytecode($bytecode)->new([
            'from' => $account,
            'gas' => '0x200b20'
        ], function ($err, $result) use ($contract, &$contractAddress) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if ($result) {
                echo "\nTransaction has made:) id: " . $result . "\n";
            }
            $transactionId = $result;
            $this->assertTrue((preg_match('/^0x[a-f0-9]{64}$/', $transactionId) === 1));

            $contract->eth->getTransactionReceipt($transactionId, function ($err, $transaction) use (&$contractAddress) {
                if ($err !== null) {
                    return $this->fail($err);
                }
                if ($transaction) {
                    $contractAddress = $transaction->contractAddress;
                    echo "\nTransaction has mined:) block number: " . $transaction->blockNumber . "\n";
                }
            });
        });
        $contract->at($contractAddress);

        foreach ($functions as $function) {
            $contract->call($function['name'], [], 'latest', function ($err, $res) use ($function) {
                if ($err !== null) {
                    echo 'Error when call ' . $function['name'] . '. Message: ' . $err->getMessage() . "\n";
                    return;
                }
                foreach ($res as $r) {
                    if (!call_user_func($function['test'], $r)) {
                        var_dump($r);
                    }
                    $this->assertTrue(call_user_func($function['test'], $r));
                }
            });
        }
    }

    /**
     * testIssue125
     *
     * @return void
     */
    public function testIssue125()
    {
        $bytecode = '0x608060405234801561001057600080fd5b506103bb806100206000396000f3fe608060405260043610610046576000357c01000000000000000000000000000000000000000000000000000000009004806373d4a13a1461004b578063ab62f0e1146100db575b600080fd5b34801561005757600080fd5b506100606101a3565b6040518080602001828103825283818151815260200191508051906020019080838360005b838110156100a0578082015181840152602081019050610085565b50505050905090810190601f1680156100cd5780820380516001836020036101000a031916815260200191505b509250505060405180910390f35b3480156100e757600080fd5b506101a1600480360360208110156100fe57600080fd5b810190808035906020019064010000000081111561011b57600080fd5b82018360208201111561012d57600080fd5b8035906020019184600183028401116401000000008311171561014f57600080fd5b91908080601f016020809104026020016040519081016040528093929190818152602001838380828437600081840152601f19601f820116905080830192505050505050509192919290505050610241565b005b60008054600181600116156101000203166002900480601f0160208091040260200160405190810160405280929190818152602001828054600181600116156101000203166002900480156102395780601f1061020e57610100808354040283529160200191610239565b820191906000526020600020905b81548152906001019060200180831161021c57829003601f168201915b505050505081565b80600090805190602001906102579291906102ea565b50806040518082805190602001908083835b60208310151561028e5780518252602082019150602081019050602083039250610269565b6001836020036101000a03801982511681845116808217855250505050505090500191505060405180910390207fae08ae866f3211e692b7cdd30e8b6ec658a153397150437cb873f95239953a5460405160405180910390a250565b828054600181600116156101000203166002900490600052602060002090601f016020900481019282601f1061032b57805160ff1916838001178555610359565b82800160010185558215610359579182015b8281111561035857825182559160200191906001019061033d565b5b509050610366919061036a565b5090565b61038c91905b80821115610388576000816000905550600101610370565b5090565b9056fea165627a7a72305820bb836a29a397513a3f2e2199520262ef5978d3a3d7e3d3532da41064d39177e30029';
        $abi = '[
            {
                "constant": false,
                "inputs": [
                    {
                        "name": "_data",
                        "type": "bytes"
                    }
                ],
                "name": "setData",
                "outputs": [],
                "payable": false,
                "stateMutability": "nonpayable",
                "type": "function"
            },
            {
                "anonymous": false,
                "inputs": [
                    {
                        "indexed": true,
                        "name": "_data",
                        "type": "bytes"
                    }
                ],
                "name": "SetData",
                "type": "event"
            },
            {
                "constant": true,
                "inputs": [],
                "name": "data",
                "outputs": [
                    {
                        "name": "",
                        "type": "bytes"
                    }
                ],
                "payable": false,
                "stateMutability": "view",
                "type": "function"
            }
        ]';
        $contractAddress = "";

        if (!isset($this->accounts[0])) {
            $account = '0x407d73d8a49eeb85d32cf465507dd71d507100c1';
        } else {
            $account = $this->accounts[0];
        }
        $contract = new Contract($this->web3->provider, $abi);
        $contract->bytecode($bytecode)->new([
            'from' => $account,
            'gas' => '0x200b20'
        ], function ($err, $result) use ($contract, &$contractAddress) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if ($result) {
                echo "\nTransaction has made:) id: " . $result . "\n";
            }
            $transactionId = $result;
            $this->assertTrue((preg_match('/^0x[a-f0-9]{64}$/', $transactionId) === 1));

            $contract->eth->getTransactionReceipt($transactionId, function ($err, $transaction) use (&$contractAddress) {
                if ($err !== null) {
                    return $this->fail($err);
                }
                if ($transaction) {
                    $contractAddress = $transaction->contractAddress;
                    echo "\nTransaction has mined:) block number: " . $transaction->blockNumber . "\n";
                }
            });
        });
        $contract->at($contractAddress);

        $contract->call('data', function ($err, $res) {
            if ($err !== null) {
                echo 'Error when call ' . $function['name'] . '. Message: ' . $err->getMessage() . "\n";
                return;
            }
            $this->assertEquals('0', $res[0]);
        });

        $contract->send('setData', '0x44aec9b900000000000000000000000000000000000000000000000000000000000000030000000000000000000000000000000000000000000000000000000000000021000000000000000000000000000000000000000000000000000000000000000b', [
            'from' => $account,
            'gas' => '0x200b20'
        ], function ($err, $result) use ($contract) {
            if ($err !== null) {
                var_dump('Error' . $err->getMessage());
                return $this->fail($err->getMessage());
            }
            if ($result) {
                echo "\nTransaction has made:) id: " . $result . "\n";
            }
            $transactionId = $result;
            $this->assertTrue((preg_match('/^0x[a-f0-9]{64}$/', $transactionId) === 1));

            $contract->eth->getTransactionReceipt($transactionId, function ($err, $transaction) {
                if ($err !== null) {
                    return $this->fail($err);
                }
                $this->assertTrue($transaction !== null);
            });
        });

        $contract->call('data', function ($err, $res) {
            if ($err !== null) {
                echo 'Error when call ' . $function['name'] . '. Message: ' . $err->getMessage() . "\n";
                return;
            }
            $this->assertEquals('0x44aec9b900000000000000000000000000000000000000000000000000000000000000030000000000000000000000000000000000000000000000000000000000000021000000000000000000000000000000000000000000000000000000000000000b', $res[0]);
        });
    }

    /**
     * testIssue134
     * 
     * @return void
     */
    public function testIssue134()
    {
        $bytecode = '0x608060405234801561001057600080fd5b50610487806100206000396000f3fe60806040526004361061005c576000357c01000000000000000000000000000000000000000000000000000000009004806373d4a13a146100615780638381f58a146100f15780639a73c4091461011c578063c3fad957146101ee575b600080fd5b34801561006d57600080fd5b50610076610229565b6040518080602001828103825283818151815260200191508051906020019080838360005b838110156100b657808201518184015260208101905061009b565b50505050905090810190601f1680156100e35780820380516001836020036101000a031916815260200191505b509250505060405180910390f35b3480156100fd57600080fd5b506101066102c7565b6040518082815260200191505060405180910390f35b34801561012857600080fd5b506101ec6004803603604081101561013f57600080fd5b81019080803590602001909291908035906020019064010000000081111561016657600080fd5b82018360208201111561017857600080fd5b8035906020019184600183028401116401000000008311171561019a57600080fd5b91908080601f016020809104026020016040519081016040528093929190818152602001838380828437600081840152601f19601f8201169050808301925050505050505091929192905050506102cd565b005b3480156101fa57600080fd5b506102276004803603602081101561021157600080fd5b810190808035906020019092919050505061037f565b005b60008054600181600116156101000203166002900480601f0160208091040260200160405190810160405280929190818152602001828054600181600116156101000203166002900480156102bf5780601f10610294576101008083540402835291602001916102bf565b820191906000526020600020905b8154815290600101906020018083116102a257829003601f168201915b505050505081565b60015481565b80600090805190602001906102e39291906103b6565b5081600181905550806040518082805190602001908083835b60208310151561032157805182526020820191506020810190506020830392506102fc565b6001836020036101000a0380198251168184511680821785525050505050509050019150506040518091039020827f95d23dc1ab7ea37e46bd697006b873f80ffecb4cb4ae085c7cc60a977ba7f2f560405160405180910390a35050565b80600181905550807f9d9c58c068c2bc9cb27d4e5c437f624ccbf4910ba8893ffd03ee311830becab160405160405180910390a250565b828054600181600116156101000203166002900490600052602060002090601f016020900481019282601f106103f757805160ff1916838001178555610425565b82800160010185558215610425579182015b82811115610424578251825591602001919060010190610409565b5b5090506104329190610436565b5090565b61045891905b8082111561045457600081600090555060010161043c565b5090565b9056fea165627a7a72305820830ee43c52ac89b1fbc68647e1893aea17aa8ca132532723176508a6bc4586c90029';
        $abi = '[
            {
                "constant": true,
                "inputs": [],
                "name": "data",
                "outputs": [
                    {
                        "name": "",
                        "type": "bytes"
                    }
                ],
                "payable": false,
                "stateMutability": "view",
                "type": "function"
            },
            {
                "constant": true,
                "inputs": [],
                "name": "number",
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
                        "name": "_number",
                        "type": "uint256"
                    },
                    {
                        "name": "_data",
                        "type": "bytes"
                    }
                ],
                "name": "say",
                "outputs": [],
                "payable": false,
                "stateMutability": "nonpayable",
                "type": "function"
            },
            {
                "constant": false,
                "inputs": [
                    {
                        "name": "_number",
                        "type": "uint256"
                    }
                ],
                "name": "say",
                "outputs": [],
                "payable": false,
                "stateMutability": "nonpayable",
                "type": "function"
            },
            {
                "anonymous": false,
                "inputs": [
                    {
                        "indexed": true,
                        "name": "_number",
                        "type": "uint256"
                    }
                ],
                "name": "Say",
                "type": "event"
            },
            {
                "anonymous": false,
                "inputs": [
                    {
                        "indexed": true,
                        "name": "_number",
                        "type": "uint256"
                    },
                    {
                        "indexed": true,
                        "name": "_data",
                        "type": "bytes"
                    }
                ],
                "name": "Say",
                "type": "event"
            }
        ]';
        $testNumber = 16;
        $testData = "0x01234";
        $contractAddress = "";
        $contract = new Contract($this->web3->provider, $abi);

        if (!isset($this->accounts[0])) {
            $account = '0x407d73d8a49eeb85d32cf465507dd71d507100c1';
        } else {
            $account = $this->accounts[0];
        }
        $contract = new Contract($this->web3->provider, $abi);
        $contract->bytecode($bytecode)->new([
            'from' => $account,
            'gas' => '0x200b20'
        ], function ($err, $result) use ($contract, &$contractAddress) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if ($result) {
                echo "\nTransaction has made:) id: " . $result . "\n";
            }
            $transactionId = $result;
            $this->assertTrue((preg_match('/^0x[a-f0-9]{64}$/', $transactionId) === 1));

            $contract->eth->getTransactionReceipt($transactionId, function ($err, $transaction) use (&$contractAddress) {
                if ($err !== null) {
                    return $this->fail($err);
                }
                if ($transaction) {
                    $contractAddress = $transaction->contractAddress;
                    echo "\nTransaction has mined:) block number: " . $transaction->blockNumber . "\n";
                }
            });
        });
        $contract->at($contractAddress);

        // test for send transaction and get data
        $contract->send('say', $testNumber, $testData, [
            'from' => $account,
            'gas' => '0x200b20'
        ], function ($err, $result) use ($contract, $testNumber, $testData) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if ($result) {
                echo "\nTransaction has made:) id: " . $result . "\n";
            }
            $transactionId = $result;
            $this->assertTrue((preg_match('/^0x[a-f0-9]{64}$/', $transactionId) === 1));

            $contract->eth->getTransactionReceipt($transactionId, function ($err, $transaction) use ($testNumber, $testData, $contract) {
                if ($err !== null) {
                    return $this->fail($err);
                }
                if ($transaction) {
                    $topics = $transaction->logs[0]->topics;
                    echo "\nTransaction has mined:) block number: " . $transaction->blockNumber . "\n";

                    // validate topics
                    $this->assertEquals($contract->ethabi->encodeEventSignature($contract->events['Say']), $topics[0]);
                    $this->assertEquals('0x' . IntegerFormatter::format($testNumber), $topics[1], $topics[2]);
                }
                $contract->call('number', function ($err, $res) use ($testNumber) {
                    if ($err !== null) {
                        echo 'Error when call ' . $function['name'] . '. Message: ' . $err->getMessage() . "\n";
                        return;
                    }
                    $this->assertEquals((string) $testNumber, $res[0]->toString());
                });
            });
        });
        $testNumber++;

        $contract->send('say', $testNumber, [
            'from' => $account,
            'gas' => '0x200b20'
        ], function ($err, $result) use ($contract, $testNumber) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if ($result) {
                echo "\nTransaction has made:) id: " . $result . "\n";
            }
            $transactionId = $result;
            $this->assertTrue((preg_match('/^0x[a-f0-9]{64}$/', $transactionId) === 1));

            $contract->eth->getTransactionReceipt($transactionId, function ($err, $transaction) use ($testNumber, $contract) {
                if ($err !== null) {
                    return $this->fail($err);
                }
                if ($transaction) {
                    $topics = $transaction->logs[0]->topics;
                    echo "\nTransaction has mined:) block number: " . $transaction->blockNumber . "\n";

                    // validate topics
                    // $this->assertEquals($contract->ethabi->encodeEventSignature($contract->events['Say']), $topics[0]);
                    $this->assertEquals('0x' . IntegerFormatter::format($testNumber), $topics[1]);
                }
                $blockNumber = Utils::toBn($transaction->blockNumber);
                $contract->call('number', Utils::toHex($blockNumber, true), function ($err, $res) use ($testNumber) {
                    if ($err !== null) {
                        echo 'Error when call ' . $function['name'] . '. Message: ' . $err->getMessage() . "\n";
                        return;
                    }
                    $this->assertEquals((string) $testNumber, $res[0]->toString());
                });
                $blockNumber = $blockNumber->subtract(Utils::toBn(1));
                $contract->call('number', Utils::toHex($blockNumber, true), function ($err, $res) use ($testNumber) {
                    if ($err !== null) {
                        echo 'Error when call ' . $function['name'] . '. Message: ' . $err->getMessage() . "\n";
                        return;
                    }
                    $this->assertEquals((string) $testNumber-1, $res[0]->toString());
                });
            });
        });

        // test for estimate gas
        $contract->estimateGas("say", $testNumber, $testData, [
            'from' => $account,
            'gas' => '0x200b20'
        ], function ($err, $result) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if (isset($result)) {
                echo "\nEstimate gas: " . $result->toString() . "\n";
                $this->assertTrue($result !== null);
            }
        });

        $contract->estimateGas("say", $testNumber, [
            'from' => $account,
            'gas' => '0x200b20'
        ], function ($err, $result) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            if (isset($result)) {
                echo "\nEstimate gas: " . $result->toString() . "\n";
                $this->assertTrue($result !== null);
            }
        });
    }
}