#!/usr/bin/env bash

HOST=$1
TOKEN=$2
IP=172.17.8.123

curl -X POST -d host=$HOST -d token=$TOKEN http://$IP:49400/tokens -v

