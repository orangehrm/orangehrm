<?php

class ohrmValidatorDateConditionalFilter extends ohrmValidatorConditionalFilter {

    protected function configure($options = array(), $messages = array()) {
        parent::configure($options, $messages);

        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
        $this->addOption('date_format', $inputDatePattern);        
        $this->addOption('values', array('from', 'to'));
        
        $this->addMessage('value1_required', __(ValidationMessages::REQUIRED));
        $this->addMessage('value2_required', __(ValidationMessages::REQUIRED));
        $this->addMessage('value1_value2_required', __(ValidationMessages::REQUIRED));
        $this->addMessage('value1_greater_than_value2', __('Should be greater than first value'));
        $this->addMessage('value1_invalid', ValidationMessages::DATE_FORMAT_INVALID);
        $this->addMessage('value2_invalid', ValidationMessages::DATE_FORMAT_INVALID);
        $this->addMessage('value1_and_value2_invalid', ValidationMessages::DATE_FORMAT_INVALID);
        
        $this->messageArguments['format'] = get_datepicker_date_format($inputDatePattern);
    }

    protected function isValid($value) {
        $date = $this->getDate($value);

        if (is_null($date)) {
            return false;
        } else {
            return true;
        }
    }

    protected function validatedBetween($value1, $value2) {
        $valid = false;
        $date1 = $this->getDate($value1);
        $date2 = $this->getDate($value2);
        if (!empty($date1) && !empty($date2)) {
            
            $date1Obj = new DateTime($date1);
            $date2Obj = new DateTime($date2);
            
            $valid = $date2Obj >= $date1Obj;
        }
        return $valid;
    }

    protected function getDate($value) {
        $date = null;
        $valid = false;

        $trimmedValue = trim($value);
        $pattern = $this->getOption('date_format');

        // check date format
        if (is_string($value) && !empty($pattern)) {
            $localizationService = new LocalizationService();
            $result = $localizationService->convertPHPFormatDateToISOFormatDate($pattern, $trimmedValue);

            if ($result != "Invalid date") {
                $date = $result;
                $valid = true;
            }
        }
        return($date);
    }
    
    protected function doClean($value) {
        $value = parent::doClean($value);
        
        $valueNames = $this->getOption('values');
        $value1Name = $valueNames[0];
        $value2Name = $valueNames[1];        
        
        $value1Available = isset($value[$value1Name]) && ($value[$value1Name] !== '');
        $value2Available = isset($value[$value2Name]) && ($value[$value2Name] !== ''); 
        
        if ($value1Available) {
            $date1 = $this->getDate($value[$value1Name]);
            if (!empty($date1)) {
                $date1Obj = new DateTime($date1);
                $value[$value1Name] = $date1Obj->format('Y-m-d');                
            }
        }
        
        if ($value2Available) {
            $date2 = $this->getDate($value[$value2Name]);
            if (!empty($date2)) {
                $date2Obj = new DateTime($date2);
                $value[$value2Name] = $date2Obj->format('Y-m-d');                
            }            
        }
        
        return $value;
    }

}

