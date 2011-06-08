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
 * Form class for employee direct deposit
 */
class EmployeeDirectDepositForm extends BaseForm {
    
    const ACCOUNT_TYPE_SAVINGS = 'SAVINGS';
    const ACCOUNT_TYPE_CHECKING = 'CHECKING';
    const ACCOUNT_TYPE_OTHER = 'OTHER';
          
    private $accountTypes;
    
    public function configure() {
        
        $this->accountTypes = array('' => '-- ' . __('Select') . ' --',
                             self::ACCOUNT_TYPE_SAVINGS => __('Savings'),
                             self::ACCOUNT_TYPE_CHECKING => __('Checking'),
                             self::ACCOUNT_TYPE_OTHER => __('Other'));
        
        // Note: Widget names were kept from old non-symfony version
        $this->setWidgets(array(
            'id' => new sfWidgetFormInputHidden(),
            'account' => new sfWidgetFormInputText(),
            'account_type' => new sfWidgetFormSelect(array('choices' => $this->accountTypes)),
            'account_type_other' => new sfWidgetFormInputText(),
            'routing_num' => new sfWidgetFormInputText(),
            'amount' => new sfWidgetFormInputText(),
        ));
        
        $this->setValidators(array(
            'id' => new sfValidatorNumber(array('required' => false, 'min'=> 0)),
            'account' => new sfValidatorString(array('required' => true, 'max_length'=>100)),
            'account_type' => new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->accountTypes))),
            'account_type_other' => new sfValidatorString(array('required' => false)), // only required if account_type = 'OTHER'.
            'routing_num' => new sfValidatorNumber(array('required' => true, 'trim'=>true)),
            'amount' => new sfValidatorNumber(array('required' => true, 'min' => 0, 'max'=> 999999999.99)),
        ));

         $this->widgetSchema->setNameFormat('directdeposit[%s]');
         
        // set up your post validator method
        $this->validatorSchema->setPostValidator(
          new sfValidatorCallback(array(
            'callback' => array($this, 'postValidate')
          ))
        );

    }

    public function postValidate($validator, $values) {

        $accountType = $values['account_type'];
        
        if ($accountType == self::ACCOUNT_TYPE_OTHER) {
            $other = $values['account_type_other'];
            if ($other == '') {
                $message = __('Please Specify Other Account Type');
                $error = new sfValidatorError($validator, $message);
                throw new sfValidatorErrorSchema($validator, array('account_type_other' => $error));                

            } 
        }
                
        /* 
         * Validate amount field :decimal (11,2) - 
         * ie. Precision is 11 digits
         */        
        /*amount = $values['amount'];
        
        // Round to 2 decimals
        $amount = round($amount, 2);
        
        // Format as string and replace decimal point if any 
        /*$amountStr = str_replace('.', '', sprintf("%.2F", $amount));
        var_dump(sprintf("%.2F", $amount));
        
        // Check that number of digits is 11 or less
        var_dump($amountStr);die;
        if (strlen($amountStr) > 11) {
            $message = __('Amount is too large. Should be 11 digits or less');
            $error = new sfValidatorError($validator, $message);            
            throw new sfValidatorErrorSchema($validator, array('amount' => $error)); 
        } else {
            $values['amount'] = $amount;
        }*/

        
        return $values;
    }
    
    /**
     * Adds direct deposit information to the salary object
     * 
     * @param type $salary EmpBasicsalary object - passed by reference
     * @return None 
     */
    public function getDirectDeposit(&$salary) {
        
        $id = $this->getValue('id');
        if (!empty($id)) {
            $salary->directDebit->id = $id;
        }
        
        $salary->directDebit->account = $this->getValue('account');
        $accountType = $this->getValue('account_type');
        
        if ($accountType == self::ACCOUNT_TYPE_OTHER) {
            $salary->directDebit->account_type = $this->getValue('account_type_other');
        } else {
            $salary->directDebit->account_type = $accountType;
        }
        
        $salary->directDebit->routing_num = $this->getValue('routing_num');        
        $salary->directDebit->amount = $this->getValue('amount');        
    }
    
    public function getAccountTypeDescription($accountType) {
        $accountTypeDescription = $accountType;
        
        if (!empty($accountType) && isset($this->accountTypes[$accountType])) {
            $accountTypeDescription = $this->accountTypes[$accountType];            
        }

        return($accountTypeDescription);
    }
  
}

