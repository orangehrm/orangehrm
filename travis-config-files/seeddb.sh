#!/bin/bash
if [[ ${SEED} == true ]];
then
    cd ../devTools/load/general; php load-employees.php
	cd ../recruitment; php load-candidates.php
fi

exit 0;
