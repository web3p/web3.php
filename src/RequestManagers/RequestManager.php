<?php

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
     * construct
     * 
     * @param string $host
     * @return void
     */
    public function __construct($host)
    {
        $this->host = $host;

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
     * getHost
     * 
     * @return void
     */
    public function getHost()
    {
        return $this->host;
    }
}