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
        if (!($language instanceof I18NLanguage) || !$language->getEnabled() || !$language->getAdded()) {
            $this->handleBadRequest();
            $this->forwardToSecureAction();
        }

        $this->form = new SearchTranslationLanguageForm();
        if ($request->getMethod() === sfWebRequest::POST) {
            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {
                $baseUrl = url_for('admin/languageCustomization') . "?langId={$language->getId()}";
                if ($this->form->getValue('reset')) {
                    $this->redirect($baseUrl);
                }
                $query = http_build_query($this->getFilteredValues($this->form));
                $this->redirect($baseUrl . (empty($query) ? '' : '&' . $query));
            } else {
                $this->getUser()->setFlash('search.warning', __(TopLevelMessages::VALIDATION_FAILED), false);
                $this->handleBadRequest();
            }
        }

        $this->getI18NService()->syncI18NTranslations($language->getCode());

        $pageNo = intval($request->getParameter('pageNo', 1));
        $sortField = $request->getParameter('sortField', 'ls.value');
        $sortOrder = $request->getParameter('sortOrder', 'ASC');
        $limit = sfConfig::get('app_items_per_page');
        $offset = ($pageNo - 1) * $limit;

        $group = $request->getParameter('group');
        $sourceText = $request->getParameter('sourceText');
        $translatedText = $request->getParameter('translatedText');
        $translated = $request->getParameter('translated');
        $translated = is_null($translated) ? null : filter_var(
            $translated,
            FILTER_VALIDATE_BOOLEAN,
            FILTER_NULL_ON_FAILURE
        );

        $searchParams = new ParameterObject(
            [
                'sortField' => $sortField,
                'sortOrder' => $sortOrder,
                'offset' => $offset,
                'limit' => $limit,
                'group' => $group,
                'sourceText' => $sourceText,
                'translatedText' => $translatedText,
                'translated' => $translated,
                'langCode' => $language->getCode(),
            ]
        );

        $translations = $this->getI18NService()->searchTranslations($searchParams);
        $translationsCount = $this->getI18NService()->searchTranslations($searchParams, true);
        $this->_setListComponent($translations, $translationsCount, $language);

        $this->form->setDefaults(
            [
                'langPackage' => $language->getName(),
                'sourceLang' => __('English - US'),
                'group' => $searchParams->getParameter('group'),
                'sourceText' => $searchParams->getParameter('sourceText'),
                'translatedText' => $searchParams->getParameter('translatedText'),
                'translated' => $searchParams->getParameter('translated'),
            ]
        );
        $this->langId = $language->getId();
    }

    private function _setListComponent($translations, $count, $language)
    {
        $configurationFactory = $this->_getConfigurationFactory($language);
        $runtimeDefinitions = [];
        $buttons = [];
        $buttons['Save'] = [
            'label' => __('Save'),
            'class' => 'table-top-btn',
        ];
        $buttons['Cancel'] = [
            'label' => __('Cancel'),
            'class' => 'cancel table-top-btn',
        ];

        $runtimeDefinitions['formAction'] = 'admin/saveLanguageCustomization?langId=' . $language->getId();
        $runtimeDefinitions['buttons'] = $buttons;
        $configurationFactory->setRuntimeDefinitions($runtimeDefinitions);
        ohrmListComponent::setActivePlugin('orangehrmAdminPlugin');
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setListData($translations);
        ohrmListComponent::setNumberOfRecords($count);
        ohrmListComponent::setItemsPerPage(sfConfig::get('app_items_per_page'));
    }

    /**
     * @param I18NLanguage $language
     * @return LanguageCustomizationHeaderFactory
     */
    private function _getConfigurationFactory(I18NLanguage $language): LanguageCustomizationHeaderFactory
    {
        $configFactory = new LanguageCustomizationHeaderFactory();
        $configFactory->setLanguage($language);
        return $configFactory;
    }

    /**
     * @param SearchTranslationLanguageForm $form
     * @return array|null
     */
    private function getFilteredValues(SearchTranslationLanguageForm $form)
    {
        $values = $form->getValues();
        unset($values['reset']);
        foreach ($values as $key => $value) {
            if (is_null($value) || (is_string($value) && $value === '')) {
                unset($values[$key]);
            }
        }
        return $values;
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
