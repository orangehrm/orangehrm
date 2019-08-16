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

include_once(realpath(dirname(__FILE__)) . '/../symfony/plugins/orangehrmCorePlugin/lib/utility/PasswordHash.php');

class SystemConfiguration
{
    const KEY_INSTANCE_IDENTIFIER = "instance.identifier";
    const KEY_INSTANCE_IDENTIFIER_CHECKSUM = "instance.identifier_checksum";

    /**
     * Returns a database connection
     * @param bool $dbSelect
     * @return PDO|void
     */
    public function createDbConnection($dbSelect = true)
    {
        $host = $_SESSION['dbHostName'];
        $username = $_SESSION['dbUserName'];
        $password = $_SESSION['dbPassword'];
        $dbname = $_SESSION['dbName'];
        $port = $_SESSION['dbHostPort'];

        $dsn = sprintf("mysql:host=%s;charset=utf8mb4;", $host);

        if ($dbSelect) {
            $dsn .= sprintf("dbname=%s;", $dbname);
        }

        if (!is_null($port)) {
            $dsn .= sprintf("port=%s;", $port);
        }

        try {
            $dbConnection = new PDO($dsn, $username, $password);
        } catch (PDOException $e) {
            throw $e;
        }

        return $dbConnection;
    }

    /**
     * Set the organization name in Admin > General Info > Organization Name
     * @param $orgnaizationName
     */
    public function setOrganizationName($orgnaizationName)
    {
        $query = "INSERT INTO `ohrm_organization_gen_info` (`name`) VALUES (?)";
        $dbConnection = $this->createDbConnection();
        $statement = $dbConnection->prepare($query);
        $statement->execute(array($orgnaizationName));
    }

    /**
     * Set the country name in Admin > General Info > Country
     * @param $countryCode
     */
    public function setCountry($countryCode)
    {
        $query = "UPDATE `ohrm_organization_gen_info` SET `country` = ? WHERE `ohrm_organization_gen_info`.`id` = 1";
        $dbConnection = $this->createDbConnection();
        $statement = $dbConnection->prepare($query);
        $statement->execute(array($countryCode));
    }


    /**
     * Set the language in Admin > Configuration > Localization > Language
     * @param $languageCode
     */
    public function setLanguage($languageCode)
    {
        $query = "UPDATE `hs_hr_config` SET `value` = ? WHERE `hs_hr_config`.`key` = 'admin.localization.default_language'";
        $dbConnection = $this->createDbConnection();
        $statement = $dbConnection->prepare($query);
        $statement->execute(array($languageCode));
    }


    /**
     * Create an admin employee with first name and last name
     * @param $firstName
     * @param $lastName
     */
    public function setAdminName($firstName, $lastName)
    {
        $query = "INSERT INTO `hs_hr_employee` (`emp_number`, `employee_id`, `emp_lastname`, `emp_firstname`) VALUES ('1', '0001', ?, ?)";
        $dbConnection = $this->createDbConnection();
        $statement = $dbConnection->prepare($query);
        $statement->execute(array($lastName, $firstName));
    }


    /**
     * Set the email address of admin employee in PIM > Contact Details > Work Email
     * @param $email
     */
    public function setAdminEmail($email)
    {
        $query = "UPDATE `hs_hr_employee` SET `emp_work_email` = ? WHERE `hs_hr_employee`.`emp_number` = 1";
        $dbConnection = $this->createDbConnection();
        $statement = $dbConnection->prepare($query);
        $statement->execute(array($email));
    }


    /**
     * Set the contact number of admin employee in PIM > Contact Details > Work Telephone
     * @param $contactNumber
     */
    public function setAdminContactNumber($contactNumber)
    {
        $query = "UPDATE `hs_hr_employee` SET `emp_work_telephone` = ? WHERE `hs_hr_employee`.`emp_number` = 1";
        $dbConnection = $this->createDbConnection();
        $statement = $dbConnection->prepare($query);
        $statement->execute(array($contactNumber));
    }


    /**
     * Create an Admin user with user name and password
     * @param $userName
     * @param $password
     */
    public function createAdminUser($userName, $password)
    {
        $passwordHasher = new PasswordHash();
        $hash = $passwordHasher->hash($password);

        $query = "INSERT INTO `ohrm_user` (`user_role_id`, `emp_number`, `user_name`, `user_password`) VALUES ('1', '1', ?, ?)";
        $dbConnection = $this->createDbConnection();
        $statement = $dbConnection->prepare($query);
        $statement->execute(array($userName, $hash));
    }

    /**
     * Set the instance identifier value to db
     * @param string $organizationName
     * @param string $email
     * @param string $adminFirstName
     * @param string $adminLastName
     * @param string $host
     * @param string $country
     * @param string $ohrmVersion
     */
    public function setInstanceIdentifier(
        $organizationName,
        $email,
        $adminFirstName,
        $adminLastName,
        $host,
        $country,
        $ohrmVersion
    )
    {
        $instanceIdentifier = $this->createInstanceIdentifier(
            $organizationName,
            $email,
            $adminFirstName,
            $adminLastName,
            $host,
            $country,
            $ohrmVersion
        );
        $query = "INSERT INTO `hs_hr_config` (`key`, `value`) VALUES (?, ?)";
        $dbConnection = $this->createDbConnection();
        $statement = $dbConnection->prepare($query);
        $statement->execute(array(self::KEY_INSTANCE_IDENTIFIER, $instanceIdentifier));
    }

    /**
     * Create instance identifier value
     * @param string $organizationName
     * @param string $email
     * @param string $adminFirstName
     * @param string $adminLastName
     * @param string $host
     * @param string $country
     * @param string $ohrmVersion
     * @return string
     */
    public function createInstanceIdentifier(
        $organizationName,
        $email,
        $adminFirstName,
        $adminLastName,
        $host,
        $country,
        $ohrmVersion
    )
    {
        if (is_null($host)) {
            $host = '';
        }
        if (is_null($country)) {
            $country = '';
        }
        return base64_encode(
            $organizationName .
            '_' . $email .
            '_' . $adminFirstName .
            '_' . $adminLastName .
            '_' . $host .
            '_' . $country .
            '_' . $ohrmVersion
        );
    }

    /**
     * Create instance identifier checksum value
     * @param string $organizationName
     * @param string $email
     * @param string $adminFirstName
     * @param string $adminLastName
     * @param string $host
     * @param string $country
     * @param string $ohrmVersion
     * @return string
     */
    public function createInstanceIdentifierChecksum(
        $organizationName,
        $email,
        $adminFirstName,
        $adminLastName,
        $host,
        $country,
        $ohrmVersion
    )
    {
        if (is_null($host)) {
            $host = '';
        }
        if (is_null($country)) {
            $country = '';
        }
        $params = array(
            'organizationName' => $organizationName,
            'organizationEmail' => $email,
            'adminFirstName' => $adminFirstName,
            'adminLastName' => $adminLastName,
            'host' => $host,
            'country' => $country,
            'ohrmVersion' => $ohrmVersion
        );

        return base64_encode(serialize($params));
    }

    /**
     * Set the instance identifier checksum value
     * @param string $organizationName
     * @param string $email
     * @param string $adminFirstName
     * @param string $adminLastName
     * @param string $host
     * @param string $country
     * @param string $ohrmVersion
     */
    public function setInstanceIdentifierChecksum(
        $organizationName,
        $email,
        $adminFirstName,
        $adminLastName,
        $host,
        $country,
        $ohrmVersion
    )
    {
        $instanceIdentifierChecksum = $this->createInstanceIdentifierChecksum(
            $organizationName,
            $email,
            $adminFirstName,
            $adminLastName,
            $host,
            $country,
            $ohrmVersion
        );
        $query = "INSERT INTO `hs_hr_config` (`key`, `value`) VALUES (?, ?)";
        $dbConnection = $this->createDbConnection();
        $statement = $dbConnection->prepare($query);
        $statement->execute(array(self::KEY_INSTANCE_IDENTIFIER_CHECKSUM, $instanceIdentifierChecksum));
    }

    /**
     * get ohrmVersion
     * @return string ohrmVersion
     */
    public function getOhrmVersion()
    {
        if (!class_exists('sysConf')) {
            require_once ROOT_PATH . '/lib/confs/sysConf.php';
        }
        $sysConf = new sysConf();
        return $sysConf->getVersion();
    }
}
