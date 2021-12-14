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

namespace OrangeHRM\Time\TimeSheetPeriod;

class WeeklyTimesheetPeriod extends TimesheetPeriod
{
    /**
     * @var string
     */
    private string $startDate;

    /**
     * @param $currentDate
     * @param $xml
     * @return array
     */
    public function calculateDaysInTheTimesheetPeriod($currentDate, $xml): array
    {
        // TODO
        $clientTimeZoneOffset = sfContext::getInstance()->getUser()->getUserTimeZoneOffset();
        date_default_timezone_set($this->getLocalTimezone($clientTimeZoneOffset));
        $this->startDate = $xml->StartDate;
        $day = date('N', strtotime($currentDate));

        $diff = $this->startDate - $day;
        if ($diff > 0) {
            $diff-=7;
        }

        $sign = ($diff < 0) ? "" : "+";

        $r = mktime('0', '0', '0', date('m', strtotime("{$sign}{$diff} day", strtotime($currentDate))), date('d', strtotime("{$sign}{$diff} day", strtotime($currentDate))), date('Y', strtotime("{$sign}{$diff} day", strtotime($currentDate))));

        for ($i = 0; $i < 7; $i++) {
            $dates[$i] = date("Y-m-d H:i", strtotime("+" . $i . " day", $r));
        }
        return $dates;
    }

    public function setTimesheetPeriodAndStartDate($startDay)
    {
        // TODO
        return "<TimesheetPeriod><PeriodType>Weekly</PeriodType><ClassName>WeeklyTimesheetPeriod</ClassName><StartDate>" . $startDay . "</StartDate><Heading>Week</Heading></TimesheetPeriod>";
    }

    public function getLocalTimezone($clientTimeZoneOffset)
    {
        // TODO
        $offset = $clientTimeZoneOffset;
        $zonelist =
                [
                    'Kwajalein' => -12.00,
                    'Pacific/Midway' => -11.00,
                    'Pacific/Honolulu' => -10.00,
                    'America/Anchorage' => -9.00,
                    'America/Los_Angeles' => -8.00,
                    'America/Denver' => -7.00,
                    'America/Tegucigalpa' => -6.00,
                    'America/New_York' => -5.00,
                    'America/Caracas' => -4.50,
                    'America/Halifax' => -4.00,
                    'America/St_Johns' => -3.50,
                    'America/Argentina/Buenos_Aires' => -3.00,
                    'America/Sao_Paulo' => -3.00,
                    'Atlantic/South_Georgia' => -2.00,
                    'Atlantic/Azores' => -1.00,
                    'Europe/Dublin' => 0,
                    'Europe/Belgrade' => 1.00,
                    'Europe/Minsk' => 2.00,
                    'Asia/Kuwait' => 3.00,
                    'Asia/Tehran' => 3.50,
                    'Asia/Muscat' => 4.00,
                    'Asia/Yekaterinburg' => 5.00,
                    'Asia/Kolkata' => 5.50,
                    'Asia/Katmandu' => 5.45,
                    'Asia/Dhaka' => 6.00,
                    'Asia/Rangoon' => 6.50,
                    'Asia/Krasnoyarsk' => 7.00,
                    'Asia/Brunei' => 8.00,
                    'Asia/Seoul' => 9.00,
                    'Australia/Darwin' => 9.50,
                    'Australia/Canberra' => 10.00,
                    'Asia/Magadan' => 11.00,
                    'Pacific/Fiji' => 12.00,
                    'Pacific/Tongatapu' => 13.00
        ];
        $index = array_keys($zonelist, $offset);
        if (sizeof($index) != 1) {
            return false;
        }
        return $index[0];
    }
}
