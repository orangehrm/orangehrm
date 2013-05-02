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
 *
 */

/**
 * ohrmDateValidator validates dates in the current date format.
 */
class ohrmDateValidator extends sfValidatorBase {
    const OUTPUT_FORMAT = 'Y-m-d';

    /**
     * Configure validator.
     * Output format is always yyyy-mm-dd
     * 
     * @param <type> $options
     * @param <type> $messages
     */
    protected function configure($options = array(), $messages = array()) {

        $this->addMessage('bad_format', '"%value%" does not match the date format (%date_format%).');
        $this->addOption('date_format', null);
        $this->addOption('date_format_error');
        $this->addOption('min', null);
        $this->addOption('max', null);
    }

    /**
     * @see sfValidatorBase
     */
    protected function doClean($value) {

        $trimmedValue = trim($value);
        $pattern = $this->getOption('date_format');
        
        if (empty($pattern)) {
            $pattern = sfContext::getInstance()->getUser()->getDateFormat();
        }

        $required = $this->getOption('required');
        $isDefaultValue = strcasecmp(str_replace('yyyy', 'yy', $trimmedValue), get_datepicker_date_format($pattern)) == 0;
        
        if (($trimmedValue == '') || $isDefaultValue) {
            if (!$required) {
                // If not required and empty or the format pattern, return valid                
                return null;                
            } else {
                throw new sfValidatorError($this, 'required');                
            }
        }

        $localizationService = new LocalizationService();
        $result = $localizationService->convertPHPFormatDateToISOFormatDate($pattern, $trimmedValue);
        $valid = ($result == "Invalid date") ? false : true;
        if (!$valid) {
            throw new sfValidatorError($this, 'bad_format', array('value' => $value, 'date_format' => $this->getOption('date_format_error') ? $this->getOption('date_format_error') : get_datepicker_date_format($pattern)));
        }
        return $result;
    }

}
