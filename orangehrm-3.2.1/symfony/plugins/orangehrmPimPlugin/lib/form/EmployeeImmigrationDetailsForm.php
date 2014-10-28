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
class EmployeeImmigrationDetailsForm extends sfForm {

    public $fullName;
    private $employeeService;
    private $countryService;
    public $empPassports;
    public $countries;
    
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
        $this->immigrationPermission = $this->getOption('immigrationPermission');
        
        $empNumber = $this->getOption('empNumber');
        $employee = $this->getEmployeeService()->getEmployee($empNumber);
        $this->fullName = $employee->getFullName();
        
        $this->empPassports = $this->getEmployeeService()->getEmployeeImmigrationRecords($empNumber);
        
        $widgets = array('emp_number' => new sfWidgetFormInputHidden(array(), array('value' => $empNumber)));
        $validators = array('emp_number' => new sfValidatorString(array('required' => true)));

        if ($this->immigrationPermission->canRead()) {
            $immigrationDetailsWidgets = $this->getImmigrationDetailsWidgets();
            $immigrationDetailsValidators = $this->getImmigrationDetailsValidators();

            if (!($this->immigrationPermission->canUpdate() || $this->immigrationPermission->canCreate())) {
                foreach ($immigrationDetailsWidgets as $widgetName => $widget) {
                    $widget->setAttribute('disabled', 'disabled');
                }
            }
            $widgets = array_merge($widgets, $immigrationDetailsWidgets);
            $validators = array_merge($validators, $immigrationDetailsValidators);
        }

        $this->setWidgets($widgets);
        $this->setValidators($validators);
        

        $this->widgetSchema->setNameFormat('immigration[%s]');
    }
    
    /*
     * get immigration form widgets
     * 
     */
     public function getImmigrationDetailsWidgets() {
        $widgets = array();
        $this->countries = $this->getCountryList();
        
        //creating widgets
        $widgets['seqno'] = new sfWidgetFormInputHidden();
        $widgets['type_flag'] = new sfWidgetFormChoice(array('expanded' => true, 'choices'  => array(
                    EmployeeImmigrationRecord::TYPE_PASSPORT => __('Passport'), EmployeeImmigrationRecord::TYPE_VISA => __('Visa')), 'default' => EmployeeImmigrationRecord::TYPE_PASSPORT));
        $widgets['country'] = new sfWidgetFormSelect(array('choices' => $this->countries));
        $widgets['number'] = new sfWidgetFormInputText();
        $widgets['i9_status'] = new sfWidgetFormInputText();
        $widgets['passport_issue_date'] =new ohrmWidgetDatePicker(array(), array('id' => 'immigration_passport_issue_date'));
        $widgets['passport_expire_date'] = new ohrmWidgetDatePicker(array(), array('id' => 'immigration_passport_expire_date'));
        $widgets['i9_review_date'] = new ohrmWidgetDatePicker(array(), array('id' => 'immigration_i9_review_date'));
        $widgets['comments'] =  new sfWidgetFormTextarea();
        
        return $widgets;
    }

    /*
     * get immigration form validators
     */
    public function getImmigrationDetailsValidators() {
        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
        
        $validators = array(
                'seqno' => new sfValidatorNumber(array('required' => false)),
                'type_flag' => new sfValidatorChoice(array('required' => true,
                        'choices' => array(EmployeeImmigrationRecord::TYPE_PASSPORT, EmployeeImmigrationRecord::TYPE_VISA))),
                'country' => new sfValidatorString(array('required' => false)),
                'number' => new sfValidatorString(array('required' => true, 'trim'=>true)),
                'i9_status' => new sfValidatorString(array('required' => false, 'trim'=>true)),
                'passport_issue_date' => new ohrmDateValidator(array('date_format'=>$inputDatePattern, 'required'=>false), array('invalid'=>"Date format should be". $inputDatePattern)),
                'passport_expire_date' => new ohrmDateValidator(array('date_format'=>$inputDatePattern, 'required'=>false), array('invalid'=>"Date format should be". $inputDatePattern)),
                'i9_review_date' => new ohrmDateValidator(array('date_format'=>$inputDatePattern, 'required'=>false), array('invalid'=>"Date format should be". $inputDatePattern)),
                'comments' => new sfValidatorString(array('required' => false))
        );
        return $validators;
    }

    /**
     * Returns Country Service
     * @returns CountryService
     */
    public function getCountryService() {
        if(is_null($this->countryService)) {
            $this->countryService = new CountryService();
        }
        return $this->countryService;
    }

    /**
     * Returns Country List
     * @return array
     */
    private function getCountryList() {
        $list = array("" => "-- " . __('Select') . " --");
        $countries = $this->getCountryService()->getCountryList();
        foreach($countries as $country) {
            $list[$country->cou_code] = $country->cou_name;
        }
        return $list;
    }

    public function populateEmployeePassport() {

        $immigrationRecord = $this->getEmployeeService()->getEmployeeImmigrationRecords($this->getValue('emp_number'), $this->getValue('seqno'));
        
        if(!$immigrationRecord instanceof EmployeeImmigrationRecord) {
            $immigrationRecord = new EmployeeImmigrationRecord();
        }

        $immigrationRecord->empNumber = $this->getValue('emp_number');
        $immigrationRecord->recordId = $this->getValue('seqno');
        $immigrationRecord->type = $this->getValue('type_flag');

        $country = $this->getValue('country');
        if(!empty($country)) {
            $immigrationRecord->countryCode = $country;
        } else {
            $immigrationRecord->countryCode = null;
        }

        $immigrationRecord->countryCode = $this->getValue('country');
        $immigrationRecord->number = $this->getValue('number');
        $immigrationRecord->status = $this->getValue('i9_status');
        $immigrationRecord->issuedDate = $this->getValue('passport_issue_date');
        $immigrationRecord->expiryDate = $this->getValue('passport_expire_date');
        $immigrationRecord->reviewDate = $this->getValue('i9_review_date');
        $immigrationRecord->notes = $this->getValue('comments');

        return $immigrationRecord;
        
    }
}
?>
