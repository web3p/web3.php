<?php

/**
 * This file is part of the web3.php package.
 * 
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Web3;

use \InvalidArgumentException;
use \RuntimeException;
use Web3\Providers\Provider;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\RequestManager;
use Web3\RequestManagers\HttpRequestManager;
use Web3\Utils;
use Web3\Eth;
use Web3\Contracts\Ethabi;
use Web3\Contracts\Types\Address;
use Web3\Contracts\Types\Boolean;
use Web3\Contracts\Types\Bytes;
use Web3\Contracts\Types\DynamicBytes;
use Web3\Contracts\Types\Integer;
use Web3\Contracts\Types\Str;
use Web3\Contracts\Types\Uinteger;
use Web3\Validators\AddressValidator;
use Web3\Validators\HexValidator;
use Web3\Validators\StringValidator;
use Web3\Validators\TagValidator;
use Web3\Validators\QuantityValidator;
use Web3\Formatters\AddressFormatter;

class Contract
{
    /**
     * provider
     *
     * @var \Web3\Providers\Provider
     */
    protected $provider;

    /**
     * abi
     * 
     * @var array
     */
    protected $abi;

    /**
     * constructor
     * 
     * @var array
     */
    protected $constructor = [];

    /**
     * functions
     * 
     * @var array
     */
    protected $functions = [];

    /**
     * events
     * 
     * @var array
     */
    protected $events = [];

    /**
     * toAddress
     * 
     * @var string
     */
    protected $toAddress;

    /**
     * bytecode
     * 
     * @var string
     */
    protected $bytecode;

    /**
     * eth
     * 
     * @var \Web3\Eth
     */
    protected $eth;

    /**
     * ethabi
     * 
     * @var \Web3\Contracts\Ethabi
     */
    protected $ethabi;

    /**
     * defaultBlock
     *
     * @var mixed
     */
    protected $defaultBlock;

    /**
     * construct
     *
     * @param string|\Web3\Providers\Provider $provider
     * @param string|\stdClass|array $abi
     * @param mixed $defaultBlock
     * @return void
     */
    public function __construct($provider, $abi, $defaultBlock = 'latest')
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

        $abiArray = [];
        if (is_string($abi)) {
            $abiArray = json_decode($abi, true);

            if (JSON_ERROR_NONE !== json_last_error()) {
                throw new InvalidArgumentException('abi decode error: ' . json_last_error_msg());
            }
        } else {
            $abiArray = Utils::jsonToArray($abi);
        }
        foreach ($abiArray as $item) {
            if (isset($item['type'])) {
                if ($item['type'] === 'function') {
                    $this->functions[] = $item;
                } elseif ($item['type'] === 'constructor') {
                    $this->constructor = $item;
                } elseif ($item['type'] === 'event') {
                    $this->events[$item['name']] = $item;
                }
            }
        }
        if (TagValidator::validate($defaultBlock) || QuantityValidator::validate($defaultBlock)) {
            $this->defaultBlock = $defaultBlock;
        } else {
            $this->$defaultBlock = 'latest';
        }
        $this->abi = $abiArray;
        $this->eth = new Eth($this->provider);
        $this->ethabi = new Ethabi([
            'address' => new Address,
            'bool' => new Boolean,
            'bytes' => new Bytes,
            'dynamicBytes' => new DynamicBytes,
            'int' => new Integer,
            'string' => new Str,
            'uint' => new Uinteger,
        ]);
    }

    /**
     * call
     * 
     * @param string $name
     * @param array $arguments
     * @return void
     */
    // public function __call($name, $arguments)
    // {
    //     if (empty($this->provider)) {
    //         throw new RuntimeException('Please set provider first.');
    //     }
    //     $class = explode('\\', get_class());
    //     if (preg_match('/^[a-zA-Z0-9]+$/', $name) === 1) {
    //     }
    // }

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
     * @return $this
     */
    public function setProvider($provider)
    {
        if ($provider instanceof Provider) {
            $this->provider = $provider;
        }
        return $this;
    }

    /**
     * getDefaultBlock
     * 
     * @return string
     */
    public function getDefaultBlock()
    {
        return $this->defaultBlock;
    }

    /**
     * setDefaultBlock
     *
     * @param mixed $defaultBlock
     * @return $this
     */
    public function setDefaultBlock($defaultBlock)
    {
        if (TagValidator::validate($defaultBlock) || QuantityValidator::validate($defaultBlock)) {
            $this->defaultBlock = $defaultBlock;
        } else {
            $this->$defaultBlock = 'latest';
        }
        return $this;
    }

    /**
     * getFunctions
     * get an array of all methods in the loaded contract
     *
     * @return array
     */
    public function getFunctions()
    {
        return $this->functions;
    }

    /**
     * getEvents
     * get an array of all events (and their inputs) in the loaded contract
     *
     * @return array
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @return string
     */
    public function getToAddress()
    {
        return $this->toAddress;
    }

    /**
     * getConstructor
     *
     * @return array
     */
    public function getConstructor()
    {
        return $this->constructor;
    }

    /**
     * getAbi
     *
     * @return array
     */
    public function getAbi()
    {
        return $this->abi;
    }

    /**
     * setAbi
     * 
     * @param string $abi
     * @return $this
     */
    public function setAbi($abi)
    {
        return $this->abi($abi);
    }

    /**
     * getEthabi
     * 
     * @return array
     */
    public function getEthabi()
    {
        return $this->ethabi;
    }

    /**
     * getEth
     * 
     * @return \Web3\Eth
     */
    public function getEth()
    {
        return $this->eth;
    }

    /**
     * setBytecode
     * 
     * @param string $bytecode
     * @return $this
     */
    public function setBytecode($bytecode)
    {
        return $this->bytecode($bytecode);
    }

    /**
     * setToAddress
     * 
     * @param string $bytecode
     * @return $this
     */
    public function setToAddress($address)
    {
        return $this->at($address);
    }

    /**
     * at
     * set the address of the deployed contract to make calls to
     * 
     * @param string $address
     * @return $this
     */
    public function at($address)
    {
        if (AddressValidator::validate($address) === false) {
            throw new InvalidArgumentException('Please make sure the contract address is valid.');
        }
        $this->toAddress = AddressFormatter::format($address);

        return $this;
    }

    /**
     * bytecode
     * 
     * @param string $bytecode
     * @return $this
     */
    public function bytecode($bytecode)
    {
        if (HexValidator::validate($bytecode) === false) {
            throw new InvalidArgumentException('Please make sure the bytecode input is valid.');
        }
        $this->bytecode = Utils::stripZero($bytecode);

        return $this;
    }

    /**
     * abi
     * 
     * @param string $abi
     * @return $this
     */
    public function abi($abi)
    {
        if (StringValidator::validate($abi) === false) {
            throw new InvalidArgumentException('Please make sure the abi input is valid.');
        }
        $abiArray = [];
        if (is_string($abi)) {
            $abiArray = json_decode($abi, true);

            if (JSON_ERROR_NONE !== json_last_error()) {
                throw new InvalidArgumentException('abi decode error: ' . json_last_error_msg());
            }
        } else {
            $abiArray = Utils::jsonToArray($abi);
        }

        foreach ($abiArray as $item) {
            if (isset($item['type'])) {
                if ($item['type'] === 'function') {
                    $this->functions[] = $item;
                } elseif ($item['type'] === 'constructor') {
                    $this->constructor = $item;
                } elseif ($item['type'] === 'event') {
                    $this->events[$item['name']] = $item;
                }
            }
        }
        $this->abi = $abiArray;

        return $this;
    }

    /**
     * new
     * Deploy a new contract, along with any relevant parameters for its constructor.
     * 
     * @param mixed
     * @return void
     */
    public function new()
    {
        if (isset($this->constructor)) {
            $constructor = $this->constructor;
            $arguments = func_get_args();
            $callback = array_pop($arguments);

            $input_count = isset($constructor['inputs']) ? count($constructor['inputs']) : 0;
            if (count($arguments) < $input_count) {
                throw new InvalidArgumentException('Please make sure you have included all constructor parameters and a callback function.');
            }
            if (is_callable($callback) !== true) {
                throw new InvalidArgumentException('The last parameter must be a callback function.');
            }
            if (!isset($this->bytecode)) {
                throw new InvalidArgumentException('Please call bytecode($bytecode) before new().');
            }
            $params = array_splice($arguments, 0, $input_count);
            $data = $this->ethabi->encodeParameters($constructor, $params);
            $transaction = [];

            if (count($arguments) > 0) {
                $transaction = $arguments[0];
            }
            $transaction['data'] = '0x' . $this->bytecode . Utils::stripZero($data);

            $this->eth->sendTransaction($transaction, function ($err, $transaction) use ($callback){
                if ($err !== null) {
                    return call_user_func($callback, $err, null);
                }
                return call_user_func($callback, null, $transaction);
            });
        }
    }

    /**
     * send
     * Send inputs to a specific method of the deployed contract 
     * (interacts with chain data and can alter it: costs gas)
     * 
     * @param mixed
     * @return void
     */
    public function send()
    {
        if (isset($this->functions)) {
            $arguments = func_get_args();
            $method = array_splice($arguments, 0, 1)[0];
            $callback = array_pop($arguments);

            if (!is_string($method)) {
                throw new InvalidArgumentException('Please make sure the method name is supplied as a string as the first parameter.');
            }

            $functions = [];
            foreach ($this->functions as $function) {
                if ($function["name"] === $method) {
                    $functions[] = $function;
                }
            };
            if (count($functions) < 1) {
                throw new InvalidArgumentException('Please make sure the named method exists in the contract.');
            }
            if (is_callable($callback) !== true) {
                throw new InvalidArgumentException('The last parameter must be a callback function.');
            }

            // check the last one in arguments is transaction object
            $argsLen = count($arguments);
            $transaction = [];
            $hasTransaction = false;

            if ($argsLen > 0) {
                $transaction = $arguments[$argsLen - 1];
            }
            if (
                isset($transaction["from"]) ||
                isset($transaction["to"]) ||
                isset($transaction["gas"]) ||
                isset($transaction["gasPrice"]) ||
                isset($transaction["value"]) ||
                isset($transaction["data"]) ||
                isset($transaction["nonce"])
            ) {
                $hasTransaction = true;
            } else {
                $transaction = [];
            }

            $params = [];
            $data = "";
            $functionName = "";
            foreach ($functions as $function) {
                if ($hasTransaction) {
                    if ($argsLen - 1 !== count($function['inputs'])) {
                        continue;
                    } else {
                        $paramsLen = $argsLen - 1;
                    }
                } else {
                    if ($argsLen !== count($function['inputs'])) {
                        continue;
                    } else {
                        $paramsLen = $argsLen;
                    }
                }
                try {
                    $params = array_splice($arguments, 0, $paramsLen);
                    $data = $this->ethabi->encodeParameters($function, $params);
                    $functionName = Utils::jsonMethodToString($function);
                } catch (InvalidArgumentException $e) {
                    continue;
                }
                break;
            }
            if (empty($data) || empty($functionName)) {
                throw new InvalidArgumentException('Please make sure you have included all parameters of the method and a callback function.');
            }
            $functionSignature = $this->ethabi->encodeFunctionSignature($functionName);
            $transaction['to'] = $this->toAddress;
            $transaction['data'] = $functionSignature . Utils::stripZero($data);

            $this->eth->sendTransaction($transaction, function ($err, $transaction) use ($callback){
                if ($err !== null) {
                    return call_user_func($callback, $err, null);
                }
                return call_user_func($callback, null, $transaction);
            });
        }
    }

    /**
     * call
     * Call a specific method of the deployed contract 
     * (read-only, cannot alter chain data: does not cost gas)
     *
     * @param mixed
     * @return void
     */
    public function call()
    {
        if (isset($this->functions)) {
            $arguments = func_get_args();
            $method = array_splice($arguments, 0, 1)[0];
            $callback = array_pop($arguments);

            if (!is_string($method)) {
                throw new InvalidArgumentException('Please make sure the method name is supplied as a string as the first parameter.');
            }

            $functions = [];
            foreach ($this->functions as $function) {
                if ($function["name"] === $method) {
                    $functions[] = $function;
                }
            };
            if (count($functions) < 1) {
                throw new InvalidArgumentException('Please make sure the named method exists in the contract.');
            }
            if (is_callable($callback) !== true) {
                throw new InvalidArgumentException('The last parameter must be a callback function.');
            }

            // check the arguments
            $argsLen = count($arguments);
            $transaction = [];
            $defaultBlock = $this->defaultBlock;
            $params = [];
            $data = "";
            $functionName = "";
            foreach ($functions as $function) {
                try {
                    $paramsLen = count($function['inputs']);
                    $params = array_slice($arguments, 0, $paramsLen);
                    $data = $this->ethabi->encodeParameters($function, $params);
                    $functionName = Utils::jsonMethodToString($function);
                } catch (InvalidArgumentException $e) {
                    continue;
                }
                break;
            }
            if (empty($data) || empty($functionName)) {
                throw new InvalidArgumentException('Please make sure you have included all parameters of the method and a callback function.');
            }
            // remove arguments
            array_splice($arguments, 0, $paramsLen);
            $argsLen -= $paramsLen;

            if ($argsLen > 1) {
                $defaultBlock = $arguments[$argsLen - 1];
                $transaction = $arguments[$argsLen - 2];
            } else if ($argsLen > 0) {
                if (is_array($arguments[$argsLen - 1])) {
                    $transaction = $arguments[$argsLen - 1];
                } else {
                    $defaultBlock = $arguments[$argsLen - 1];
                }
            }
            if (!TagValidator::validate($defaultBlock) && !QuantityValidator::validate($defaultBlock)) {
                $defaultBlock = $this->defaultBlock;
            }
            if (
                !is_array($transaction) &&
                !isset($transaction["from"]) &&
                !isset($transaction["to"]) &&
                !isset($transaction["gas"]) &&
                !isset($transaction["gasPrice"]) &&
                !isset($transaction["value"]) &&
                !isset($transaction["data"]) &&
                !isset($transaction["nonce"])
            ) {
                $transaction = [];
            }
            $functionSignature = $this->ethabi->encodeFunctionSignature($functionName);
            $transaction['to'] = $this->toAddress;
            $transaction['data'] = $functionSignature . Utils::stripZero($data);

            $this->eth->call($transaction, $defaultBlock, function ($err, $transaction) use ($callback, $function){
                if ($err !== null) {
                    return call_user_func($callback, $err, null);
                }
                $decodedTransaction = $this->ethabi->decodeParameters($function, $transaction);

                return call_user_func($callback, null, $decodedTransaction);
            });
        }
    }

    /**
     * estimateGas
     * Estimate function gas.
     * 
     * @param mixed
     * @return void
     */
    public function estimateGas()
    {
        if (isset($this->functions) || isset($this->constructor)) {
            $arguments = func_get_args();
            $callback = array_pop($arguments);

            if (empty($this->toAddress) && !empty($this->bytecode)) {
                $constructor = $this->constructor;

                if (count($arguments) < count($constructor['inputs'])) {
                    throw new InvalidArgumentException('Please make sure you have included all constructor parameters and a callback function.');
                }
                if (is_callable($callback) !== true) {
                    throw new InvalidArgumentException('The last parameter must be a callback function.');
                }
                if (!isset($this->bytecode)) {
                    throw new InvalidArgumentException('Please call bytecode($bytecode) before estimateGas().');
                }
                $params = array_splice($arguments, 0, count($constructor['inputs']));
                $data = $this->ethabi->encodeParameters($constructor, $params);
                $transaction = [];

                if (count($arguments) > 0) {
                    $transaction = $arguments[0];
                }
                $transaction['data'] = '0x' . $this->bytecode . Utils::stripZero($data);
            } else {
                $method = array_splice($arguments, 0, 1)[0];

                if (!is_string($method)) {
                    throw new InvalidArgumentException('Please make sure the method name is supplied as a string as the first parameter.');
                }
    
                $functions = [];
                foreach ($this->functions as $function) {
                    if ($function["name"] === $method) {
                        $functions[] = $function;
                    }
                };
                if (count($functions) < 1) {
                    throw new InvalidArgumentException('Please make sure the named method exists in the contract.');
                }
                if (is_callable($callback) !== true) {
                    throw new InvalidArgumentException('The last parameter must be a callback function.');
                }
    
                // check the last one in arguments is transaction object
                $argsLen = count($arguments);
                $transaction = [];
                $hasTransaction = false;

                if ($argsLen > 0) {
                    $transaction = $arguments[$argsLen - 1];
                }
                if (
                    isset($transaction["from"]) ||
                    isset($transaction["to"]) ||
                    isset($transaction["gas"]) ||
                    isset($transaction["gasPrice"]) ||
                    isset($transaction["value"]) ||
                    isset($transaction["data"]) ||
                    isset($transaction["nonce"])
                ) {
                    $hasTransaction = true;
                } else {
                    $transaction = [];
                }

                $params = [];
                $data = "";
                $functionName = "";
                foreach ($functions as $function) {
                    if ($hasTransaction) {
                        if ($argsLen - 1 !== count($function['inputs'])) {
                            continue;
                        } else {
                            $paramsLen = $argsLen - 1;
                        }
                    } else {
                        if ($argsLen !== count($function['inputs'])) {
                            continue;
                        } else {
                            $paramsLen = $argsLen;
                        }
                    }
                    try {
                        $params = array_splice($arguments, 0, $paramsLen);
                        $data = $this->ethabi->encodeParameters($function, $params);
                        $functionName = Utils::jsonMethodToString($function);
                    } catch (InvalidArgumentException $e) {
                        continue;
                    }
                    break;
                }
                if (empty($data) || empty($functionName)) {
                    throw new InvalidArgumentException('Please make sure you have included all parameters of the method and a callback function.');
                }
                $functionSignature = $this->ethabi->encodeFunctionSignature($functionName);
                $transaction['to'] = $this->toAddress;
                $transaction['data'] = $functionSignature . Utils::stripZero($data);
            }

            $this->eth->estimateGas($transaction, function ($err, $gas) use ($callback) {
                if ($err !== null) {
                    return call_user_func($callback, $err, null);
                }
                return call_user_func($callback, null, $gas);
            });
        }
    }

    /**
     * getData
     * Get the contract method's call data.
     * With this function, you can send signed contract method transactions.
     * 1. Get the method data with parameters.
     * 2. Sign the data with user private key.
     * 3. Call sendRawTransaction.
     * 
     * @param mixed
     * @return void
     */
    public function getData()
    {
        if (isset($this->functions) || isset($this->constructor)) {
            $arguments = func_get_args();
            $functionData = '';

            if (empty($this->toAddress) && !empty($this->bytecode)) {
                $constructor = $this->constructor;

                if (count($arguments) < count($constructor['inputs'])) {
                    throw new InvalidArgumentException('Please make sure you have included all constructor parameters and a callback function.');
                }
                if (!isset($this->bytecode)) {
                    throw new InvalidArgumentException('Please call bytecode($bytecode) before getData().');
                }
                $params = array_splice($arguments, 0, count($constructor['inputs']));
                $data = $this->ethabi->encodeParameters($constructor, $params);
                $functionData = $this->bytecode . Utils::stripZero($data);
            } else {
                $method = array_splice($arguments, 0, 1)[0];

                if (!is_string($method)) {
                    throw new InvalidArgumentException('Please make sure the method name is supplied as a string as the first parameter.');
                }
    
                $functions = [];
                foreach ($this->functions as $function) {
                    if ($function["name"] === $method) {
                        $functions[] = $function;
                    }
                };
                if (count($functions) < 1) {
                    throw new InvalidArgumentException('Please make sure the named method exists in the contract.');
                }
    
                $params = $arguments;
                $data = "";
                $functionName = "";
                foreach ($functions as $function) {
                    if (count($arguments) !== count($function['inputs'])) {
                        continue;
                    }
                    try {
                        $data = $this->ethabi->encodeParameters($function, $params);
                        $functionName = Utils::jsonMethodToString($function);
                    } catch (InvalidArgumentException $e) {
                        continue;
                    }
                    break;
                }
                if (empty($data) || empty($functionName)) {
                    throw new InvalidArgumentException('Please make sure you have included all parameters of the method and a callback function.');
                }
                $functionSignature = $this->ethabi->encodeFunctionSignature($functionName);
                $functionData = Utils::stripZero($functionSignature) . Utils::stripZero($data);
            }
            return $functionData;
        }
    }

    /**
     * getEventLogs
     * 
     * @param string $eventName
     * @param string|int $fromBlock
     * @param string|int $toBlock
     * @return array
     */
    public function getEventLogs(string $eventName, $fromBlock = 'latest', $toBlock = 'latest')
    {
        //try to ensure block numbers are valid together
        if ($fromBlock !== 'latest') {
            if (!is_int($fromBlock) || $fromBlock < 1) {
                throw new InvalidArgumentException('Please make sure fromBlock is a valid block number');
            } else if ($toBlock !== 'latest' && $fromBlock > $toBlock) {
                throw new InvalidArgumentException('Please make sure fromBlock is equal or less than toBlock');
            }
        }

        if ($toBlock !== 'latest') {
            if (!is_int($toBlock) || $toBlock < 1) {
                throw new InvalidArgumentException('Please make sure toBlock is a valid block number');
            } else if ($fromBlock === 'latest') {
                throw new InvalidArgumentException('Please make sure toBlock is equal or greater than fromBlock');
            }
        }

        $eventLogData = [];

        //ensure the event actually exists before trying to filter for it
        if (!array_key_exists($eventName, $this->events)) {
            throw new InvalidArgumentException("'{$eventName}' does not exist in the ABI for this contract");
        }

        //indexed and non-indexed event parameters must be treated separately
        //indexed parameters are stored in the 'topics' array
        //non-indexed parameters are stored in the 'data' value
        $eventParameterNames = [];
        $eventParameterTypes = [];
        $eventIndexedParameterNames = [];
        $eventIndexedParameterTypes = [];

        foreach ($this->events[$eventName]['inputs'] as $input) {
            if ($input['indexed']) {
                $eventIndexedParameterNames[] = $input['name'];
                $eventIndexedParameterTypes[] = $input['type'];
            } else {
                $eventParameterNames[] = $input['name'];
                $eventParameterTypes[] = $input['type'];
            }
        }

        $numEventIndexedParameterNames = count($eventIndexedParameterNames);

        //filter through log data to find any logs which match this event (topic) from
        //this contract, between these specified blocks (defaulting to the latest block only)
        $this->eth->getLogs([
            'fromBlock' => (is_int($fromBlock)) ? '0x' . dechex($fromBlock) : $fromBlock,
            'toBlock' => (is_int($toBlock)) ? '0x' . dechex($toBlock) : $toBlock,
            'topics' => [$this->ethabi->encodeEventSignature($this->events[$eventName])],
            'address' => $this->toAddress
        ],
        function ($error, $result) use (&$eventLogData, $eventParameterTypes, $eventParameterNames, $eventIndexedParameterTypes, $eventIndexedParameterNames) {
            if ($error !== null) {
                throw new RuntimeException($error->getMessage());
            }

            foreach ($result as $object) {
                //decode the data from the log into the expected formats, with its corresponding named key
                $decodedData = array_combine($eventParameterNames, $this->ethabi->decodeParameters($eventParameterTypes, $object->data));

                //decode the indexed parameter data
                for ($i = 0; $i < $numEventIndexedParameterNames; $i++) {
                    //topics[0] is the event signature, so we start from $i + 1 for the indexed parameter data
                    $decodedData[$eventIndexedParameterNames[$i]] = $this->ethabi->decodeParameters([$eventIndexedParameterTypes[$i]], $object->topics[$i + 1])[0];
                }

                //include block metadata for context, along with event data
                $eventLogData[] = [
                    'transactionHash' => $object->transactionHash,
                    'blockHash' => $object->blockHash,
                    'blockNumber' => hexdec($object->blockNumber),
                    'data' => $decodedData
                ];
            }
        });

        return $eventLogData;
    }
}
