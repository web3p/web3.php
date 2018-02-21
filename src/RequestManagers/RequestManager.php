<?php

/**
 * This file is part of web3.php package.
 * 
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Web3\RequestManagers;

class RequestManager
{
    /**
     * host
     * 
     * @var string
     */
    protected $host;

    /**
     * timeout
     * 
     * @var float
     */
    protected $timeout;
    
    /**
     * construct
     * 
     * @param string $host
     * @param float $timeout
     * @return void
     */
    public function __construct($host, $timeout=1)
    {
        $this->host = $host;
        $this->timeout = (float) $timeout;
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
     * getHost
     * 
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * getTimeout
     * 
     * @return float
     */
    public function getTimeout()
    {
        return $this->timeout;
    }
}