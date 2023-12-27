<?php

/**
 * This file is part of web3.php package.
 * Reference: ccxt
 * 
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

 namespace Web3\RequestManagers;

use Ratchet\Client\Connector;
use React;
use React\Async;
use React\EventLoop\Loop;
use React\Promise\Deferred;
use React\Promise\Timer;
use React\Promise\Timer\TimeoutException;

// use ccxt\RequestTimeout;
// use ccxt\NetworkError;
// use ccxt\Exchange;

use Ratchet\RFC6455\Messaging\Frame;
use Ratchet\RFC6455\Messaging\Message;

use Exception;
use RuntimeException;

class NoOriginHeaderConnector extends Connector {
    public function generateRequest($url, array $subProtocols, array $headers) {
        return parent::generateRequest($url, $subProtocols, $headers)->withoutHeader('Origin');
    }
}

class WsClient {

    public $url;
    public $futures = array();
    public $subscriptions = array();
    public $rejections = array();
    public $options = array();

    public $on_message_callback;
    public $on_error_callback;
    public $on_close_callback;
    public $on_connected_callback;

    public $error;
    public $connectionStarted;
    public $connectionEstablished;
    public $timeout = 1;
    public $pingInterval;
    public $keepAlive = 30;
    public $maxPingPongMisses = 2.0;
    public $lastPong = null;
    public $ping = null;
    public $verbose = false; // verbose output
    public $gunzip = false;
    public $inflate = false;
    public $connection = null;
    public $connected;
    public $isConnected = false;
    public $noOriginHeader = true;
    public $heartbeat = null;

    // ratchet/pawl/reactphp stuff
    public $connector = null;

    // protected $deferred;
    protected $deferredMessages;


    // ------------------------------------------------------------------------

    // public function future($message_hash) {
    //     if (!array_key_exists($message_hash, $this->futures)) {
    //         $this->futures[$message_hash] = new Future();
    //     }
    //     $future = $this->futures[$message_hash];
    //     if (array_key_exists($message_hash, $this->rejections)) {
    //         $future->reject($this->rejections[$message_hash]);
    //         unset($this->rejections[$message_hash]);
    //     }
    //     return $future;
    // }

    // public function resolve($result, $message_hash) {
    //     if (array_key_exists($message_hash, $this->futures)) {
    //         $promise = $this->futures[$message_hash];
    //         $promise->resolve($result);
    //         unset($this->futures[$message_hash]);
    //     }
    //     return $result;
    // }

    // public function reject($result, $message_hash = null) {
    //     if ($message_hash) {
    //         if (array_key_exists($message_hash, $this->futures)) {
    //             $promise = $this->futures[$message_hash];
    //             unset($this->futures[$message_hash]);
    //             $promise->reject($result);
    //         } else {
    //             $this->rejections[$message_hash] = $result;
    //         }
    //     } else {
    //         $message_hashes = array_keys($this->futures);
    //         foreach ($message_hashes as $message_hash) {
    //             $this->reject($result, $message_hash);
    //         }
    //     }
    //     return $result;
    // }

    public function __construct(
            $url,
            callable $on_message_callback,
            callable $on_error_callback,
            callable $on_close_callback,
            callable $on_connected_callback,
            $config
        ) {

        $this->url = $url;

        $this->on_message_callback = $on_message_callback;
        $this->on_error_callback = $on_error_callback;
        $this->on_close_callback = $on_close_callback;
        $this->on_connected_callback = $on_connected_callback;

        // foreach ($config as $key => $value) {
        //     $this->{$key} =
        //         (property_exists($this, $key) && is_array($this->{$key}) && is_array($value)) ?
        //             array_replace_recursive($this->{$key}, $value) :
        //             $value;
        // }

        $deferred = new Deferred();
        $this->connected = $deferred->promise();
        $this->deferredMessages[] = $deferred;
    }

    public function set_ws_connector() {
        $react_default_connector = new React\Socket\Connector();
        if ($this->noOriginHeader) {
            $this->connector = new NoOriginHeaderConnector(Loop::get(), $react_default_connector);
        } else {
            $this->connector = new Connector(Loop::get(), $react_default_connector);
        }
    }

    public function create_connection() {
        return React\Async\async(function () {
            $timeout = $this->timeout;
            $headers = property_exists($this, 'options') && array_key_exists('headers', $this->options) ? $this->options['headers'] : [];
            $promise = call_user_func($this->connector, $this->url, [], $headers);
            var_dump(get_class($promise));
            Timer\timeout($promise, $timeout, Loop::get())->then(
                function($connection) {
                    $this->connection = $connection;
                    $this->connection->on('message', array($this, 'on_message'));
                    $this->connection->on('close', array($this, 'on_close'));
                    $this->connection->on('error', array($this, 'on_error'));
                    $this->connection->on('pong', array($this, 'on_pong'));
                    $this->isConnected = true;
                    $this->connectionEstablished = $this->milliseconds();
                    $deferred = array_shift($this->deferredMessages);
                    $deferred->resolve($this->url);
                    $this->set_ping_interval();
                    $on_connected_callback = $this->on_connected_callback;
                    $on_connected_callback($this);
                },
                function(\Exception $error) {
                    // the ordering of these exceptions is important
                    // since one inherits another
                    if ($error instanceof TimeoutException) {
                        // $error = new RequestTimeout($error->getMessage());
                    } else if ($error instanceof RuntimeException) {
                        // connection failed or rejected
                        // $error = new NetworkError($error->getMessage());
                    }
                    $this->on_error($error);
                }
            );
        })();
    }

    public function connect($backoff_delay = 0) {
        if (!$this->connection) {
            $this->connection = true;
            if ($backoff_delay) {
                $callback = array($this, 'create_connection');
                Loop::addTimer(((float)$backoff_delay), $callback);
            } else {
                $this->create_connection();
            }
        }
        return $this->connected;
    }

    public function send($data) {
        return React\Async\async(function () use ($data) {
            $this->connection->send($data);
            // $this->connection->send($data);
            $deferred = new Deferred();
            $this->deferredMessages[] = $deferred;
            return Async\await($deferred->promise());
        })();
    }

    public function close() {
        $this->connection->close();
    }

    public function on_pong($message) {
        $this->lastPong = $this->milliseconds();
    }

    public function on_error($error) {
        $this->error = $error;
        $on_error_callback = $this->on_error_callback;
        $on_error_callback($this, $error);
        $this->reset($error);
    }

    public function on_close($message) {
        $on_close_callback = $this->on_close_callback;
        $on_close_callback($this, $message);
        if (!$this->error) {
            // todo: exception types for server-side disconnects
            // $this->reset(new NetworkError($message));
        }
    }

    public function on_message(Message $message) {

        try {
            $message = (string) $message;
            // $message = json_decode($message, true);
        } catch (Exception $e) {
            // reset with a json encoding error?
        }

        try {
            // $on_message_callback = $this->on_message_callback;
            // $on_message_callback($this, $message);
            $deferred = array_shift($this->deferredMessages);
            if ($deferred) $deferred->resolve($message);
        } catch (Exception $error) {
            // $this->reject($error);
        }
    }

    public function reset($error) {
        $this->clear_ping_interval();
        // $this->reject($error);
    }

    public function set_ping_interval() {
        if ($this->keepAlive) {
            $delay = $this->keepAlive;
            $this->pingInterval = Loop::addPeriodicTimer($delay, array($this, 'on_ping_interval'));
        }
    }

    public function clear_ping_interval() {
        if ($this->pingInterval) {
            Loop::cancelTimer($this->pingInterval);
        }
    }

    public function milliseconds() {
        list($msec, $sec) = explode(' ', microtime());
        return (int)($sec . substr($msec, 2, 3));
    }

    public function on_ping_interval() {
        if ($this->keepAlive && $this->isConnected) {
            $now = $this->milliseconds();
            $this->lastPong = isset ($this->lastPong) ? $this->lastPong : $now;
            if (($this->lastPong + $this->keepAlive * $this->maxPingPongMisses) < $now) {
                // $this->on_error(new RequestTimeout('Connection to ' . $this->url . ' timed out due to a ping-pong keepalive missing on time'));
            } else {
                $this->connection->send(new Frame('', true, Frame::OP_PING));
            }
        }
    }
};
