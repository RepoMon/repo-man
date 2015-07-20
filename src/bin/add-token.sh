#!/usr/bin/env bash

HOST=$1
TOKEN=$2

curl -X POST -d host=$HOST -d token=$TOKEN 'http://192.168.59.103:49400/tokens' -v

