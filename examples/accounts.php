<?php

require('./exampleBase.php');

$eth = $web3->eth;

echo 'Eth Get Account and Balance' . PHP_EOL;
$eth->accounts(function ($err, $accounts) use ($eth) {
    if ($err !== null) {
        echo 'Error: ' . $err->getMessage();
        return;
    }
    foreach ($accounts as $account) {
        echo 'Account: ' . $account . PHP_EOL;

        $eth->getBalance($account, function ($err, $balance) {
            if ($err !== null) {
                echo 'Error: ' . $err->getMessage();
                return;
            }
            echo 'Balance: ' . $balance . PHP_EOL;
        });
    }
});