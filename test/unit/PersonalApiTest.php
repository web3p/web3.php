<?php

namespace Test\Unit;

use RuntimeException;
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
    public function setUp()
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
                return $this->fail($e->getMessage());
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
                return $this->fail($e->getMessage());
            }
            $this->newAccount = $account;
            $this->assertTrue(is_string($account));
        });

        $personal->unlockAccount($this->newAccount, '123456', function ($err, $unlocked) {
            if ($err !== null) {
                // infura banned us to use unlock account
                return $this->assertTrue($err->getCode() === 405);
            }
            $this->assertTrue($unlocked);
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

        $personal->sendTransaction([
            'from' => "0xb60e8dd61c5d32be8058bb8eb970870f07233155",
            'to' => "0xd46e8dd67c5d32be8058bb8eb970870f07244567",
            'gas' => "0x76c0",
            'gasPrice' => "0x9184e72a000",
            'value' => "0x9184e72a",
            'data' => "0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675"
        ], '123456', function ($err, $transaction) {
            if ($err !== null) {
                // infura banned us to use send transaction
                return $this->assertTrue($err->getCode() === 405);
            }
            $this->assertTrue(is_string($transaction));
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
}