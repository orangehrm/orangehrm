<?php

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
            $this->setDefault('date', $date);
       
            } else {
            
            $this->setDefault('employeeName', 'Type for hints...');
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
        $escapeCharSet = array(38, 39, 34, 60, 61, 62, 63, 64, 58, 59, 94, 96);
        $employeeService = new EmployeeService();
        $employeeService->setEmployeeDao(new EmployeeDao());

        $employeeUnique = array();
        foreach ($employeeList as $employee) {

            if (!isset($employeeUnique[$employee->getEmpNumber()])) {

                $name = $employee->getFirstName() . " " . $employee->getMiddleName();
                $name = trim(trim($name) . " " . $employee->getLastName());

                foreach ($escapeCharSet as $char) {
                    $name = str_replace(chr($char), (chr(92) . chr($char)), $name);
                }

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
        return $employee->getFirstName() . " " . $employee->getLastName();
    }

}

?>
