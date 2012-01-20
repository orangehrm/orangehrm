<?php

class ohrmValidatorConditionalFilter extends sfValidatorBase {

    protected function configure($options = array(), $messages = array()) {
        $this->addOption('operators', array(1 => '<', 2 => '>', 3 => 'BETWEEN'));
        $this->addOption('operator_field', 'comparision');
        $this->addOption('operator_param_count', array('<' => 1,
                                                       '>' => 1,
                                                       '=' => 1,
                                                       '<>' => 1,
                                                       'BETWEEN' => 2));
        $this->addOption('values', array('value1', 'value2'));
        $this->addOption('required', false);
        $this->addMessage('required', 'Field is required.');
        $this->addMessage('value1_required', 'Value required');
        $this->addMessage('value2_required', 'Second value required');
        $this->addMessage('value1_value2_required', 'Both values required');
        $this->addMessage('value1_greater_than_value2', 'Second value should be greater or equal to first value');
        $this->addMessage('value1_invalid', 'First value should be a number.');
        $this->addMessage('value2_invalid', 'Second value should be a number.');
        $this->addMessage('value1_and_value2_invalid', 'First and second values should be numbers.');
    }
    
    protected function isValid($value) {
        return is_numeric($value);
    }

    protected function validatedBetween($value1, $value2) {
        return $value2 >= $value1;
    }
    
    /**
     * @see sfValidatorBase
     */
    protected function doClean($value) {
        $clean = $value;
        
        $required = $this->getOption('required');
        $operators = $this->getOption('operators');
        $operatorParam = $this->getOption('operator_field');
        
        $operatorKey = isset($value[$operatorParam]) ? $value[$operatorParam] : null;
        
        if (empty($value) || !is_array($value) || empty($operatorKey) 
                || !isset($operators[$operatorKey])) {

            if ($required) {                
                 throw new sfValidatorError($this, 'required');
            }
            
            // required error
        } else {
            $operator = $operators[$operatorKey];
            $paramCountList = $this->getOption('operator_param_count');
            $paramCount = $paramCountList[$operator];
            
            $valueNames = $this->getOption('values');
            $value1Name = $valueNames[0];
            $value2Name = $valueNames[1];
            
            $value1Available = isset($value[$value1Name]) && ($value[$value1Name] !== '');
            $value2Available = isset($value[$value2Name]) && ($value[$value2Name] !== '');


            switch ($paramCount) {
                case 1:
                    if (!$value1Available) {
                        throw new sfValidatorError($this, 'value1_required');
                    } else {
                        $value1 = $value[$value1Name];

                        if (!$this->isValid($value1)) {
                            throw new sfValidatorError($this, 'value1_invalid');
                        }
                    }
                    break;
                case 2: 
                    if (!$value1Available && !$value2Available) {
                        throw new sfValidatorError($this, 'value1_value2_required');
                    } else if (!$value1Available) {
                        throw new sfValidatorError($this, 'value1_required');
                    } else if (!$value2Available) {
                        throw new sfValidatorError($this, 'value2_required');
                    } else if ($operator == 'BETWEEN') {
                           
                        $value1 = $value[$value1Name];
                        $value2 = $value[$value2Name];
                        
                        $value1Valid = $this->isValid($value1);
                        $value2Valid = $this->isValid($value2);
                            
                        if (!$value1Valid && !$value2Valid) {
                            throw new sfValidatorError($this, 'value1_invalid');
                        } else if (!$value1Valid) {
                            throw new sfValidatorError($this, 'value1_invalid');
                        } else if (!$value2Valid) {
                        throw new sfValidatorError($this, 'value1_and_value2_invalid');
                        } else if (!$this->validatedBetween($value1, $value2)) {
                            throw new sfValidatorError($this, 'value1_greater_than_value2');
                        }
                    }
                    break;
            }
            
            
          
        }

        return $clean;
    }
}

