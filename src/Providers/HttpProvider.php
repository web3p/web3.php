<?php

/**
 * This file is part of web3.php package.
 * 
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Web3\Providers;

use Web3\Providers\Provider;
use Web3\Providers\IProvider;
use Web3\RequestManagers\RequestManager;

class HttpProvider extends Provider implements IProvider
{
    /**
     * methods
     * 
     * @var array
     */
    protected $methods = [];

    /**
     * construct
     * 
     * @param \Web3\RequestManagers\RequestManager $requestManager
     * @return void
     */
    public function __construct(RequestManager $requestManager)
    {
        parent::__construct($requestManager);
    }

    /**
     * send
     * 
     * @param \Web3\Methods\Method $method
     * @param callable $callback
     * @return void
     */
    public function send($method, $callback)
    {
        $payload = $method->toPayloadString();

        if (!$this->isBatch) {
            $proxy = function ($err, $res) use ($method, $callback) {
                if ($err !== null) {
                    return call_user_func($callback, $err, null);
                }
                if (!is_array($res)) {
                    $res = $method->transform([$res], $method->outputFormatters);
                    return call_user_func($callback, null, $res[0]);
                }
                $res = $method->transform($res, $method->outputFormatters);

                return call_user_func($callback, null, $res);
            };
            $this->requestManager->sendPayload($payload, $proxy);
        } else {
            $this->methods[] = $method;
            $this->batch[] = $payload;
        }
    }

    /**
     * batch
     * 
     * @param bool $status
     * @return void
     */
    public function batch($status)
    {
        $status = is_bool($status);

        $this->isBatch = $status;
    }

    /**
     * execute
     * 
     * @param callable $callback
     * @return void
     */
    public function execute($callback)
    {
        if (!$this->isBatch) {
            throw new \RuntimeException('Please batch json rpc first.');
        }
        $methods = $this->methods;
        $proxy = function ($err, $res) use ($methods, $callback) {
            if ($err !== null) {
                return call_user_func($callback, $err, null);
            }
            foreach ($methods as $key => $method) {
                if (isset($res[$key])) {
                    if (!is_array($res[$key])) {
                        $transformed = $method->transform([$res[$key]], $method->outputFormatters);
                        $res[$key] = $transformed[0];
                    } else {
                        $transformed = $method->transform($res[$key], $method->outputFormatters);
                        $res[$key] = $transformed;
                    }
                }
            }
            return call_user_func($callback, null, $res);
        };
        $this->requestManager->sendPayload('[' . implode(',', $this->batch) . ']', $proxy);
        $this->methods[] = [];
        $this->batch = [];
    }
}