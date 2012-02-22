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
class AttendanceRecordSearchForm extends sfForm {

    public function configure() {

        $date = $this->getOption('date');
        $employeeId = $this->getOption('employeeId');
        $trigger = $this->getOption('trigger');

        $this->setWidgets(array(
            'employeeName' => new sfWidgetFormInputText(array(), array('class' => 'inputFormatHint', 'id' => 'employee')),
            'date' => new sfWidgetFormInputText(array(), array('class' => 'date', 'margin' => '0')),
            'employeeId' => new sfWidgetFormInputHidden(),
        ));

        if ($trigger) {
            
            $this->setDefault('employeeName', $this->getEmployeeName($employeeId));
            $this->setDefault('date', set_datepicker_date_format($date));
       
            } else {
            
            $this->setDefault('employeeName', __('Type for hints').'...');
        }

        $this->widgetSchema->setNameFormat('attendance[%s]');

        $this->setValidators(array(
            'date' => new sfValidatorDate(array(), array('required' => __('Enter Date'))),
            'employeeName' => new sfValidatorString(array(), array('required' => __('Enter Employee Name'))),
            'employeeId' => new sfValidatorString(),
        ));
    }

    public function getEmployeeListAsJson($employeeList) {

        $jsonArray = array();
        $employeeService = new EmployeeService();
        $employeeService->setEmployeeDao(new EmployeeDao());

        $employeeUnique = array();
        foreach ($employeeList as $employee) {

            if (!isset($employeeUnique[$employee->getEmpNumber()])) {

                $name = $employee->getFullName();
                $employeeUnique[$employee->getEmpNumber()] = $name;
                $jsonArray[] = array('name' => $name, 'id' => $employee->getEmpNumber());
                
            }
        }

        $jsonString = json_encode($jsonArray);

        return $jsonString;
    }

    public function getEmployeeName($employeeId) {

        $employeeService = new EmployeeService();
        $employee = $employeeService->getEmployee($employeeId);
        if($employee->getMiddleName()!= null){
        return $employee->getFirstName() . " " . $employee->getMiddleName()." ". $employee->getLastName();
        
        }
        else{
            return $employee->getFirstName() . " " . $employee->getLastName();
        }
    }

}

?>
