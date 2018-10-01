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

require_once(realpath(dirname(__FILE__)) . '/../lib/confs/sysConf.php');
require_once(realpath(dirname(__FILE__)) . '/SystemConfiguration.php');

class SystemDetailHelper
{
    private $dbConnection = null;

    /**
     * Return database connection
     * @return null|PDO|void
     */
    public function getDbConn()
    {
        if (!$this->dbConnection instanceof PDO) {
            $systemConfiguration = new SystemConfiguration();
            $this->dbConnection = $systemConfiguration->createDbConnection(false);
        }

        return $this->dbConnection;
    }

    /**
     * Return PHP version
     * @return string
     */
    public function getPhpVersion()
    {
        return phpversion();
    }

    /**
     * Return web server details
     * @return mixed
     */
    public function getServerDetails()
    {
        return $_SERVER['SERVER_SOFTWARE'];
    }

    /**
     * Return MySQL client version
     * @return mixed
     */
    public function getMySqlClientVersion()
    {
        return $this->getPdoAttribute(PDO::ATTR_CLIENT_VERSION);
    }

    /**
     * Return MySQL server version
     * @return mixed
     */
    public function getMySqlServerVersion()
    {
        return $this->getPdoAttribute(PDO::ATTR_SERVER_VERSION);
    }

    /**
     * Return MySQL connection type
     * @return mixed
     */
    public function getMySqlConnectionType()
    {
        return $this->getPdoAttribute(PDO::ATTR_CONNECTION_STATUS);
    }

    /**
     * Return PDO attribute if database connection successful
     * @param $attribute
     * @return mixed|void
     */
    public function getPdoAttribute($attribute)
    {
        $dbConn = $this->getDbConn();
        if ($dbConn instanceof PDO) {
            return $dbConn->getAttribute($attribute);
        }
        return;
    }

    /**
     * Return running operating system details
     * @return array
     */
    public function getOsDetails()
    {
        return array(
            "os" => php_uname('s'),
            "release_name" => php_uname('r'),
            "version_info" => php_uname('v'),
        );
    }

    /**
     * Return PHP environment details
     * @return array
     */
    public function getPhpDetails()
    {
        return array(
            "version" => $this->getPhpVersion()
        );
    }

    /**
     * Return MySQL details
     * @return array
     */
    public function getMySqlDetails()
    {
        return array(
            "client_version" => $this->getMySqlClientVersion(),
            "server_version" => $this->getMySqlServerVersion(),
            "conn_type" => $this->getMySqlConnectionType()
        );
    }

    /**
     * Return OrangeHRM details
     * @return array
     */
    public function getOhrmDetails()
    {
        $sysConf = new sysConf();
        return array(
            "version" => $sysConf->getVersion()
        );
    }

    /**
     * Return array of system details
     * @return array
     */
    public function getSystemDetailsAsArray()
    {
        return array(
            "os" => $this->getOsDetails(),
            "php" => $this->getPhpDetails(),
            "mysql" => $this->getMySqlDetails(),
            "server" => $this->getServerDetails(),
            "ohrm" => $this->getOhrmDetails(),
        );
    }

    /**
     * Return json string of system details
     * @return string
     */
    public function getSystemDetailsAsJson()
    {
        return json_encode($this->getSystemDetailsAsArray());
    }
}
