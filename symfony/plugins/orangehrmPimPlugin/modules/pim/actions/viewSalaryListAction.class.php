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

/**
 * viewSalaryAction
 */
class viewSalaryListAction extends basePimAction {

    public function execute($request) {

        $loggedInEmpNum = $this->getUser()->getEmployeeNumber();
        $loggedInUserName = $_SESSION['fname'];

        $salary = $request->getParameter('salary');
        $empNumber = (isset($salary['emp_number'])) ? $salary['emp_number'] : $request->getParameter('empNumber');
        $this->empNumber = $empNumber;
        $this->essUserMode = !$this->isAllowedAdminOnlyActions($loggedInEmpNum, $empNumber);

        $this->ownRecords = ($loggedInEmpNum == $empNumber) ? true : false;

        $this->salaryPermissions = $this->getDataGroupPermissions('salary_details', $empNumber);

        $adminMode = $this->getUser()->hasCredential(Auth::ADMIN_ROLE);
        $this->isSupervisor = $this->isSupervisor($loggedInEmpNum, $empNumber);

        $this->essMode = !$adminMode && !empty($loggedInEmpNum) && ($empNumber == $loggedInEmpNum);

        if (!$this->IsActionAccessible($empNumber)) {
            $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
        }

        $employee = $this->getEmployeeService()->getEmployee($empNumber);
        $params = array('empNumber' => $empNumber, 'ESS' => $this->essMode,
            'employee' => $employee,
            'loggedInUser' => $loggedInEmpNum,
            'loggedInUserName' => $loggedInUserName,
            'salaryPermissions' => $this->salaryPermissions);

        $this->form = new EmployeeSalaryForm(array(), $params, true);

        // TODO: Use embedForm or mergeForm?
        $this->directDepositForm = new EmployeeDirectDepositForm(array(), array(), true);

        if ($this->getRequest()->isMethod('post')) {

            // Handle the form submission    
            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {

                if ($this->salaryPermissions->canCreate() || $this->salaryPermissions->canUpdate()) {

                    $salary = $this->form->getSalary();

                    $setDirectDebit = $this->form->getValue('set_direct_debit');
                    $directDebitOk = true;

                    if (!empty($setDirectDebit)) {

                        $this->directDepositForm->bind($request->getParameter($this->directDepositForm->getName()));

                        if ($this->directDepositForm->isValid()) {
                            $this->directDepositForm->getDirectDeposit($salary);
                        } else {

                            $validationMsg = '';
                            foreach ($this->directDepositForm->getWidgetSchema()->getPositions() as $widgetName) {
                                if ($this->directDepositForm[$widgetName]->hasError()) {
                                    $validationMsg .= $widgetName . ' ' . __($this->directDepositForm[$widgetName]->getError()->getMessageFormat());
                                }
                            }

                            $this->getUser()->setFlash('warning', $validationMsg);
                            $directDebitOk = false;
                        }
                    } else {
                        $salary->directDebit->delete();
                        $salary->clearRelated('directDebit');
                    }

                    if ($directDebitOk) {
                        $service = $this->getEmployeeService();
                        $this->setOperationName('UPDATE SALARY');
                        $service->saveEmployeeSalary($salary);                

                        $this->getUser()->setFlash('salary.success', __(TopLevelMessages::SAVE_SUCCESS));  
                    }
                }
            } else {
                $validationMsg = '';
                foreach ($this->form->getWidgetSchema()->getPositions() as $widgetName) {
                    if ($this->form[$widgetName]->hasError()) {
                        $validationMsg .= $widgetName . ' ' . __($this->form[$widgetName]->getError()->getMessageFormat());
                    }
                }

                $this->getUser()->setFlash('warning', $validationMsg);
            }
            $this->redirect('pim/viewSalaryList?empNumber=' . $empNumber);  
        } else {
            if ($this->salaryPermissions->canRead()) {
                $this->salaryList = $this->getEmployeeService()->getEmployeeSalaries($empNumber);
            }
        }
        $this->listForm = new DefaultListForm();
    }

}
