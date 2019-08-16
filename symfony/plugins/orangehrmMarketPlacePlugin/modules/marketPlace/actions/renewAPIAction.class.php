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
 * Class renewAPI
 */

class renewAPIAction extends baseAddonAction
{
    /**
     * @param sfRequest $request
     * @return mixed|string
     */
    public function execute($request)
    {
        try {
            if (ini_get('max_execution_time') < 600) {
                ini_set('max_execution_time', 600);
            }
            $data = $request->getParameterHolder()->getAll();
            $addonId = $data['addonID'];
            $addonLicenseContent = $this->getApiManagerService()->getAddonLicense($addonId);
            $addon = $this->getMarcketplaceService()->getAddonById($addonId);
            if($addon instanceof Addon) {
                $pluginName = $addon->getPluginName();
                $addonName = $addon->getAddonName();
            }
            if (is_string($addonLicenseContent) && strlen($addonLicenseContent) > 0) {
                file_put_contents(sfConfig::get('sf_root_dir') . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . $pluginName . DIRECTORY_SEPARATOR . 'ohrm.license.php', $addonLicenseContent);
            } else {
                throw new Exception('Error when renewing the license file');
            }
            $result = $this->getMarcketplaceService()->changeAddonStatus(
                [$addonName],
                MarketplaceDao::ADDON_STATUS_RENEWED,
                MarketplaceDao::ADDON_STATUS_INSTALLED
            );
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
            echo json_encode($e->getCode());
            return sfView::NONE;
        }
    }
}
