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
		    'empStatusId' => new sfValidatorNumber(array('required' => false)),
		    'name' => new sfValidatorString(array('required' => true, 'max_length' => 52)),
		    'hours' => new sfValidatorNumber(array('required' => false)),
		    'availableEmp' => new sfValidatorString(array('required' => false)),
		    'assignedEmp' => new sfValidatorString(array('required' => false)),
		));

		$this->widgetSchema->setNameFormat('workShift[%s]');				
	}
	
	public function getEmployeeList(){
		
		$temp = array();
		$employeeService = new EmployeeService();
		$employeeService->setEmployeeDao(new EmployeeDao());
		$employeeList = $employeeService->getEmployeeList('empNumber', 'ASC', true);
		foreach ($employeeList as $employee){
			$temp[] = $employee->getFullName();
		}
		return $temp;		
	}
}

