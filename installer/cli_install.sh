#!/bin/bash

INSTALL_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

count=$#
help="\nPlease set all the parameters mentioned below. Short codes need '-' symbol as prefix and name cantain '--' as prefix.
    \nHere mention example of set server name.\nEx:-h localhost or --HostName localhost \n\nName of the  server set using: -h | --HostName
    \nDatabase port number: -p | --port\nDatabase name: -d | --DatabaseName\nDefault admin user name: -a | --AdminUserName
    \nOrangehrm database user name (need only, if use new user to orangehrm): -o | --OrangehrmDatabaseUser
    \nPrivileged database user name: -u | --PrivilegedDatabaseUser\nCompany name(Optional, But dont set it as empty. Put - for not set): -cn| --CompanyName
    \n\nIs same user use to orangehrm set y (Set y/N)-sm| --UseTheSameOhrmDatabaseUser\nData encryption need (Set y/n) -e | --Encryption
    \nDatabase to use(Set y/N) -c | --IsExistingDatabase\n\nAfter you pass parameter you entered details will show in terminal.
    Hash mark (#) appear to didn't included values. Those details can fill next phase after accept license agreement. Passwords also need to fill there.\n"

if [ $# -eq 0 ]; then
    php "$INSTALL_DIR/cli_install.php" $count
elif [ $# -ge 2 ]; then

while [[ $# -gt 1 ]]
do
key="$1"

case $key in
    -h|--HostName)
    dbHostName="$2"
    shift # past argument
    ;;
    -p|--port)
    dbPort="$2"
    shift # past argument
    ;;
    -d|--DatabaseName)
    DatabaseName="$2"
    shift # past argument
    ;;
    -a|--AdminUserName)
    adminUserName="$2"
    shift # past argument
    ;;
    -o|--OrangehrmDatabaseUser)
    dbOhrmDbUserName="$2"
    shift # past argument
    ;;
    -u|--PrivilegedDatabaseUser)
    PrivilegedDatabaseUser="$2"
    shift # past argument
    ;;
    -e|--Encryption)
    Encryption="$2"
    shift # past argument
    ;;
    -c|--IsExistingDatabase)
    IsExistingDatabase="$2"
    shift # past argument
    ;;
    -sm|--UseTheSameOhrmDatabaseUser)
    UseTheSameOhrmDatabaseUser="$2"
    shift # past argument
    ;;
    -cn|--CompanyName)
    CompanyName="$2"
    shift # past argument
    ;;
    *)
            # unknown option
    ;;
esac
shift # past argument or value
done


if [[ -z "$dbHostName" ]];then
dbHostName="#"
fi

if [[ -z "$dbPort" ]];then
dbPort="#"
fi

if [[ -z "$DatabaseName" ]];then
DatabaseName="#"
fi

if [[ -z "$adminUserName" ]];then
adminUserName="#"
fi

if [[ -z "$dbOhrmDbUserName" ]];then
dbOhrmDbUserName="#"
fi

if [[ -z "$PrivilegedDatabaseUser" ]];then
PrivilegedDatabaseUser="#"
fi

if [[ -z "$Encryption" ]];then
Encryption="#"
fi

if [[ -z "$UseTheSameOhrmDatabaseUser" ]];then
UseTheSameOhrmDatabaseUser="#"
fi

if [[ -z "$IsExistingDatabase" ]];then
 IsExistingDatabase="#"
fi

if [[ -z "$CompanyName" ]];then
 CompanyName="#"
fi

echo "---------------------"

echo Host Name  = "${dbHostName}"
echo Database Port     = "${dbPort}"
echo Database Name   = "${DatabaseName}"

echo Admin User Name  = "${adminUserName}"
echo Orangehrm Database User     = "${dbOhrmDbUserName}"
echo Privileged Database User   = "${PrivilegedDatabaseUser}"
echo Encryption = "${Encryption}"

echo Is Existing Database  = "${IsExistingDatabase}"
echo Use The Same Ohrm Database User     = "${UseTheSameOhrmDatabaseUser}"

echo Company Name = "${CompanyName}"



php "$INSTALL_DIR/cli_install.php" $count $dbHostName $dbPort $DatabaseName $adminUserName $dbOhrmDbUserName $PrivilegedDatabaseUser $Encryption $IsExistingDatabase $UseTheSameOhrmDatabaseUser $CompanyName

else
echo -e $help
fi

