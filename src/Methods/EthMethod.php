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
use Web3\Methods\IMethod;
use Web3\Methods\JSONRPC;

class EthMethod extends JSONRPC implements IMethod
{
    /**
     * inputFormatters
     * 
     * @var array
     */
    protected $inputFormatters = [];

    /**
     * outputFormatters
     * 
     * @var array
     */
    protected $outputFormatters = [];

    /**
     * defaultValues
     * 
     * @var array
     */
    protected $defaultValues = [];

    /**
     * construct
     * 
     * @param string $method
     * @param array $arguments
     * @return void
     */
    // public function __construct($method='', $arguments=[])
    // {
    //     parent::__construct($method, $arguments);
    // }

    /**
     * getInputFormatters
     * 
     * @return array
     */
    public function getInputFormatters()
    {
        return $this->inputFormatters;
    }

    /**
     * getOutputFormatters
     * 
     * @return array
     */
    public function getOutputFormatters()
    {
        return $this->outputFormatters;
    }

    /**
     * transform
     * 
     * @param array $params
     * @param array $rules
     * @return array
     */
    public function transform($params, $rules)
    {
        if (!is_array($params)) {
            throw new InvalidArgumentException('Please use array params when call transform.');
        }
        if (!is_array($rules)) {
            throw new InvalidArgumentException('Please use array rules when call transform.');
        }
        if (count($params) < count($rules)) {
            if (!isset($this->defaultValues) || empty($this->defaultValues)) {
                throw new \InvalidArgumentException('The params are less than inputFormatters.');
            }
            $defaultValues = $this->defaultValues;

            foreach ($defaultValues as $key => $value) {
                if (!isset($params[$key])) {
                    $params[$key] = $value;
                }

            }
        }
        foreach ($params as $key => $param) {
            if (isset($rules[$key])) {
                $params[$key] = call_user_func([$rules[$key], 'format'], $param);
            }
        }
        return $params;
    }
}