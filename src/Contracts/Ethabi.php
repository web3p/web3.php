<?php

/**
 * This file is part of web3.php package.
 * 
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Web3\Contracts;

use Web3\Utils;

class Ethabi
{
    /**
     * construct
     * 
     * @return void
     */
    public function __contruct()
    {
        // 
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
     * encodeFunctionSignature
     * 
     * @param string|stdClass|array $functionName
     * @return string
     */
    public function encodeFunctionSignature($functionName)
    {
        if (!is_string($functionName)) {
            $functionName = Utils::jsonMethodToString($functionName);
        }
        return mb_substr(Utils::sha3($functionName), 0, 10);
    }

    /**
     * encodeEventSignature
     * 
     * @param string|stdClass|array $functionName
     * @return string
     */
    public function encodeEventSignature($functionName)
    {
        if (!is_string($functionName)) {
            $functionName = Utils::jsonMethodToString($functionName);
        }
        return Utils::sha3($functionName);
    }
}