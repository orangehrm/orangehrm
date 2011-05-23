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
class EmployeeUsTaxExemptionsForm extends sfForm {

    private $countryService;
    private $employeeService;

    public function configure() {

        $status = array(0 => "-- " . __('Select') . " --", 'Single' => __('Single'), 'Married' => __('Married'), 'Other' => __('Other'));
        $states = $this->getStatesList();
        $empNumber = $this->getOption('empNumber');
        $employee = $this->getEmployeeService()->getEmployee($empNumber);
        $this->fullName = $employee->getFullName();
        $empTaxExemption = $this->getEmployeeService()->getEmployeeTaxExemptions($empNumber);

        //creating widgets
        $this->setWidgets(array(
            'empNumber' => new sfWidgetFormInputHidden(),
            'federalStatus' => new sfWidgetFormSelect(array('choices' => $status)),
            'federalExemptions' => new sfWidgetFormInputText(),
            'state' => new sfWidgetFormSelect(array('choices' => $states)),
            'stateStatus' => new sfWidgetFormSelect(array('choices' => $status)),
            'stateExemptions' => new sfWidgetFormInputText(),
            'unempState' => new sfWidgetFormSelect(array('choices' => $states)),
            'workState' => new sfWidgetFormSelect(array('choices' => $states)),
        ));

        $this->widgetSchema->setNameFormat('tax[%s]');

        //Setting validators
        $this->setValidators(array(
            'empNumber' => new sfValidatorString(array('required' => true)),
            'federalStatus' => new sfValidatorString(array('required' => false)),
            'federalExemptions' => new sfValidatorInteger(array('required' => false)),
            'state' => new sfValidatorString(array('required' => false)),
            'stateStatus' => new sfValidatorString(array('required' => false)),
            'stateExemptions' => new sfValidatorInteger(array('required' => false)),
            'unempState' => new sfValidatorString(array('required' => false)),
            'workState' => new sfValidatorString(array('required' => false)),
        ));

            $this->setDefault('empNumber', $empNumber);
            
        if($empTaxExemption != null){
        //setting the default values            
            $this->setDefault('federalStatus', $empTaxExemption->getFederalStatus());
            $this->setDefault('federalExemptions', $empTaxExemption->getFederalExemptions());
            $this->setDefault('state', $empTaxExemption->getState());
            $this->setDefault('stateStatus', $empTaxExemption->getStateStatus());
            $this->setDefault('stateExemptions', $empTaxExemption->getStateExemptions());
            $this->setDefault('unempState', $empTaxExemption->getUnemploymentState());
            $this->setDefault('workState', $empTaxExemption->getWorkState());

        }
    }

    /**
     * Get EmpUsTaxExemption object
     */
    public function getEmpUsTaxExemption() {
        
        $empNumber = $this->getValue('empNumber');
        $empUsTaxExemption = $this->getEmployeeService()->getEmployeeTaxExemptions($empNumber);

            if($empUsTaxExemption == null){
                $empUsTaxExemption = new EmpUsTaxExemption();
                $empUsTaxExemption->empNumber = $this->getValue('empNumber');
            }
            
        $empUsTaxExemption->federalStatus = $this->getValue('federalStatus');
        $empUsTaxExemption->federalExemptions = $this->getValue('federalExemptions');
        $empUsTaxExemption->state = $this->getValue('state');
        $empUsTaxExemption->stateStatus= $this->getValue('stateStatus');
        $empUsTaxExemption->stateExemptions = $this->getValue('stateExemptions');
        $empUsTaxExemption->unemploymentState = $this->getValue('unempState');
        $empUsTaxExemption->workState = $this->getValue('workState');

        return $empUsTaxExemption;
    }

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
     * Returns Country Service
     * @returns CountryService
     */
    public function getCountryService() {
        if (is_null($this->countryService)) {
            $this->countryService = new CountryService();
        }
        return $this->countryService;
    }

    /**
     * Returns States List
     * @return array
     */
    private function getStatesList() {
        $list = array("" => "-- " . __('Select') . " --");
        $states = $this->getCountryService()->getProvinceList();
        foreach ($states as $state) {
            $list[$state->province_code] = $state->province_name;
        }
        return $list;
    }

}
