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

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use RuntimeException as RPCException;
use Psr\Http\Message\ResponseInterface;
use React;
use React\Async;
use React\EventLoop\Loop;
use React\Http\Browser;
use React\Socket\Connector;
use Web3\RequestManagers\RequestManager;
use Web3\RequestManagers\IRequestManager;

class WsRequestManager extends RequestManager implements IRequestManager
{
    /**
     * client
     *
     * @var \Web3\RequestManagers\WsClient
     */
    protected $client;

    /**
     * construct
     *
     * @param string $host
     * @param int $timeout
     * @return void
     */
    public function __construct($host, $timeout = 1)
    {
        parent::__construct($host, $timeout);
        $this->client = new WsClient(
            $host,
            function ($obj, $message) {
            },
            function ($obj, $error) {
            },
            function ($obj, $close) {
            },
            function ($obj, $connected) {
            },
            [
                'timeout' => $timeout,
            ]
        );
        $this->client->set_ws_connector();
    }

    /**
     * sendPayload
     *
     * @param string $payload
     * @param callable $callback
     * @return void
     */
    public function sendPayload($payload, $callback)
    {
        if (!is_string($payload)) {
            throw new \InvalidArgumentException('Payload must be string.');
        }

        if (!$this->client->isConnected) {
            Async\await($this->client->connect(0));
        }

        $host = $this->host;
        $request = function () use ($host, $payload, $callback) {
            try {
                $res = Async\await($this->client->send($payload));
                $json = json_decode($res);

                if (JSON_ERROR_NONE !== json_last_error()) {
                    call_user_func($callback, new InvalidArgumentException('json_decode error: ' . json_last_error_msg()), null);
                }
                if (is_array($json)) {
                    // batch results
                    $results = [];
                    $errors = [];

                    foreach ($json as $result) {
                        if (property_exists($result,'result')) {
                            $results[] = $result->result;
                        } else {
                            if (isset($json->error)) {
                                $error = $json->error;
                                $errors[] = new RPCException(mb_ereg_replace('Error: ', '', $error->message), $error->code);
                            } else {
                                $results[] = null;
                            }
                        }
                    }
                    if (count($errors) > 0) {
                        call_user_func($callback, $errors, $results);
                    } else {
                        call_user_func($callback, null, $results);
                    }
                } elseif (property_exists($json,'result')) {
                    call_user_func($callback, null, $json->result);
                } else {
                    if (isset($json->error)) {
                        $error = $json->error;

                        call_user_func($callback, new RPCException(mb_ereg_replace('Error: ', '', $error->message), $error->code), null);
                    } else {
                        call_user_func($callback, new RPCException('Something wrong happened.'), null);
                    }
                }
            } catch (Exception $err) {
                call_user_func($callback, $err, null);
            }
        };

        if (function_exists('React\\Async\\async')) {
            $request = Async\async($request);
        }

        return Async\coroutine($request);
    }
}