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
        $requestManager = new RequestManager('http://localhost:8545');

        $this->assertEquals($requestManager->host, 'http://localhost:8545');

        $requestManager->host = $this->testRinkebyHost;

        $this->assertEquals($requestManager->host, 'http://localhost:8545');
    }
}