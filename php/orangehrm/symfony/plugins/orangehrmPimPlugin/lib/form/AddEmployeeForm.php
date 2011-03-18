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
class AddEmployeeForm extends sfForm {

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

        $status = array('' => "-- " . __('Select') . " --" , 'Enabled' => __('Enabled'), 'Disabled' => __('Disabled'));

        $idGenService = new IDGeneratorService();
        $idGenService->setEntity(new Employee());
        $empNumber = $idGenService->getNextID(false);
        $employeeId = str_pad($empNumber, 4, '0');
        
        $this->widgets = array(
                'photofile' => new sfWidgetFormInputFileEditable(array('edit_mode'=>false, 'with_delete' => false, 'file_src' => '')),
                'firstName' => new sfWidgetFormInputText(),
                'empNumber' => new sfWidgetFormInputHidden(),
                'lastName' => new sfWidgetFormInputText(),
                'middleName' => new sfWidgetFormInputText(),
                'employeeId' => new sfWidgetFormInputText(),
                'user_name' => new sfWidgetFormInputText(),
                'user_password' => new sfWidgetFormInputPassword(),
                're_password' => new sfWidgetFormInputPassword(),
                'status' => new sfWidgetFormSelect(array('choices' => $status))
        );

        $this->widgets['empNumber']->setDefault($empNumber);
        $this->widgets['employeeId']->setDefault($employeeId);
        $this->widgets['firstName']->setDefault($this->getOption('firstName'));
        $this->widgets['middleName']->setDefault($this->getOption('middleName'));
        $this->widgets['lastName']->setDefault($this->getOption('lastName'));
        $this->widgets['user_name']->setDefault($this->getOption('user_name'));
        $this->widgets['user_password']->setDefault($this->getOption('user_password'));
        $this->widgets['re_password']->setDefault($this->getOption('re_password'));
        $this->widgets['status']->setDefault($this->getOption('status'));

        $this->setWidgets($this->widgets);

        $this->setValidators(array(
                'photofile' =>  new sfValidatorFile(array('max_size' => 1000000, 'required' => false)),
                'firstName' => new sfValidatorString(array('required' => true, 'max_length' => 30)),
                'empNumber' => new sfValidatorString(array('required' => false)),
                'lastName' => new sfValidatorString(array('required' => true, 'max_length' => 30)),
                'middleName' => new sfValidatorString(array('required' => false, 'max_length' => 30)),
                'employeeId' => new sfValidatorString(array('required' => false, 'max_length' => 10)),
                'user_name' => new sfValidatorString(array('required' => false, 'max_length' => 20)),
                'user_password' => new sfValidatorString(array('required' => false, 'max_length' => 20)),
                're_password' => new sfValidatorString(array('required' => false, 'max_length' => 20)),
                'status' => new sfValidatorString(array('required' => false))
        ));
    }
}
?>
