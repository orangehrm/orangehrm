#!/usr/bin/env bash

set -e
PROJECT_DIR=$(realpath $(dirname $(readlink -f $0))/../../)

# Install APIDOC library https://apidocjs.com/#install
apidoc -i $PROJECT_DIR/symfony/plugins/orangehrmRESTPlugin/doc -o $PROJECT_DIR/apiDoc/

echo "Find REST API doc build files here \`"$PROJECT_DIR/apiDoc"\`"
echo "Open \`file://"$PROJECT_DIR/apiDoc/index.html"\` in browser"
