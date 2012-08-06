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
class EmployeeSkillForm extends sfForm {
    
    private $employeeService;
    public $fullName;
    private $widgets = array();
    public $empSkillList;

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
        $this->skillPermissions = $this->getOption('skillPermissions');
        
        $empNumber = $this->getOption('empNumber');
        $employee = $this->getEmployeeService()->getEmployee($empNumber);
        $this->fullName = $employee->getFullName();

        $this->empSkillList = $this->getEmployeeService()->getEmployeeSkills($empNumber);

        $widgets = array('emp_number' => new sfWidgetFormInputHidden(array(), array('value' => $empNumber)));
        $validators = array('emp_number' => new sfValidatorString(array('required' => false)));
        
        if ($this->skillPermissions->canRead()) {

            $skillsWidgets = $this->getSkillsWidgets();
            $skillsValidators = $this->getSkillsValidators();

            if (!($this->skillPermissions->canUpdate() || $this->skillPermissions->canCreate()) ) {
                foreach ($skillsWidgets as $widgetName => $widget) {
                    $widget->setAttribute('disabled', 'disabled');
                }
            }
            $widgets = array_merge($widgets, $skillsWidgets);
            $validators = array_merge($validators, $skillsValidators);
        }

        $this->setWidgets($widgets);
        $this->setValidators($validators);


        $this->widgetSchema->setNameFormat('skill[%s]');
    }
    
    
    /*
     * Tis fuction will return the widgets of the form
     */
    public function getSkillsWidgets() {
        $widgets = array();

        //creating widgets
        $widgets['code'] = new sfWidgetFormSelect(array('choices' => $this->_getSkillList()));
        $widgets['years_of_exp'] = new sfWidgetFormInputText();
        $widgets['comments'] = new sfWidgetFormTextarea();

        return $widgets;
    }

    /*
     * Tis fuction will return the form validators
     */
    public function getSkillsValidators() {
        
        $validators = array(
            'code' => new sfValidatorString(array('required' => true, 'max_length' => 13)),
            'years_of_exp' => new sfValidatorNumber(array('required' => false)),
            'comments' => new sfValidatorString(array('required' => false, 'max_length' => 100)),
        );

        return $validators;
    }

    private function _getSkillList() {
        $skillService = new SkillService();
        $skillList = $skillService->getSkillList();
        $list = array("" => "-- " . __('Select') . " --");

        foreach($skillList as $skill) {
            $list[$skill->getId()] = $skill->getName();
        }
        
        // Clear already used skill items
        foreach ($this->empSkillList as $empSkill) {
            if (isset($list[$empSkill->skillId])) {
                unset($list[$empSkill->skillId]);
            }
        }
        return $list;
    }
}
?>