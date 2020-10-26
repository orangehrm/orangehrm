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

class saveLanguageCustomizationAction extends baseAdminAction
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

        $form = new DefaultListForm();
        if ($request->getMethod() === sfWebRequest::POST) {
            $form->bind($request->getParameter($form->getName()));

            if ($form->isValid()) {
                $changedTranslatedTexts = $request->getParameter('changedTranslatedText');
                if (!empty($changedTranslatedTexts)) {
                    $this->getI18NService()->saveTranslations($changedTranslatedTexts);
                    $this->getI18NService()->markLanguageAsModified($language->getCode());
                    $this->getUser()->setFlash('success', __(TopLevelMessages::SAVE_SUCCESS), true);
                }
            } else {
                $this->getUser()->setFlash('warning', __(TopLevelMessages::VALIDATION_FAILED), true);
                $this->handleBadRequest();
            }
        }

        $this->redirect($request->getReferer());
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
