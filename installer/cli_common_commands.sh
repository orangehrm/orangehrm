#!/bin/bash

INSTALL_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

cd $INSTALL_DIR
cd ../symfony/lib;

#composer update;
#composer dump-autoload -o;

cd ..;

php symfony orangehrm:publish-assets;
php symfony doctrine:build-model;
php symfony cc;

cd ../installer;
