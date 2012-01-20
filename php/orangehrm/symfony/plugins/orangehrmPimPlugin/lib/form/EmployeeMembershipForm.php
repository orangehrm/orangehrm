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

        $memberships = $this->getMembershipList();
        $subscriptionPaidBy = array('' => "-- " . __('Select') . " --", 'Company' => __('Company'), 'Individual' => __('Individual'));
        $currency = $this->getCurrencyList();

        $empNumber = $this->getOption('empNumber');
        $employee = $this->getEmployeeService()->getEmployee($empNumber);
        $this->fullName = $employee->getFullName();

        //creating widgets
        $this->setWidgets(array(
            'empNumber' => new sfWidgetFormInputHidden(array(),
                    array('value' => $empNumber)),
            'membership' => new sfWidgetFormSelect(array('choices' => $memberships)),
            'subscriptionPaidBy' => new sfWidgetFormSelect(array('choices' => $subscriptionPaidBy)),
            'subscriptionAmount' => new sfWidgetFormInputText(),
            'currency' => new sfWidgetFormSelect(array('choices' => $currency)),
            'subscriptionCommenceDate' => new ohrmWidgetDatePickerNew(array(), array('id' => 'membership_subscriptionCommenceDate')),
            'subscriptionRenewalDate' => new ohrmWidgetDatePickerNew(array(), array('id' => 'membership_subscriptionRenewalDate'))
        ));


        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();

        //Setting validators
        $this->setValidators(array(
            'empNumber' => new sfValidatorNumber(array('required' => true, 'min' => 0)),
            'membership' => new sfValidatorChoice(array('required' => true, 'choices' => array_keys($memberships))),
            'subscriptionPaidBy' => new sfValidatorString(array('required' => false)),
            'subscriptionAmount' => new sfValidatorNumber(array('required' => false)),
            'currency' => new sfValidatorString(array('required' => false)),
            'subscriptionCommenceDate' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false),
                    array('invalid' => 'Date format should be ' . $inputDatePattern)),
            'subscriptionRenewalDate' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false),
                    array('invalid' => 'Date format should be ' . $inputDatePattern)),
        ));
        $this->widgetSchema->setNameFormat('membership[%s]');
    }

    /**
     * Returns Membership Type List
     * @return array
     */
    private function getMembershipList() {
        $list = array("" => "-- " . __('Select') . " --");
        $membershipList = $this->getMembershipService()->getMembershipList();
        foreach ($membershipList as $membership) {
            $list[$membership->getId()] = $membership->getName();
        }
        return $list;
    }

    /**
     * Returns Currency List
     * @return array
     */
    private function getCurrencyList() {
        $list = array("" => "-- " . __('Select') . " --");
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

        $empNumber = $this->getValue('empNumber');
        $membership = $this->getValue('membership');

        $employeeService = new EmployeeService();

        $membershipDetails = $employeeService->getMembershipDetail($empNumber, $membership);
        $membershipDetail = $membershipDetails[0];

        if ($membershipDetail->getEmpNumber() == null) {

            $membershipDetail = new EmployeeMemberDetail();
            $membershipDetail->empNumber = $empNumber;
            $membershipDetail->membershipCode = $membership;
        }

        $membershipDetail->subscriptionPaidBy = $this->getValue('subscriptionPaidBy');
        $membershipDetail->subscriptionAmount = $this->getValue('subscriptionAmount');
        $membershipDetail->subscriptionCurrency = $this->getValue('currency');

        $membershipDetail->subscriptionCommenceDate = $this->getValue('subscriptionCommenceDate');
        $membershipDetail->subscriptionRenewalDate = $this->getValue('subscriptionRenewalDate');

        $membershipDetail->save();
    }

}

