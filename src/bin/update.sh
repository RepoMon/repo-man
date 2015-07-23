#!/usr/bin/env bash

IP=172.17.8.123

curl -X POST http://$IP:49400/repositories/update -v

