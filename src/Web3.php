<?php

namespace Web3;

use Web3\Eth;
use Web3\Providers\Provider;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\RequestManager;
use Web3\RequestManagers\HttpRequestManager;

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
     * construct
     *
     * @param mixed string | Web3\Providers\Provider $provider
     * @return void
     */
    public function __construct($provider)
    {
        if (is_string($provider) && (filter_var($provider, FILTER_VALIDATE_URL) !== false)) {
            // check the uri schema
            if (preg_match('/^https?:\/\//', $provider) === 1) {
                $requestManeger = new HttpRequestManager($provider);

                $this->provider = new HttpProvider($requestManeger);
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
            return;
        }

        $class = explode('\\', get_class());

        if (strtolower($class[0]) === 'web3' && preg_match('/^[a-zA-Z0-9]+$/', $name) === 1) {
            $method = strtolower($class[1]) . '_' . $name;

            if ($this->provider->isBatch) {
                $this->provider->send($method, $arguments, null);
            } else {
                $callback = array_pop($arguments);

                if (is_callable($callback) !== true) {
                    throw new \RuntimeException('The last param must be callback function.');
                }
                $this->provider->send($method, $arguments, $callback);
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
    }

    /**
     * set
     * 
     * @param string $name
     * @param mixed $value
     * @return bool
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
     * @return void
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * setProvider
     * 
     * @param $provider
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
     * @return void
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