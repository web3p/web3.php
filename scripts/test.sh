#!/usr/bin/env bash

ganache -g 0 -l 6000000 --wallet.seed test,test,test,test,test,test,test,test,test,test,test,test --miner.coinbase 0x4DABDacE120050c79E355A5Ba99047B955f37fFc > /dev/null & 
ganache_pid=$!
echo "Start ganache pid: $ganache_pid and sleep 3 seconds"

sleep 3

vendor/bin/phpunit --coverage-clover=coverage.xml
ret=$?

kill -9 $ganache_pid
echo "Kill ganache-cli"

exit $ret