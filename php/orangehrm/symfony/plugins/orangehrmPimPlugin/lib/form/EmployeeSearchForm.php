<?php

/*
  // OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
  // all the essential functionalities required for any enterprise.
  // Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com

  // OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
  // the GNU General Public License as published by the Free Software Foundation; either
  // version 2 of the License, or (at your option) any later version.

  // OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
  // without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
  // See the GNU General Public License for more details.

  // You should have received a copy of the GNU General Public License along with this program;
  // if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
  // Boston, MA  02110-1301, USA
 */

/**
 * Form class for employee list in PIM
 */
class EmployeeSearchForm extends BaseForm {

    private $userType;
    private $loggedInUserId;
    
    public function configure() {

        $this->userType =  $this->getOption('userType');
        $this->loggedInUserId =  $this->getOption('loggedInUserId');
        
        $this->setWidgets(array(
            'employee_name' => new sfWidgetFormInputText(),
            'supervisor_name' => new sfWidgetFormInputText(),
            'id' => new sfWidgetFormInputText(),
            'job_title' => new sfWidgetFormSelect(array('choices'=>array())),
            'employee_status' => new sfWidgetFormSelect(array('choices'=>array())),
            'sub_unit' => new sfWidgetFormSelect(array('choices'=>array())),

        ));

        $this->widgetSchema->setNameFormat('empsearch[%s]');

        $this->setValidators(array(
            'employee_name' => new sfValidatorString(array('required' => false)),
            'supervisor_name' => new sfValidatorString(array('required' => false)),
            'id' => new sfValidatorString(array('required' => false)),
            'job_title' => new sfValidatorString(array('required' => false)),
            'employee_status' => new sfValidatorString(array('required' => false)),
            'sub_unit' => new sfValidatorString(array('required' => false)),
        ));
    }

    public function getEmployeeListAsJson() {

        $jsonArray	=	array();
        $escapeCharSet = array(38, 39, 34, 60, 61,62, 63, 64, 58, 59, 94, 96);
        $employeeService = new EmployeeService();
        $employeeService->setEmployeeDao(new EmployeeDao());

        if ($this->userType == 'Admin') {
            $employeeList = $employeeService->getEmployeeList();
        } elseif ($this->userType == 'Supervisor') {

            $employeeList = $employeeService->getSupervisorEmployeeChain($this->loggedInUserId);

        }

        $employeeUnique = array();
        foreach($employeeList as $employee) {

            if(!isset($employeeUnique[$employee->getEmpNumber()])) {

                $name = $employee->getFirstName() . " " . $employee->getMiddleName();
                $name = trim(trim($name) . " " . $employee->getLastName());

                foreach($escapeCharSet as $char) {
                    $name = str_replace(chr($char), (chr(92) . chr($char)), $name);
                }

                $employeeUnique[$employee->getEmpNumber()] = $name;
                $jsonArray[] = array('name'=>$name, 'id' => $employee->getEmpNumber());
            }

        }

        $jsonString = json_encode($jsonArray);

        return $jsonString;

    }

}

