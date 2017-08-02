
jobno=${TRAVIS_BUILD_NUMBER}".3"
echo $jobno
echo ${TRAVIS_JOB_NUMBER}
if [ ${TRAVIS_JOB_NUMBER} = $jobno ]
then

	cd var/www/site/orangehrm/devTools/load/general; php load-employees.php
	cd var/www/site/orangehrm/devTools/load/recruitment; php load-candidates.php
fi


	
