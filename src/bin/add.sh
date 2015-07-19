#!/usr/bin/env bash

URL=$1

curl -X POST -d url=$URL 'http://192.168.59.103:49400/repositories' -v

