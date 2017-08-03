#!/bin/bash


if [[ ${SEED} == true ]];
then
    echo "if statement"
    php var/www/site/orangehrm/devTools/load/general/load-employees.php
	php var/www/site/orangehrm/devTools/load/recruitment/load-candidates.php
fi

exit 0;
