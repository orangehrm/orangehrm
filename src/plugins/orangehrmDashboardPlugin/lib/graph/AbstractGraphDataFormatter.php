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

/**
 * Description of AbstractGraphDataFormatter
 */
abstract class AbstractGraphDataFormatter {

    protected function formatLabel($baseValuesArray, $fieldMap) {
        $formattedLabel = '';
        if (is_array($fieldMap)) {
            throw new Exception('Only one field can be used if the label format is not specified');
        } else {
            if (strtotime($baseValuesArray[$fieldMap]) > 0) {
                $timeStamp = strtotime($baseValuesArray[$fieldMap]);
                $formattedLabel = set_datepicker_date_format($timeStamp);
            } else {
                $formattedLabel = htmlspecialchars($baseValuesArray[$fieldMap]);
            }
        }
        return $formattedLabel;
    }
    
    abstract public function format(array $data);
    abstract public function extractLabels(array $data, $fieldMap);

}
