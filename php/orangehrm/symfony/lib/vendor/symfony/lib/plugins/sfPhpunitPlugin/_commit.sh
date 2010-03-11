#!/bin/sh
# call this script from your symfony root dir!
# . plugins/sfPhpunitPlugin/_commit.sh 
cp test/unit/sfPhpunitCreateFunctionalTestTaskTest.php plugins/sfPhpunitPlugin/test/
cp test/unit/sfPhpunitCreateUnitTestTaskTest.php plugins/sfPhpunitPlugin/test/
echo ">> done"