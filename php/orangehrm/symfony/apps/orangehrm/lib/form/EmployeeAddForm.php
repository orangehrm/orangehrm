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
 * Form class for add employee action
 */
class EmployeeAddForm extends BaseForm {

    public function configure() {

        // Note: Widget names were kept from old non-symfony version
        $this->setWidgets(array(
            'txtEmployeeId' => new sfWidgetFormInputText(),
            'txtEmpFirstName' => new sfWidgetFormInputText(),
			'txtEmpMiddleName' => new sfWidgetFormInputText(),
            'txtEmpLastName' => new sfWidgetFormInputText(),
            'txtEmpNickName' => new sfWidgetFormInputText(),

            // this parameter is for php file upload
            'MAX_FILE_SIZE' => new sfWidgetFormInputHidden(),
            'photofile' => new sfWidgetFormInputFile()
        ));

        $employeeService = new EmployeeService();

        $this->setDefault('txtEmployeeId', $employeeService->getDefaultEmployeeId());
        $this->setValidators(array(
            'txtEmployeeId' => new sfValidatorString(array('required' => false)),
            'txtEmpFirstName' => new sfValidatorString(array('required' => true),
                   array('required' => 'First Name Empty!')),
            'txtEmpMiddleName' => new sfValidatorString(array('required' => false)),
            'txtEmpLastName' => new sfValidatorString(array('required' => true),
                   array('required' => 'Last Name Empty!')),
            'txtEmpNickName' => new sfValidatorString(array('required' => false)),
            'MAX_FILE_SIZE' => new sfValidatorString(array('required' => true)),
            'photofile' => new sfValidatorFile(array('required' => false)),
        ));
    }

    /**
     * Get employee object with values filled using form values
     */
    public function getEmployee() {

        $employee = new Employee();
        $employee->employeeId = $this->getValue('txtEmployeeId');
        $employee->firstName = $this->getValue('txtEmpFirstName');
        $employee->middleName = $this->getValue('txtEmpMiddleName');
        $employee->lastName = $this->getValue('txtEmpLastName');
        $employee->nickName = $this->getValue('txtEmpNickName');

        return $employee;
    }

}

