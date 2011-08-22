<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of viewEmployeeTimesheetForm
 *
 * @author orangehrm
 */
class viewEmployeeTimesheetForm extends sfFormSymfony {

    private $timesheetService;

    public function getTimesheetService() {

        if (is_null($this->timesheetService)) {

            $this->timesheetService = new TimesheetService();
        }

        return $this->timesheetService;
    }

    public function configure() {

        $this->setWidgets(array(
            'employeeName' => new sfWidgetFormInputText(array(), array('class' => 'inputFormatHint', 'id' => 'employee')),
            'employeeId' => new sfWidgetFormInputHidden(),
        ));

        $this->setDefault('employeeId', '23');
        $this->widgetSchema->setNameFormat('time[%s]');
        $this->setDefault('employeeName', 'Type for hints...');

        $this->setValidators(array(
            'employeeName' => new sfValidatorString(array(), array('required' => 'Enter Employee Name')),
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

}

?>
