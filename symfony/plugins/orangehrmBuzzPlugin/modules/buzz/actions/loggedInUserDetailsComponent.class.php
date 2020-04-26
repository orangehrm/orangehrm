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
 * Description of loggedInUserDetailsComponent
 *
 * @author aruna
 */
class loggedInUserDetailsComponent extends sfComponent {

    private $employeeService;
    protected $buzzUserService;

    /**
     * 
     * @return BuzzUserService
     */
    protected function getBuzzUserService() {
        if (!$this->buzzUserService instanceof BuzzUserService) {
            $this->buzzUserService = new BuzzUserService();
        }
        return $this->buzzUserService;
    }

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
     * get logged In emplyee number from cookie service
     * @return Int
     */
    public function getLogedInEmployeeNumber() {
        $employeeNumber=$this->getBuzzUserService()->getEmployeeNumber();
        if(UserRoleManagerFactory::getUserRoleManager()->getUser() != null){
            $employeeNumber = $this->getUser()->getAttribute('auth.empNumber');
        }
        return $employeeNumber;
    }

    public function execute($request) {

        $this->empNumber = $this->getLogedInEmployeeNumber();

        $this->employee = $this->getEmployeeService()->getEmployee($this->empNumber);
        if ($this->employee) {
            $this->name = $this->employee->getFirstAndLastNames();
            $this->jobtitle = ' ' . $this->employee->getJobTitleName();
        } else {
            $this->name = 'Admin';
            $this->jobtitle = '  ';
        }
    }

}
