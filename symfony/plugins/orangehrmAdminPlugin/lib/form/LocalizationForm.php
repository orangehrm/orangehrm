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

/**
 * Localization form class
 */
class LocalizationForm extends BaseForm {

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

    /**
     * the configure method
     */
    public function configure() {

        //Setting widgets
        $this->setWidgets(array(
            'dafault_language' => new sfWidgetFormSelect(array('choices' => $this->getLanguages())),
            'use_browser_language' => new sfWidgetFormInputCheckbox(),
            'default_date_format' => new sfWidgetFormSelect(array('choices' => $this->__getDateFormats()))
        ));

        //Setting validators
        $this->setValidators(array(
            'dafault_language' => new sfValidatorString(array('required' => false)),
            'use_browser_language' => new sfValidatorString(array('required' => false)),
            'default_date_format' => new sfValidatorString(array('required' => false))
        ));

        $this->widgetSchema->setNameFormat('localization[%s]');

        $useBrowserLanguage = $this->getConfigService()->getAdminLocalizationUseBrowserLanguage();
        $useBrowserLanguage = ($useBrowserLanguage == "Yes") ? 1 : null;

        //set default values
        $this->setDefaults(array(
            'dafault_language' => $this->getConfigService()->getAdminLocalizationDefaultLanguage(),
            'use_browser_language' => $useBrowserLanguage,
            'default_date_format' => $this->getConfigService()->getAdminLocalizationDefaultDateFormat()
        ));
    }

    /**
     * this is used to get the posted widget values
     * @return <type>
     */
    public function getFormValues() {

        return array('defaultLanguage' => $this->getValue('dafault_language'),
            'setBrowserLanguage' => $this->getValue('use_browser_language'),
            'defaultDateFormat' => $this->getValue('default_date_format'));
    }

    /**
     * To make date format array
     * User can eneble any of the commented date formats below if someone is going to write
     * more date formats the key values should be in the format of php and the values should
     * be according to the jQuery datepicker date format
     * @return string
     */
    private function __getDateFormats() {

        $dateFormats = array(
            'Y-m-d' => 'yyyy-mm-dd ( '.date('Y-m-d').' )',
            'd-m-Y' => 'dd-mm-yyyy ( '.date('d-m-Y').' )',
            'm-d-Y' => 'mm-dd-yyyy ( '.date('m-d-Y').' )',
            'Y-d-m' => 'yyyy-dd-mm ( '.date('Y-d-m').' )',
            'm-Y-d' => 'mm-yyyy-dd ( '.date('m-Y-d').' )',
            'd-Y-m' => 'dd-yyyy-mm ( '.date('d-Y-m').' )',
            'Y/m/d' => 'yyyy/mm/dd ( '.date('Y/m/d').' )',
            'Y m d' => 'yyyy mm dd ( '.date('Y m d').' )',
            'Y-M-d' => 'yyyy-M-dd ( '.date('Y-M-d').' )',
            'l, d-M-Y' => 'DD, dd-M-yyyy ( '.date('l, d-M-Y').' )',
            'D, d M Y' => 'D, dd M yyyy ( '.date('D, d M Y').' )'
        );
        return $dateFormats;
    }

    /**
     * this is used to make language list from supported_languages.yml
     * @return <type>
     */
    public function getLanguages() {
        $localizationService = new LocalizationService();
        return $localizationService->getSupportedLanguageListFromYML();
    }

}
