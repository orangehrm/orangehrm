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
 * Actions class for PIM module ReportTo
 */
class viewReportToDetailsAction extends basePimAction {

    private $employeeService;

    /**
     * Get EmployeeService
     * @returns EmployeeService
     */
    public function getEmployeeService() {
        if (is_null($this->employeeService)) {
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
    public function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }

    public function execute($request) {

        $loggedInEmpNum = $this->getUser()->getEmployeeNumber();

        $reportTo = $request->getParameter('reportto');
        $empNumber = (isset($membership['empNumber'])) ? $membership['empNumber'] : $request->getParameter('empNumber');
        $this->empNumber = $empNumber;
        $this->essUserMode = !$this->isAllowedAdminOnlyActions($loggedInEmpNum, $empNumber);

        $adminMode = $this->getUser()->hasCredential(Auth::ADMIN_ROLE);
        $supervisorMode = $this->isSupervisor($loggedInEmpNum, $empNumber);

        if ($empNumber != $loggedInEmpNum && (!$supervisorMode && !$adminMode)) {
            $this->redirect('pim/viewReportToDetails?empNumber=' . $loggedInEmpNum);
        }

        if ($this->getUser()->hasFlash('templateMessage')) {
            list($this->messageType, $this->message) = $this->getUser()->getFlash('templateMessage');
        }

        $essMode = !$adminMode && !empty($loggedInEmpNum) && ($empNumber == $loggedInEmpNum);
        $param = array('empNumber' => $empNumber, 'ESS' => $essMode);

        $this->setForm(new EmployeeReportToForm(array(), $param, true));

        $this->deleteSupForm = new EmployeeReportToSupervisorDeleteForm(array(), $param, true);
        $this->deleteSubForm = new EmployeeReportToSubordinateDeleteForm(array(), $param, true);
        $this->supDetails = $this->getEmployeeService()->getSupervisorListForEmployee($this->empNumber);
        $this->subDetails = $this->getEmployeeService()->getSubordinateListForEmployee($this->empNumber);

    }

}
