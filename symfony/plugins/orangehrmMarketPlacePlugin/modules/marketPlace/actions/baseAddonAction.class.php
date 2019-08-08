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
 * Class baseAddonAction
 */
abstract class baseAddonAction extends sfAction
{
    /**
     * No Network Error Message
     */
    const NO_NETWORK_ERR_MESSAGE = "Please connect to the internet to view the available add-ons.";
    /**
     * Marketplace middlewere Error Message
     */
    const MP_MIDDLEWERE_ERR_MESSAGE = "Error Occur Please try again later";
    /**
     * network error code
     */
    const ERROR_CODE_NO_CONNECTION = 3000;
    /**
     * Marketplace error message code
     */
    const ERROR_CODE_EXCEPTION = 1;

    private $marcketplaceService = null;
    private $apiManagerService = null;
    private $addonList = null;

    /**
     * @return APIManagerService
     */
    protected function getApiManagerService()
    {
        if (!isset($this->apiManagerService)) {
            $this->apiManagerService = new APIManagerService();
        }
        return $this->apiManagerService;
    }

    /**
     * @return MarketplaceService|null
     */
    protected function getMarcketplaceService()
    {
        if (!isset($this->marcketplaceService)) {
            $this->marcketplaceService = new MarketplaceService();
        }
        return $this->marcketplaceService;
    }

    /**
     * @return array
     */
    protected function getInstalledAddons()
    {
        return $this->getMarcketplaceService()->getInstalledAddons();
    }

    /**
     * @param bool $includeDescription
     * @return array
     * @throws CoreServiceException
     */
    protected function getAddons($includeDescription = false)
    {
        if (!isset($this->addonList)) {
            $this->addonList = $this->getApiManagerService()->getAddons($includeDescription);
        }
        return $this->addonList;
    }
}
