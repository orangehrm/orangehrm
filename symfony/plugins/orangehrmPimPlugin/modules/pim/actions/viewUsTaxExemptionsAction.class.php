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
class viewUsTaxExemptionsAction extends basePimAction {

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

    public function execute($request) {

        $loggedInEmpNum = $this->getUser()->getEmployeeNumber();

        $tax = $request->getParameter('tax');
        $empNumber = (isset($tax['empNumber']))?$tax['empNumber']:$request->getParameter('empNumber');
        $this->empNumber = $empNumber;
        $this->taxExemptionPermission = $this->getDataGroupPermissions('tax_exemptions', $empNumber);
        
        $this->essUserMode = !$this->isAllowedAdminOnlyActions($loggedInEmpNum, $empNumber);

        if (!$this->IsActionAccessible($empNumber)) {
            $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
        }

        $param = array('empNumber' => $empNumber, 'taxExemptionPermission' => $this->taxExemptionPermission);
        $this->form = new EmployeeUsTaxExemptionsForm(array(), $param, true);

        if ($this->getUser()->hasFlash('templateMessage')) {
            list($this->messageType, $this->message) = $this->getUser()->getFlash('templateMessage');
        }

        if ($this->taxExemptionPermission->canUpdate()){
            
            if ($request->isMethod('post')) {
                
                $this->form->bind($request->getParameter($this->form->getName()));
                if ($this->form->isValid()) {
                    $empUsTaxExemption = $this->form->getEmpUsTaxExemption();
                    $this->getEmployeeService()->saveEmployeeTaxExemptions($empUsTaxExemption, false);
                    $this->getUser()->setFlash('success', __(TopLevelMessages::SAVE_SUCCESS));
                    $this->redirect('pim/viewUsTaxExemptions?empNumber='. $empUsTaxExemption->getEmpNumber());
                }
            }
        }
    }

}

