#!/bin/bash


if [[ ${SEED} == true ]];
then
    echo "if statement"
    cd var/www/site/orangehrm/devTools/load/general; php load-employees.php
	cd var/www/site/orangehrm/devTools/load/recruitment; php load-candidates.php
fi

exit 0;
