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

        $this->paidAddonIds = $this->getMarcketplaceService()->getPaidAddonIds();
        $buyNowPendingAddons = $this->getMarcketplaceService()->getInstalationPendingAddonIds();
        $this->renewedAddonIds = $this->getMarcketplaceService()->getRenewedAddonIds();
        $renewPendingAddons = $this->getMarcketplaceService()->getRenewPendingAddonIds();

        if ($buyNowPendingAddons || $renewPendingAddons) {
            $readyToUseAddons = $this->getApiManagerService()->getAddonPaymentStatus();
            $readyToUseAddonNames = array_column($readyToUseAddons, 'title');
            if (count($readyToUseAddonNames) > 0) {
                $requestedToPaidCount = $this->getMarcketplaceService()->changeAddonStatus(
                    $readyToUseAddonNames,
                    MarketplaceDao::ADDON_STATUS_REQUESTED,
                    MarketplaceDao::ADDON_STATUS_PAID
                );

                if ($requestedToPaidCount > 0) {
                    $this->paidAddonIds = $this->getMarcketplaceService()->getPaidAddonIds();
                    $buyNowPendingAddons = array_diff($buyNowPendingAddons, $this->paidAddonIds);
                }

                $renewRequestedToRenewedCount = $this->getMarcketplaceService()->changeAddonStatus(
                    $readyToUseAddonNames,
                    MarketplaceDao::ADDON_STATUS_RENEW_REQUESTED,
                    MarketplaceDao::ADDON_STATUS_RENEWED
                );

                if ($renewRequestedToRenewedCount > 0) {
                    $this->renewedAddonIds = $this->getMarcketplaceService()->getRenewedAddonIds();
                    $renewPendingAddons = array_diff($renewPendingAddons, $this->renewedAddonIds);
                }
            }
        }

        $this->buyNowPendingAddon = $buyNowPendingAddons;
        $this->renewPendingAddons = $renewPendingAddons;
        $this->buyNowForm = new BuyNowForm();
        $this->dataGroupPermission = $this->getPermissions();
        $this->canRead = $this->dataGroupPermission->canRead();
        $this->canCreate = $this->dataGroupPermission->canCreate();
        $this->canDelete = $this->dataGroupPermission->canDelete();
        $this->exception = false;
        try {
            $addonList = $this->getAddons(true);
            $this->addonList = $addonList;
            $versionById = array_column($addonList, 'version', 'id');
            $expirationDates = $this->getMarcketplaceService()->getExpirationDatesOfInstalledPaidAddons();
            $this->expirationDates = $expirationDates;
            $this->getMarcketplaceService()->markExpiredAddons();
            $installAddons = $this->getInstalledAddons();
            $updatePendingAddons = [];
            $pluginsDir = sfConfig::get('sf_plugins_dir');
            foreach ($installAddons as $installAddon) {
                $addonId = $installAddon['id'];
                $filePath = $pluginsDir . DIRECTORY_SEPARATOR . $installAddon['PluginName'] . DIRECTORY_SEPARATOR .
                    'config' . DIRECTORY_SEPARATOR . 'app.yml';
                $content = sfYaml::load($filePath);
                $currentVersion = $content['all'][$installAddon['PluginName']]['version'];
                if (version_compare($currentVersion, $versionById[$addonId]['name']) === -1) {
                    $updatePendingAddons[] = $addonId;
                }
            }
            $this->paidTypeAddonIds = array_values(array_map(function($addon) {
                return $addon['id'];
            }, array_filter($addonList, function ($addon) {
                return $addon['type'] === 'paid';
            })));
            $this->updatePendingAddons = $updatePendingAddons;
            $this->installedAddons = array_column($installAddons, 'id');
            $this->expiredAddons = $this->getMarcketplaceService()->getExpiredAddons();
        } catch (GuzzleHttp\Exception\ConnectException $e) {
            $this->getMarketPlaceLogger()->error($e->getCode() . ' : ' . $e->getMessage());
            $this->getMarketPlaceLogger()->error($e->getTraceAsString());
            $this->exception = true;
            $this->errorMessage = self::NO_NETWORK_ERR_MESSAGE;
        } catch (Exception $e) {
            $this->exception = true;
            $this->errorMessage = self::MP_MIDDLEWERE_ERR_MESSAGE;
            $this->getMarketPlaceLogger()->error($e->getCode() . ' : ' . $e->getMessage());
            $this->getMarketPlaceLogger()->error($e->getTraceAsString());
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
