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

use Web3\Eth;
use Web3\Net;
use Web3\Personal;
use Web3\Shh;
use Web3\Utils;
use Web3\Providers\Provider;
use Web3\Providers\HttpProvider;
use Web3\Providers\WsProvider;

class Web3
{
    /**
     * provider
     *
     * @var \Web3\Providers\Provider
     */
    protected $provider;

    /**
     * eth
     * 
     * @var \Web3\Eth
     */
    protected $eth;

    /**
     * net
     * 
     * @var \Web3\Net
     */
    protected $net;

    /**
     * personal
     * 
     * @var \Web3\Personal
     */
    protected $personal;

    /**
     * shh
     * 
     * @var \Web3\Shh
     */
    protected $shh;

    /**
     * utils
     * 
     * @var \Web3\Utils
     */
    protected $utils;

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
        'web3_clientVersion', 'web3_sha3'
    ];

    /**
     * construct
     *
     * @param string|\Web3\Providers\Provider $provider
     * @param float $timeout
     * @return void
     */
    public function __construct($provider, $timeout = 1)
    {
        if (is_string($provider) && (filter_var($provider, FILTER_VALIDATE_URL) !== false)) {
            // check the uri schema
            if (preg_match('/^https?:\/\//', $provider) === 1) {
                $this->provider = new HttpProvider($provider, $timeout);
            } else if (preg_match('/^wss?:\/\//', $provider) === 1) {
                $this->provider = new WsProvider($provider, $timeout);
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

        $class = explode('\\', get_class($this));

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
                return $this->provider->send($methodObject, $callback);
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
     * getEth
     * 
     * @return \Web3\Eth
     */
    public function getEth()
    {
        if (!isset($this->eth)) {
            $eth = new Eth($this->provider);
            $this->eth = $eth;
        }
        return $this->eth;
    }

    /**
     * getNet
     * 
     * @return \Web3\Net
     */
    public function getNet()
    {
        if (!isset($this->net)) {
            $net = new Net($this->provider);
            $this->net = $net;
        }
        return $this->net;
    }

    /**
     * getPersonal
     * 
     * @return \Web3\Personal
     */
    public function getPersonal()
    {
        if (!isset($this->personal)) {
            $personal = new Personal($this->provider);
            $this->personal = $personal;
        }
        return $this->personal;
    }

    /**
     * getShh
     * 
     * @return \Web3\Shh
     */
    public function getShh()
    {
        if (!isset($this->shh)) {
            $shh = new Shh($this->provider);
            $this->shh = $shh;
        }
        return $this->shh;
    }

    /**
     * getUtils
     * 
     * @return \Web3\Utils
     */
    public function getUtils()
    {
        if (!isset($this->utils)) {
            $utils = new Utils;
            $this->utils = $utils;
        }
        return $this->utils;
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

        $this->provider->batch($status);
    }
}