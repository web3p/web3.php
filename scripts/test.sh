#!/usr/bin/env bash

ganache-cli -g 0 -l 0 > /dev/null & 
ganachecli_pid=$!
echo "Start ganache-cli pid: $ganachecli_pid"

vendor/bin/phpunit --coverage-clover=coverage.xml

kill -9 $ganachecli_pid
echo "Kill ganache-cli"