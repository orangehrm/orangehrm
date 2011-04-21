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
class saveDeleteLanguageAction extends sfAction {

    private $employeeService;
    
    /**
     * Get EmployeeService
     * @returns EmployeeService
     */
    public function getEmployeeService() {
        if(is_null($this->employeeService)) {
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

        $language = $request->getParameter('language');
        $empNumber = (isset($language['emp_number']))?$language['emp_number']:$request->getParameter('empNumber');

        $this->setLanguageForm(new EmployeeLanguageForm(array(), array('empNumber' => $empNumber), true));

        if ($request->isMethod('post')) {
            if ( $request->getParameter('option') == "save") {

                $this->languageForm->bind($request->getParameter($this->languageForm->getName()));

                if ($this->languageForm->isValid()) {
                    $language = $this->getLanguage($this->languageForm);
                    $this->getEmployeeService()->saveLanguage($language);
                    $this->getUser()->setFlash('templateMessage', array('success', __('Language Details Saved Successfully')));
                } else {
                    $this->getUser()->setFlash('templateMessage', array('warning', __('Form Validation Failed.')));
                }
            }

            //this is to delete 
            if ($request->getParameter('option') == "delete") {
                $deleteIds = $request->getParameter('delLanguage');

                if(count($deleteIds) > 0) {var_dump($request->getParameter('delLanguage'));die;
                    $this->getEmployeeService()->deleteLanguage($empNumber, $request->getParameter('delLanguage'), $request->getParameter('lang_type'));
                    $this->getUser()->setFlash('templateMessage', array('success', __('Language Details(s) Deleted Successfully')));
                }
            }
        }

        $this->redirect('pim/viewQualifications?empNumber='. $empNumber);
    }

    private function getLanguage(sfForm $form) {

        $post = $form->getValues();

        $language = $this->getEmployeeService()->getLanguage($post['emp_number'], $post['code'], $post['lang_type']);

        if(!$language instanceof EmployeeLanguage) {
            $language = new EmployeeLanguage();
        }

        $language->emp_number = $post['emp_number'];
        $language->code = $post['code'];
        $language->lang_type = $post['lang_type'];
        $language->competency = $post['competency'];
        $language->comments = $post['comments'];

        return $language;
    }
}
?>