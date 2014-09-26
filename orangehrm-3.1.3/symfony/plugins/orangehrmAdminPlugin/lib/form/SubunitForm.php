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
class SubunitForm extends sfForm {

    protected $companyStructureService;

    /**
     *
     * @return EmployeeService 
     */
    public function getCompanyStructureService() {
        if (!($this->companyStructureService instanceof CompanyStructureService)) {
            $this->companyStructureService = new CompanyStructureService();
        }
        return $this->companyStructureService;
    }
    /**
     *
     * @param EmployeeService $employeeService 
     */
    public function setCompanyStructureService(CompanyStructureService $companyStructureService) {
        $this->companyStructureService = $companyStructureService;
    }
    
    public function configure() {
        $this->setWidgets(array(
            'hdnId' => new sfWidgetFormInputHidden(),
            'hdnParent' => new sfWidgetFormInputHidden(),
            'txtUnit_Id' => new sfWidgetFormInputText(),
            'txtName' => new sfWidgetFormInputText(),
            'txtDescription' => new sfWidgetFormTextArea(array(), array('rows' => 5, 'cols' => 20)),
        ));
        
        $this->setValidators(array(
            'hdnId' => new sfValidatorString(array('required' => TRUE, 'max_length' => 255)),
            'hdnParent' => new sfValidatorString(array('required' => TRUE, 'max_length' => 255)),
            'txtUnit_Id' => new sfValidatorString(array('required' => FALSE, 'max_length' => 255)),
            'txtName' => new sfValidatorString(array('required' => TRUE, 'max_length' => 255)),
            'txtDescription' => new sfValidatorString(array('required' => FALSE, 'max_length' => 400)),
        ));
        
        $required = '<span class="required new"> * </span>';
        $this->widgetSchema->setLabels(array(
            'txtUnit_Id' => __('Unit Id'),
            'txtName' => __('Name') . $required,
            'txtDescription' => __('Description'),
        ));
        
       // $this->widgetSchema->setNameFormat('companyStructure[%s]');
    }

    

}