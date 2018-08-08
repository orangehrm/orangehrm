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

class UpgradeSystemConfiguration
{

    /**
     * create and returns a database connection
     * @return mysqli|void
     */
    private function createDbConnection() {
        $host = $_SESSION['dbHostName'];
        $username = $_SESSION['dbUserName'];
        $password = $_SESSION['dbPassword'];
        $dbname = $_SESSION['dbName'];
        $port = $_SESSION['dbHostPort'];

        if (!$port) {
            $dbConnection = mysqli_connect($host, $username, $password, $dbname);
        } else {
            $dbConnection = mysqli_connect($host, $username, $password, $dbname, $port);
        }

        if (!$dbConnection) {
            return;
        }
        $dbConnection->set_charset("utf8");

        return $dbConnection;
    }

    /**
     * Executes a mysql query using the given database connection
     * @param $dbConnection
     * @param $query
     * @return bool|mysqli_result
     */
    private function executeSql($dbConnection, $query) {

        $result = mysqli_query($dbConnection, $query);
        $row = mysqli_fetch_array($result);

        mysqli_close($dbConnection);

        return $row;
    }

    /**
     * Get the organization name from Admin > General Info > Organization Name
     * @return bool|mysqli_result|string
     */
    public function getOrganizationName() {
        $query = "SELECT `name` FROM `ohrm_organization_gen_info`";
        $dbConnection = $this->createDbConnection();

        $row = $this->executeSql($dbConnection, $query);
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
    public function getCountry() {
        $query = "SELECT `country` FROM `ohrm_organization_gen_info`";
        $dbConnection = $this->createDbConnection();
        $row = $this->executeSql($dbConnection, $query);
        $countryCode = $row['country'];

        if($countryCode) {
            return $countryCode;
        } else {
            return "Not Captured";
        }
    }

    /**
     * Get the language from Admin > Configuration > Localization > Language
     * @return bool|mysqli_result|string
     */
    public function getLanguage() {
        $query = "SELECT `value` FROM `hs_hr_config` WHERE `key` = 'admin.localization.default_language'";
        $dbConnection = $this->createDbConnection();
        $row =  $this->executeSql($dbConnection, $query);
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
    public function getFirstName() {
        $adminEmpNumber = $this->getAdminEmployeeNumber();
        $query = "SELECT `emp_firstname` FROM `hs_hr_employee` WHERE  `emp_number` = '$adminEmpNumber';";

        $dbConnection = $this->createDbConnection();
        $row = $this->executeSql($dbConnection, $query);
        $firstName = $row['emp_firstname'];

        return $firstName;
    }

    /**
     * Get an admin employee with last name
     * @return bool|mysqli_result
     */
    public function getLastName() {
        $adminEmpNumber = $this->getAdminEmployeeNumber();
        $query = "SELECT `emp_lastname` FROM `hs_hr_employee` WHERE  `emp_number` = '$adminEmpNumber'";

        $dbConnection = $this->createDbConnection();
        $row = $this->executeSql($dbConnection, $query);
        $lastName = $row['emp_lastname'];

        return $lastName;
    }

    /**
     * Get the email address of admin employee from PIM > Contact Details > Work Email
     * @return bool|mysqli_result
     */
    public function getAdminEmail() {
        $adminEmpNumber = $this->getAdminEmployeeNumber();
        $query = "SELECT `emp_work_email` FROM `hs_hr_employee` WHERE `emp_number` = '$adminEmpNumber';";

        $dbConnection = $this->createDbConnection();
        $row = $this->executeSql($dbConnection, $query);
        $adminEmail = $row['emp_work_email'];

        return $adminEmail;
    }

    /**
     * Get the contact number of admin employee from PIM > Contact Details > Work Telephone
     * @return bool|mysqli_result
     */
    public function getAdminContactNumber() {
        $adminEmpNumber = $this->getAdminEmployeeNumber();
        $query = "SELECT `emp_work_telephone` FROM `hs_hr_employee` WHERE `emp_number` = '$adminEmpNumber'";
        $dbConnection = $this->createDbConnection();
        $row = $this->executeSql($dbConnection, $query);
        $adminContactNumber = $row['emp_work_telephone'];

        return $adminContactNumber;
    }

    /**
     * Retrun admin user name
     * @return mixed
     */
    public function getAdminUserName() {
        $adminIdQuery = "SELECT `id` FROM `ohrm_user_role` WHERE  `name` = 'Admin' LIMIT 1";
        $dbConnection = $this->createDbConnection();
        $adminIdRow = $this->executeSql($dbConnection, $adminIdQuery);
        $adminId = $adminIdRow['id'];

        $adminNameQuery = "SELECT `user_name` FROM `ohrm_user` WHERE  `user_role_id` = '$adminId'";
        $dbConnection = $this->createDbConnection();
        $adminEmpNameRow = $this->executeSql($dbConnection, $adminNameQuery);

        return $adminEmpNameRow['user_name'];

    }

    /**
     * Get Admin employee number
     * @return mixed
     */
    private function getAdminEmployeeNumber() {
        $adminIdQuery = "SELECT `id` FROM `ohrm_user_role` WHERE  `name` = 'Admin' LIMIT 1";
        $dbConnection = $this->createDbConnection();
        $adminIdRow = $this->executeSql($dbConnection, $adminIdQuery);
        $adminId = $adminIdRow['id'];

        $adminEmpNumberQuery = "SELECT `emp_number` FROM `ohrm_user` WHERE  `user_role_id` = '$adminId'";
        $dbConnection = $this->createDbConnection();
        $adminEmpNumberRow = $this->executeSql($dbConnection, $adminEmpNumberQuery);
        $adminEmpNumber = $adminEmpNumberRow['emp_number'];

        return $adminEmpNumber;
    }
}