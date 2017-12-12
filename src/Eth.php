<?php

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
    private $methods = [
        'eth_protocolVersion' => [],
    ];

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

        if (strtolower($class[1]) === 'eth' && preg_match('/^[a-zA-Z0-9]+$/', $name) === 1) {
            $method = strtolower($class[1]) . '_' . $name;

            if (!array_key_exists($method, $this->methods)) {
                throw new \RuntimeException('Unallowed rpc method: ' . $method);
            }
            $allowedMethod = $this->methods[$method];

            if (isset($allowedMethod['params'])) {
                // validate params
                foreach ($allowedMethod['params'] as $key => $rule) {
                    if (call_user_func([$rule, 'validate'], $arguments[$key]) === false) {
                        throw new \RuntimeException('Wrong type of ' . $name . ' method argument ' . $key . '.');
                    }
                }
            }
            if ($this->provider->isBatch) {
                $this->provider->send($method, $arguments, null);
            } else {
                $callback = array_pop($arguments);

                if (is_callable($callback) !== true) {
                    throw new \InvalidArgumentException('The last param must be callback function.');
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
}