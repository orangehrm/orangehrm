#!/bin/bash

function usage(){
    echo "A helper script for running our PHP tests (Web by default). Run this from within the Container."
    echo ""
    echo "usage: phpTests.sh [-P] [-s] [-c] <testpath> [<group>]"
    echo ""
    echo "args:"
    echo "    <testpath>"
    echo "        absolute path (or relative path from the target project directory) to the test directory or file"
    echo "    <group>"
    echo "        the group of tests to run (see https://phpunit.readthedocs.io/en/9.5/annotations.html#group)"
    echo ""
    echo "examples:"
    echo "    ./phpTests.sh /vagrant/Web/_tests/unit"
    echo "        runs unit tests for Web"
    echo ""
    echo "    cd /vagrant/Web && ../script/phpTests.sh _tests/unit"
    echo "        runs unit tests for Web using a relative path"
    echo ""
    echo "    ./phpTests.sh /vagrant/Web/_tests/integration"
    echo "        runs integration tests for Web"
    echo ""
    echo "    ./phpTests.sh -s /vagrant/Web-Secure/_tests/unit"
    echo "        runs unit tests for Web-Secure"
    echo ""
    echo "    ./phpTests.sh -P /vagrant/Web/_tests/integration"
    echo "        runs integration tests for Web in parallel"
    echo ""
    echo "    ./phpTests.sh /vagrant/Web/_tests/unit/lib/BankAccountTest.php"
    echo "        runs bank account unit tests for Web"
}

function runUnitTests {
    echo "Running unit tests"
    ../src/vendor/bin/phpunit $2  $1
}


if [ ! -e "$1" ]
then
    usage
    exit 1
fi



# Check if a group name was given
if [ -n "$2" ]
then
    group="--group $2"
fi

runUnitTests $1 "$group"
