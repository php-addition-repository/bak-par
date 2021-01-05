#!/usr/bin/env bash

set -e

if [[ "$#" -ne 3 ]]; then
    echo "Missing arguments"
    exit 1
fi

USER=${1}
GROUP=${2}
DIR=${3}

if [[ ! -d "${DIR}" ]]; then
    mkdir -p "${DIR}"
fi
chown "${USER}":"${GROUP}" "${DIR}"
chmod 775 "${DIR}"
