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
    /**
     * marketpalce string to get permissions
     */
    const MARKETPLACE = 'Marketplace';

    private $dataGroupPermission = null;

    /**
     * @param sfRequest $request
     * @return mixed|void
     * @throws CoreServiceException
     * @throws DaoException
     */
    public function execute($request)
    {
        if (ini_get('max_execution_time') < 600) {
            ini_set('max_execution_time', 600);
        }
        $data = $this->getMarcketplaceService()->getInstalationPendingAddonIds();
        $this->buyNowPendingAddon = $data;
        $this->buyNowForm = new BuyNowForm();
        $this->dataGroupPermission = $this->getPermissions();
        $this->canRead = $this->dataGroupPermission->canRead();
        $this->canCreate = $this->dataGroupPermission->canCreate();
        $this->canDelete = $this->dataGroupPermission->canDelete();
        $this->exception = false;
        try {
            $addonList = $this->getAddons();
            $this->addonList = $addonList;
            $installAddons = $this->getInstalledAddons();
            $this->installedAddons = $installAddons[0];
        } catch (GuzzleHttp\Exception\ConnectException $e) {
            Logger::getLogger("orangehrm")->error($e->getCode() . ' : ' . $e->getMessage());
            Logger::getLogger("orangehrm")->error($e->getTraceAsString());
            $this->exception = true;
            $this->errorMessage = self::NO_NETWORK_ERR_MESSAGE;
        } catch (Exception $e) {
            $this->exception = true;
            $this->errorMessage = self::MP_MIDDLEWERE_ERR_MESSAGE;
            Logger::getLogger("orangehrm")->error($e->getCode() . ' : ' . $e->getMessage());
            Logger::getLogger("orangehrm")->error($e->getTraceAsString());
        }
    }

    /**
     * @param $dataGroups
     * @param bool $self
     * @return mixed
     */
    protected function getDataGroupPermissions($dataGroups, $self = false)
    {
        return $this->getContext()->getUserRoleManager()->getDataGroupPermissions($dataGroups, array(), array(), $self, array());
    }

    /**
     * @return mixed
     */
    protected function getPermissions()
    {
        return $this->getDataGroupPermissions(self::MARKETPLACE, false);
    }
}
