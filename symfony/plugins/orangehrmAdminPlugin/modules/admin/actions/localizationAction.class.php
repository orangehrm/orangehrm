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
 * this action is used to set languages and the different date formats for the OrangeHRM
 */
class localizationAction extends sfAction {

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
     * to set Localization form
     * @param sfForm $form
     */
    public function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }

    /**
     * execute function
     * @param <type> $request
     */
    public function execute($request) {

        $this->setForm(new LocalizationForm());
        $languages = $this->getRequest()->getLanguages();
        $this->browserLanguage = $languages[0];

        if ($request->isMethod('post')) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                
                // For reloading main menu (index.php)
                $_SESSION['load.admin.localization'] = true;

                $formValues = $this->form->getFormValues();               
                $defaultLanguage = $formValues['defaultLanguage'];
                $setBrowserLanguage = !empty($formValues['setBrowserLanguage']) ? "Yes" : "No";
                $supprotedLanguages = $this->form->getLanguages();
                if($setBrowserLanguage == "Yes" && in_array($languages[0], $supprotedLanguages)){
                   $defaultLanguage = $languages[0];
                }
                $this->getUser()->setCulture($defaultLanguage);
                $this->getConfigService()->setAdminLocalizationDefaultLanguage($formValues['defaultLanguage']);
                $this->getConfigService()->setAdminLocalizationUseBrowserLanguage($setBrowserLanguage);
                $this->getUser()->setDateFormat($formValues['defaultDateFormat']);
                $this->getConfigService()->setAdminLocalizationDefaultDateFormat($formValues['defaultDateFormat']);

                $this->getUser()->setFlash('success', __(TopLevelMessages::SAVE_SUCCESS));
                $this->redirect("admin/localization");
            }
        }
    }   

}

