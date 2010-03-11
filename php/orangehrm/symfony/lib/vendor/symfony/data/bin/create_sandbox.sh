#!/bin/sh

# creates a symfony sandbox for this symfony version

echo ">>> initialization"
DIR=../`dirname $0`
SANDBOX_NAME=sf_sandbox
APP_NAME=frontend
PHP=php

echo ">>> project initialization"
rm -rf ${SANDBOX_NAME}
mkdir ${SANDBOX_NAME}
cd ${SANDBOX_NAME}

echo ">>> create a new project and a new app"
${PHP} ${DIR}/../../data/bin/symfony generate:project ${SANDBOX_NAME}
${PHP} symfony generate:app ${APP_NAME}

echo ">>> add LICENSE"
cp ${DIR}/../../LICENSE LICENSE

echo ">>> add README"
cp ${DIR}/../../data/bin/SANDBOX_README README

echo ">>> add symfony command line for windows users"
cp ${DIR}/../../data/bin/symfony.bat symfony.bat

echo ">>> freeze symfony"
${PHP} symfony project:freeze ${DIR}/..

echo ">>> default to sqlite"
${PHP} symfony configure:database "sqlite:%SF_DATA_DIR%/sandbox.db"

echo ">>> add some empty files in empty directories"
touch apps/${APP_NAME}/modules/.sf apps/${APP_NAME}/i18n/.sf
touch cache/.sf doc/.sf log/.sf plugins/.sf
touch test/unit/.sf test/functional/.sf test/functional/${APP_NAME}/.sf
touch web/images/.sf web/js/.sf web/uploads/assets/.sf

touch data/sandbox.db
chmod 777 data
chmod 777 data/sandbox.db

echo ">>> create archive"
cd ..
tar --exclude=".svn" -zcpf ${SANDBOX_NAME}.tgz ${SANDBOX_NAME}
zip -rq ${SANDBOX_NAME}.zip ${SANDBOX_NAME} -x \*/\*.svn/\*

echo ">>> cleanup"
rm -rf ${SANDBOX_NAME}
