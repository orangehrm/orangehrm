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
 * Class ohrmBuyNowAPIAction
 */
class ohrmBuyNowAPIAction extends baseAddonAction
{
    /**
     * @param sfRequest $request
     * @return mixed|string
     */
    public function execute($request)
    {
        try {
            $data = $request->getParameterHolder()->getAll();
            $addonId = $data['buyAddonID'];
            $addonList = $this->getAddons();
            foreach ($addonList as $addon) {
                if ($addon['id'] == $addonId) {
                    $addonDetail = $addon;
                }
            }
            $result = $this->buyNow($data, $addonDetail);
            echo json_encode($result);
            return sfView::NONE;
        } catch (GuzzleHttp\Exception\ConnectException $e) {
            Logger::getLogger("orangehrm")->error($e->getCode() . ' : ' . $e->getMessage());
            Logger::getLogger("orangehrm")->error($e->getTraceAsString());
            echo json_encode(self::ERROR_CODE_NO_CONNECTION);
            return sfView::NONE;
        } catch (Exception $e) {
            Logger::getLogger("orangehrm")->error($e->getCode() . ' : ' . $e->getMessage());
            Logger::getLogger("orangehrm")->error($e->getTraceAsString());
            echo json_encode(self::ERROR_CODE_EXCEPTION);
            return sfView::NONE;
        }
    }

    /**
     * @param $data
     * @param $addonDetail
     * @return string
     * @throws CoreServiceException
     * @throws DaoException
     */
    public function buyNow($data, $addonDetail)
    {
        $result = $this->getApiManagerService()->buyNowAddon($data);
        if(!$data['isRenew']) {
            $addonData = array(
                'id' => $addonDetail['id'],
                'addonName' => $addonDetail['title'],
                'type' => $addonDetail['type'],
                'status' => MarketplaceDao::ADDON_STATUS_REQUESTED,
                'version' => $addonDetail['version']['name']
            );
            $this->getMarcketplaceService()->installOrRequestAddon($addonData);
        } else {
            $this->getMarcketplaceService()->changeAddonStatus(
                [$addonDetail['title']],
                MarketplaceDao::ADDON_STATUS_EXPIRED,
                MarketplaceDao::ADDON_STATUS_RENEW_REQUESTED
            );
        }
        return $result;
    }
}
