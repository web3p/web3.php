<?php

/**
 * This file is part of web3.php package.
 * 
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Web3\Methods;

use InvalidArgumentException;
use Web3\Methods\IRPC;

class JSONRPC implements IRPC
{
    /**
     * id
     * 
     * @var int
     */
    protected $id = 0;

    /**
     * rpcVersion
     * 
     * @var string
     */
    protected $rpcVersion = '2.0';

    /**
     * method
     * 
     * @var string
     */
    protected $method = '';

    /**
     * arguments
     * 
     * @var array
     */
    protected $arguments = [];

    /**
     * construct
     * 
     * @param string $method
     * @param array $arguments
     * @return void
     */
    public function __construct($method, $arguments)
    {
        $this->method = $method;
        $this->arguments = $arguments;
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
     * __toString
     * 
     * @return string
     */
    public function __toString()
    {
        $payload = $this->toPayload();

        return json_encode($payload);
    }

    /**
     * setId
     * 
     * @param int $id
     * @return bool
     */
    public function setId($id)
    {
        if (!is_int($id)) {
            throw new InvalidArgumentException('Id must be integer.');
        }
        $this->id = $id;

        return true;
    }

    /**
     * getId
     * 
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * getRpcVersion
     * 
     * @return string
     */
    public function getRpcVersion()
    {
        return $this->rpcVersion;
    }

    /**
     * getMethod
     * 
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * setArguments
     * 
     * @param array $arguments
     * @return bool
     */
    public function setArguments($arguments)
    {
        if (!is_array($arguments)) {
            throw new InvalidArgumentException('Please use array when call setArguments.');
        }
        $this->arguments = $arguments;

        return true;
    }

    /**
     * getArguments
     * 
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * toPayload
     * 
     * @return array
     */
    public function toPayload()
    {
        if (empty($this->method) || !is_string($this->method)) {
            throw new InvalidArgumentException('Please check the method set properly.');
        }
        if (empty($this->id)) {
            $id = rand();
        } else {
            $id = $this->id;
        }
        $rpc = [
            'id' => $id,
            'jsonrpc' => $this->rpcVersion,
            'method' => $this->method
        ];

        if (count($this->arguments) > 0) {
            $rpc['params'] = $this->arguments;
        }
        return $rpc;
    }

    /**
     * toPayloadString
     * 
     * @return string
     */
    public function toPayloadString()
    {
        $payload = $this->toPayload();

        return json_encode($payload);
    }
}