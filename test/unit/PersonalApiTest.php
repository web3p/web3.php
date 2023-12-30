<?php

namespace Test\Unit;

use RuntimeException;
use InvalidArgumentException;
use Test\TestCase;

class PersonalApiTest extends TestCase
{
    /**
     * personal
     * 
     * @var Web3\Personal
     */
    protected $personal;

    /**
     * newAccount
     * 
     * @var string
     */
    protected $newAccount;

    /**
     * setUp
     * 
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->personal = $this->web3->personal;
    }

    /**
     * testListAccounts
     * 
     * @return void
     */
    public function testListAccounts()
    {
        $personal = $this->personal;

        $personal->listAccounts(function ($err, $accounts) {
            if ($err !== null) {
                // infura banned us to use list accounts
                return $this->assertTrue($err->getCode() === 405);
            }
            $this->assertTrue(is_array($accounts));
        });
    }

    /**
     * testNewAccount
     * 
     * @return void
     */
    public function testNewAccount()
    {
        $personal = $this->personal;

        $personal->newAccount('123456', function ($err, $account) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(is_string($account));
        });
    }

    /**
     * testUnlockAccount
     * 
     * @return void
     */
    public function testUnlockAccount()
    {
        $personal = $this->personal;

        // create account
        $personal->newAccount('123456', function ($err, $account) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->newAccount = $account;
            $this->assertTrue(is_string($account));
        });

        $personal->unlockAccount($this->newAccount, '123456', function ($err, $unlocked) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue($unlocked);
        });
    }

    /**
     * testUnlockAccountWithDuration
     * 
     * @return void
     */
    public function testUnlockAccountWithDuration()
    {
        $personal = $this->personal;

        // create account
        $personal->newAccount('123456', function ($err, $account) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->newAccount = $account;
            $this->assertTrue(is_string($account));
        });

        $personal->unlockAccount($this->newAccount, '123456', 100, function ($err, $unlocked) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue($unlocked);
        });
    }

    /**
     * testLockAccount
     * 
     * @return void
     */
    public function testLockAccount()
    {
        $personal = $this->personal;

        // create account
        $personal->newAccount('123456', function ($err, $account) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->newAccount = $account;
            $this->assertTrue(is_string($account));
        });

        $personal->unlockAccount($this->newAccount, '123456', function ($err, $unlocked) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue($unlocked);
        });

        $personal->lockAccount($this->newAccount, function ($err, $locked) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue($locked);
        });
    }

    /**
     * testSendTransaction
     * 
     * @return void
     */    
    public function testSendTransaction()
    {
        $personal = $this->personal;

        // create account
        $personal->newAccount('123456', function ($err, $account) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->newAccount = $account;
            $this->assertTrue(is_string($account));
        });

        $this->web3->eth->accounts(function ($err, $accounts) use ($personal) {
            $this->web3->eth->sendTransaction([
                'from' => $accounts[0],
                'to' => $this->newAccount,
                'value' => '0xfffffffffffff',
            ], function ($err, $transaction) {
                if ($err !== null) {
                    return $this->fail($err->getMessage());
                }
                $this->assertTrue(is_string($transaction));
                $this->assertTrue(mb_strlen($transaction) === 66);
            });
        });

        $this->web3->eth->accounts(function ($err, $accounts) use ($personal) {
            $personal->sendTransaction([
                'from' => $this->newAccount,
                'to' => $accounts[0],
                'value' => '0x01',
                'gasLimit' => 21000,
                'gasPrice' => 5000000000,
            ], '123456', function ($err, $transaction) {
                if ($err !== null) {
                    return $this->fail($err->getMessage());
                }
                $this->assertTrue(is_string($transaction));
                $this->assertTrue(mb_strlen($transaction) === 66);
            });
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

        $personal = $this->personal;

        $personal->hello(function ($err, $hello) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(true);
        });
    }

    /**
     * testWrongParam
     * 
     * @return void
     */
    public function testWrongParam()
    {
        $this->expectException(RuntimeException::class);

        $personal = $this->personal;

        $personal->newAccount($personal, function ($err, $account) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(is_string($account));
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

        $personal = $this->personal;

        $personal->newAccount('123456');
    }
}