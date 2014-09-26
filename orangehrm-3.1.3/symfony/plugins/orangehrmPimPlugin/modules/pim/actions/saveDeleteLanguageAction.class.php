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
class saveDeleteLanguageAction extends basePimAction {
    
    /**
     * @param sfForm $form
     * @return
     */
    public function setLanguageForm(sfForm $form) {
        if (is_null($this->languageForm)) {
            $this->languageForm = $form;
        }
    }
    
    public function execute($request) {
        $form = new DefaultListForm();
        $form->bind($request->getParameter($form->getName()));
        $language = $request->getParameter('language');
        $empNumber = (isset($language['emp_number']))?$language['emp_number']:$request->getParameter('empNumber');

        if (!$this->IsActionAccessible($empNumber)) {
            $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
        }
        $this->languagePermissions = $this->getDataGroupPermissions('qualification_languages', $empNumber);
        
        $this->setLanguageForm(new EmployeeLanguageForm(array(), array('empNumber' => $empNumber, 'languagePermissions' => $this->languagePermissions), true));

        if ($request->isMethod('post')) {
            if ( $request->getParameter('option') == "save") {

                $this->languageForm->bind($request->getParameter($this->languageForm->getName()));

                if ($this->languageForm->isValid()) {
                    $language = $this->getLanguage($this->languageForm);
                    if ($language != NULL) {
                        $this->getEmployeeService()->saveEmployeeLanguage($language);
                        $this->getUser()->setFlash('language.success', __(TopLevelMessages::SAVE_SUCCESS));
                    } 
                } else {
                    $this->getUser()->setFlash('language.warning', __('Form Validation Failed'));
                }
            }

            //this is to delete 
            if ($this->languagePermissions->canDelete()) {
                if ($request->getParameter('option') == "delete") {
                    $deleteIds = $request->getParameter('delLanguage');
                    $languagesToDelete = array();

                    foreach ($deleteIds as $value) {
                        $parts = explode("_", $value, 2);
                        if (count($parts) == 2) {
                            $languagesToDelete[] = array($parts[0] => $parts[1]); 
                        }
                    }

                    if (count($languagesToDelete) > 0) {
                        if ($form->isValid()) {
                            $this->getEmployeeService()->deleteEmployeeLanguages($empNumber, $languagesToDelete);
                            $this->getUser()->setFlash('language.success', __(TopLevelMessages::DELETE_SUCCESS));
                        }
                    }
                }
            }
        }
        $this->getUser()->setFlash('qualificationSection', 'language');
        $this->redirect('pim/viewQualifications?empNumber='. $empNumber . '#language');
    }

    private function getLanguage(sfForm $form) {

        $post = $form->getValues();

        $language = $this->getEmployeeService()->getEmployeeLanguages($post['emp_number'], $post['code'], $post['lang_type']);
        
        $isAllowed = FALSE;
        if (!$language instanceof EmployeeLanguage) {
            if($this->languagePermissions->canCreate()){
                $language = new EmployeeLanguage();
                $isAllowed = TRUE;
            }
        } else {
            if($this->languagePermissions->canUpdate()){
                $isAllowed = TRUE;
            } else {
                $this->getUser()->setFlash('warning', __("You don't have update permission"));
            }
        }
        if ($isAllowed) {
            $language->empNumber = $post['emp_number'];
            $language->langId = $post['code'];
            $language->fluency = $post['lang_type'];
            $language->competency = $post['competency'];
            $language->comments = $post['comments'];

            return $language;
        } else {
            return NULL;
        }
    }
}