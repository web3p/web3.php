<?php

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
     * @param string $method
     * @param array $arguments
     * @param callable $callback
     * @return void
     */
    public function send($method, $arguments, $callback)
    {
        $rpc = $this->createRpc($method, $arguments);

        if (!$this->isBatch) {            
            $this->requestManager->sendPayload(json_encode($rpc), $callback);
        } else {
            $this->batch[] = json_encode($rpc);
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