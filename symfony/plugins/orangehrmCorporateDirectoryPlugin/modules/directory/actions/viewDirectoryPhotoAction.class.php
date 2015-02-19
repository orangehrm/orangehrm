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

/**
 * Displays Employee Photo in Corporate directory
 *
 */
class viewDirectoryPhotoAction extends basePimAction {

    private $employeeService;

    public function execute($request) {
        $empNumber = $request->getParameter('empNumber');

        $employeeService = $this->getEmployeeService();
        $empPicture = $employeeService->getEmployeePicture($empNumber);

        if (!empty($empPicture)){ 
            $contents = $empPicture->picture;
            $contentType = $empPicture->file_type;
            $fileSize = $empPicture->size;
            $fileName = $empPicture->filename;
        } else {
            $tmpName = ROOT_PATH . '/symfony/web/themes/' . $this->_getThemeName() . '/images/default-photo.png';
            $fp = fopen($tmpName,'r');
            $fileSize = filesize($tmpName);
            $contents = fread($fp, $fileSize);
            $contentType = "image/gif";
            fclose($fp);
        }
        
        $checksum = md5($contents);

        // Allow client side cache image unless image checksum changes.
        $eTag = $request->getHttpHeader('If-None-Match');

        $response = $this->getResponse();

        if ($eTag == $checksum) {
            $response->setStatusCode('304');
        } else {
            $response->setContentType($contentType);
            $response->setContent($contents);
        }

        // Setting "Pragra" header to null does not prevent it being added automatically
        // Therefore, we set this to "Public" to override "Pragma: no-cache" added because of settings in factory.yml        
        $response->setHttpHeader('Pragma', 'Public');

        $response->setHttpHeader('ETag', $checksum);
        $response->addCacheControlHttpHeader('public, max-age=0, must-revalidate');

        $response->send();

        return sfView::NONE;
    }

    /**
     * Get EmployeeService
     * @returns EmployeeService
     */
    public function getEmployeeService() {
        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }

    /**
     * Set EmployeeService
     * @param EmployeeService $employeeService
     */
    public function setEmployeeService(EmployeeService $employeeService) {
        $this->employeeService = $employeeService;
    }

    protected function _getThemeName() {

        $sfUser = $this->getUser();

        if (!$sfUser->hasAttribute('meta.themeName')) {
            $sfUser->setAttribute('meta.themeName', OrangeConfig::getInstance()->getAppConfValue(ConfigService::KEY_THEME_NAME));
        }

        return $sfUser->getAttribute('meta.themeName');
    }

}