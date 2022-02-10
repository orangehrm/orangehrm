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

use DateTime;
use DateTimeZone;
use Exception;
use OrangeHRM\Attendance\Exception\AttendanceServiceException;
use OrangeHRM\Entity\WorkflowStateMachine;

class MyAttendanceRecordAPI extends EmployeeAttendanceRecordAPI
{
    /**
     * @inheritDoc
     */
    protected function extractPunchDateTime(string $dateTime, float $timezoneOffset): DateTime
    {
        $timezone = $this->getDateTimeHelper()->getTimezoneByTimezoneOffset($timezoneOffset);
        $userDateTime = new DateTime($dateTime, $timezone);
        //user can change current time config disabled and system generated date time is not valid
        if (!$this->getAttendanceService()->canUserChangeCurrentTime() && !$this->isCurrantDateTimeValid(
                $dateTime,
                $timezone
            )) {
            throw AttendanceServiceException::invalidDateTime();
        }
        return $userDateTime;
    }

    /**
     * If the configuration disabled for users to edit the date time, we should check the user provided timestamp with the
     * exact timestamp in the user's timezone. Those two should be same if the user provides true data. The margin of error
     * can be +/- 180 seconds
     * @param  string  $dateTime
     * @param  DateTimeZone  $timezone
     * @return bool
     * @throws Exception
     */
    protected function isCurrantDateTimeValid(string $dateTime, DateTimeZone $timezone): bool
    {
        $currentDateTime = $this->getDateTimeHelper()->getNow($timezone);
        $userProvidedDateTime = new DateTime($dateTime, $timezone);
        $dateTimeDifference = $currentDateTime->getTimestamp() - $userProvidedDateTime->getTimestamp();
        return ($dateTimeDifference < 180 && $dateTimeDifference > -180);
    }

    /**
     * @param  array  $allowedActions
     * @return void
     */
    protected function userAllowedPunchInActions(array $allowedActions): void{
       $allowed = in_array(
            WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME,
            $allowedActions
        );
    }

    /**
     * @param  array  $allowedActions
     * @return void
     */
    protected function userAllowedPunchOutActions(array $allowedActions): void{
        $allowed = in_array(
            WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME,
            $allowedActions
        );
    }
}
