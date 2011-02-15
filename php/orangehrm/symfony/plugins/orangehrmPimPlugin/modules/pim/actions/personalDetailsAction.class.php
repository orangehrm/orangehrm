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
 * personalDetailsAction
 *
 */
class personalDetailsAction extends sfAction {

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
    public function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }

    public function execute($request) {
        $this->showBackButton = true;
        
        $personal = $request->getParameter('personal');
        $empNumber = (isset($personal['txtEmpID']))?$personal['txtEmpID']:$request->getParameter('empNumber');
        $this->empNumber = $empNumber;

        // TODO: Improve
        $adminMode = $this->getUser()->hasCredential(Auth::ADMIN_ROLE);
        if (!$adminMode) {
            $supervisorMode = $this->getUser()->hasCredential(Auth::SUPERVISOR_ROLE);
        } else {
            $supervisorMode = false;
        }

        $loggedInEmpNum = $this->getUser()->getEmployeeNumber();
        $essMode = !$adminMode && !empty($loggedInEmpNum) && ($empNumber == $loggedInEmpNum);
        $param = array('empNumber' => $empNumber, 'ESS' => $essMode);

        OrangeConfig::getInstance()->loadAppConf();
        $this->showDeprecatedFields = OrangeConfig::getInstance()->getAppConfValue(Config::KEY_PIM_SHOW_DEPRECATED);
        
        $this->setForm(new EmployeePersonalDetailsForm(array(), $param, true));
        if ($request->isMethod('post')) {

            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $employee = $this->form->getEmployee();
                $this->getEmployeeService()->savePersonalDetails($employee, $essMode);
                $this->redirect('pim/personalDetails?empNumber='. $empNumber);
            }
        }
    }
}
?>
