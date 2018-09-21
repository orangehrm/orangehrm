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

class OrangeHrmRegistration
{
    private $sysConf = null;
    private $systemValidator = null;

    /**
     * Get instance of sysConf
     * @return null|sysConf
     */
    private function getSysConf() {
        if (!defined('ROOT_PATH')) {
            $rootPath = realpath(dirname(__FILE__));
            define('ROOT_PATH', $rootPath);
        }
        require_once(ROOT_PATH . '/lib/confs/sysConf.php');
        if (is_null($this->sysConf)) {
            $this->sysConf = new sysConf();
        }
        return $this->sysConf;
    }

    /**
     * Get the registration URL
     * @return null|string
     */
    private function getRegistrationUrl() {
        return $this->getSysConf()->getRegistrationUrl();
    }
    /**
     * Send the registration data captured during the installation
     * @return bool
     */
    public function sendRegistrationData() {
        $mode = $this->getSysConf()->getMode();
        if ($mode == sysConf::PROD_MODE) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->getRegistrationUrl());
            curl_setopt($ch, CURLOPT_POST, 1);

            $platformData = array(
                "os" => $this->getOsDetails(),
                "php" => $this->getPhpDetails(),
                "mysql" => $this->getMySqlDetails(),
                "server" => $this->getServerDetails(),
                "ohrm" => $this->getOhrmDetails(),
            );

            $data = "username=" . $_SESSION['defUser']['AdminUserName']
                . "&email=" . $_SESSION['defUser']['organizationEmailAddress']
                . "&telephone=" . ($_SESSION['defUser']['contactNumber'] ? $_SESSION['defUser']['contactNumber'] : "Not captured")
                . "&admin_first_name=" . $_SESSION['defUser']['adminEmployeeFirstName']
                . "&admin_last_name=" . $_SESSION['defUser']['adminEmployeeLastName']
                . "&timezone=" . ($_SESSION['defUser']['timezone'] ? $_SESSION['defUser']['timezone'] : "Not captured")
                . "&language=" . ($_SESSION['defUser']['language'] ? $_SESSION['defUser']['language'] : "Not captured")
                . "&country=" . $_SESSION['defUser']['country']
                . "&organization_name=" . $_SESSION['defUser']['organizationName']
                . "&type=" . "0"
                . "&instance_identifier=" . $this->getInstanceIdentifier()
                . "&platform_data=" . json_encode($platformData);

            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if (!($http_status === 200)) {
                return false;
            } else {

                return true;
            }
        }
        return false;
    }

    /**
     * Create a unique instance identifier and return
     * @return string
     */
    private function getInstanceIdentifier() {
        $unencodedIdentifier = $_SESSION['defUser']['organizationName'] . '_' . $_SESSION['defUser']['organizationEmailAddress'] . '_' . date('Y-m-d') . $_SESSION['defUser']['randomNumber'];
        return base64_encode($unencodedIdentifier);
    }

    /**
     * Return running operating system details
     * @return array
     */
    private function getOsDetails() {
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
    private function getPhpDetails() {
        return array(
            "version" => $this->getSysValidator()->getPhpVersion()
        );
    }

    /**
     * Return MySQL details
     * @return array
     */
    private function getMySqlDetails() {
        return array(
            "client_version" => $this->getSysValidator()->getMySqlClientVersion(),
            "server_version" => $this->getSysValidator()->getMySqlServerVersion(),
            "conn_type" => $this->getSysValidator()->getMySqlConnectionType()
        );
    }

    /**
     * Return web server details
     * @return array
     */
    private function getServerDetails() {
        return array(
          "version" => $this->getSysValidator()->getServerDetails()
        );
    }

    /**
     * Return OrangeHRM details
     * @return array
     */
    private function getOhrmDetails() {
        return array(
            "version" => $this->getSysConf()->getVersion()
        );
    }

    /**
     * Return object of SystemValidator
     * @return null|SystemValidator
     */
    private function getSysValidator() {
        require_once(realpath(dirname(__FILE__)) . "/environmentCheck/SystemValidator.php");
        if (is_null($this->systemValidator)) {
            $this->systemValidator = new SystemValidator();
        }
        return $this->systemValidator;
    }
}