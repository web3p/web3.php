#!/usr/bin/env bash

ganache-cli -g 0 -l 0 > /dev/null & vendor/bin/phpunit --coverage-clover=coverage.xml