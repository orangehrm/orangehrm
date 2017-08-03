#!/bin/bash


if [[ ${SEED} == true ]];
then
    echo "if statement"
    ls
    cd ../devTools/load/general; php load-employees.php
	cd ../devTools/load/recruitment; php load-candidates.php
fi

exit 0;
