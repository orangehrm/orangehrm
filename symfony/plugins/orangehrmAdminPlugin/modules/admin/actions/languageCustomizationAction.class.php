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
 *
 */

class languageCustomizationAction extends baseAdminAction
{
    /**
     * @var null|I18NService
     */
    private $i18nService = null;

    public function execute($request)
    {
        // select menu item
        $request->setParameter('initialActionName', 'languagePackage');

        $langId = $request->getParameter('langId');
        if (empty($langId)) {
            $this->handleBadRequest();
            $this->forwardToSecureAction();
        }
        $language = $this->getI18NService()->getLanguageById($langId);
        if (!($language instanceof I18NLanguage) || !$language->getEnabled()) {
            $this->handleBadRequest();
            $this->forwardToSecureAction();
        }

        $this->getI18NService()->syncI18NTranslations($language->getCode());

        $translations = $this->getI18NService()->getTranslationsByCode($language->getCode());
        $this->_setListComponent($translations);
    }

    private function _setListComponent($translations)
    {
        $configurationFactory = $this->_getConfigurationFactory();
        $runtimeDefinitions = [];
        $buttons = [];
        $buttons['Save'] = ['label' => 'Save'];
        $buttons['Cancel'] = [
            'label' => 'Cancel',
            'class' => 'reset'
        ];

        $runtimeDefinitions['buttons'] = $buttons;
        $configurationFactory->setRuntimeDefinitions($runtimeDefinitions);
        ohrmListComponent::setActivePlugin('orangehrmAdminPlugin');
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setListData($translations);
        ohrmListComponent::setItemsPerPage(sfConfig::get('app_items_per_page'));
    }

    /**
     * @return LanguageCustomizationHeaderFactory
     */
    private function _getConfigurationFactory()
    {
        return new LanguageCustomizationHeaderFactory();
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
