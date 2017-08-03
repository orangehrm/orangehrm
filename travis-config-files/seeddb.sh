#!/bin/bash

set -ev
jobno=${TRAVIS_BUILD_NUMBER}.3
echo $jobno
echo $TRAVIS_JOB_NUMBER
echo "script"
if [ $TRAVIS_JOB_NUMBER == $jobno ];
then
    echo "if statement"
    #php var/www/site/orangehrm/devTools/load/general/load-employees.php
	#php var/www/site/orangehrm/devTools/load/recruitment/load-candidates.php
fi

exit 0;
	
