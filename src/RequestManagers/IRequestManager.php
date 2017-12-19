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

interface IRequestManager
{
    /**
     * sendPayload
     * 
     * @param string $payload
     * @param callable $callback
     * @return void
     */
    public function sendPayload($payload, $callback);
}