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
class saveDeleteLicenseAction extends basePimAction {
    
    /**
     * @param sfForm $form
     * @return
     */
    public function setLicenseForm(sfForm $form) {
        if (is_null($this->licenseForm)) {
            $this->licenseForm = $form;
        }
    }
    
    public function execute($request) {
        $form = new DefaultListForm();
        $form->bind($request->getParameter($form->getName()));
        $license = $request->getParameter('license');
        $empNumber = (isset($license['emp_number']))?$license['emp_number']:$request->getParameter('empNumber');

        if (!$this->IsActionAccessible($empNumber)) {
            $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
        }
        
        $this->licensePermissions = $this->getDataGroupPermissions('qualification_license', $empNumber);
        $this->setLicenseForm(new EmployeeLicenseForm(array(), array('empNumber' => $empNumber, 'licensePermissions' => $this->licensePermissions), true));

        if ($request->isMethod('post')) {
            if ( $request->getParameter('option') == "save") {

                $this->licenseForm->bind($request->getParameter($this->licenseForm->getName()));

                if ($this->licenseForm->isValid()) {
                    $license = $this->getLicense($this->licenseForm);
                    if (!empty($license)){
                        $this->getEmployeeService()->saveEmployeeLicense($license);
                        $this->getUser()->setFlash('license.success', __(TopLevelMessages::SAVE_SUCCESS));
                    }
                } else {
                    $this->getUser()->setFlash('license.warning', __('Form Validation Failed'));
                }
            }

            //this is to delete 
            if ($this->licensePermissions->canDelete()) {
                if ($request->getParameter('option') == "delete") {
                    $deleteIds = $request->getParameter('delLicense');

                    if(count($deleteIds) > 0) {
                        if ($form->isValid()) {
                            $this->getEmployeeService()->deleteEmployeeLicenses($empNumber, $request->getParameter('delLicense'));
                            $this->getUser()->setFlash('license.success', __(TopLevelMessages::DELETE_SUCCESS));
                        }
                    }
                }
            }
        }
        $this->getUser()->setFlash('qualificationSection', 'license');
        $this->redirect('pim/viewQualifications?empNumber='. $empNumber . '#license');
    }

    private function getLicense(sfForm $form) {

        $post = $form->getValues();

        $license = $this->getEmployeeService()->getEmployeeLicences($post['emp_number'], $post['code']);
        
        $isAllowed = FALSE;
        if (!$license instanceof EmployeeLicense) {
            if($this->licensePermissions->canCreate()){
                $license = new EmployeeLicense();
                $isAllowed = TRUE;
            }
        } else {
            if($this->licensePermissions->canUpdate()){
                $isAllowed = TRUE;                
            }
        }
        if ($isAllowed) {
            $license->empNumber = $post['emp_number'];
            $license->licenseId = $post['code'];
            $license->licenseNo = $post['license_no'];
            $license->licenseIssuedDate = $post['date'];
            $license->licenseExpiryDate = $post['renewal_date'];
            return $license;
        } else {
            return NULL;
        }       
                
    }
}