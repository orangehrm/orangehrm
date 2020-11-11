<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/2/17
 * Time: 9:04 PM
 */

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

namespace Orangehrm\Rest\Api\User;

use Orangehrm\Rest\Api\Attendance\PunchTimeAPI;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Http\Response;
use Orangehrm\Rest\Api\Attendance\PunchOutAPI;
use \PluginAttendanceRecord;
use \DateTimeZone;
use \DateTime;
use \Exception;


class EmployeePunchStatusAPI extends PunchTimeAPI
{
    public function getStatusDetails()
    {
        $empNumber = $this->getAttendanceService()->GetLoggedInEmployeeNumber();
        if (!$this->checkValidEmployee($empNumber)) {
            throw new RecordNotFoundException('Employee Id' . $empNumber . ' Not Found');
        }
        $punchInTime = null;
        $punchOutTime = null;
        $punchInNote = null;
        $punchOutNote = null;
        $displayPunchInTimeZoneOffset = null;
        $displayPunchOutTimeZoneOffset = null;
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
                $punchOutTime = $lastPunchOutRecord->getPunchOutUserTime();
                $punchOutNote = $lastPunchOutRecord->getPunchOutNote();
                $punchOutTimeZone = $lastPunchOutRecord->getPunchOutTimeOffset();
                $displayPunchOutTimeZoneOffset = $this->getAttendanceService()->getOriginDisplayTimeZoneOffset(
                    $punchOutTimeZone
                );
            } else {
                $punchState = null;
            }
        } else {
            $punchState = PluginAttendanceRecord::STATE_PUNCHED_IN;
            $punchInTime = $lastPunchInRecord->getPunchOutUserTime();
            $punchInNote = $lastPunchInRecord->getPunchOutNote();
            $punchInTimeZone = $lastPunchInRecord->getPunchOutTimeOffset();
            $displayPunchInTimeZoneOffset = $this->getAttendanceService()->getOriginDisplayTimeZoneOffset(
                $punchInTimeZone
            );
        }
        $punchTimeEditableDetails = $this->getPunchTimeEditable();
        return new Response(
            array(
                'punchInTime' => $punchInTime,
                'punchOutTime' => $punchOutTime,
                'punchInNote' => $punchInNote,
                'punchOutNote' => $punchOutNote,
                'PunchInTimeZoneOffset' => $displayPunchInTimeZoneOffset,
                'PunchOutTimeZoneOffset' => $displayPunchOutTimeZoneOffset,
                'dateTimeEditable' => $punchTimeEditableDetails['editable'],
                'currentUtcDateTime' => $punchTimeEditableDetails['serverUtcTime'],
                'punchState' => $punchState
            )
        );
    }
}
