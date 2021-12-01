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
class MonthlyTimesheetPeriod extends TimesheetPeriod {

    public function calculateDaysInTheTimesheetPeriod($currentDate, $xml) {

        $startDay = (String) $xml->StartDate;
        ;
        list($year, $month, $day) = explode("-", $currentDate);
        if ($startDay <= $day) {
            $start_of_month = mktime(00, 00, 00, $month, $startDay, $year);
            $end_of_month = mktime(23, 59, 59, $month + 1, $startDay, $year);
        } else {
            $start_of_month = mktime(00, 00, 00, $month - 1, $startDay, $year);
            $end_of_month = mktime(23, 59, 59, $month, $startDay, $year);
        }
        $startDate = date('Y-m-d', $start_of_month);
        $endDate = date('Y-m-d', $end_of_month);

        if ($startDate < $endDate) {
            $dates_range[] = $startDate;
            $startDate = strtotime($startDate);
            $endDate = strtotime($endDate);
            while ($startDate != $endDate) {
                $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate) + 1, date("Y", $startDate));
                $dates_range[] = date('Y-m-d', $startDate);
            }
        }
        array_pop($dates_range);
        return $dates_range;
    }

    public function setTimesheetPeriodAndStartDate($startDay) {

        return "<TimesheetPeriod><PeriodType>Monthly</PeriodType><ClassName>MonthlyTimesheetPeriod</ClassName><StartDate>" . $startDay . "</StartDate><Heading>Month</Heading></TimesheetPeriod>";
    }

    public function getDatesOfTheTimesheetPeriod($startDate, $endDate) {
        
        $clientTimeZoneOffset = sfContext::getInstance()->getUser()->getUserTimeZoneOffset();
        $serverTimezoneOffset = ((int) date('Z'));
        $timeStampDiff = $clientTimeZoneOffset * 3600 - $serverTimezoneOffset;
        
        

        if ($startDate < $endDate) {
            $dates_range[] = $startDate;
            
            $startDate = strtotime($startDate) + $timeStampDiff;
            $endDate = strtotime($endDate) + $timeStampDiff;
            while (date('Y-m-d', $startDate) != date('Y-m-d', $endDate)) {
                $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate) + 1, date("Y", $startDate));
                $dates_range[] = date('Y-m-d', $startDate);
            }
        }
        return $dates_range;
    }

}

?>
