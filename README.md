# web3.php

[![Build Status](https://travis-ci.org/sc0Vu/web3.php.svg?branch=master)](https://travis-ci.org/sc0Vu/web3.php)
[![codecov](https://codecov.io/gh/sc0Vu/web3.php/branch/master/graph/badge.svg)](https://codecov.io/gh/sc0Vu/web3.php)

A php interface for interacting with the Ethereum blockchain and ecosystem.

# Install
```
composer require sc0vu/web3.php dev-master
```

Or you can add this line in composer.json

```
"sc0vu/web3.php: "dev-master"
```


# Usage

### New instance
```
use Web3\Web3;

$web3 = new Web3('http://localhost:8545');
```

### Using provider
```
use Web3\Web3;
use Web3\Providers\HttpProvider;

$web3 = new Web3(new HttpProvider('http://localhost:8545'));
```

### You can use callback to each rpc call:
```
$web3->clientVersion(function ($err, $version) {
    if ($err !== null) {
        // do something
        return;
    }
    if (isset($client->result)) {
        echo 'Client version: ' . $version->result;
    } else {
        // do something rpc error
    }
});
```

### Eth
```
use Web3\Web3;

$web3 = new Web3('http://localhost:8545');
$eth = $web3->eth;
```

Or

```
use Web3\Eth;

$eth = new Eth('http://localhost:8545');
```

### Net
```
use Web3\Web3;

$web3 = new Web3('http://localhost:8545');
$net = $web3->net;
```

Or

```
use Web3\Net;

$net = new Net('http://localhost:8545');
```

### Batch

web3

```
$web3->batch(true);
$web3->clientVersion();
$web3->hash('0x1234');
$web3->execute(function ($err, $data) {
    if ($err !== null) {
        // do something
        return;
    }
    // do something
});
```

eth

```
$eth->batch(true);
$eth->protocolVersion();
$eth->syncing();

$eth->provider->execute(function ($err, $data) {
    if ($err !== null) {
        // do something
        return;
    }
    // do something
});
```

net

```
$net->batch(true);
$net->version();
$net->listening();

$net->provider->execute(function ($err, $data) {
    if ($err !== null) {
        // do something
        return;
    }
    // do something
});
```

# API

Todo.

# License
MIT
