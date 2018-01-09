<?php

/**
 * This file is part of web3.php package.
 * 
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Web3\Providers;

use Web3\Providers\Provider;
use Web3\Providers\IProvider;
use Web3\RequestManagers\RequestManager;

class HttpProvider extends Provider implements IProvider
{
    /**
     * construct
     * 
     * @param \Web3\RequestManagers\RequestManager $requestManager
     * @return void
     */
    public function __construct(RequestManager $requestManager)
    {
        parent::__construct($requestManager);
    }

    /**
     * send
     * 
     * @param \Web3\Methods\Method $method
     * @param callable $callback
     * @return void
     */
    public function send($method, $callback)
    {
        $payload = $method->toPayloadString();

        if (!$this->isBatch) {          
            $this->requestManager->sendPayload($payload, $callback);
        } else {
            $this->batch[] = $payload;
        }
    }

    /**
     * batch
     * 
     * @param bool $status
     * @return void
     */
    public function batch($status)
    {
        $status = is_bool($status);

        $this->isBatch = $status;
    }

    /**
     * execute
     * 
     * @param callable $callback
     * @return void
     */
    public function execute($callback)
    {
        if (!$this->isBatch) {
            throw new \RuntimeException('Please batch json rpc first.');
        }
        $this->requestManager->sendPayload('[' . implode(',', $this->batch) . ']', $callback);
        $this->batch = [];
    }

    /**
     * createRpc
     * 
     * @param string $rpc
     * @param array $arguments
     * @return array
     */
    protected function createRpc($rpc, $arguments)
    {
        $this->id += 1;

        $rpc = [
            'id' => $this->id,
            'jsonrpc' => $this->rpcVersion,
            'method' => $rpc
        ];

        if (count($arguments) > 0) {
            $rpc['params'] = $arguments;
        }
        return $rpc;
    }
}