<?php

require('./exampleBase.php');

$personal = $web3->personal;
$newAccount = '';

echo 'Personal Create Account and Unlock Account' . PHP_EOL;

// create account
$personal->newAccount('123456', function ($err, $account) use (&$newAccount) {
	if ($err !== null) {
	    echo 'Error: ' . $err->getMessage();
		return;
	}
	$newAccount = $account;
	echo 'New account: ' . $account . PHP_EOL;
});

$personal->unlockAccount($newAccount, '123456', function ($err, $unlocked) {
	if ($err !== null) {
		echo 'Error: ' . $err->getMessage();
		return;
	}
	if ($unlocked) {
        echo 'New account is unlocked!' . PHP_EOL;
	} else {
	    echo 'New account isn\'t unlocked' . PHP_EOL;
	}
});


// get balance
$web3->eth->getBalance($newAccount, function ($err, $balance) {
	if ($err !== null) {
		echo 'Error: ' . $err->getMessage();
		return;
	}
	echo 'Balance: ' . $balance->toString() . PHP_EOL;
});

// remember to lock account after transaction
$personal->lockAccount($newAccount, function ($err, $locked) {
	if ($err !== null) {
		echo 'Error: ' . $err->getMessage();
		return;
	}
	if ($locked) {
        echo 'New account is locked!' . PHP_EOL;
	} else {
	    echo 'New account isn\'t locked' . PHP_EOL;
	}
});
