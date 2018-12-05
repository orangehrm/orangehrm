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
 * Boston, MA 02110-1301, USA
 */

/**
 * Class MarketplaceService
 */
class MarketplaceService extends ConfigService
{
    private $marketplaceDao = null;
    /**
     * key name for ID in hs_hr_config table
     */
    const CLIENT_ID = "client_id";
    /**
     * key name for Scret in hs_hr_config table
     */
    const CLIENT_SECRET = "client_secret";
    /**
     * key name for marketpalce URL in hs_hr_config table
     */
    const BASE_URL = 'base_url';

    /**
     * @return array
     */
    public function getInstalledAddonIds()
    {
        $result = $this->getMarketplaceDao()->getInstalledAddonIds();
        return $result;
    }

    /**
     * @return MarketplaceDao
     */
    public function getMarketplaceDao()
    {
        if (!isset($this->marketplaceDao)) {
            $this->marketplaceDao = new MarketplaceDao();
        }
        return $this->marketplaceDao;
    }

    /**
     * @return String
     * @throws CoreServiceException
     */
    public function getClientId()
    {
        return $this->_getConfigValue(self::CLIENT_ID);
    }

    /**
     * @return String
     * @throws CoreServiceException
     */
    public function getClientSecret()
    {
        return $this->_getConfigValue(self::CLIENT_SECRET);
    }

    /**
     * @return String
     * @throws CoreServiceException
     */
    public function getBaseURL()
    {
        return $this->_getConfigValue(self::BASE_URL);
    }
}
