#!/bin/sh
# call this script from your symfony root dir!
# . plugins/sfPhpunitPlugin/_testme.sh
# to aprove the copy:
# . plugins/sfPhpunitPlugin/_testme.sh 1
if [ "$1" = "1" ]
	then
		cp plugins/sfPhpunitPlugin/test/sfPhpunitCreateFunctionalTestTaskTest.php test/unit/
		cp plugins/sfPhpunitPlugin/test/sfPhpunitCreateUnitTestTaskTest.php test/unit/
		echo ">> copied tests"
fi

echo ">> run the tests with:"
echo ">> php symfony test:unit sfPhpunitCreateUnitTestTask"
echo ">> php symfony test:unit sfPhpunitCreateFunctionalTestTask"