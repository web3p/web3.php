<?php

require('../vendor/autoload.php');

use Web3\Web3;

$web3 = new Web3('http://192.168.99.100:8545');
$eth = $web3->eth;

echo 'Eth Get Account and Balance' . PHP_EOL;
$eth->accounts(function ($err, $accounts) use ($eth) {
    if ($err !== null) {
        echo 'Error: ' . $err->getMessage();
        return;
    }
    foreach ($accounts->result as $account) {
        echo 'Account: ' . $account . PHP_EOL;

        $eth->getBalance($account, function ($err, $balance) {
            if ($err !== null) {
                echo 'Error: ' . $err->getMessage();
                return;
            }
            echo 'Balance: ' . $balance->result . PHP_EOL;
        });
    }
});