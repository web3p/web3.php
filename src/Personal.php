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
use Web3\Validators\AddressValidator;
use Web3\Validators\StringValidator;
use Web3\Validators\QuantityValidator;
use Web3\Validators\TransactionValidator;

class Personal
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
        'personal_listAccounts' => [],
        'personal_newAccount' => [
            'params' => [
                [
                    'validators' => StringValidator::class
                ]
            ]
        ],
        'personal_unlockAccount' => [
            'params' => [
                [
                    'validators' => AddressValidator::class
                ], [
                    'validators' => StringValidator::class
                ], [
                    'default' => 300,
                    'validators' => QuantityValidator::class
                ]
            ]
        ],
        'personal_sendTransaction' => [
            'params' => [
                [
                    'validators' => TransactionValidator::class
                ], [
                    'validators' => StringValidator::class
                ]
            ]
        ]
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

        if (strtolower($class[1]) === 'personal' && preg_match('/^[a-zA-Z0-9]+$/', $name) === 1) {
            $method = strtolower($class[1]) . '_' . $name;

            if (!array_key_exists($method, $this->methods)) {
                throw new \RuntimeException('Unallowed rpc method: ' . $method);
            }
            $allowedMethod = $this->methods[$method];

            if ($this->provider->isBatch) {
                $callback = null;
            } else {
                $callback = array_pop($arguments);

                if (is_callable($callback) !== true) {
                    throw new \InvalidArgumentException('The last param must be callback function.');
                }
            }
            if (isset($allowedMethod['params']) && is_array($allowedMethod['params'])) {
                // validate params
                foreach ($allowedMethod['params'] as $key => $param) {
                    if (isset($param['validators'])) {
                        if (is_array($param['validators'])) {
                            $isError = true;

                            foreach ($param['validators'] as $rule) {
                                if (isset($arguments[$key])) {
                                    if (call_user_func([$rule, 'validate'], $arguments[$key]) === true) {
                                        $isError = false;
                                        break;
                                    }
                                } else {
                                    if (isset($param['default'])) {
                                        $arguments[$key] = $param['default'];
                                        $isError = false;
                                        break;
                                    }
                                }
                            }
                            if ($isError === true) {
                                throw new \RuntimeException('Wrong type of ' . $name . ' method argument ' . $key . '.');
                            }
                        } else {
                            if (!isset($arguments[$key]) || call_user_func([$param['validators'], 'validate'], $arguments[$key]) === false) {
                                if (isset($param['default']) && !isset($arguments[$key])) {
                                    $arguments[$key] = $param['default'];
                                } else {
                                    throw new \RuntimeException('Wrong type of ' . $name . ' method argument ' . $key . '.');
                                }
                            }
                        }
                    }
                }
            }
            $this->provider->send($method, $arguments, $callback);
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