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

class exportLanguagePackageAction extends baseAdminAction
{
    /**
     * @var null|I18NService
     */
    private $i18nService = null;

    public function execute($request)
    {
        $langId = $request->getParameter('langId');
        if (empty($langId)) {
            $this->redirect('admin/languagePackage');
        }
        $language = $this->getI18NService()->getLanguageById($langId);
        if (!($language instanceof I18NLanguage) || !$language->getEnabled()) {
            $this->redirect('admin/languagePackage');
        }

        $zipFilePath = $this->getI18NService()->exportLanguagePack($language->getCode());

        if (is_null($zipFilePath) || $zipFilePath == false) {
            $this->getUser()->setFlash('error', __('Failed to Export'), true);
            $this->redirect('admin/languagePackage');
        }

        $fileName = sprintf('i18n-%s.zip', $language->getCode());
        $fileLength = filesize($zipFilePath);

        $response = $this->getResponse();
        $response->setHttpHeader('Pragma', 'public');
        $response->setHttpHeader('Expires', '0');
        $response->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0, max-age=0");
        $response->setHttpHeader("Cache-Control", "private", false);
        $response->setHttpHeader("Content-Type", 'application/zip');
        $response->setHttpHeader("Content-Disposition", 'attachment; filename="' . $fileName . '";');
        $response->setHttpHeader("Content-Transfer-Encoding", "binary");
        $response->setHttpHeader("Content-Length", $fileLength);
        $response->setContent(file_get_contents($zipFilePath));
        $response->send();

        // Delete temp zip from symfony/cache directory
        unlink($zipFilePath);

        return sfView::NONE;
    }

    /**
     * @return I18NService
     */
    private function getI18NService()
    {
        if (is_null($this->i18nService)) {
            $this->i18nService = new I18NService();
        }
        return $this->i18nService;
    }
}
