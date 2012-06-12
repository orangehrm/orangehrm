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
 * Formats date using current date format.
 *
 * @param Date $date in YYYY-MM-DD format
 * @return formatted date.
 */

function set_datepicker_date_format($date) {

    if (sfContext::hasInstance()) {
        $dateFormat = sfContext::getInstance()->getUser()->getDateFormat();
    } else{
        $configService = new ConfigService();
        $dateFormat = $configService->getAdminLocalizationDefaultDateFormat();
    }

    if (empty($date)) {
        $formattedDate = null;
    } else {
        $dateArray = explode('-', $date);
        $dateTime = new DateTime();
        $year = $dateArray[0];
        $month = $dateArray[1];
        $day = $dateArray[2];
        
        // For timestamp fields, clean time part from $day (day will look like "21 00:00:00"
        $day = trim($day);
        $spacePos = strpos($day, ' ');
        if ($spacePos !== FALSE) {
            $day = substr($day, 0, $spacePos);
        }
        
        $dateTime->setDate($year, $month, $day);
        $formattedDate = $dateTime->format($dateFormat);
    }

    return $formattedDate;
}

function get_datepicker_date_format($symfonyDateFormat) {
    $jsDateFormat = "";

    $len = strlen($symfonyDateFormat);

    for ($i = 0; $i < $len; $i++) {
        $char = $symfonyDateFormat{$i};
        switch ($char) {
            case 'j':
                $jsDateFormat .= 'd';
                break;
            case 'd':
                $jsDateFormat .= 'dd';
                break;
            case 'l':
                $jsDateFormat .= 'DD';
                break;
            case 'L':
                $jsDateFormat .= 'DD';
                break;
            case 'n':
                $jsDateFormat .= 'm';
                break;
            case 'm':
                $jsDateFormat .= 'mm';
                break;
            case 'F':
                $jsDateFormat .= 'MM';
                break;
            case 'Y':
                $jsDateFormat .= 'yy';
                break;
            default:
                $jsDateFormat .= $char;
                break;
        }
    }
    return($jsDateFormat);
}