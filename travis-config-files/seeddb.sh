#!/bin/bash
jobno="${TRAVIS_BUILD_NUMBER}.3"
echo "${TRAVIS_BUILD_NUMBER}.3"
echo "${TRAVIS_JOB_NUMBER}"
if [ "${TRAVIS_JOB_NUMBER}" == "$jobno" ]
then
    php var/www/site/orangehrm/devTools/load/genera/load-employees.php
	php var/www/site/orangehrm/devTools/load/recruitment/load-candidates.php
fi


	
