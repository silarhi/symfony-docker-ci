#!/bin/bash

# ./docker/entrypoint.sh
set -e

# Performance: avoid to parse .env file from each requests as we use real env variable with docker
composer dump-env prod

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- apache2-foreground "$@"
fi

exec "$@"
