#!/bin/bash

cd ../symfony/lib;

 composer update;
 composer dump-autoload -o;

 cd ..;

 php symfony orangehrm:publish-assets;
 php symfony doctrine:build-model;
 php symfony cc;

cd ../installer;
