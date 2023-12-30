<?php

namespace Test\Unit;

use Test\TestCase;
use Web3\Providers\Provider;
use Web3\Providers\HttpProvider;

class ProviderTest extends TestCase
{
    /**
     * testNewProvider
     * 
     * @return void
     */
    public function testNewProvider()
    {
        $provider = new HttpProvider('http://localhost:8545');

        $this->assertEquals($provider->host, 'http://localhost:8545');
    }
}