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

require_once(realpath(dirname(__FILE__)).'/SystemDetailHelper.php');

class OrangeHrmRegistration
{
    private $sysConf = null;
    private $systemConfiguration = null;

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
     * Get instance of SystemConfiguration
     * @return SystemConfiguration systemConfiguration
     */
    private function getSystemConfigurationInstance() {
        if (is_null($this->systemConfiguration)) {
            $this->systemConfiguration = new SystemConfiguration();
        }
        return $this->systemConfiguration;
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

            $data = "username=" . $_SESSION['defUser']['AdminUserName']
                . "&email=" . $_SESSION['defUser']['organizationEmailAddress']
                . "&telephone=" . ($_SESSION['defUser']['contactNumber'] ? $_SESSION['defUser']['contactNumber'] : "Not captured")
                . "&admin_first_name=" . $_SESSION['defUser']['adminEmployeeFirstName']
                . "&admin_last_name=" . $_SESSION['defUser']['adminEmployeeLastName']
                . "&timezone=" . ($_SESSION['defUser']['timezone'] ? $_SESSION['defUser']['timezone'] : "Not captured")
                . "&language=" . ($_SESSION['defUser']['language'] ? $_SESSION['defUser']['language'] : "Not captured")
                . "&country=" . $_SESSION['defUser']['country']
                . "&organization_name=" . $_SESSION['defUser']['organizationName']
                . "&type=" . $_SESSION['defUser']['type']
                . "&instance_identifier=" . $this->getSystemConfigurationInstance()->createInstanceIdentifier($_SESSION['defUser']['organizationName'], $_SESSION['defUser']['organizationEmailAddress'], $_SESSION['defUser']['adminEmployeeFirstName'], $_SESSION['defUser']['adminEmployeeLastName'], $_SERVER['HTTP_HOST'], $_SESSION['country'], $this->getSysConf()->getVersion())
                . "&system_details=" . $this->getSystemDetails();

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
     * Return system details as a JSON string
     * @return string
     */
    private function getSystemDetails() {
        $sysDetailHelper = new SystemDetailHelper();
        return $sysDetailHelper->getSystemDetailsAsJson();
    }
}
