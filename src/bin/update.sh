#!/usr/bin/env bash

DIR=`dirname "${BASH_SOURCE[0]}" `
. $DIR/.config


curl -X POST http://$IP:$PORT/repositories/update -v

