<?php
/**
 * Created by PhpStorm.
 * User: madhuka
 * Date: 5/10/18
 * Time: 4:53 PM
 */


require __DIR__ . "/../../symfony/lib/vendor/autoload.php";
class SystemValidator
{
    private $systemRequirements;

    public function __construct() {
        $this->systemRequirements = sfYaml::load(file_get_contents( dirname(__FILE__) . '/system_requirements.yml'));

    }

    public function isPhpCompatible() {
        $currentVersion = phpversion();
        return $this->isWithinRange($currentVersion, $this->systemRequirements['phpversion']['range'], $this->systemRequirements['phpversion']['min'], $this->systemRequirements['phpversion']['max']);
    }

    public function isMySqlCompatible($host, $userName, $password) {
        $currentVersion = $this->getMySqlVersion($host, $userName, $password);

        if (strpos($currentVersion, 'MariaDB') !== false) {
            return $this->isWithinRange($this->getMariaDbVersion($currentVersion), $this->systemRequirements['mariadbversion']['range'], $this->systemRequirements['mariadbversion']['min'], $this->systemRequirements['mariadbversion']['max']);
        }
        return $this->isWithinRange($currentVersion, $this->systemRequirements['mysqlversion']['range'], $this->systemRequirements['mysqlversion']['min'], $this->systemRequirements['mysqlversion']['max']);
    }

    public function getPhpErrorMessage() {
        if ($this->systemRequirements['phpversion']['range']) {
            $message =  "Compatible PHP versions:";
            foreach ($this->systemRequirements['phpversion']['range'] as $key => $version) {
                if ($key == 0) {
                    $message = $message . $version;
                } else {
                    $message = $message . ", " . $version;
                }

            }
            return $message . " Installed version is " . phpversion();
        } else {
            return "PHP Version should be higher than " . $this->systemRequirements['phpversion']['min']. " and lower than " . $this->systemRequirements['phpversion']['max'] . " Installed version is " . phpversion();
        }
    }

    public function getMysqlErrorMessage($host, $userName, $password) {
        $currentVersion = $this->getMySqlVersion($host, $userName, $password);
        if (strpos($currentVersion, 'MariaDB') !== false) {
            return $this->getDatabaseErrorMessage($currentVersion, $this->systemRequirements['mariadbversion'], 'MariaDB');
        } else {
            return $this->getDatabaseErrorMessage($currentVersion, $this->systemRequirements['mysqlversion'], 'MySql');
        }
    }

    private function isWithinRange($value, $range, $min, $max) {
        if ($range) {
            if (in_array($value, $range)) {
                return true;
            }
            return false;

        } else {
            if (version_compare($value, $min) >= 0 && version_compare($max, $value) >= 0) {
                return true;
            }
            return false;
        }
    }

    private function getMariaDbVersion($mariadbVersionString) {
        $mariaDBVersionArray = explode("-", $mariadbVersionString);
        $mariaDBVersion = $mariaDBVersionArray[0];

        return $mariaDBVersion;
    }

    private function getMySqlVersion($host, $userName, $password) {
        $mysqli = new mysqli($host, $userName, $password);

        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }
        $currentVersion = $mysqli->server_info;

        $mysqli->close();
        return $currentVersion;
    }

    private function getDatabaseErrorMessage($currentVersion, $versionData = array(), $dbServer) {
        if ($versionData['range']) {
            $message =  "Compatible ". $dbServer ." versions:";
            foreach ($versionData['range'] as $key => $version) {
                if ($key == 0) {
                    $message = $message . $version;
                } else {
                    $message = $message . ", " . $version;
                }
            }
            return $message . " Installed version is " . $currentVersion;
        } else {
            return $dbServer . " Version should be higher than " . $versionData['min']. " and lower than " . $versionData['max'] . " Installed version is " . $currentVersion;
        }
    }
}
