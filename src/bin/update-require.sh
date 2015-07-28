#!/usr/bin/env bash

URL=$1
IP=192.168.59.103

curl \
    -X POST \
    -d require='{"behat/behat":"2.5.5"}' \
    -d repository=https://github.com/timothy-r/testing \
    http://$IP:49400/dependencies/composer -v

