<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
 */
require('Messages.php');

class DetailsHandler
{
    private $configIni;
    private $organizationEmail = null;
    private $contactNumber = null;

    /**
     * Load the config.ini file
     * @return array|bool
     */
    private function getConfigIni() {
        if (is_null($this->configIni)) {
            $this->configIni = parse_ini_file("config.ini");
        }
        return $this->configIni;
    }

    function getMessages()
    {
        if (!isset($messageList)) {
            $messageList = new Messages();
        }
        return $messageList;
    }


    public function checkDetailsValidation()
    {

        $configurationDataSet = $this->getConfigIni();

        $dbHostName = $configurationDataSet["HostName"];
        $dbHostPort = $configurationDataSet["HostPort"];
        $dbName = $configurationDataSet["DatabaseName"];

        $adminUserName = $configurationDataSet["AdminUserName"];
        $adminPassword = $configurationDataSet["AdminPassword"];

        $dbOHRMUserName = $configurationDataSet["OrangehrmDatabaseUser"];
        $dbOHRMPassword = $configurationDataSet["OrangehrmDatabasePassword"];

        $dbUserName = $configurationDataSet["PrivilegedDatabaseUser"];
        $dbPassword = $configurationDataSet["PrivilegedDatabasePassword"];

        $databaseRootPassword = $configurationDataSet["DatabaseRootPassword"];

        $encryption = $configurationDataSet["Encryption"]; //"true = Active"/"Failed"

        $dbCreateMethod = $configurationDataSet["IsExistingDatabase"]; //existing/new
        $sameOhrmUser = $configurationDataSet["UseTheSameOhrmDatabaseUser"];

        $organizationName = $configurationDataSet["organizationName"];
        $adminEmployeeFirstName = $configurationDataSet["adminEmployeeFirstName"];
        $adminEmployeeLastName = $configurationDataSet["adminEmployeeLastName"];

        $this->setConfigurationFromParameter($dbHostName, $dbHostPort, $dbName, $adminUserName, $adminPassword, $dbOHRMUserName, $dbOHRMPassword, $dbUserName, $dbPassword, $databaseRootPassword, $encryption, $dbCreateMethod, $sameOhrmUser, $organizationName, $adminEmployeeFirstName, $adminEmployeeLastName);
    }

    /**
     * Get the organization name
     * @return mixed
     */
    public function getOrganizationName()
    {
        if ($_SESSION['defUser']['organizationName']) {
            return $_SESSION['defUser']['organizationName'];
        }

        $configurationDataSet = $this->getConfigIni();
        return $configurationDataSet["organizationName"];
    }

    /**
     * Get the Admin Employee First name
     * @return mixed
     */
    public function getAdminEmployeeFirstName()
    {
        $configurationDataSet = $this->getConfigIni();

        if ($_SESSION['defUser']['adminEmployeeFirstName']) {
            return $_SESSION['defUser']['adminEmployeeFirstName'];
        }
        return $configurationDataSet["adminEmployeeFirstName"];
    }

    /**
     * Get the Admin Employee last name
     * @return mixed
     */
    public function getAdminEmployeeLastName()
    {
        $configurationDataSet = $this->getConfigIni();

        if ($_SESSION['defUser']['adminEmployeeLastName']) {
            return $_SESSION['defUser']['adminEmployeeLastName'];
        }
        return $configurationDataSet["adminEmployeeLastName"];
    }

    /**
     * Get the Admin Employee organization Email
     * @return mixed|null|string
     */
    public function getOrganizationEmailAddress()
    {
        $configurationDataSet = $this->getConfigIni();

        $this->organizationEmail = $this->organizationEmail ? $this->organizationEmail : $configurationDataSet["organizationEmailAddress"];

        if (!$this->isValidUserInput("Organization Email Address ", $this->organizationEmail)) {
            $this->organizationEmail = $this->takeUserInput("Please enter valid Email Address (eg:test@test.com):");
            $this->getOrganizationEmailAddress();
        }

        return $this->organizationEmail;
    }

    /**
     * Get the Admin employee contact number
     * @return mixed|null|string
     */
    public function getContactNumber()
    {
        $configurationDataSet = $this->getConfigIni();
        $this->contactNumber = $this->contactNumber ? $this->contactNumber : $configurationDataSet["contactNumber"];

        if (!$this->isValidUserInput("contact number", $this->contactNumber)) {
            $this->contactNumber = $this->takeUserInput("Please enter valid contact number :");

            if (!$this->contactNumber) {
                $this->contactNumber = 'Not captured';
            }
            $this->getContactNumber();
        }
        return $this->contactNumber;
    }

    /**
     * Get the host name
     * @return mixed
     */
    public function getHost()
    {
        $configurationDataSet = $this->getConfigIni();

        if ($_SESSION['dbInfo']['dbHostName']) {
            return $_SESSION['dbInfo']['dbHostName'];
        }
        return $configurationDataSet["HostName"];
    }

    /**
     * Get the port number
     * @return mixed
     */
    public function getPort()
    {
        $configurationDataSet = $this->getConfigIni();

        if ($_SESSION['dbInfo']['dbHostPort']) {
            return $_SESSION['dbInfo']['dbHostPort'];
        }
        return $configurationDataSet["HostPort"];
    }

    /**
     * Get the Admin user name
     * @return mixed
     */
    public function getOrangehrmDatabaseUser()
    {
        $configurationDataSet = $this->getConfigIni();

        if ($_SESSION['dbInfo']['dbUserName']) {
            return $_SESSION['dbInfo']['dbUserName'];
        }

        if ($_SESSION['dbInfo']['dbOHRMUserName']) {
            return $_SESSION['dbInfo']['dbOHRMUserName'];
        }
        return $configurationDataSet["OrangehrmDatabaseUser"];
    }

    /**
     * Get the database password
     * @return mixed
     */
    public function getOrangehrmDatabasePassword()
    {
        $configurationDataSet = $this->getConfigIni();

        if ($_SESSION['dbInfo']['dbPassword']) {
            return $_SESSION['dbInfo']['dbPassword'];
        }

        if ($_SESSION['dbInfo']['dbOHRMPassword']) {
            return $_SESSION['dbInfo']['dbOHRMPassword'];
        }
        return $configurationDataSet["OrangehrmDatabasePassword"];
    }

    /**
     * Get the database name
     * @return mixed
     */
    public function getDatabaseName()
    {
        $configurationDataSet = $this->getConfigIni();

        if ($_SESSION['dbInfo']['dbName']) {
            return $_SESSION['dbInfo']['dbName'];
        }
        return $configurationDataSet["DatabaseName"];
    }

    /**
     * Get the Admin username
     * @return mixed
     */
    public function getAdminUserName()
    {
        $configurationDataSet = $this->getConfigIni();

        if ($_SESSION['defUser']['AdminUserName']) {
            return $_SESSION['defUser']['AdminUserName'];
        }
        return $configurationDataSet["AdminUserName"];
    }

    /**
     * Get the admin password
     * @return mixed
     */
    public function getAdminPassword()
    {
        $configurationDataSet = $this->getConfigIni();

        if ($_SESSION['defUser']['AdminPassword']) {
            return $_SESSION['defUser']['AdminPassword'];
        }
        return $configurationDataSet["AdminPassword"];
    }

    /*
     *get details related Database connection and validation parts.
     *details assign from with config.ini file or user inputs.
     *$_SESSION['dbInfo'] validation part will do in BasicConfiguration.php file , BasicConfiguration class->dbConfigurationCheck()
     */
    public function setConfigurationFromParameter($hostName, $hostPort, $databaseName, $adminUserName, $adminPassword, $orangehrmDatabaseUser, $orangehrmDatabasePassword, $privilegedDatabaseUser, $privilegedDatabasePassword, $databaseRootPassword, $encryption, $DatabaseToUse, $sameOhrmUser, $organizationName, $adminEmployeeFirstName, $adminEmployeeLastName)
    {
        $_SESSION['dbInfo']['dbHostName'] = $this->isFillInConfig($hostName, "Host name ");
        $_SESSION['dbInfo']['dbHostPort'] = $this->isFillInConfig($hostPort, "Port number ");
        $_SESSION['dbInfo']['dbName'] = $this->isFillInConfig($databaseName, "Database name ");


        //boolean configurations
        $_SESSION['ENCRYPTION'] = $this->IsNeedEncryption($encryption); //"true = Active"/"Failed"

        $_SESSION['cMethod'] = $_SESSION['dbCreateMethod'] = $this->IsExistingDB($DatabaseToUse); //existing/new
        $sameUser = $this->IsSameOhrmUser($sameOhrmUser);

        //Admin and password
        $_SESSION['defUser']['AdminUserName'] = $this->isFilledInConfigRegister($adminUserName, "Admin user name ");
        $_SESSION['defUser']['AdminPassword'] = $this->getPasswordFromUser($adminPassword, "Default admin ");


        //Root password set to an environment variable. Its use in plugin install page. - (root)
        $DatabaseRootPWD = $this->getPasswordFromUser($databaseRootPassword, "Database root ");
        putenv("DatabaseRootPassword=$DatabaseRootPWD");

        $_SESSION['INSTALLING'] = 0;
        $_SERVER['HTTP_USER_AGENT'] = 0;

        if ($sameUser) {
            $_SESSION['chkSameUser'] = 1; //dbConfig.php SameUser check box
        } else {
        //OHRMUserName and password
            $_SESSION['dbInfo']['dbOHRMUserName'] = $this->isFillInConfig($orangehrmDatabaseUser, "OrangeHRM DB User ");
            $_SESSION['dbInfo']['dbOHRMPassword'] = $this->getPasswordFromUser($orangehrmDatabasePassword, "Orangehrm database user ");
        }

        //Set Privileged Database User and password
        if ($_SESSION['dbCreateMethod'] == "existing") {
            $_SESSION['dbInfo']['dbOHRMUserName'] = $_SESSION['dbInfo']['dbUserName'] = $this->isFillInConfig($_SESSION['dbInfo']['dbOHRMUserName'], "OrangeHRM DB User ");
            $_SESSION['dbInfo']['dbOHRMPassword'] = $_SESSION['dbInfo']['dbPassword'] = $this->getPasswordFromUser($_SESSION['dbInfo']['dbOHRMPassword'], "Orangehrm database user ");
        } else {
            $_SESSION['dbInfo']['dbUserName'] = $this->isFillInConfig($privilegedDatabaseUser, "privileged Database User ");
            $_SESSION['dbInfo']['dbPassword'] = $this->getPasswordFromUser($privilegedDatabasePassword, "privileged Database ");
        }

        if ($_SESSION['ENCRYPTION']) {
            $keyResult = createKeyFile('key.ohrm');
        } else {
            $_SESSION['ENCRYPTION'] = 'Failed';
        }
        $_SESSION['defUser']['organizationName'] = $this->isFilledInConfigRegister($organizationName, "Organization Name ");
        $_SESSION['defUser']['adminEmployeeFirstName'] = $this->isFilledInConfigRegister($adminEmployeeFirstName, "Admin Employee First Name ");
        $_SESSION['defUser']['adminEmployeeLastName'] = $this->isFilledInConfigRegister($adminEmployeeLastName, "Admin Employee Last Name ");

        $this->getMessages()->displayMessage(Messages::SEPERATOR . "\n");
    }


    function isFillInConfig($SessionStatus, $inputType)
    {
        if (!isset($SessionStatus) || trim($SessionStatus) === '') {
            $messages = getMessages();
            echo "Please enter $inputType : ";
            $userValue = fopen("php://stdin", "r");
            $returnValue = fgets($userValue);

        } else {
            $returnValue = $SessionStatus;
        }

        return trim($returnValue);
    }

    /**
     * Check whether registration data are valid
     * @param $SessionStatus
     * @param $inputType
     * @param bool $invalidInput
     * @return string
     */
    private function isFilledInConfigRegister($SessionStatus, $inputType)
    {
        if (!isset($SessionStatus) || trim($SessionStatus) === '') {
            $returnValue = $this->takeUserInput("Please enter valid $inputType : ");

            return $returnValue ? ($this->isValidUserInput($inputType, $returnValue) ? $returnValue : $this->isFilledInConfigRegister(null, $inputType)) : ($this->isFilledInConfigRegister(null, $inputType));

        } else {
            $returnValue = $SessionStatus;
        }

        return $returnValue;
    }

    /**
     * Validate input
     * @param $inputType
     * @param $inputValue
     * @return bool|mixed
     */
    private function isValidUserInput($inputType, $inputValue) {
        if ($inputType == 'Organization Email Address ') {
            return filter_var($inputValue, FILTER_VALIDATE_EMAIL);
        }

        if ($inputType == 'contact number') {
            if (!($inputValue == 'Not captured')) {
                if(preg_match("/^\+?[0-9]+$/", $inputValue)) {
                    return true;
                }
                return false;
            }
            return true;
        }
        return true;
    }

    function getPasswordFromUser($SessionStatus, $passwordType)
    {

        if (!isset($SessionStatus) || trim($SessionStatus) === '') {
            // Get the password
            fwrite(STDOUT, "Please enter $passwordType password: ");
            $password =  $this->getPassword(true);

            if ($password == '') {
                $this->getPasswordFromUser($SessionStatus, $passwordType);
            }

            return $password;
        } else {
            return $SessionStatus;
        }
    }

    /**
     * Get a password from the shell.
     *
     * This function works on *nix systems only and requires shell_exec and stty.
     *
     * @param  boolean $stars Wether or not to output stars for given characters
     * @return string
     */
    function getPassword($stars = false)
    {
        // Get current style
        $oldStyle = shell_exec('stty -g');

        if ($stars === false) {
            shell_exec('stty -echo');
            $password = rtrim(fgets(STDIN), "\n");
        } else {
            shell_exec('stty -icanon -echo min 1 time 0');

            $password = '';
            while (true) {
                $char = fgetc(STDIN);

                if ($char === "\n") {
                    $this->getMessages()->displayMessage("\n");
                    break;
                } else if (ord($char) === 127) {
                    if (strlen($password) > 0) {
                        fwrite(STDOUT, "\x08 \x08");
                        $password = substr($password, 0, -1);
                    }
                } else {
                    fwrite(STDOUT, "*");
                    $password .= $char;
                }
            }
        }

        // Reset old style
        shell_exec('stty ' . $oldStyle);

        // Return the password
        return trim($password);
    }

    function IsExistingDB($SessionStatus, $message = ": ")
    {
        //return existing/new

        if (!isset($SessionStatus) || trim($SessionStatus) === '') {
            $SessionStatus = $this->takeUserInput("Are you using existing database?  Type y/N" . $message);
            $SessionStatus = strtolower(trim($SessionStatus));
            return (trim($SessionStatus) == 'y') ? 'existing' : ($SessionStatus == 'n' ? 'new' : $this->IsExistingDB(null, " (previous insert invalid): "));
        }
        $SessionStatus = strtolower(trim($SessionStatus));
        return $SessionStatus == 'y' ? 'existing' : ($SessionStatus == 'n' ? 'new' : $this->IsExistingDB(null));
    }


    function IsNeedEncryption($encryptNeed, $message = ": ")
    {
        if (!isset($encryptNeed) || $encryptNeed === '') {
            $encryptNeed = $this->takeUserInput("Do you want data encryption? type y/N" . $message);
            $encryptNeed = strtolower(trim($encryptNeed));
            return ($encryptNeed == 'y') ? true : ($encryptNeed == 'n' ? false : $this->IsNeedEncryption(null, " (previous insert invalid): "));
        }
        $encryptNeed = strtolower(trim($encryptNeed));
        return ($encryptNeed == 'y' || $encryptNeed == 1) ? true : ($encryptNeed == 'n' ? false : $this->IsNeedEncryption(null));
    }

    function IsSameOhrmUser($SessionStatus, $message = ": ")
    {
        //same-true  /  new - false
        $sameOhrm = strtolower(trim($SessionStatus));
        if (!isset($sameOhrm) || $sameOhrm === '') {
            $sameOhrm = $this->takeUserInput("Are you using same database user to orangehrm? Type 'y' to same user / 'n' to new user" . $message);
            return (trim($sameOhrm) == 'y') ? true : ($sameOhrm == 'n' ? false : $this->IsSameOhrmUser(null, " (previous insert invalid): "));
        }
        return $sameOhrm == 'y' ? true : ($sameOhrm == 'n' ? false : $this->IsSameOhrmUser(null));
    }

    function takeUserInput($textToGetInput)
    {
        $messages = getMessages();
        echo $textToGetInput;
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        return trim($line);
    }

}

?>

