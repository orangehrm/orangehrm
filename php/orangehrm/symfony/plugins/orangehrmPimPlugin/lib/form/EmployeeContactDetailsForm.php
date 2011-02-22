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
class EmployeeConactDetailsForm extends sfForm {

    private $countryService;
    private $employeeService;
    private $widgets = array();
    public $fullName;

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

        $countries = $this->getCountryList();
        $states = $this->getStatesList();
        $empNumber = $this->getOption('empNumber');
        $employee = $this->getEmployeeService()->getEmployee($empNumber);
        $this->fullName = $employee->getFullName();

        //creating widgets
        $this->widgets['empNumber'] = new sfWidgetFormInputHidden();
        $this->widgets['country'] = new sfWidgetFormSelect(array('choices' => $countries));
        $this->widgets['state'] = new sfWidgetFormSelect(array('choices' => $states));
        $this->widgets['street1'] = new sfWidgetFormInput();
        $this->widgets['street2'] = new sfWidgetFormInput();
        $this->widgets['city'] = new sfWidgetFormInput();
        $this->widgets['province'] = new sfWidgetFormInput();
        $this->widgets['emp_zipcode'] = new sfWidgetFormInput();
        $this->widgets['emp_hm_telephone'] = new sfWidgetFormInput();
        $this->widgets['emp_mobile'] = new sfWidgetFormInput();
        $this->widgets['emp_work_telephone'] = new sfWidgetFormInput();
        $this->widgets['emp_work_email'] = new sfWidgetFormInput();
        $this->widgets['emp_oth_email'] = new sfWidgetFormInput();

        //setting the default values
        $this->widgets['empNumber']->setDefault($employee->empNumber);
        $this->widgets['country']->setDefault($employee->country);
        $this->widgets['state']->setDefault($employee->province);
        $this->widgets['street1']->setDefault($employee->street1);
        $this->widgets['street2']->setDefault($employee->street2);
        $this->widgets['city']->setDefault($employee->city);
        $this->widgets['province']->setDefault($employee->province);
        $this->widgets['emp_zipcode']->setDefault($employee->emp_zipcode);
        $this->widgets['emp_hm_telephone']->setDefault($employee->emp_hm_telephone);
        $this->widgets['emp_mobile']->setDefault($employee->emp_mobile);
        $this->widgets['emp_work_telephone']->setDefault($employee->emp_work_telephone);
        $this->widgets['emp_work_email']->setDefault($employee->emp_work_email);
        $this->widgets['emp_oth_email']->setDefault($employee->emp_oth_email);
        
        $this->setWidgets($this->widgets);

        //setting validators
        $this->setValidators(array(
                'empNumber' => new sfValidatorString(array('required' => true)),
                'country' => new sfValidatorString(array('required' => false)),
                'state' => new sfValidatorString(array('required' => false)),
                'street1' => new sfValidatorString(array('required' => false)),
                'street2' => new sfValidatorString(array('required' => false)),
                'city' => new sfValidatorString(array('required' => false)),
                'province' => new sfValidatorString(array('required' => false)),
                'emp_zipcode' => new sfValidatorString(array('required' => false)),
                'emp_hm_telephone' => new sfValidatorString(array('required' => false)),
                'emp_mobile' => new sfValidatorString(array('required' => false)),
                'emp_work_telephone' => new sfValidatorString(array('required' => false)),
                'emp_work_email' => new sfValidatorEmail(array('required' => false)),
                'emp_oth_email' => new sfValidatorEmail(array('required' => false)),
        ));

        $this->widgetSchema->setNameFormat('contact[%s]');
    }

    /**
     * Get Employee object
     */
    public function getEmployee() {
        $employee = new Employee();
        $employee->empNumber = $this->getValue('empNumber');
        $employee->street1 = $this->getValue('street1');
        $employee->street2 = $this->getValue('street2');
        $employee->city = $this->getValue('city');
        $employee->country = $this->getValue('country');

        $province = $this->getValue('province');
        if($employee->country == "US") {
            $province = $this->getValue('state');
        }

        $employee->province = $province;
        $employee->emp_zipcode = $this->getValue('emp_zipcode');
        $employee->emp_hm_telephone = $this->getValue('emp_hm_telephone');
        $employee->emp_mobile = $this->getValue('emp_mobile');
        $employee->emp_work_telephone = $this->getValue('emp_work_telephone');
        $employee->emp_work_email = $this->getValue('emp_work_email');
        $employee->emp_oth_email = $this->getValue('emp_oth_email');

        return $employee;
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
        $list = array(0 => __('Select Country'));
        $countries = $this->getCountryService()->getCountryList();
        foreach($countries as $country) {
            $list[$country->cou_code] = $country->cou_name;
        }
        return $list;
    }

    /**
     * Returns States List
     * @return array
     */
    private function getStatesList() {
        $list = array("" => __('Select State'));
        $states = $this->getCountryService()->getProvinceList();
        foreach($states as $state) {
            $list[$state->province_code] = $state->province_name;
        }
        return $list;
    }
}
?>