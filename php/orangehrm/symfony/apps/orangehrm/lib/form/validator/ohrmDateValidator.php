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

    const OUTPUT_FORMAT = 'yyyy-mm-dd';

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

        $date = null;
        $valid = false;
        
        // check date format
        if (is_string($value) && $pattern = $this->getOption('date_format')) {

            $dateFormat = new sfDateFormat();
            try {
                $dateParts = $dateFormat->getDate($value, $pattern);

                if (is_array($dateParts) && isset($dateParts['year']) && isset($dateParts['mon']) && isset($dateParts['mday'])) {

                    $day = $dateParts['mday'];
                    $month = $dateParts['mon'];
                    $year = $dateParts['year'];

                    // Additional check done for 3 digit years, or more than 4 digit years
                    if (checkdate($month, $day, $year) && ($year >= 1000) && ($year <= 9999) ) {
                        $dateTime = new DateTime();
                        $dateTime->setTimezone(new DateTimeZone(date_default_timezone_get()));
                        $dateTime->setDate($year, $month, $day);

                        $date = $dateTime->format('Y-m-d');
                        $valid = true;
                    }
                }


            } catch (Exception $e) {
                $valid = false;
            }
        }

        if (!$valid) {
            throw new sfValidatorError($this, 'bad_format', array('value' => $value, 'date_format' => $this->getOption('date_format_error') ? $this->getOption('date_format_error') : $this->getOption('date_format')));
        }

        return($date);
    }
}
