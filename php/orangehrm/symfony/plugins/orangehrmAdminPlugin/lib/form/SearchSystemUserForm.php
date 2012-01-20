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
 *
 */
class SearchSystemUserForm extends BaseForm {

    private $systemUserService;

    public function getSystemUserService() {
        $this->systemUserService = new SystemUserService();
        return $this->systemUserService;
    }

    public function configure() {

        $userRoleList = $this->getPreDefinedUserRoleList();
        $statusList = $this->getStatusList();

        $this->setWidgets(array(
            'userName' => new sfWidgetFormInputText(),
            'userType' => new sfWidgetFormSelect(array('choices' => $userRoleList)),
            'employeeName' => new sfWidgetFormInputText(),
            'employeeId' => new sfWidgetFormInputHidden(),
            'status' => new sfWidgetFormSelect(array('choices' => $statusList)),
        ));

        $this->setValidators(array(
            'userName' => new sfValidatorString(array('required' => false)),
            'userType' => new sfValidatorString(array('required' => false)),
            'employeeName' => new sfValidatorString(array('required' => false)),
            'employeeId' => new sfValidatorString(array('required' => false)),
            'status' => new sfValidatorString(array('required' => false)),
        ));

        $this->widgetSchema->setNameFormat('searchSystemUser[%s]');
    }

    /**
     * Get Pre Defined User Role List
     * 
     * @return array
     */
    private function getPreDefinedUserRoleList() {
        $list = array();
        $list[] = __("All");
        $userRoles = $this->getSystemUserService()->getPreDefinedUserRoles();
        foreach ($userRoles as $userRole) {
            $list[$userRole->getId()] = $userRole->getName();
        }
        return $list;
    }

    private function getStatusList() {
        $list = array();
        $list[''] = __("All");
        $list['1'] = __("Enabled");
        $list['0'] = __("Disabled");

        return $list;
    }

    public function getEmployeeListAsJson() {

        $jsonArray = array();
        $employeeService = new EmployeeService();
        $employeeService->setEmployeeDao(new EmployeeDao());

        $employeeList = $employeeService->getEmployeeList();

        $employeeUnique = array();
        foreach ($employeeList as $employee) {
            $workShiftLength = 0;

            if (!isset($employeeUnique[$employee->getEmpNumber()])) {

                $name = $employee->getFullName();

                $employeeUnique[$employee->getEmpNumber()] = $name;
                $jsonArray[] = array('name' => $name, 'id' => $employee->getEmpNumber());
            }
        }

        $jsonString = json_encode($jsonArray);

        return $jsonString;
    }

    public function setDefaultDataToWidgets($searchClues) {
        $this->setDefault('userName', $searchClues['userName']);
        $this->setDefault('userType', $searchClues['userType']);
        $this->setDefault('employeeName', $searchClues['employeeName']);
        $this->setDefault('employeeId', $searchClues['employeeId']);
        $this->setDefault('status', $searchClues['status']);
    }

}

