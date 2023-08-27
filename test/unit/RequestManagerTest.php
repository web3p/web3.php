<?php

namespace Test\Unit;

use Test\TestCase;
use Web3\RequestManagers\RequestManager;

class RequestManagerTest extends TestCase
{
    /**
     * testSetHost
     * 
     * @return void
     */
    public function testSetHost()
    {
        $requestManager = new RequestManager('http://localhost:8545', 0.1);
        $this->assertEquals($requestManager->host, 'http://localhost:8545');
        $this->assertEquals($requestManager->timeout, 0.1);

        // there is no setter for host and timeout
        $requestManager->host = $this->testHost2;
        $requestManager->timeout = 1;
        $this->assertEquals($requestManager->host, 'http://localhost:8545');
        $this->assertEquals($requestManager->timeout, 0.1);
    }
}