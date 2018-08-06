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

include_once('../symfony/plugins/orangehrmCorePlugin/lib/utility/PasswordHash.php');

class SystemConfiguration
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

        mysqli_close($dbConnection);

        return $result;
    }

    /**
     * Set the organization name in Admin > General Info > Organization Name
     * @param $orgnaizationName
     */
    public function setOrganizationName($orgnaizationName) {
        $query = "INSERT INTO `ohrm_organization_gen_info` (`name`) VALUES ('$orgnaizationName')";
        $dbConnection = $this->createDbConnection();
        $this->executeSql($dbConnection, $query);
    }

    /**
     * Set the country name in Admin > General Info > Country
     * @param $countryCode
     */
    public function setCountry($countryCode){
        $query = "UPDATE `ohrm_organization_gen_info` SET `country` = '$countryCode' WHERE `ohrm_organization_gen_info`.`id` = 1";
        $dbConnection = $this->createDbConnection();
        $this->executeSql($dbConnection, $query);
    }


    /**
     * Set the language in Admin > Configuration > Localization > Language
     * @param $languageCode
     */
    public function setLanguage($languageCode) {
        $query = "UPDATE `hs_hr_config` SET `value` = '$languageCode' WHERE `hs_hr_config`.`key` = 'admin.localization.default_language'";
        $dbConnection = $this->createDbConnection();
        $this->executeSql($dbConnection, $query);
    }


    /**
     * Create an admin employee with first name and last name
     * @param $firstName
     * @param $lastName
     */
    public function setAdminName($firstName, $lastName) {
        $query = "INSERT INTO `hs_hr_employee` (`emp_number`, `employee_id`, `emp_lastname`, `emp_firstname`) VALUES ('0001', '0001', '$lastName', '$firstName')";
        $dbConnection = $this->createDbConnection();
        $this->executeSql($dbConnection, $query);
    }


    /**
     * Set the email address of admin employee in PIM > Contact Details > Work Email
     * @param $email
     */
    public function setAdminEmail($email) {
        $query = "UPDATE `hs_hr_employee` SET `emp_work_email` = '$email' WHERE `hs_hr_employee`.`emp_number` = 1";
        $dbConnection = $this->createDbConnection();
        $this->executeSql($dbConnection, $query);
    }


    /**
     * Set the contact number of admin employee in PIM > Contact Details > Work Telephone
     * @param $contactNumber
     */
    public function setAdminContactNumber($contactNumber) {
        $query = "UPDATE `hs_hr_employee` SET `emp_work_telephone` = '$contactNumber' WHERE `hs_hr_employee`.`emp_number` = 1";
        $dbConnection = $this->createDbConnection();
        $this->executeSql($dbConnection, $query);
    }


    /**
     * Create an Admin user with user name and password
     * @param $userName
     * @param $password
     */
    public function createAdminUser($userName, $password) {
        $passwordHasher = new PasswordHash();
        $hash = $passwordHasher->hash($password);

        $query = "INSERT INTO `ohrm_user` (`user_role_id`, `emp_number`, `user_name`, `user_password`) VALUES ('1', '1', '$userName', '$hash')";
        $dbConnection = $this->createDbConnection();
        $this->executeSql($dbConnection, $query);
    }
}