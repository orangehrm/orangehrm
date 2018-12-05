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
 * Class installAddonAPI
 */
class installAddonAPIAction extends baseAddonAction
{
    /**
     * @param sfRequest $request
     * @return mixed|string
     * @throws CoreServiceException
     * @throws sfStopException
     */
    public function execute($request)
    {
        $addonList = $this->getAddons();
        $data = $request->getParameterHolder()->getAll();
        $addonId = $data['installAddonID'];
        $addonURL = null;
        foreach ($addonList as $addon) {
            if ($addon['id'] == $addonId) {
                $addonURL = $addon['links']['file'];
            }
        }
        $result = $this->getAddonFile($addonURL);
        echo json_encode($result);
        return sfView::NONE;
    }

    /**
     * @param $addonURL
     * @param $version
     * @return bool|string
     * @throws CoreServiceException
     */
    private function getAddonFile($addonURL)
    {
        $addon = $this->getApiManagerService()->getAddonFile($addonURL);
        if ($addon == 'Network Error') {
            return $addon;
        } else {
            return $this->installAddon($addon);
        }
    }

    /**
     * @param $addon
     * @return bool
     */
    protected function installAddon($addon)
    {
//    implement instalation part here and return weather Instalation "success" or "fail"
// you will receive addon base 64 encoded in parameters.
        return 'Success';
    }
}
