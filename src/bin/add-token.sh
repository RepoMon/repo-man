#!/usr/bin/env bash

DIR=`dirname "${BASH_SOURCE[0]}" `
. $DIR/.config

HOST=$1
TOKEN=$2


curl -X POST -d host=$HOST -d token=$TOKEN http://$IP:$PORT/tokens -v

