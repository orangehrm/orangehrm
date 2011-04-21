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
class saveDeleteLicenseAction extends sfAction {

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
    public function setLicenseForm(sfForm $form) {
        if (is_null($this->licenseForm)) {
            $this->licenseForm = $form;
        }
    }
    
    public function execute($request) {

        $license = $request->getParameter('license');
        $empNumber = (isset($license['emp_number']))?$license['emp_number']:$request->getParameter('empNumber');

        $this->setLicenseForm(new EmployeeLicenseForm(array(), array('empNumber' => $empNumber), true));

        if ($request->isMethod('post')) {
            if ( $request->getParameter('option') == "save") {

                $this->licenseForm->bind($request->getParameter($this->licenseForm->getName()));

                if ($this->licenseForm->isValid()) {
                    $license = $this->getLicense($this->licenseForm);
                    $this->getEmployeeService()->saveLicense($license);
                    $this->getUser()->setFlash('templateMessage', array('success', __('License Details Saved Successfully')));
                } else {
                    $this->getUser()->setFlash('templateMessage', array('warning', __('Form Validation Failed.')));
                }
            }

            //this is to delete 
            if ($request->getParameter('option') == "delete") {
                $deleteIds = $request->getParameter('delLicense');

                if(count($deleteIds) > 0) {
                    $this->getEmployeeService()->deleteLicense($empNumber, $request->getParameter('delLicense'));
                    $this->getUser()->setFlash('templateMessage', array('success', __('License Details(s) Deleted Successfully')));
                }
            }
        }

        $this->redirect('pim/viewQualifications?empNumber='. $empNumber);
    }

    private function getLicense(sfForm $form) {

        $post = $form->getValues();

        $license = $this->getEmployeeService()->getLicense($post['emp_number'], $post['code']);

        if(!$license instanceof EmployeeLicense) {
            $license = new EmployeeLicense();
        }

        $license->emp_number = $post['emp_number'];
        $license->code = $post['code'];
        $license->license_no = $post['license_no'];
        $license->date = $post['date'];
        $license->renewal_date = $post['renewal_date'];

        return $license;
    }
}
?>