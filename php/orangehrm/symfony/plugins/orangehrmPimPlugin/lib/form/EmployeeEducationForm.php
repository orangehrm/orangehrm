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
class EmployeeEducationForm extends sfForm {
    
    private $employeeService;
    public $fullName;
    private $widgets = array();
    public $empEducationList;

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

        $this->empEducationList = $this->getEmployeeService()->getEmployeeEducationList($empNumber);

        //initializing the components
        $this->widgets = array(
            'id' => new sfWidgetFormInputHidden(),
            'emp_number' => new sfWidgetFormInputHidden(),
            'code' => new sfWidgetFormSelect(array('choices' => $this->_getEducationList())),
            'institute' => new sfWidgetFormInputText(),
            'major' => new sfWidgetFormInputText(),
            'year' => new sfWidgetFormInputText(),
            'gpa' => new sfWidgetFormInputText(),
            'start_date' => new ohrmWidgetDatePickerNew(array(), array('id' => 'education_start_date')),
            'end_date' => new ohrmWidgetDatePickerNew(array(), array('id' => 'education_end_date'))
        );

        $this->widgets['emp_number']->setDefault($empNumber);
        $this->setWidgets($this->widgets);

        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
        
        $this->setValidator('id', new sfValidatorString(array('required' => false)));
        $this->setValidator('emp_number', new sfValidatorString(array('required' => false)));
        $this->setValidator('code', new sfValidatorString(array('required' => true,
            'max_length' => 13)));
        $this->setValidator('institute', new sfValidatorString(array('required' => false,
            'max_length' => 100)));
        $this->setValidator('major', new sfValidatorString(array('required' => false,
            'max_length' => 100)));
        $this->setValidator('year', new sfValidatorNumber(array('required' => false, 'max'=>9999, 'min'=>0)));
        $this->setValidator('gpa', new sfValidatorString(array('required' => false,
            'max_length' => 25)));

        $this->setValidator('start_date', new ohrmDateValidator(
                array('date_format'=>$inputDatePattern, 'required' => false),
                array('invalid'=>'Date format should be'. $inputDatePattern)));

        $this->setValidator('end_date', new ohrmDateValidator(
                array('date_format'=>$inputDatePattern, 'required' => false),
                array('invalid'=>'Date format should be'. $inputDatePattern)));

        $this->widgetSchema->setNameFormat('education[%s]');
    }

    private function _getEducationList() {
        $educationService = new EducationService();
        $educationList = $educationService->getEducationList();
        $list = array("" => "-- " . __('Select') . " --");

        foreach($educationList as $education) {
            $list[$education->getId()] = $education->getName();
        }
        
        return $list;
    }
}
?>