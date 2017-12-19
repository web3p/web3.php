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
            if (isset($accounts->result)) {
                $this->assertTrue(is_array($accounts->result));
            } else {
                $this->fail($accounts->error->message);
            }
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
                // infura banned us to use new account
                return $this->assertTrue($err->getCode() === 405);
            }
            if (isset($account->result)) {
                $this->assertTrue(is_array($account->result));
            } else {
                $this->fail($account->error->message);
            }
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

        $personal->unlockAccount('0x407d73d8a49eeb85d32cf465507dd71d507100c1', '123456', function ($err, $account) {
            if ($err !== null) {
                // infura banned us to use unlock account
                return $this->assertTrue($err->getCode() === 405);
            }
            if (isset($account->result)) {
                $this->assertTrue(is_bool($account->result));
            } else {
                $this->fail($account->error->message);
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

        $personal = $this->personal;

        $personal->hello(function ($err, $hello) {
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