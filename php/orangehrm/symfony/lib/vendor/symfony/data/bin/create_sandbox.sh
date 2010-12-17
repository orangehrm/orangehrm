#!/bin/sh

# Creates a sandbox for this symfony version

echo ">>> sandbox initialization"
DIR=`pwd`/`dirname $0`
SANDBOX_NAME=sf_sandbox
PHP=php

rm -rf /tmp/${SANDBOX_NAME}
mkdir /tmp/${SANDBOX_NAME}
cd /tmp/${SANDBOX_NAME}

echo ">>> embed symfony"
mkdir -p lib/vendor/symfony
cp -R ${DIR}/../../* lib/vendor/symfony

echo ">>> create a new project and a new app"
${PHP} lib/vendor/symfony/data/bin/symfony generate:project ${SANDBOX_NAME} --installer=${DIR}/sandbox_installer.php

echo ">>> create archives"
cd ..
tar --exclude=".svn" -zcpf ${DIR}/../../${SANDBOX_NAME}.tgz ${SANDBOX_NAME}
zip -rq ${DIR}/../../${SANDBOX_NAME}.zip ${SANDBOX_NAME} -x \*/\*.svn/\*

echo ">>> cleanup"
rm -rf ${SANDBOX_NAME}
