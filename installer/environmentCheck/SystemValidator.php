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
     * @param $port
     * @return bool
     */
    public function isMySqlCompatible($host, $userName, $password, $port)
    {
        $currentVersion = $this->getMySqlVersion($host, $userName, $password, $port);

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
     * @param $port
     * @return string
     */
    public function getMysqlErrorMessage($host, $userName, $password, $port)
    {
        $currentVersion = $this->getMySqlVersion($host, $userName, $password, $port);
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
     * @param $port
     * @param $userName
     * @param $password
     * @return string
     */
    private function getMySqlVersion($host, $userName, $password, $port)
    {
        $mysqli = new mysqli($host, $userName, $password, null, $port);

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
     * Return availability of the extension
     *
     * @param $extensionName
     * @return bool
     */
    public function hasExtensionEnabled($extensionName)
    {
        return extension_loaded($extensionName);
    }

    /**
     * Return status of SimpleXML extensions and it's requirements
     *
     * @return bool
     */
    public function isSimpleXMLEnabled()
    {
        if (
            $this->hasExtensionEnabled('SimpleXML') &&
            $this->hasExtensionEnabled('libxml') &&
            $this->hasExtensionEnabled('xml')
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return zip extension status
     *
     * @return bool
     */
    public function isZipEnabled()
    {
        return $this->hasExtensionEnabled('zip');
    }
}
