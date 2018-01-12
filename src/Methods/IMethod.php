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

interface IMethod
{
    /**
     * transform
     * 
     * @param array &$data
     * @param array $rules
     * @return array
     */
    public function transform($data, $rules);
}