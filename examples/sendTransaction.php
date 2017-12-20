<?php

require('./exampleBase.php');

$eth = $web3->eth;

echo 'Eth Send Transaction' . PHP_EOL;
$eth->accounts(function ($err, $accounts) use ($eth) {
    if ($err !== null) {
        echo 'Error: ' . $err->getMessage();
        return;
    }
    $fromAccount = $accounts->result[0];
    $toAccount = $accounts->result[1];

    // get balance
    $eth->getBalance($fromAccount, function ($err, $balance) use($fromAccount) {
        if ($err !== null) {
            echo 'Error: ' . $err->getMessage();
            return;
        }
        echo $fromAccount . ' Balance: ' . $balance->result . PHP_EOL;
    });
    $eth->getBalance($toAccount, function ($err, $balance) use($toAccount) {
        if ($err !== null) {
            echo 'Error: ' . $err->getMessage();
            return;
        }
        echo $toAccount . ' Balance: ' . $balance->result . PHP_EOL;
    });

    // send transaction
    $eth->sendTransaction([
        'from' => $fromAccount,
        'to' => $toAccount,
        'value' => '0x11'
    ], function ($err, $transaction) use ($eth, $fromAccount, $toAccount) {
        if ($err !== null) {
            echo 'Error: ' . $err->getMessage();
            return;
        }
        echo 'Tx hash: ' . $transaction->result . PHP_EOL;

        // get balance
        $eth->getBalance($fromAccount, function ($err, $balance) use($fromAccount) {
            if ($err !== null) {
                echo 'Error: ' . $err->getMessage();
                return;
            }
            echo $fromAccount . ' Balance: ' . $balance->result . PHP_EOL;
        });
        $eth->getBalance($toAccount, function ($err, $balance) use($toAccount) {
            if ($err !== null) {
                echo 'Error: ' . $err->getMessage();
                return;
            }
            echo $toAccount . ' Balance: ' . $balance->result . PHP_EOL;
        });
    });
});