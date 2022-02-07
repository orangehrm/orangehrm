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

namespace OrangeHRM\Attendance\Api;

use OrangeHRM\Attendance\Exception\AttendanceServiceException;

class MyAttendanceRecordAPI extends EmployeeAttendanceRecordAPI
{

    /**
     * @inheritDoc
     */
    protected function extractTimestamp(string $date, string $time, float $timezoneOffset): int
    {
        $dateTime = $this->getDateTimeHelper()->getDateTimeByString($date.' '.$time);
        $timeStamp = $this->getDateTimeHelper()->getTimestampByDateTime($dateTime);
        //user can change current time config disabled and system generated date time is not valid
        if (!$this->canUserChangeCurrentTime() && !$this->isCurrantDateTimeValid($timezoneOffset, $timeStamp)) {
            throw AttendanceServiceException::invalidDateTime();
        }
        return $timeStamp;
    }
    /**
     * If the configuration disabled for users to edit the date time, we should check the user provided timestamp with the
     * exact timestamp in the user's timezone. Those two should be same if the user provides true data. The margin of error
     * can be +/- 60 seconds
     * @param  float  $timezoneOffset
     * @param  int  $userProvidedTimestamp
     * @return bool
     */
    protected function isCurrantDateTimeValid(float $timezoneOffset, int $userProvidedTimestamp): bool
    {
        $serverDateTime = $this->getDateTimeHelper()->getNow();
        $timestampDiff = $this->getDateTimeHelper()->getTimestampDifference($serverDateTime, $timezoneOffset);
        $userActualTimestamp = $this->getDateTimeHelper()->getTimestampByDateTime($serverDateTime) + $timestampDiff;
        return (($userActualTimestamp - $userProvidedTimestamp) < 60 && ($userActualTimestamp - $userProvidedTimestamp) > -60);
    }
}
