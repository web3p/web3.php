# web3.php

[![Build Status](https://travis-ci.org/sc0Vu/web3.php.svg?branch=master)](https://travis-ci.org/sc0Vu/web3.php)
[![codecov](https://codecov.io/gh/sc0Vu/web3.php/branch/master/graph/badge.svg)](https://codecov.io/gh/sc0Vu/web3.php)

A php interface for interacting with the Ethereum blockchain and ecosystem.

# Install

```
composer sc0vu/web3.php
```


# Usage

### Web3

###### simple
```
use Web3/Web3;

$web3 = new Web3('http://localhost:8545');
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

###### batch
```
use Web3/Web3;

$web3 = new Web3('http://localhost:8545');
$web3->batch(true);
$web3->clientVersion();
$web3->hash('0x1234');
$web3->execute(function ($err, $data) {
    if ($err !== null) {
        // do something
        return;
    }
    // do something
})
```

### Eth

Todo

# License
MIT
