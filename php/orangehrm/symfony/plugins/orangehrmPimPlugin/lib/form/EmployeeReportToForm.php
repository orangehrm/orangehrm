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
class EmployeeReportToForm extends BaseForm {

    public $fullName;
    public $empNumber;
    private $employeeService;

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

    public function configure() {

        $reportingModeType = $this->getReportingMethodType();

        $this->empNumber = $this->getOption('empNumber');
        $employee = $this->getEmployeeService()->getEmployee($this->empNumber);
        $this->fullName = $employee->getFullName();

        //creating widgets
        $this->setWidgets(array(
            'empNumber' => new sfWidgetFormInputHidden(array(),
                    array('value' => $this->empNumber)),
            'type_flag' => new sfWidgetFormChoice(array('expanded' => true, 'choices' => array(
                    ReportTo::SUPERVISOR => __('Supervisor'), ReportTo::SUBORDINATE => __('Subordinate')), 'default' => ReportTo::SUPERVISOR)),
            'name' => new sfWidgetFormInputText(),
            'reportingModeType' => new sfWidgetFormSelect(array('choices' => $reportingModeType)),
            'reportingMethod' => new sfWidgetFormInputText()
        ));


        //Setting validators
        $this->setValidators(array(
            'empNumber' => new sfValidatorNumber(array('required' => true, 'min' => 0)),
            'type_flag' => new sfValidatorChoice(array('required' => true,
                'choices' => array(ReportTo::SUPERVISOR, ReportTo::SUBORDINATE))),
            'name' => new sfValidatorString(array('required' => true), array('required' => 'Employee name required')),
            'reportingModeType' => new sfValidatorString(array('required' => true), array('required' => 'Select reporting mode')),
            'reportingMethod' => new sfValidatorString(array('required' => false)),
        ));
        $this->widgetSchema->setNameFormat('reportto[%s]');
    }

    /**
     * Returns Reporting method Type
     * @return array
     */
    private function getReportingMethodType() {

        $list = array("" => "-- " . __('Select') . " --");

        $reportingModeTypes = $this->getEmployeeService()->getReportModeList();

        foreach ($reportingModeTypes as $reportingModeType) {
            $list[$reportingModeType->reportModeId] = $reportingModeType->reportModeName;
        }
        $list[-1] = __('Other');
        return $list;
    }

    public function getEmployeeListAsJson() {

        $jsonArray = array();
        $escapeCharSet = array(38, 39, 34, 60, 61, 62, 63, 64, 58, 59, 94, 96);
        $employeeService = $this->getEmployeeService();

        //if ($this->userType == 'Admin') {
        $employeeList = $employeeService->getEmployeeList();
        // } elseif ($this->userType == 'Supervisor') {
        //$employeeList = $employeeService->getSupervisorEmployeeChain($this->loggedInUserId);
        // }

        $employeeUnique = array();
        foreach ($employeeList as $employee) {

            if (!isset($employeeUnique[$employee->getEmpNumber()])) {

                $name = $employee->getFirstName() . " " . $employee->getMiddleName();
                $name = trim(trim($name) . " " . $employee->getLastName());

                foreach ($escapeCharSet as $char) {
                    $name = str_replace(chr($char), (chr(92) . chr($char)), $name);
                }

                $employeeUnique[$employee->getEmpNumber()] = $name;
                
                if ($employee->getEmpNumber() != $this->empNumber) {
                    $jsonArray[] = array('name' => $name, 'id' => $employee->getEmpNumber());
                }
            }
        }

        $jsonString = json_encode($jsonArray);

        return $jsonString;
    }

    /**
     * Save membership
     */
    public function save() {

        $empNumber = $this->getValue('empNumber');
        $membershipType = $this->getValue('membershipType');
        $membership = $this->getValue('membership');

        $employeeService = new EmployeeService();
        $membershipDetails = $employeeService->getMembershipDetail($empNumber, $membershipType, $membership);
        $membershipDetail = $membershipDetails[0];

        if ($membershipDetail->getEmpNumber() == null) {

            $membershipDetail = new EmployeeMemberDetail();
            $membershipDetail->empNumber = $empNumber;
            $membershipDetail->membershipTypeCode = $membershipType;
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

