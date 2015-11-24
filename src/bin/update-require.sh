#!/usr/bin/env bash

DIR=`dirname "${BASH_SOURCE[0]}" `
. $DIR/.config

URL=$1

curl \
    -X POST \
    -d require='{"behat/behat":"2.5.5", "symfony/symfony" : "2.7.2", "doctrine/dbal" :"2.3.5"}' \
    -d repository=$URL \
    http://$IP:$PORT/dependencies -v
