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

require_once ROOT_PATH.'/installer/SystemConfiguration.php';

class UpgradeSystemConfiguration
{

    const KEY_INSTANCE_IDENTIFIER = "instance.identifier";
    const KEY_INSTANCE_IDENTIFIER_CHECKSUM = "instance.identifier_checksum";
    private $sysConf = null;

    /**
     * create and returns a database connection
     * @return mysqli|void
     */
    private function createDbConnection()
    {
        $host = $_SESSION['dbHostName'];
        $username = $_SESSION['dbUserName'];
        $password = $_SESSION['dbPassword'];
        $dbname = $_SESSION['dbName'];
        $port = $_SESSION['dbHostPort'];

        if (!$port) {
            $dbConnection = new PDO('mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8mb4', $username, $password);
        } else {
            $dbConnection = new PDO('mysql:host=' . $host . ';port=' . $port . ';dbname=' . $dbname . ';charset=utf8mb4', $username, $password);
        }

        if (!$dbConnection) {
            return;
        }

        return $dbConnection;
    }

    /**
     * Get the organization name from Admin > General Info > Organization Name
     * @return bool|mysqli_result|string
     */
    public function getOrganizationName()
    {
        $query = "SELECT `name` FROM `ohrm_organization_gen_info`";
        $dbConnection = $this->createDbConnection();
        $statement = $dbConnection->query($query);
        $row = $statement->fetch();
        $orgnaizationName = $row['name'];

        if ($orgnaizationName) {
            return $orgnaizationName;
        } else {
            return "Not Captured";
        }
    }

    /**
     * Get the country name from Admin > General Info > Country
     * @return bool|mysqli_result|string
     */
    public function getCountry()
    {
        $query = "SELECT `country` FROM `ohrm_organization_gen_info`";
        $dbConnection = $this->createDbConnection();
        $statement = $dbConnection->query($query);
        $row = $statement->fetch();
        $countryCode = $row['country'];

        if ($countryCode) {
            return $countryCode;
        } else {
            return "Not Captured";
        }
    }

    /**
     * Get the language from Admin > Configuration > Localization > Language
     * @return bool|mysqli_result|string
     */
    public function getLanguage()
    {
        $query = "SELECT `value` FROM `hs_hr_config` WHERE `key` = 'admin.localization.default_language'";
        $dbConnection = $this->createDbConnection();
        $statement = $dbConnection->query($query);
        $row = $statement->fetch();
        $languageCode = $row['value'];

        if ($languageCode) {
            return $languageCode;
        } else {
            return "Not Captured";
        }
    }

    /**
     * Get an admin employee with first name
     * @return bool|mysqli_result
     */
    public function getFirstName()
    {
        $adminEmpNumber = $this->getAdminEmployeeNumber();
        $query = "SELECT `emp_firstname` FROM `hs_hr_employee` WHERE  `emp_number` = '$adminEmpNumber';";

        $dbConnection = $this->createDbConnection();
        $statement = $dbConnection->query($query);
        $row = $statement->fetch();
        $firstName = $row['emp_firstname'];

        return $firstName;
    }

    /**
     * Get an admin employee with last name
     * @return bool|mysqli_result
     */
    public function getLastName()
    {
        $adminEmpNumber = $this->getAdminEmployeeNumber();
        $query = "SELECT `emp_lastname` FROM `hs_hr_employee` WHERE  `emp_number` = '$adminEmpNumber'";

        $dbConnection = $this->createDbConnection();
        $statement = $dbConnection->query($query);
        $row = $statement->fetch();
        $lastName = $row['emp_lastname'];

        return $lastName;
    }

    /**
     * Get the email address of admin employee from PIM > Contact Details > Work Email
     * @return bool|mysqli_result
     */
    public function getAdminEmail()
    {
        $adminEmpNumber = $this->getAdminEmployeeNumber();
        $query = "SELECT `emp_work_email` FROM `hs_hr_employee` WHERE `emp_number` = '$adminEmpNumber';";

        $dbConnection = $this->createDbConnection();
        $statement = $dbConnection->query($query);
        $row = $statement->fetch();
        $adminEmail = $row['emp_work_email'];

        return $adminEmail;
    }

    /**
     * Get the contact number of admin employee from PIM > Contact Details > Work Telephone
     * @return bool|mysqli_result
     */
    public function getAdminContactNumber()
    {
        $adminEmpNumber = $this->getAdminEmployeeNumber();
        $query = "SELECT `emp_work_telephone` FROM `hs_hr_employee` WHERE `emp_number` = '$adminEmpNumber'";
        $dbConnection = $this->createDbConnection();
        $statement = $dbConnection->query($query);
        $row = $statement->fetch();
        $adminContactNumber = $row['emp_work_telephone'];

        return $adminContactNumber;
    }

    /**
     * Retrun admin user name
     * @return mixed
     */
    public function getAdminUserName()
    {
        $adminIdQuery = "SELECT `id` FROM `ohrm_user_role` WHERE  `name` = 'Admin' LIMIT 1";
        $dbConnection = $this->createDbConnection();
        $statement = $dbConnection->query($adminIdQuery);
        $adminIdRow = $statement->fetch();
        $adminId = $adminIdRow['id'];

        $adminNameQuery = "SELECT `user_name` FROM `ohrm_user` WHERE  `user_role_id` = '$adminId'";
        $dbConnection = $this->createDbConnection();
        $statement = $dbConnection->query($adminNameQuery);
        $adminEmpNameRow = $statement->fetch();

        return $adminEmpNameRow['user_name'];

    }

    /**
     * Get Admin employee number
     * @return mixed
     */
    private function getAdminEmployeeNumber()
    {
        $adminIdQuery = "SELECT `id` FROM `ohrm_user_role` WHERE  `name` = 'Admin' LIMIT 1";
        $dbConnection = $this->createDbConnection();
        $statement = $dbConnection->query($adminIdQuery);
        $adminIdRow = $statement->fetch();
        $adminId = $adminIdRow['id'];

        $adminEmpNumberQuery = "SELECT `emp_number` FROM `ohrm_user` WHERE  `user_role_id` = '$adminId'";
        $dbConnection = $this->createDbConnection();
        $statement = $dbConnection->query($adminEmpNumberQuery);
        $adminEmpNumberRow = $statement->fetch();

        $adminEmpNumber = $adminEmpNumberRow['emp_number'];

        return $adminEmpNumber;
    }

    /**
     * Set the instance identifier of upgraded instance
     */
    public function setInstanceIdentifier()
    {
        $this->sysConf = $this->getSystemConfiguration();
        $this->sysConf->setInstanceIdentifier(
            $_SESSION['defUser']['organizationName'],
            $_SESSION['defUser']['organizationEmailAddress'],
            $_SESSION['defUser']['adminEmployeeFirstName'],
            $_SESSION['defUser']['adminEmployeeLastName'],
            $_SERVER['HTTP_HOST'], $_SESSION['defUser']['country'],
            $this->sysConf->getOhrmVersion()
        );
    }

    /**
     * Get instance identifier
     * @return mixed
     */
    public function getInstanceIdentifier()
    {
        $query = "SELECT `value` FROM `hs_hr_config` WHERE `key`='" . self::KEY_INSTANCE_IDENTIFIER . "'";
        $dbConnection = $this->createDbConnection();
        $statement = $dbConnection->query($query);
        $configValue = $statement->fetch();
        return $configValue['value'];
    }

    /**
     * Check whether the instance identifier exists
     * @return bool
     */
    public function hasSetInstanceIdentifier()
    {
        $instanceIdentifier = $this->getInstanceIdentifier();
        if (!is_null($instanceIdentifier)) {
            return true;
        }
        return false;
    }

    /**
     * Set the instance identifier checksum
     */
    public function setInstanceIdentifierChecksum()
    {
        $this->sysConf = $this->getSystemConfiguration();
        $this->sysConf->setInstanceIdentifierChecksum(
            $_SESSION['defUser']['organizationName'],
            $_SESSION['defUser']['organizationEmailAddress'],
            $_SESSION['defUser']['adminEmployeeFirstName'],
            $_SESSION['defUser']['adminEmployeeLastName'],
            $_SERVER['HTTP_HOST'], $_SESSION['defUser']['country'],
            $this->sysConf->getOhrmVersion()
        );
    }

    /**
     * Get instance identifier checksum value
     * @return mixed
     */
    public function getInstanceIdentifierChecksum()
    {
        $query = "SELECT `value` FROM `hs_hr_config` WHERE `key`='" . self::KEY_INSTANCE_IDENTIFIER_CHECKSUM . "'";
        $dbConnection = $this->createDbConnection();
        $statement = $dbConnection->query($query);
        $configValue = $statement->fetch();
        return $configValue['value'];
    }

    /**
     * Check whether the instance identifier checksum exists
     * @return bool
     */
    public function hasSetInstanceIdentifierChecksum()
    {
        $instanceIdentifierChecksum = $this->getInstanceIdentifierChecksum();
        if (!is_null($instanceIdentifierChecksum)) {
            return true;
        }
        return false;
    }

    /**
     * gets an instance of SystemConfiguration
     * @return SystemConfiguration
     */
    private function getSystemConfiguration()
    {
        if (is_null($this->sysConf)) {
            $this->sysConf = new SystemConfiguration();
        }
        return $this->sysConf;
    }
}
