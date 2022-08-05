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

namespace OrangeHRM\Admin\Controller\File;

use OrangeHRM\Admin\Service\LocalizationService;
use OrangeHRM\Core\Controller\AbstractFileController;
use OrangeHRM\Entity\I18NLanguage;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;

class LanguagePackage extends AbstractFileController
{
    /**
     * @var LocalizationService|null
     */
    protected ?LocalizationService $localizationService = null;

    /**
     * @return LocalizationService
     */
    public function getLocalizationService(): LocalizationService
    {
        if(!$this->localizationService instanceof LocalizationService) {
            $this->localizationService = new LocalizationService();
        }
        return $this->localizationService;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        $languageId = $request->attributes->get('languageId');
        $response = $this->getResponse();

        if ($languageId) {
            $language = $this->getLocalizationService()->getLocalizationDao()->getLanguageById($languageId);
            dump($language);
            if(!($language instanceof I18NLanguage) || !$language->isEnabled()) {
                $this->redirect('admin/languagePackage');
            }

            $zipFilePath = $this->getLocalizationService()->exportLanguagePackage($languageId);

        }
        return $this->handleBadRequest();
    }
}
