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
 * Form class for employee salary detail
 */
class EmployeeSalaryForm extends BaseForm {
    
    public $fullName;
    
    private $currencyService;
    
    public $havePayGrades = false;
    

    /**
     * Get CurrencyService
     * @returns CurrencyService
     */
    public function getCurrencyService() {
        if(is_null($this->currencyService)) {
            $this->currencyService = new CurrencyService();
        }
        return $this->currencyService;
    }

    /**
     * Set CurrencyService
     * @param CurrencyService $currencyService
     */
    public function setCurrencyService(CurrencyService $currencyService) {
        $this->currencyService = $currencyService;
    }
    
    public function configure() {
        
        $empNumber = $this->getOption('empNumber');
        $employee = $this->getOption('employee');
        $this->fullName = $employee->getFullName();
        
        $payGrades = $this->_getPayGrades();
        $currencies = $this->_getCurrencies();
        $payPeriods = $this->_getPayPeriods();
        
        // Note: Widget names were kept from old non-symfony version
        $this->setWidgets(array(
            'id' => new sfWidgetFormInputHidden(),
            'emp_number' => new sfWidgetFormInputHidden(),
            'currency_id' => new sfWidgetFormSelect(array('choices'=>$currencies)),
            'basic_salary' => new sfWidgetFormInputText(),
            'payperiod_code' =>  new sfWidgetFormSelect(array('choices'=>$payPeriods)),
	    'salary_component' => new sfWidgetFormInputText(),
	    'comments' => new sfWidgetFormTextArea(),
            'set_direct_debit' => new sfWidgetFormInputCheckbox(),
        ));

        $this->setDefault('emp_number', $empNumber);
        
        if (count($payGrades) > 0) {
            $this->havePayGrades = true;            
            $this->setWidget('sal_grd_code', new sfWidgetFormSelect(array('choices'=>$payGrades)));
        } else {
            $this->setWidget('sal_grd_code', new sfWidgetFormInputHidden());
        }
        
        // Remove default options from list validated against
        unset($payGrades['']);
        unset($currencies['']);
        unset($payPeriods['']);
        
        $this->setValidators(array(
            'id' => new sfValidatorNumber(array('required' => false, 'min'=> 0)),
            'emp_number' => new sfValidatorNumber(array('required' => true, 'min'=>0)),
            'currency_id' => new sfValidatorChoice(array('required' => true, 'choices' => array_keys($currencies))),
            'basic_salary' => new sfValidatorNumber(array('required' => true, 'trim'=>true, 'min' => 0, 'max'=> 999999999.99)),
            'payperiod_code' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($payPeriods))),
            'salary_component' => new sfValidatorString(array('required' => false, 'max_length'=>100)),
            'comments' => new sfValidatorString(array('required' => false, 'max_length'=>255)),
            'set_direct_debit' => new sfValidatorString(array('required' => false)),
        ));

        if ($this->havePayGrades) {
            $this->setValidator('sal_grd_code', new sfValidatorChoice(array('required' => false, 'choices' => array_keys($payGrades))));
        } else {
            // We do not expect a value. Validate as an empty string
            $this->setValidator('sal_grd_code', new sfValidatorString(array('required' => false, 'max_length' => 10)));
        }
        
         $this->widgetSchema->setNameFormat('salary[%s]');
         
        // set up your post validator method
        $this->validatorSchema->setPostValidator(
          new sfValidatorCallback(array(
            'callback' => array($this, 'postValidate')
          ))
        );

    }

    public function postValidate($validator, $values) {
        $jobService = new JobService();

        $salaryGrade = $values['sal_grd_code'];

        $salary = $values['basic_salary'];
            
        if (!empty($salaryGrade)) {
            
            $salaryDetail = $jobService->getSalaryCurrencyDetail($salaryGrade, $values['currency_id']);

            
            if (empty($salaryDetail)) {

                $message = sfContext::getInstance()->getI18N()->__('Invalid Salary Grade.');
                $error = new sfValidatorError($validator, $message);
                throw new sfValidatorErrorSchema($validator, array('' => $error));

            } else if ( (!is_null($salaryDetail->min_salary) && ($salary < $salaryDetail->min_salary)) ||
                        (!is_null($salaryDetail->max_salary) && ($salary > $salaryDetail->max_salary)) ) {

                $message = sfContext::getInstance()->getI18N()->__('Salary should be within min and max');
                $error = new sfValidatorError($validator, $message);
                throw new sfValidatorErrorSchema($validator, array('basic_salary' => $error));
            }
        } else {
            $values['sal_grd_code'] = null;
        }

        // cleanup cmbPayPeriod
        $payPeriod = $values['payperiod_code'];
        if ($payPeriod == '0' || $payPeriod = '') {
            $values['payperiod_code'] = null;
        }
        
        // Convert salary to a string. Since database field is a string field.
        // Otherwise, it may be converted to a string using scientific notation when encrypting.
        //        
        
        // Remove trailing zeros - will always have decimal point, so 
        // only trailing decimals are removed.
        $formattedSalary = rtrim(sprintf("%.2F", $salary), '0');
        
        // Remove decimal point (if it is the last char).
        $formattedSalary = rtrim($formattedSalary, '.');
        
        $values['basic_salary'] = $formattedSalary;
        
        return $values;
    }

    /**
     * Get EmpBasicsalary object
     */
    public function getSalary() {

        $id = $this->getValue('id');
                           
        $empSalary = false;
        
        if (!empty($id)) {
            $empSalary = Doctrine::getTable('EmpBasicsalary')->find($id);
        }
        
        if ($empSalary === false) {
            $empSalary = new EmpBasicsalary();
        }
        
        $empSalary->emp_number = $this->getValue('emp_number');
        $empSalary->sal_grd_code = $this->getValue('sal_grd_code');
        $empSalary->currency_id = $this->getValue('currency_id');
        $empSalary->payperiod_code = $this->getValue('payperiod_code');
        $empSalary->salary_component = $this->getValue('salary_component');
        $empSalary->basic_salary = $this->getValue('basic_salary');
        $empSalary->comments = $this->getValue('comments');

        $setDirectDebit = $this->getValue('set_direct_debit');
        if ($setDirectDebit) {
        }
        
        return $empSalary;

    }
    
    private function _getPayGrades() {
        $choices = array();
        
        $jobService = new JobService();
        $payGrades = $jobService->getSaleryGradeList();
        
        if (count($payGrades) > 0) {
            $choices = array('' => '-- ' . __('Select') . ' --');

            foreach ($payGrades as $payGrade) {
                $choices[$payGrade->getSalGrdCode()] = $payGrade->getSalGrdName();
            }
        }
        return $choices;
    }
    
    /**
     * Get Pay Periods as array.
     * 
     * @return Array (empty array if no pay periods defined).
     */
    private function _getPayPeriods() {
        $payPeriods = Doctrine::getTable('Payperiod')->findAll();        

        $choices = array('' => '-- ' . __('Select') . ' --');

        foreach ($payPeriods as $payPeriod) {
            $choices[$payPeriod->getCode()] = $payPeriod->getName();
        }
        
        return $choices;
    }
    
    private function _getCurrencies() {
        $currencies = $this->getCurrencyService()->getCurrencyList();
        $choices = array('' => '-- ' . __('Select') . ' --');

        foreach ($currencies as $currency) {
            $choices[$currency->getCurrencyId()] = $currency->getCurrencyName();
        }
        return $choices;        
    }
}

