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
 * Class ohrmAddonsAction
 */
class ohrmAddonsAction extends baseAddonAction
{
    private $apiManagerService = null;

    /**
     * @param sfRequest $request
     * @return mixed|void
     * @throws CoreServiceException
     */
    public function execute($request)
    {
        $this->isNetwork = true;
        $addonList = $this->getAddons();
        if (gettype($addonList) == 'array') {
            $this->addonList = $addonList;
            $this->installedAddons = $this->getInstalledAddons();
        } else {
            $this->isNetwork = false;
        }
    }

    /**
     * @return array
     * @throws CoreServiceException
     */
    public function getAddons()
    {
        $output = $this->getApiManagerService()->getAddons();
        return $output;
    }

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
}
