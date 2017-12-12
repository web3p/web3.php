<?php

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