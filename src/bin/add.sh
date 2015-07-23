#!/usr/bin/env bash

URL=$1
IP=172.17.8.123

curl -X POST -d url=$URL http://$IP:49400/repositories -v

