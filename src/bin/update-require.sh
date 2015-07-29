#!/usr/bin/env bash

URL=$1
IP=172.17.8.123

curl \
    -X POST \
    -d require='{"behat/behat":"2.5.5", "symfony/symfony" : "2.7.2", "doctrine/dbal" :"2.3.5"}' \
    -d repository=$URL \
    http://$IP:49400/dependencies -v

