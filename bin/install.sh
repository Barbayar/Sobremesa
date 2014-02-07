#!/bin/bash

BASEDIR=$(dirname $0)
cd "${BASEDIR}/.."

# installs composer
curl -sS https://getcomposer.org/installer | php

# installs dependencies
php composer.phar install

# makes data directory
mkdir data
chmod 777 data
