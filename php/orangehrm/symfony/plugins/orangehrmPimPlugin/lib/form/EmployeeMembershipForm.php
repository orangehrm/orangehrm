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
 * Form class for employee membership detail
 */
class EmployeeMembershipForm extends BaseForm {

    public $fullName;
    private $employeeService;
    private $membershipService;
    private $currencyService;

    /**
     * Get EmployeeService
     * @returns EmployeeService
     */
    public function getEmployeeService() {
        if (is_null($this->employeeService)) {
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

    /**
     * Returns Membership Service
     * @returns MembershipService
     */
    public function getMembershipService() {
        if (is_null($this->membershipService)) {
            $this->membershipService = new MembershipService();
        }
        return $this->membershipService;
    }

    /**
     * Returns Currency Service
     * @returns CurrencyService
     */
    public function getCurrencyService() {
        if (is_null($this->currencyService)) {
            $this->currencyService = new CurrencyService();
        }
        return $this->currencyService;
    }

    public function configure() {

        $membershipType = $this->getMembershipTypeList();
        $membership = array('' => "-- " . __('Select Membership') . " --");
        $subscriptionPaidBy = array('' => "-- " . __('Select') . " --", 'C' => __('Company'), 'I' => __('Individual'));
        $currency = $this->getCurrencyList();

        $empNumber = $this->getOption('empNumber');
        $employee = $this->getEmployeeService()->getEmployee($empNumber);
        $this->fullName = $employee->getFullName();

        //creating widgets
        $this->setWidgets(array(
            'empNumber' => new sfWidgetFormInputHidden(array(),
                    array('value' => $empNumber)),
            'membershipType' => new sfWidgetFormSelect(array('choices' => $membershipType)),
            'membership' => new sfWidgetFormSelect(array('choices' => $membership)),
            'subscriptionPaidBy' => new sfWidgetFormSelect(array('choices' => $subscriptionPaidBy)),
            'subscriptionAmount' => new sfWidgetFormInputText(),
            'currency' => new sfWidgetFormSelect(array('choices' => $currency)),
            'subscriptionCommenceDate' => new sfWidgetFormInputText(),
            'subscriptionRenewalDate' => new sfWidgetFormInputText(),
        ));

        $this->widgetSchema->setNameFormat('membership[%s]');
        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();

        //Setting validators
        $this->setValidators(array(
            'empNumber' => new sfValidatorNumber(array('required' => true, 'min' => 0)),
            'membershipType' => new sfValidatorString(array('required' => false)),
            'membership' => new sfValidatorString(array('required' => false)),
            'subscriptionPaidBy' => new sfValidatorString(array('required' => false)),
            'subscriptionAmount' => new sfValidatorNumber(array('required' => false)),
            'currency' => new sfValidatorString(array('required' => false)),
            'subscriptionCommenceDate' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false),
                    array('invalid' => 'Date format should be ' . strtoupper($inputDatePattern))),
            'subscriptionRenewalDate' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false),
                    array('invalid' => 'Date format should be ' . strtoupper($inputDatePattern))),
        ));
    }

    /**
     * Returns Membership Type List
     * @return array
     */
    private function getMembershipTypeList() {
        $list = array("" => "-- " . __('Select Membership Type') . " --");
        $membershipTypes = $this->getMembershipService()->getMembershipTypeList();
        foreach ($membershipTypes as $membershipType) {
            $list[$membershipType->getMembershipTypeCode()] = $membershipType->getMembershipTypeName();
        }
        return $list;
    }

    /**
     * Returns Currency List
     * @return array
     */
    private function getCurrencyList() {
        $list = array("" => "-- " . __('Select Currency Type') . " --");
        $currencies = $this->getCurrencyService()->getCurrencyList();
        foreach ($currencies as $currency) {
            $list[$currency->getCurrencyId()] = $currency->getCurrencyName();
        }
        return $list;
    }

     /**
     * Save membership
     */
    public function save() {

    }

}

