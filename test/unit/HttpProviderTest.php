<?php

namespace Test\Unit;

use RuntimeException;
use Test\TestCase;
use Web3\RequestManagers\HttpRequestManager;
use Web3\RequestManagers\HttpAsyncRequestManager;
use Web3\Providers\HttpProvider;
use Web3\Methods\Web3\ClientVersion;

class HttpProviderTest extends TestCase
{
    /**
     * testSend
     * 
     * @return void
     */
    public function testSend()
    {
        $requestManager = new HttpRequestManager($this->testHost);
        $provider = new HttpProvider($requestManager);
        $method = new ClientVersion('web3_clientVersion', []);

        $provider->send($method, function ($err, $version) {
            if ($err !== null) {
                $this->fail($err->getMessage());
            }
            $this->assertTrue(is_string($version));
        });
    }

    /**
     * testBatch
     * 
     * @return void
     */
    public function testBatch()
    {
        $requestManager = new HttpRequestManager($this->testHost);
        $provider = new HttpProvider($requestManager);
        $method = new ClientVersion('web3_clientVersion', []);
        $callback = function ($err, $data) {
            if ($err !== null) {
                $this->fail($err->getMessage());
            }
            $this->assertEquals($data[0], $data[1]);
        };

        try {
            $provider->execute($callback);
        } catch (RuntimeException $err) {
            $this->assertTrue($err->getMessage() !== true);
        }

        $provider->batch(true);
        $provider->send($method, null);
        $provider->send($method, null);
        $provider->execute($callback);
    }

    /**
     * testSendAsync
     * 
     * @return void
     */
    public function testSendAsync()
    {
        $requestManager = new HttpAsyncRequestManager($this->testHost);
        $provider = new HttpProvider($requestManager);
        $method = new ClientVersion('web3_clientVersion', []);

        // \React\Async\await($provider->send($method, function ($err, $version) {
        //     if ($err !== null) {
        //         $this->fail($err->getMessage());
        //     }
        //     $this->assertTrue(is_string($version));
        // }));
        $a = $provider->send($method, function ($err, $version) {
            if ($err !== null) {
                $this->fail($err->getMessage());
            }
            $this->assertTrue(is_string($version));
        });
        $b = $provider->send($method, function ($err, $version) {
            if ($err !== null) {
                $this->fail($err->getMessage());
            }
            $this->assertTrue(is_string($version));
        });
        $c = $provider->send($method, function ($err, $version) {
            if ($err !== null) {
                $this->fail($err->getMessage());
            }
            $this->assertTrue(is_string($version));
        });
        \React\Async\await(\React\Async\parallel([
            function () use ($a) { return $a; },
            function () use ($b) { return $b; },
            function () use ($c) { return $c; }
        ]));
    }

    /**
     * testBatchAsync
     * 
     * @return void
     */
    public function testBatchAsync()
    {
        $requestManager = new HttpAsyncRequestManager($this->testHost);
        $provider = new HttpProvider($requestManager);
        $method = new ClientVersion('web3_clientVersion', []);
        $callback = function ($err, $data) {
            if ($err !== null) {
                $this->fail($err->getMessage());
            }
            $this->assertEquals($data[0], $data[1]);
        };

        try {
            \React\Async\await($provider->execute($callback));
        } catch (RuntimeException $err) {
            $this->assertTrue($err->getMessage() !== true);
        }

        $provider->batch(true);
        $provider->send($method, null);
        $provider->send($method, null);
        \React\Async\await($provider->execute($callback));
    }
}