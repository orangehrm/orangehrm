<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2012 OrangeHRM Inc., http://www.orangehrm.com
 *
 * Please refer the file license/LICENSE.TXT for the license which includes terms and conditions on using this software.
 *
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