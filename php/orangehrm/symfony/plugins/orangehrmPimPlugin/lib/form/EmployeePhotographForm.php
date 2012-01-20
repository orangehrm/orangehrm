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
class EmployeePhotographForm extends BaseForm {
    
    public $fullName;
    private $employeeService;
    private $widgets = array();

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
    
    public function configure() {

        $empNumber = $this->getOption('empNumber');
        $employee = $this->getEmployeeService()->getEmployee($empNumber);
        $this->fullName = $employee->getFullName();

        $this->widgets = array(
            'emp_number' => new sfWidgetFormInputHidden(),
            'photofile' => new sfWidgetFormInputFileEditable(
	        array(
	            'edit_mode'=>false,
	            'with_delete' => false,
	            'file_src' => '',
	        ))
        );
        
        $this->widgets['emp_number']->setDefault($employee->empNumber);
        $this->setWidgets($this->widgets);

        $this->setValidators(array(
            'emp_number' => new sfValidatorString(array('required' => true)),
            'photofile' =>  new sfValidatorFile(
	        array(
	            'max_size' => 1000000,
	            'required' => true,
	        ))
        ));
    }
}
?>
