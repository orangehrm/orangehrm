<?php

class ohrmValidatorNumberRange extends sfValidatorBase {

    protected function configure($options = array(), $messages = array()) {

    }

    /**
     * @see sfValidatorBase
     */
    protected function doClean($value) {
        $clean = (string) $value;
        
    
        $this->setMessage('invalid', '2nd Selected Age Lager Than The 1st Selected Age.');
        $compareValidator = new sfValidatorSchemaCompare('value1', sfValidatorSchemaCompare::LESS_THAN_EQUAL, 'value2', array('throw_global_error' => true), array('invalid' => $this->getMessage('invalid')));

        $compareValidator->clean($value);

        return $clean;
    }
}

