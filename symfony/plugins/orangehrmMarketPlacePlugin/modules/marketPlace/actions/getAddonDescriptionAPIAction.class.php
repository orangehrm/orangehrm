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
 * Class getAddonDescriptionAPIAction
 */
class getAddonDescriptionAPIAction extends baseAddonAction
{
    /**
     * @param sfRequest $request
     * @return mixed|string
     * @throws CoreServiceException
     * return string is base 64 encoded html which is coming from MP backend
     * 0 indicate Marketplace network error
     * 1 indicate Marketplace middlewere down.
     */
    public function execute($request)
    {
        $data = $request->getParameterHolder()->getAll();
        $addonId = $data['addonID'];
        try {
            $addonList = $this->getAddons();
            foreach ($addonList as $addon) {
                if ($addon['id'] == $addonId) {
                    $addonDesCriptionURL = $addon['links']['desc'];
                }
            }
            $addonDescription = $this->getDescription($addonDesCriptionURL);
            $this->addonDescription = base64_decode($addonDescription);
        } catch (GuzzleHttp\Exception\ConnectException $e) {
            $this->getMarketPlaceLogger()->error($e->getCode() . ' : ' . $e->getMessage());
            $this->getMarketPlaceLogger()->error($e->getTraceAsString());
            echo json_encode(self::ERROR_CODE_NO_CONNECTION);
            return sfView::NONE;
        } catch (Exception $e) {
            $this->getMarketPlaceLogger()->error($e->getCode() . ' : ' . $e->getMessage());
            $this->getMarketPlaceLogger()->error($e->getTraceAsString());
            echo json_encode(self::ERROR_CODE_EXCEPTION);
            return sfView::NONE;
        }
    }

    /**
     * @param $addonDesCriptionURL
     * @return string
     * @throws CoreServiceException
     * @throws sfStopException
     */
    protected function getDescription($addonDesCriptionURL)
    {
        $description = $this->getApiManagerService()->getDescription($addonDesCriptionURL);
        return $description['description'];
    }
}
