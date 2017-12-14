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
     * setUp
     * 
     * @return void
     */
    public function setUp()
    {
        $web3 = new Web3('https://rinkeby.infura.io/vuethexplore');
        $this->web3 = $web3;
    }

    /**
     * tearDown
     * 
     * @return void
     */
    public function tearDown() {}
}