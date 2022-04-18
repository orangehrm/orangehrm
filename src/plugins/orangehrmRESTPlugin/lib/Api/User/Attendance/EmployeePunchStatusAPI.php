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

namespace Orangehrm\Rest\Api\User\Attendance;

use Orangehrm\Rest\Api\Attendance\PunchTimeAPI;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Http\Response;
use \PluginAttendanceRecord;
use \AttendanceRecord;


class EmployeePunchStatusAPI extends PunchTimeAPI
{
    public function getStatusDetailsOfLatestAttendanceRecord()
    {
        $empNumber = $this->getLoggedInEmployeeNumber();
        if (!$this->checkValidEmployee($empNumber)) {
            throw new RecordNotFoundException('Employee Id' . $empNumber . ' Not Found');
        }
        $punchTime = null;
        $punchNote = null;
        $lastPunchInRecord = $this->getAttendanceService()->getLatestPunchInRecord(
            $empNumber,
            PluginAttendanceRecord::STATE_PUNCHED_IN
        );
        if (!$lastPunchInRecord) {
            $lastPunchOutRecord = $this->getAttendanceService()->getLatestPunchInRecord(
                $empNumber,
                PluginAttendanceRecord::STATE_PUNCHED_OUT
            );
            if ($lastPunchOutRecord) {
                $punchState = PluginAttendanceRecord::STATE_PUNCHED_OUT;
                $punchTime = $lastPunchOutRecord->getPunchOutUserTime();
                $punchNote = $lastPunchOutRecord->getPunchOutNote();
                $punchTimeZoneOffset = $lastPunchOutRecord->getPunchOutTimeOffset();
            } else {
                $punchState = AttendanceRecord::STATE_INITIAL;
            }
        } else {
            $punchState = PluginAttendanceRecord::STATE_PUNCHED_IN;
            $punchTime = $lastPunchInRecord->getPunchInUserTime();
            $punchNote = $lastPunchInRecord->getPunchInNote();
            $punchTimeZoneOffset = $lastPunchInRecord->getPunchInTimeOffset();
        }

        $editable = $this->getAttendanceService()->getPunchTimeUserConfiguration();
        $currentUTCDateTime = $this->getCurrentUTCTime();
        return new Response(
            array(
                'punchTime' => $punchTime,
                'punchNote' => $punchNote,
                'punchTimeZoneOffset' => $punchTimeZoneOffset,
                'dateTimeEditable' => $editable,
                'currentUTCDateTime' => $currentUTCDateTime,
                'punchState' => $punchState
            )
        );
    }
}
