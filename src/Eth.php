<?php

/**
 * This file is part of web3.php package.
 * 
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Web3;

use Web3\Providers\Provider;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\RequestManager;
use Web3\RequestManagers\HttpRequestManager;

class Eth
{
    /**
     * provider
     *
     * @var \Web3\Providers\Provider
     */
    protected $provider;

    /**
     * methods
     * 
     * @var array
     */
    private $methods = [];

    /**
     * allowedMethods
     * 
     * @var array
     */
    private $allowedMethods = [
        'eth_protocolVersion', 'eth_syncing', 'eth_coinbase', 'eth_mining', 'eth_hashrate', 'eth_gasPrice', 'eth_accounts', 'eth_blockNumber', 'eth_getBalance', 'eth_getStorageAt', 'eth_getTransactionCount', 'eth_getBlockTransactionCountByHash', 'eth_getBlockTransactionCountByNumber', 'eth_getUncleCountByBlockHash', 'eth_getUncleCountByBlockNumber', 'eth_getUncleByBlockHashAndIndex', 'eth_getUncleByBlockNumberAndIndex', 'eth_getCode', 'eth_sign', 'eth_sendTransaction', 'eth_sendRawTransaction', 'eth_call', 'eth_estimateGas', 'eth_getBlockByHash', 'eth_getBlockByNumber', 'eth_getTransactionByHash', 'eth_getTransactionByBlockHashAndIndex', 'eth_getTransactionByBlockNumberAndIndex', 'eth_getTransactionReceipt', 'eth_compileSolidity', 'eth_compileLLL', 'eth_compileSerpent', 'eth_getWork', 'eth_newFilter', 'eth_newBlockFilter', 'eth_newPendingTransactionFilter', 'eth_uninstallFilter', 'eth_getFilterChanges', 'eth_getFilterLogs', 'eth_getLogs', 'eth_submitWork', 'eth_submitHashrate'
    ];

    /**
     * construct
     *
     * @param string|\Web3\Providers\Provider $provider
     * @return void
     */
    public function __construct($provider)
    {
        if (is_string($provider) && (filter_var($provider, FILTER_VALIDATE_URL) !== false)) {
            // check the uri schema
            if (preg_match('/^https?:\/\//', $provider) === 1) {
                $requestManager = new HttpRequestManager($provider);

                $this->provider = new HttpProvider($requestManager);
            }
        } else if ($provider instanceof Provider) {
            $this->provider = $provider;
        }
    }

    /**
     * call
     * 
     * @param string $name
     * @param array $arguments
     * @return void
     */
    public function __call($name, $arguments)
    {
        if (empty($this->provider)) {
            throw new \RuntimeException('Please set provider first.');
        }

        $class = explode('\\', get_class());

        if (preg_match('/^[a-zA-Z0-9]+$/', $name) === 1) {
            $method = strtolower($class[1]) . '_' . $name;

            if (!in_array($method, $this->allowedMethods)) {
                throw new \RuntimeException('Unallowed rpc method: ' . $method);
            }
            if ($this->provider->isBatch) {
                $callback = null;
            } else {
                $callback = array_pop($arguments);

                if (is_callable($callback) !== true) {
                    throw new \InvalidArgumentException('The last param must be callback function.');
                }
            }
            if (!array_key_exists($method, $this->methods)) {
                // new the method
                $methodClass = sprintf("\Web3\Methods\%s\%s", ucfirst($class[1]), ucfirst($name));
                $methodObject = new $methodClass($method, $arguments);
                $this->methods[$method] = $methodObject;
            } else {
                $methodObject = $this->methods[$method];
            }
            if ($methodObject->validate($arguments)) {
                $inputs = $methodObject->transform($arguments, $methodObject->inputFormatters);
                $methodObject->arguments = $inputs;
                $this->provider->send($methodObject, $callback);
            }
        }
    }

    /**
     * get
     * 
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $method = 'get' . ucfirst($name);

        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], []);
        }
        return false;
    }

    /**
     * set
     * 
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    public function __set($name, $value)
    {
        $method = 'set' . ucfirst($name);

        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], [$value]);
        }
        return false;
    }

    /**
     * getProvider
     * 
     * @return \Web3\Providers\Provider
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * setProvider
     * 
     * @param \Web3\Providers\Provider $provider
     * @return bool
     */
    public function setProvider($provider)
    {
        if ($provider instanceof Provider) {
            $this->provider = $provider;
            return true;
        }
        return false;
    }

    /**
     * batch
     * 
     * @param bool $status
     * @return void
     */
    public function batch($status)
    {
        $this->provider->batch($status);
    }
}
