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

 namespace Web3\Providers;

use Ratchet\Client\Connector;
use React;
use React\Async;
use React\EventLoop\Loop;
use React\Promise\Deferred;
use React\Promise\Timer;
use React\Promise\Timer\TimeoutException;

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

    public $error;
    public $connectionEstablished;
    public $timeout = 1;
    // is server with feature ping/pong and keep alive?
    // can remove this if not?
    public $pingInterval;
    public $keepAlive = 30;
    public $maxPingPongMisses = 2.0;
    public $lastPong = null;
    public $ping = null;
    public $connection = null;
    public $connected;
    public $isConnected = false;
    public $noOriginHeader = true;
    public $headers = [];

    // ratchet/pawl/reactphp stuff
    public $connector = null;

    protected $deferredConnected;
    protected $deferredMessages = [];

    public function resolve($result) {
        $deferred = array_shift($this->deferredMessages);
        if ($deferred) {
            $deferred->resolve($result);
        }
    }

    public function reject($result) {
        foreach ($this->deferredMessages as $deferred) {
            $deferred->reject($result);
        }
    }

    public function __construct(
            $url,
            $timeout = 1,
            $keepAlive = 30,
            $maxPingPongMisses = 2.0,
            $noOriginHeader = true,
            $headers = []
        ) {

        $this->url = $url;

        $this->timeout = $timeout;
        $this->keepAlive = $keepAlive;
        $this->maxPingPongMisses = $maxPingPongMisses;
        $this->noOriginHeader = $noOriginHeader;
        $this->headers = $headers;

        $deferred = new Deferred();
        $this->connected = $deferred->promise();
        $this->deferredConnected = $deferred;
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
        $connect = function () {
            $timeout = $this->timeout;
            $headers = $this->headers;
            $promise = call_user_func($this->connector, $this->url, [], $headers);
            Timer\timeout($promise, $timeout, Loop::get())->then(
                function($connection) {
                    $this->connection = $connection;
                    $this->connection->on('message', array($this, 'on_message'));
                    $this->connection->on('close', array($this, 'on_close'));
                    $this->connection->on('error', array($this, 'on_error'));
                    $this->connection->on('pong', array($this, 'on_pong'));
                    $this->isConnected = true;
                    $this->connectionEstablished = $this->milliseconds();
                    $this->deferredConnected->resolve($this->url);
                    $this->set_ping_interval();
                },
                function(\Exception $error) {
                    // the ordering of these exceptions is important
                    // since one inherits another
                    if ($error instanceof TimeoutException) {
                    } else if ($error instanceof RuntimeException) {
                        // connection failed or rejected
                    }
                    $this->on_error($error);
                }
            );
        };
        if (function_exists('React\\Async\\async')) {
            $connect = Async\async($connect);
        }
        return Async\coroutine($connect);
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
        $send = function () use ($data) {
            $this->connection->send($data);
            $deferred = new Deferred();
            $this->deferredMessages[] = $deferred;
            return Async\await($deferred->promise());
        };
        if (function_exists('React\\Async\\async')) {
            $send = Async\async($send);
        }
        return Async\coroutine($send);
    }

    public function close() {
        $this->connection->close();
    }

    public function on_pong($message) {
        $this->lastPong = $this->milliseconds();
    }

    public function on_error($error) {
        $this->error = $error;
        $this->reset($error);
    }

    public function on_close($message) {
        if (!$this->error) {
            // todo: exception types for server-side disconnects
            $this->reset(new RuntimeException($message));
        }
    }

    public function on_message(Message $message) {

        try {
            $message = (string) $message;
        } catch (Exception $e) {
            // reset with a json encoding error?
        }

        try {
            $this->resolve($message);
        } catch (Exception $error) {
            $this->reject($error);
        }
    }

    public function reset($error) {
        $this->clear_ping_interval();
        $this->reject($error);
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
                $this->on_error(new RuntimeException('Connection to ' . $this->url . ' timed out due to a ping-pong keepalive missing on time'));
            } else {
                $this->connection->send(new Frame('', true, Frame::OP_PING));
            }
        }
    }
};
