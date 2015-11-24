#!/usr/bin/env bash

DIR=`dirname "${BASH_SOURCE[0]}" `
. $DIR/.config


curl -X GET http://$IP:$PORT/dependency/report -H "Accept: text/csv" -v

