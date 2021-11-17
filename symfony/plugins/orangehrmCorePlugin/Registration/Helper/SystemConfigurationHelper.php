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
 */

namespace OrangeHRM\Core\Registration\Helper;

use OrangeHRM\Config\SysConf;

class SystemConfigurationHelper
{

    public function getSystemDetailsAsArray(): array
    {
        return array(
            "os" => $this->getOsDetails(),
            "php" => $this->getPhpDetails(),
            "mysql" => $this->getMySqlDetails(),
            "server" => $this->getServerDetails(),
            "ohrm" => $this->getOHRMDetails(),
        );
    }

    public function getOsDetails(): array
    {
        return array(
            "os" => php_uname('s'),
            "release_name" => php_uname('r'),
            "version_info" => php_uname('v'),
        );
    }

    public function getPhpDetails(): array
    {
        return array(
            "version" => phpversion()
        );
    }

    public function getServerDetails()
    {
        return $_SERVER['SERVER_SOFTWARE'];
    }

    public function getMySqlDetails(): array
    {
        return array(
            "client_version" => '',
            "server_version" => '',
            "conn_type" => ''
        );
    }

    public function getOHRMDetails(): array
    {
        $sysConf = new SysConf();
        $configs = $sysConf->getSysConfigs();
        return array(
            "version" => $configs['version']
        );
    }

    public function getSystemDetailsAsJson()
    {
        return json_encode($this->getSystemDetailsAsArray());
    }


}
