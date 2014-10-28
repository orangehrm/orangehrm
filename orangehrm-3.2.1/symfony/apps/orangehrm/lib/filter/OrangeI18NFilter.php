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
class OrangeI18NFilter extends sfFilter {

    private $configService;

    /**
     * to get confuguration service
     * @return <type>
     */
    public function getConfigService() {
        if (is_null($this->configService)) {
            $this->configService = new ConfigService();
            $this->configService->setConfigDao(new ConfigDao());
        }
        return $this->configService;
    }

    /**
     *  to set configuration service
     * @param ConfigService $configService
     */
    public function setConfigService(ConfigService $configService) {
        $this->configService = $configService;
    }

    public function execute($filterChain) {

        $languages = $this->getContext()->getRequest()->getLanguages();
        $userCulture = $this->getConfigService()->getAdminLocalizationDefaultLanguage();
        $localizationService = new LocalizationService();
        $languageToSet = (!empty($languages[0]) && $this->getConfigService()->getAdminLocalizationUseBrowserLanguage() == "Yes" && key_exists($languages[0], $localizationService->getSupportedLanguageListFromYML())) ? $languages[0] : $userCulture;
        $datePattern = $this->getContext()->getUser()->getDateFormat();
        $datePattern = isset($datePattern) ? $datePattern : $this->getConfigService()->getAdminLocalizationDefaultDateFormat();

        $user = $this->getContext()->getUser();
        $user->setCulture($languageToSet);
        $user->setDateFormat($datePattern);

        // Execute next filter in filter chain
        $filterChain->execute();
    }

}