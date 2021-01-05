#!/usr/bin/env bash

set -e

if [[ "$#" -ne 1 ]]; then
    echo "Missing arguments"
    exit 1
fi

TZ=${1}

ln -snf "/usr/share/zoneinfo/${TZ}" /etc/localtime
echo "${TZ}" > /etc/timezone
