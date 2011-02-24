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
        
        $empNumber = $this->getOption('empNumber');
        $employee = $this->getEmployeeService()->getEmployee($empNumber);
        $this->fullName = $employee->getFullName();
        $this->countries = $this->getCountryList();
        $this->empPassports = $this->getEmployeeService()->getEmployeePassport($empNumber);

        $this->setWidgets(array(
                'emp_number' => new sfWidgetFormInputHidden(array('default' => $empNumber)),
                'seqno' => new sfWidgetFormInputHidden(),
                'type_flag' => new sfWidgetFormChoice(array('expanded' => true, 'choices'  => array(
                    EmpPassport::TYPE_PASSPORT => __('Passport'), EmpPassport::TYPE_VISA => __('Visa')), 'default' => EmpPassport::TYPE_PASSPORT)),
                'country' => new sfWidgetFormSelect(array('choices' => $this->countries)),
                'number' => new sfWidgetFormInputText(),
                'i9_status' => new sfWidgetFormInputText(),
                'passport_issue_date' => new sfWidgetFormInputText(),
                'passport_expire_date' => new sfWidgetFormInputText(),
                'i9_review_date' => new sfWidgetFormInputText(),
                'comments' => new sfWidgetFormTextarea(),
        ));

        $this->setValidators(array(
                'emp_number' => new sfValidatorNumber(array('required' => false)),
                'seqno' => new sfValidatorNumber(array('required' => false)),
                'type_flag' => new sfValidatorChoice(array('required' => true,
                        'choices' => array(EmpPassport::TYPE_PASSPORT, EmpPassport::TYPE_VISA))),
                'country' => new sfValidatorString(array('required' => false)),
                'number' => new sfValidatorString(array('required' => true, 'trim'=>true)),
                'i9_status' => new sfValidatorString(array('required' => false, 'trim'=>true)),
                'passport_issue_date' => new sfValidatorString(array('required' => true, 'trim'=>true)),
                'passport_expire_date' => new sfValidatorString(array('required' => true, 'trim'=>true)),
                'i9_review_date' => new sfValidatorString(array('required' => false)),
                'comments' => new sfValidatorString(array('required' => false))
        ));

        $this->widgetSchema->setNameFormat('immigration[%s]');
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
        $list = array("" => __('Select Country'));
        $countries = $this->getCountryService()->getCountryList();
        foreach($countries as $country) {
            $list[$country->cou_code] = $country->cou_name;
        }
        return $list;
    }

    public function populateEmployeePassport() {

        $empPassport = $this->getEmployeeService()->getEmployeePassport($this->getValue('emp_number'), $this->getValue('seqno'));
        
        if(!$empPassport instanceof EmpPassport) {
            $empPassport = new EmpPassport();
        }

        $empPassport->emp_number = $this->getValue('emp_number');
        $empPassport->seqno = $this->getValue('seqno');
        $empPassport->type_flag = $this->getValue('type_flag');
        $empPassport->country = null;

        if($this->getValue('country') != '') {
            $empPassport->country = $this->getValue('country');
        }

        $empPassport->number = $this->getValue('number');
        $empPassport->i9_status = $this->getValue('i9_status');
        $empPassport->passport_issue_date = $this->getValue('passport_issue_date');
        $empPassport->passport_expire_date = $this->getValue('passport_expire_date');
        $empPassport->i9_review_date = $this->getValue('i9_review_date');
        $empPassport->comments = $this->getValue('comments');

        return $empPassport;
        
    }
}
?>
