<?php

namespace Web3;

use Web3\Providers\Provider;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\RequestManager;
use Web3\RequestManagers\HttpRequestManager;
use Web3\Validators\AddressValidator;
use Web3\Validators\TagValidator;
use Web3\Validators\QuantityValidator;
use Web3\Validators\BlockHashValidator;
use Web3\Validators\HexValidator;
use Web3\Validators\TransactionValidator;
use Web3\Validators\BooleanValidator;
use Web3\Validators\StringValidator;
use Web3\Validators\FilterValidator;
use Web3\Validators\NonceValidator;

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
        'eth_syncing' => [],
        'eth_coinbase' => [],
        'eth_mining' => [],
        'eth_hashrate' => [],
        'eth_gasPrice' => [],
        'eth_accounts' => [],
        'eth_blockNumber' => [],
        'eth_getBalance' => [
            'params'=> [
                [
                    'validators' => AddressValidator::class,
                ], [
                    'default' => 'latest',
                    'validators' => [
                        TagValidator::class, QuantityValidator::class,
                    ]
                ]
            ]
        ],
        'eth_getStorageAt' => [
            'params'=> [
                [
                    'validators' => AddressValidator::class,
                ], [
                    'validators' => QuantityValidator::class,
                ], [
                    'default' => 'latest',
                    'validators' => [
                        TagValidator::class, QuantityValidator::class,
                    ]
                ],
            ]
        ],
        'eth_getTransactionCount' => [
            'params'=> [
                [
                    'validators' => AddressValidator::class,
                ], [
                    'default' => 'latest',
                    'validators' => [
                        TagValidator::class, QuantityValidator::class,
                    ]
                ],
            ]
        ],
        'eth_getBlockTransactionCountByHash' => [
            'params' => [
                [
                    'validators' => BlockHashValidator::class
                ]
            ]
        ],
        'eth_getBlockTransactionCountByNumber' => [
            'params' => [
                [
                    'default' => 'latest',
                    'validators' => [
                        TagValidator::class, QuantityValidator::class,
                    ]
                ]
            ]
        ],
        'eth_getUncleCountByBlockHash' => [
            'params' => [
                [
                    'validators' => BlockHashValidator::class
                ]
            ]
        ],
        'eth_getUncleCountByBlockNumber' => [
            'params' => [
                [
                    'default' => 'latest',
                    'validators' => [
                        TagValidator::class, QuantityValidator::class,
                    ]
                ]
            ]
        ],
        'eth_getCode' => [
            'params'=> [
                [
                    'validators' => AddressValidator::class,
                ], [
                    'default' => 'latest',
                    'validators' => [
                        TagValidator::class, QuantityValidator::class,
                    ]
                ],
            ]
        ],
        'eth_sign' => [
            'params'=> [
                [
                    'validators' => AddressValidator::class,
                ], [
                    'validators' => HexValidator::class
                ],
            ]
        ],
        'eth_sendTransaction' => [
            'params' => [
                [
                    'validators' => TransactionValidator::class
                ]
            ]
        ],
        'eth_sendRawTransaction' => [
            'params' => [
                [
                    'validators' => HexValidator::class
                ]
            ]
        ],
        'eth_call' => [
            'params' => [
                [
                    'validators' => TransactionValidator::class
                ], [
                    'default' => 'latest',
                    'validators' => [
                        QuantityValidator::class, TagValidator::class
                    ]
                ]
            ]
        ],
        'eth_estimateGas' => [
            'params' => [
                [
                    'validators' => TransactionValidator::class
                ]
            ]
        ],
        'eth_getBlockByHash' => [
            'params' => [
                [
                    'validators' => BlockHashValidator::class
                ], [
                    'validators' => BooleanValidator::class
                ]
            ]
        ],
        'eth_getBlockByNumber' => [
            'params' => [
                [
                    'validators' => [
                        QuantityValidator::class, TagValidator::class
                    ]
                ], [
                    'validators' => BooleanValidator::class
                ]
            ]
        ],
        'eth_getTransactionByHash' => [
            'params' => [
                [
                    'validators' => BlockHashValidator::class
                ]
            ]
        ],
        'eth_getTransactionByBlockHashAndIndex' => [
            'params' => [
                [
                    'validators' => BlockHashValidator::class
                ], [
                    'validators' => QuantityValidator::class
                ]
            ]
        ],
        'eth_getTransactionByBlockNumberAndIndex' => [
            'params' => [
                [
                    'validators' => [
                        QuantityValidator::class, TagValidator::class
                    ]
                ], [
                    'validators' => QuantityValidator::class
                ]
            ]
        ],
        'eth_getTransactionReceipt' => [
            'params' => [
                [
                    'validators' => BlockHashValidator::class
                ]
            ]
        ],
        'eth_getUncleByBlockHashAndIndex' => [
            'params' => [
                [
                    'validators' => BlockHashValidator::class
                ], [
                    'validators' => QuantityValidator::class
                ]
            ]
        ],
        'eth_getUncleByBlockNumberAndIndex' => [
            'params' => [
                [
                    'validators' => [
                        QuantityValidator::class, TagValidator::class
                    ]
                ], [
                    'validators' => QuantityValidator::class
                ]
            ]
        ],
        'eth_getCompilers' => [],
        'eth_compileSolidity' => [
            'params' => [
                [
                    'validators' => StringValidator::class
                ]
            ]
        ],
        'eth_compileLLL' => [
            'params' => [
                [
                    'validators' => StringValidator::class
                ]
            ]
        ],
        'eth_compileSerpent' => [
            'params' => [
                [
                    'validators' => StringValidator::class
                ]
            ]
        ],
        'eth_newFilter' => [
            'params' => [
                [
                    'validators' => FilterValidator::class
                ]
            ]
        ],
        'eth_newBlockFilter' => [
            'params' => [
                [
                    'validators' => QuantityValidator::class
                ]
            ]
        ],
        'eth_newPendingTransactionFilter' => [],
        'eth_uninstallFilter' => [
            'params' => [
                [
                    'validators' => QuantityValidator::class
                ]
            ]
        ],
        'eth_getFilterChanges' => [
            'params' => [
                [
                    'validators' => QuantityValidator::class
                ]
            ]
        ],
        'eth_getFilterLogs' => [
            'params' => [
                [
                    'validators' => QuantityValidator::class
                ]
            ]
        ],
        'eth_getLogs' => [
            'params' => [
                [
                    'validators' => FilterValidator::class
                ]
            ]
        ],
        'eth_getWork' => [],
        'eth_submitWork' => [
            'params' => [
                [
                    'validators' => NonceValidator::class
                ], [
                    'validators' => BlockHashValidator::class
                ], [
                    'validators' => BlockHashValidator::class
                ]
            ]
        ],
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
}