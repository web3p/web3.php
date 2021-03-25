#!/usr/bin/env bash

ganache-cli -g 0 -l 6000000 > /dev/null & 
ganachecli_pid=$!
echo "Start ganache-cli pid: $ganachecli_pid and sleep 3 seconds"

sleep 3

vendor/bin/phpunit --coverage-clover=coverage.xml
ret=$?

kill -9 $ganachecli_pid
echo "Kill ganache-cli"

exit $ret