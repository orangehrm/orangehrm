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
class WorkExperienceForm extends sfForm {
    
    private $employeeService;
    public $fullName;
    private $widgets = array();
    public $workExperiences;

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

        //initializing the components
        $this->widgets = array(
            'emp_number' => new sfWidgetFormInputHidden(),
            'seqno' => new sfWidgetFormInputHidden(),
            'employer' => new sfWidgetFormInputText(),
            'jobtitle' => new sfWidgetFormInputText(),
            'from_date' => new sfWidgetFormInputText(),
            'to_date' => new sfWidgetFormInputText(),
            'comments' => new sfWidgetFormTextarea()
        );

        $this->widgets['emp_number']->setDefault($empNumber);
        $this->setWidgets($this->widgets);

        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
        
        $this->setValidator('emp_number', new sfValidatorString(array('required' => false)));
        $this->setValidator('seqno', new sfValidatorString(array('required' => false)));
        $this->setValidator('employer', new sfValidatorString(array('required' => true,
            'max_length' => 100)));
        $this->setValidator('jobtitle', new sfValidatorString(array('required' => true,
            'max_length' => 120)));

        $this->setValidator('from_date', new ohrmDateValidator(
                array('date_format'=>$inputDatePattern, 'required' => false),
                array('required'=>'Date field is required', 
                      'invalid'=>'Date format should be ' . strtoupper($inputDatePattern))));

        $this->setValidator('to_date', new ohrmDateValidator(
                array('date_format'=>$inputDatePattern, 'required' => false),
                array('required'=>'Date field is required', 
                      'invalid'=>'Date format should be ' . strtoupper($inputDatePattern))));

        $this->setValidator('comments', new sfValidatorString(array('required' => false,
            'max_length' => 200)));

        $this->workExperiences = $this->getEmployeeService()->getWorkExperience($empNumber);
        $this->widgetSchema->setNameFormat('experience[%s]');
    }
}
?>