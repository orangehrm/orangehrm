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
 * Actions class for PIM module dependents
 */
class viewDependentsAction extends basePimAction {

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
        
        $dependentParams = $request->getParameter('dependent');
        $empNumber = (isset($dependentParams['empNumber']))?$dependentParams['empNumber']:$request->getParameter('empNumber');
        $this->empNumber = $empNumber;

        $adminMode = $this->getUser()->hasCredential(Auth::ADMIN_ROLE);

        if ($this->getUser()->hasFlash('templateMessage')) {
            list($this->messageType, $this->message) = $this->getUser()->getFlash('templateMessage');
        }

        //hiding the back button if its self ESS view
        if($loggedInEmpNum == $empNumber) {
            $this->showBackButton = false;
        }
        
        $essMode = !$adminMode && !empty($loggedInEmpNum) && ($empNumber == $loggedInEmpNum);
        $param = array('empNumber' => $empNumber, 'ESS' => $essMode);

        $this->setForm(new EmployeeDependentForm(array(), $param, true));
        $this->deleteForm = new EmployeeDependentsDeleteForm(array(), $param, true);

        $this->dependents = $this->getEmployeeService()->getDependents($this->empNumber);
    }
}
