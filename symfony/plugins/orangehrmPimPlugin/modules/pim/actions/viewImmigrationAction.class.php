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
class viewImmigrationAction extends basePimAction {

    /**
     * @param sfForm $form
     * @return
     */
    public function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }

    public function execute($request) {
        
        $loggedInEmpNum = $this->getUser()->getEmployeeNumber();
        $this->showBackButton = true;
        $immigration = $request->getParameter('immigration');
        $empNumber = (isset($immigration['emp_number'])) ? $immigration['emp_number'] : $request->getParameter('empNumber');
        $this->empNumber = $empNumber;

        $this->immigrationPermission = $this->getDataGroupPermissions('immigration', $empNumber);

        //hiding the back button if its self ESS view
        if ($loggedInEmpNum == $empNumber) {

            $this->showBackButton = false;
        }

        $param = array('empNumber' => $empNumber, 'immigrationPermission' => $this->immigrationPermission);
        $this->setForm(new EmployeeImmigrationDetailsForm(array(), $param, true));
        $this->empPassportDetails = $this->getEmployeeService()->getEmployeeImmigrationRecords($this->empNumber);

        if (!$this->IsActionAccessible($empNumber)) {
            $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
        }
        
        if ($this->immigrationPermission->canUpdate() || $this->immigrationPermission->canCreate()) {
            if ($request->isMethod('post')) {

                $this->form->bind($request->getParameter($this->form->getName()));

                if ($this->form->isValid()) {
                    $empPassport = $this->form->populateEmployeePassport();
                    $this->getEmployeeService()->saveEmployeeImmigrationRecord($empPassport);
                    $this->getUser()->setFlash('immigration.success', __(TopLevelMessages::SAVE_SUCCESS));
                    $this->redirect('pim/viewImmigration?empNumber=' . $empNumber);
                }
            }
            $this->listForm = new DefaultListForm();
        }
    }

}
