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
class WorkShiftForm extends BaseForm {

    private $workShiftService;
    private $employeeService;
    private $employeeList;

    public function getWorkShiftService() {
        if (is_null($this->workShiftService)) {
            $this->workShiftService = new WorkShiftService();
            $this->workShiftService->setWorkShiftDao(new WorkShiftDao());
        }
        return $this->workShiftService;
    }
    
    public function getEmployeeService() {
        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }

    public function setEmployeeService(EmployeeService $employeeService) {
        $this->employeeService = $employeeService;
    }

    public function configure() {

        $employeeList = $this->getEmployeeList();
        $this->setWidgets(array(
            'workShiftId' => new sfWidgetFormInputHidden(),
            'name' => new sfWidgetFormInputText(array(), array("maxlength" => 52)),
            'workHours' => new ohrmWidgetFormTimeRange(array(
                'from_time' => new ohrmWidgetTimeDropDown(),
                'to_time' => new ohrmWidgetTimeDropDown())),
            'availableEmp' => new sfWidgetFormSelectMany(array('choices' => $employeeList)),
            'assignedEmp' => new sfWidgetFormSelectMany(array('choices' => array())),
        ));

        $this->setValidators(array(
            'workShiftId' => new sfValidatorNumber(array('required' => false)),
            'name' => new sfValidatorString(array('required' => true, 'max_length' => 52)),
            'workHours' => new ohrmValidatorTimeRange(array(
                'from_time' => new sfValidatorTime(array(
                    'required' => true,
                    'time_format' => "/(?P<hour>2[0-3]|[01][0-9]):(?P<minute>[0-5][0-9])/",
                    'time_output' => 'H:i')),
                'to_time' => new sfValidatorTime(array(
                    'required' => true,
                    'time_format' => "/(?P<hour>2[0-3]|[01][0-9]):(?P<minute>[0-5][0-9])/",
                    'time_output' => 'H:i'))
                    )),
            'availableEmp' => new sfValidatorPass(),
            'assignedEmp' => new sfValidatorPass(),
        ));

        $requiredMarker = '&nbsp;<em>*</em>';

        $labels = array(
            'name' => __('Shift Name') . $requiredMarker,
            'workHours' => __('Work Hours') . $requiredMarker
        );

        $this->getWidgetSchema()->setNameFormat('workShift[%s]');
        $this->getWidgetSchema()->setLabels($labels);
    }

    public function save() {

        $workShiftId = $this->getValue('workShiftId');
        
        if (empty($workShiftId)) {
            $workShift = new WorkShift();
            $empArray = $this->getValue('assignedEmp');            
        } else {
            $workShift = $this->getWorkShiftService()->getWorkShiftById($workShiftId);

            $employees = $this->getValue('assignedEmp');
            $existingEmployees = $workShift->getEmployeeWorkShift();
            $idList = array();
            if ($existingEmployees[0]->getEmpNumber() != "") {
                foreach ($existingEmployees as $existingEmployee) {
                    $id = $existingEmployee->getEmpNumber();
                    if (!in_array($id, $employees)) {
                        $existingEmployee->delete();
                    } else {
                        $idList[] = $id;
                    }
                }
            }

            $employeeList = array_diff($employees, $idList);
            $newList = array();
            foreach ($employeeList as $employee) {
                $newList[] = $employee;
            }
            $empArray = $newList;
        }
        
        $workShift->setName($this->getValue('name'));
        $workHours = $this->getValue('workHours');
        $workShift->setStartTime($workHours['from']);
        $workShift->setEndTime($workHours['to']);
        $workShift->setHoursPerDay($this->getDuration($workHours['from'], $workHours['to']));
        
        if (empty($workShiftId)) {
            $workShift->save();
        } else {
            $this->getWorkShiftService()->updateWorkShift($workShift);        
        }
        
        $this->_saveEmployeeWorkShift($workShift->getId(), $empArray);
    }

    private function _saveEmployeeWorkShift($workShiftId, $empArray) {
        $empWorkShiftCollection = new Doctrine_Collection('EmployeeWorkShift');
        
        for ($i = 0; $i < count($empArray); $i++) {
            $empWorkShift = new EmployeeWorkShift();
            $empWorkShift->setWorkShiftId($workShiftId);
            $empWorkShift->setEmpNumber($empArray[$i]);
            $empWorkShiftCollection->add($empWorkShift);
        }
        $this->getWorkShiftService()->saveEmployeeWorkShiftCollection($empWorkShiftCollection);
    }

    public function getEmployeeList() {

        $empNameList = array();

        $properties = array("empNumber", "firstName", "middleName", "lastName");
        $employeeList = $this->getEmployeeService()->getEmployeePropertyList($properties, 'lastName', 'ASC', true);

        $existWorkShiftEmpList = $this->getWorkShiftService()->getWorkShiftEmployeeIdList();

        foreach ($employeeList as $employee) {

            $empNumber = $employee['empNumber'];

            if (!in_array($empNumber, $existWorkShiftEmpList)) {

                $name = trim(trim($employee['firstName'] . ' ' . $employee['middleName'], ' ') . ' ' . $employee['lastName']);
                $empNameList[$empNumber] = $name;
            }
        }
        $this->employeeList = $empNameList;
        return $empNameList;
    }

    public function getEmployeeListAsJson() {

        $jsonArray = array();
        foreach ($this->employeeList as $key => $value) {
            $jsonArray[] = array('name' => $value, 'id' => $key);
        }
        
        return json_encode($jsonArray);
    }

    public function getWorkShiftListAsJson() {

        $jsonArray = array();        
        $workShiftList = $this->getWorkShiftService()->getWorkShiftList();

        foreach ($workShiftList as $workShift) {
            $jsonArray[] = array('name' => $workShift->getName(), 'id' => $workShift->getId());
        }

        return json_encode($jsonArray);
    }

    protected function getDuration($fromTime, $toTime) {
        list($startHour, $startMin) = explode(':', $fromTime);
        list($endHour, $endMin) = explode(':', $toTime);

        $durationMinutes = (intVal($endHour) - intVal($startHour)) * 60 + (intVal($endMin) - intVal($startMin));
        $hours = $durationMinutes / 60;

        return number_format($hours, 2);
    }

}

