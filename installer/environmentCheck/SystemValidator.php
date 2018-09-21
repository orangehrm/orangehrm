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


require __DIR__ . "/../../symfony/lib/vendor/autoload.php";

class SystemValidator
{
    /**
     * @var array|null
     */
    private $systemRequirements = null;
    /**
     * @var PDO|null
     */
    private $dbConnection = null;

    public function __construct()
    {
        $this->systemRequirements = sfYaml::load(file_get_contents(dirname(__FILE__) . '/system_requirements.yml'));

    }

    /**
     * @return bool
     */
    public function isPhpCompatible()
    {
        $currentVersion = phpversion();
        return $this->isWithinRange($currentVersion, $this->systemRequirements['phpversion']['excludeRange'],
            $this->systemRequirements['phpversion']['min'], $this->systemRequirements['phpversion']['max']);
    }

    /**
     * @param $host
     * @param $userName
     * @param $password
     * @return bool
     */
    public function isMySqlCompatible($host, $userName, $password)
    {
        $currentVersion = $this->getMySqlVersion($host, $userName, $password);

        if ($this->isMariaDB($currentVersion)) {
            return $this->isWithinRange($this->getMariaDbVersion($currentVersion),
                $this->systemRequirements['mariadbversion']['excludeRange'],
                $this->systemRequirements['mariadbversion']['min'], $this->systemRequirements['mariadbversion']['max']);
        }
        return $this->isWithinRange($currentVersion, $this->systemRequirements['mysqlversion']['excludeRange'],
            $this->systemRequirements['mysqlversion']['min'], $this->systemRequirements['mysqlversion']['max']);
    }

    /**
     * @return bool
     */
    public function isWebServerCompatible()
    {
        $webServer = $_SERVER['SERVER_SOFTWARE'];

        foreach ($this->systemRequirements['webserver'] as $validServer) {
            if (strpos($webServer, $validServer) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public function getPhpErrorMessage()
    {
        return $this->getErrorMessage('PHP', phpversion(), $this->systemRequirements['phpversion']['excludeRange'],
            $this->systemRequirements['phpversion']['min'], $this->systemRequirements['phpversion']['max']);
    }

    /**
     * @param $host
     * @param $userName
     * @param $password
     * @return string
     */
    public function getMysqlErrorMessage($host, $userName, $password)
    {
        $currentVersion = $this->getMySqlVersion($host, $userName, $password);
        if ($this->isMariaDB($currentVersion)) {
            return $this->getErrorMessage('MariaDB', $currentVersion,
                $this->systemRequirements['mariadbversion']['excludeRange'],
                $this->systemRequirements['mariadbversion']['min'], $this->systemRequirements['mariadbversion']['max']);
        } else {
            return $this->getErrorMessage('MySql', $currentVersion,
                $this->systemRequirements['mysqlversion']['excludeRange'],
                $this->systemRequirements['mysqlversion']['min'], $this->systemRequirements['mysqlversion']['max']);
        }
    }

    /**
     * @return string
     */
    public function getWebServerErrorMessage()
    {
        $webServer = $_SERVER['SERVER_SOFTWARE'];

        $message = "Compatible web servers: ";
        foreach ($this->systemRequirements['webserver'] as $key => $validServer) {
            if ($key == 0) {
                $message = $message . $validServer;
            } else {
                $message = $message . ", " . $validServer;
            }
        }
        $message = $message . ". Installed server is " . $webServer;
        return $message;
    }


    /**
     * @param $value
     * @param $excludeRange
     * @param $min
     * @param $max
     * @return bool
     */
    private function isWithinRange($value, $excludeRange, $min, $max)
    {
        $points = max(substr_count($max, '.'), substr_count($min, '.'));
        $pattern = '/^(\d+)';
        for ($i = 0; $i < $points; $i++) {
            $pattern = $pattern . '\.(\d+)';
        }
        $pattern = $pattern . '/';
        preg_match($pattern, $value, $matches);
        $value = $matches[0];

        if (!(version_compare($value, $min) >= 0 && version_compare($max, $value) >= 0)) {
            return false;
        }
        if ($this->isExcluded($value, $excludeRange)) {
            return false;
        }
        return true;
    }

    /**
     * @param $mariadbVersionString
     * @return mixed
     */
    private function getMariaDbVersion($mariadbVersionString)
    {
        $mariaDBVersionArray = explode("-", $mariadbVersionString);
        $mariaDBVersion = $mariaDBVersionArray[0];

        return $mariaDBVersion;
    }

    /**
     * @param $host
     * @param $userName
     * @param $password
     * @return string
     */
    private function getMySqlVersion($host, $userName, $password)
    {
        $mysqli = new mysqli($host, $userName, $password);

        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }
        $currentVersion = $mysqli->server_info;

        $mysqli->close();
        return $currentVersion;
    }

    /**
     * @param $value
     * @param array $excludedRange
     * @return bool
     */
    private function isExcluded($value, $excludedRange = array())
    {
        if (in_array($value, $excludedRange)) {
            return true;
        }
        return false;
    }

    /**
     * @param $component
     * @param $currentVersion
     * @param $excludeRange
     * @param $min
     * @param $max
     * @return string
     */
    private function getErrorMessage($component, $currentVersion, $excludeRange, $min, $max)
    {
        $message = '';

        if ($this->isExcluded($currentVersion, $excludeRange)) {
            $message = $message . $component . " Version " . $currentVersion . " is not supported.";
        } else {
            $message = $component . " Version should be higher than " . $min . " and lower than " . $max;
        }
        $message = $message . " .Installed version is " . $currentVersion;
        return $message;

    }

    /**
     * @param $currentVersion
     * @return bool
     */
    private function isMariaDB($currentVersion)
    {
        if (strpos($currentVersion, 'MariaDB') !== false) {
            return true;
        }
        return false;
    }

    /**
     * Return database connection
     * @return null|PDO|void
     */
    public function getDbConn()
    {
        $host = $_SESSION['dbHostName'];
        $username = $_SESSION['dbUserName'];
        $password = $_SESSION['dbPassword'];
        $port = $_SESSION['dbHostPort'];

        if ($this->dbConnection instanceof PDO) {
            return $this->dbConnection;
        }

        try {
            $this->dbConnection = $this->createDbConnection($username, $password, $host, $port);
            return $this->dbConnection;
        } catch (PDOException $e) {
            return;
        }

    }

    /**
     * @param $username
     * @param $password
     * @param null $host
     * @param null $port
     * @param null $dbname
     * @param null $unix_socket
     * @return null|PDO|void
     */
    public function createDbConnection(
        $username,
        $password,
        $host = null,
        $port = null,
        $dbname = null,
        $unix_socket = null
    ) {
        $dsn = "mysql:";

        if (!is_null($host)) {
            $dsn .= "host=" . $host . ";";
        }
        if (!is_null($port)) {
            $dsn .= "port=" . $port . ";";
        }
        if (!is_null($unix_socket)) {
            $dsn = "mysql:unix_socket=" . $unix_socket . ";";
        }
        if (!is_null($dbname)) {
            $dsn .= "dbname=" . $dbname . ";";
        }

        $dsn .= "charset=utf8mb4";

        try {
            $dbConn = new PDO($dsn, $username, $password);
            return $dbConn;
        } catch (PDOException $e) {
            throw $e;
        }
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
}
