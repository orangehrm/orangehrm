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

class UpgradeOrangehrmRegistration
{
    private $sysConf = null;

    /**
     * Send the registration data captured during the installation
     * @return bool
     */
    public function sendRegistrationData() {
        $mode = $this->getSysConf()->getMode();
        if ($mode == sysConf::PROD_MODE) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->getSysConf()->getRegistrationUrl());
            curl_setopt($ch, CURLOPT_POST, 1);

            $data = "username=" . $_SESSION['defUser']['AdminUserName']
                . "&email=" . $_SESSION['defUser']['organizationEmailAddress']
                . "&telephone=" . $_SESSION['defUser']['contactNumber']
                . "&admin_first_name=" . $_SESSION['defUser']['adminEmployeeFirstName']
                . "&admin_last_name=" . $_SESSION['defUser']['adminEmployeeLastName']
                . "&timezone=" . $_SESSION['defUser']['timezone']
                . "&language=" . $_SESSION['defUser']['language']
                . "&country=" . $_SESSION['defUser']['country']
                . "&organization_name=" . $_SESSION['defUser']['organizationName']
                . "&type=" . $_SESSION['defUser']['type']
                . "&instance_identifier=" . $this->getInstanceIdentifier()
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
     * Create a unique instance identifier and return
     * @return string
     */
    private function getInstanceIdentifier() {
        return $_SESSION['defUser']['instanceIdentifier'];
    }

    /**
     * Get instance of sysConf
     * @return null|sysConf
     */
    private function getSysConf() {
        require_once(sfConfig::get('sf_root_dir') . "/../lib/confs/sysConf.php");

        if (is_null($this->sysConf)) {
            $this->sysConf = new sysConf();
        }
        return $this->sysConf;
    }

    /**
     * Return system details as a JSON string
     * @return string
     */
    private function getSystemDetails() {
        require_once(sfConfig::get('sf_root_dir') . "/../installer/SystemDetailHelper.php");
        $sysDetailHelper = new SystemDetailHelper();
        return $sysDetailHelper->getSystemDetailsAsJson();
    }
}
