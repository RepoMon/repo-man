#!/usr/bin/env bash

DIR=`dirname "${BASH_SOURCE[0]}" `
. $DIR/.config

URL=$1

curl \
    -X POST \
    -d repository=$URL \
    http://$IP:$PORT/dependencies -v
