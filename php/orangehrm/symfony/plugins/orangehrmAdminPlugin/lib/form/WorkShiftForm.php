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

	public function getWorkShiftService() {
		if (is_null($this->workShiftService)) {
			$this->workShiftService = new WorkShiftService();
			$this->workShiftService->setWorkShiftDao(new WorkShiftDao());
		}
		return $this->workShiftService;
	}

	public function configure() {

		$employeeList = $this->getEmployeeList();
		$this->setWidgets(array(
		    'workShiftId' => new sfWidgetFormInputHidden(),
		    'name' => new sfWidgetFormInputText(),
		    'hours' => new sfWidgetFormInputText(),
		    'availableEmp' => new sfWidgetFormSelectMany(array('choices' => $employeeList)),
		    'assignedEmp' => new sfWidgetFormSelectMany(array('choices' => array())),
		));

		$this->setValidators(array(
		    'workShiftId' => new sfValidatorNumber(array('required' => false)),
		    'name' => new sfValidatorString(array('required' => true, 'max_length' => 52)),
		    'hours' => new sfValidatorNumber(array('required' => true)),
		    'availableEmp' => new sfValidatorPass(),
		    'assignedEmp' => new sfValidatorPass(),
		));

		$this->widgetSchema->setNameFormat('workShift[%s]');
	}

	public function save() {

		$workShiftId = $this->getValue('workShiftId');
		if (empty($workShiftId)) {
			$workShift = new WorkShift();
			$empArray = $this->getValue('assignedEmp');
			$workShift->setName($this->getValue('name'));
			$workShift->setHoursPerDay($this->getValue('hours'));
			$workShift->save();
		} else {
			$workShift = $this->getWorkShiftService()->getWorkShiftById($workShiftId);
			$workShift->setName($this->getValue('name'));
			$workShift->setHoursPerDay($this->getValue('hours'));
			$this->getWorkShiftService()->updateWorkShift($workShift);

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

			$this->resultArray = array();

			$employeeList = array_diff($employees, $idList);
			$newList = array();
			foreach ($employeeList as $employee) {
				$newList[] = $employee;
			}
			$empArray = $newList;
		}
		$this->_saveEmployeeWorkShift($workShift->getId(), $empArray);
	}

	private function _saveEmployeeWorkShift($workShiftId, $empArray) {

		for ($i = 0; $i < sizeof($empArray); $i++) {
			$empWorkShift = new EmployeeWorkShift();
			$empWorkShift->setWorkShiftId($workShiftId);
			$empWorkShift->setEmpNumber($empArray[$i]);
			$empWorkShift->save();
		}
	}

	public function getEmployeeList() {

		$temp = array();
		$existWorkShiftEmpList = array();
		$employeeService = new EmployeeService();
		$employeeService->setEmployeeDao(new EmployeeDao());
		$employeeList = $employeeService->getEmployeeList('lastName', 'ASC', true);

		$workShiftEmpList = $this->getWorkShiftService()->getWorkShiftEmployeeList();
		foreach ($workShiftEmpList as $workShiftEmp) {
			$existWorkShiftEmpList[] = $workShiftEmp->emp_number;
		}
		foreach ($employeeList as $employee) {
			if (!in_array($employee->getEmpNumber(), $existWorkShiftEmpList)) {
				$temp[$employee->getEmpNumber()] = $employee->getFullName();
			}
		}
		return $temp;
	}

	public function getEmployeeListAsJson() {

		$jsonArray = array();
		$existWorkShiftEmpList = array();
		$employeeService = new EmployeeService();
		$employeeService->setEmployeeDao(new EmployeeDao());

		$workShiftEmpList = $this->getWorkShiftService()->getWorkShiftEmployeeList();
		foreach ($workShiftEmpList as $workShiftEmp) {
			$existWorkShiftEmpList[] = $workShiftEmp->emp_number;
		}

		$employeeList = $employeeService->getEmployeeList('lastName', 'ASC', true);
		$employeeUnique = array();
		foreach ($employeeList as $employee) {

			if (!isset($employeeUnique[$employee->getEmpNumber()])) {

				$name = $employee->getFullName();

				$employeeUnique[$employee->getEmpNumber()] = $name;
				if (!in_array($employee->getEmpNumber(), $existWorkShiftEmpList)) {
					$jsonArray[] = array('name' => $name, 'id' => $employee->getEmpNumber());
				}
			}
		}
		$jsonString = json_encode($jsonArray);

		return $jsonString;
	}

	public function getWorkShiftListAsJson() {

		$jsonArray = array();
		$workShiftList = $this->getWorkShiftService()->getWorkShiftList();

		foreach ($workShiftList as $workShift) {
			$jsonArray[] = array('name' => $workShift->getName(), 'id' => $workShift->getId());
		}

		$jsonString = json_encode($jsonArray);
		return $jsonString;
	}

}

