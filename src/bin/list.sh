#!/usr/bin/env bash

IP=172.17.8.123

curl -X GET http://$IP:49400/repositories -v
