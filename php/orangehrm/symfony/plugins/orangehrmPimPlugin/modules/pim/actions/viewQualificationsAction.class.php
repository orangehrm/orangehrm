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
class viewQualificationsAction extends sfAction {

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
    public function setWorkExperienceForm(sfForm $form) {
        if (is_null($this->workExperienceForm)) {
            $this->workExperienceForm = $form;
        }
    }
    
    public function execute($request) {
        
        $loggedInEmpNum = $this->getUser()->getEmployeeNumber();
        $this->showBackButton = true;
        $empNumber = $request->getParameter('empNumber');
        $this->empNumber = $empNumber;

        //hiding the back button if its self ESS view
        if($loggedInEmpNum == $empNumber) {

            $this->showBackButton = false;
        }

         if ($this->getUser()->hasFlash('templateMessage')) {
            list($this->messageType, $this->message) = $this->getUser()->getFlash('templateMessage');
        }
        
        $adminMode = $this->getUser()->hasCredential(Auth::ADMIN_ROLE);
        $supervisorMode = $this->isSupervisor($loggedInEmpNum, $empNumber);

        if($empNumber != $loggedInEmpNum && (!$supervisorMode && !$adminMode)) {
            //shud b redirected 2 ESS user view
            $this->redirect('pim/viewQualifications?empNumber='. $loggedInEmpNum);
        }

        $this->setWorkExperienceForm(new WorkExperienceForm(array(), array('empNumber' => $empNumber), true));
        
        
    }

    private function isSupervisor($loggedInEmpNum, $empNumber) {

        if(isset($_SESSION['isSupervisor']) && $_SESSION['isSupervisor']) {

            $empService = $this->getEmployeeService();
            $subordinates = $empService->getSupervisorEmployeeList($loggedInEmpNum);

            foreach($subordinates as $employee) {
                if($employee->getEmpNumber() == $empNumber) {
                    return true;
                }
            }
        }
        return false;
    }
}
?>