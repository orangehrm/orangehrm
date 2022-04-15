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

use OrangeHRM\Config\Config;
use OrangeHRM\ORM\Doctrine;

class SystemConfigurationHelper
{
    /**
     * @return array
     */
    public function getSystemDetailsAsArray(): array
    {
        return [
            'os' => $this->getOsDetails(),
            'php' => $this->getPhpDetails(),
            'mysql' => $this->getMySqlDetails(),
            'server' => $this->getServerDetails(),
            'ohrm' => $this->getOHRMDetails(),
        ];
    }

    /**
     * @return array
     */
    public function getOsDetails(): array
    {
        return [
            'os' => php_uname('s'),
            'release_name' => php_uname('r'),
            'version_info' => php_uname('v'),
        ];
    }

    /**
     * @return array
     */
    public function getPhpDetails(): array
    {
        return [
            'version' => phpversion()
        ];
    }

    /**
     * @return array
     */
    public function getServerDetails()
    {
        return $_SERVER['SERVER_SOFTWARE'];
    }

    /**
     * @return string[]
     */
    public function getMySqlDetails(): array
    {
        return [
            'client_version' => 'Not captured',
            'server_version' => $this->getMySqlServerVersion(),
            'conn_type' => 'Not captured',
        ];
    }

    /**
     * @return array
     */
    public function getOHRMDetails(): array
    {
        return [
            'version' => Config::PRODUCT_VERSION,
        ];
    }

    /**
     * @return false|string
     */
    public function getSystemDetailsAsJson()
    {
        return json_encode($this->getSystemDetailsAsArray());
    }

    /**
     * Return MySQL server version
     * @return string
     */
    public function getMySqlServerVersion()
    {
        return Doctrine::getEntityManager()->getConnection()
            ->getWrappedConnection()
            ->query('SELECT @@version')
            ->fetchOne();
    }
}
