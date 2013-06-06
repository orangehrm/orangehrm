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
class ohrmValidatorTimeRange extends sfValidatorBase {

    /**
     * Configures the current validator.
     *
     * Available options:
     *
     *  * from_time:   The from time validator (required)
     *  * to_time:     The to time validator (required)
     *  * from_field:  The name of the "from" date field (optional, default: from)
     *  * to_field:    The name of the "to" date field (optional, default: to)
     *
     * @param array $options    An array of options
     * @param array $messages   An array of error messages
     *
     * @see sfValidatorBase
     */
    protected function configure($options = array(), $messages = array()) {
        parent::configure($options, $messages);

        $this->setMessage('invalid', 'From time should be before to time.');

        $this->addRequiredOption('from_time');
        $this->addRequiredOption('to_time');
        $this->addOption('from_field', 'from');
        $this->addOption('to_field', 'to');
    }

    /**
     * @see sfValidatorBase
     */
    protected function doClean($value) {

        $fromField = $this->getOption('from_field');
        $toField = $this->getOption('to_field');

        $value[$fromField] = $this->getOption('from_time')->clean(isset($value[$fromField]) ? $value[$fromField] : null);
        $value[$toField] = $this->getOption('to_time')->clean(isset($value[$toField]) ? $value[$toField] : null);
        
        if ($value[$fromField] && $value[$toField]) {
            $fromTimeStamp = strtotime($value[$fromField]);
            $toTimeStamp = strtotime($value[$toField]);

            if ($toTimeStamp <= $fromTimeStamp) {
                throw new sfValidatorError($this, $this->getMessage('invalid') . '-' . $toTimeStamp . '-' . $fromTimeStamp . 
                        '-' . $value[$fromField] . '-' . $value[$toField]);
            }
        }

        return $value;
    }

}

