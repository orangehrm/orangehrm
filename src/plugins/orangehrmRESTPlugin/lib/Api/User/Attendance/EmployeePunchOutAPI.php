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

use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Http\Response;
use Orangehrm\Rest\Api\Attendance\PunchOutAPI;
use \PluginAttendanceRecord;

class EmployeePunchOutAPI extends PunchOutAPI
{
    /**
     * @return Response
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     */
    public function savePunchOut()
    {
        $params = $this->getParameters();
        $timeZoneOffset = $params[parent::PARAMETER_TIME_ZONE];
        $punchOutNote = $params[parent::PARAMETER_NOTE];
        $dateTime = $params[parent::PARAMETER_DATE_TIME];

        if (empty($dateTime)) {
            throw new InvalidParamException('Datetime Cannot Be Empty');
        }
        if (empty($timeZoneOffset)) {
            throw new InvalidParamException('TimeZone Offset Cannot Be Empty');
        }

        if (!in_array($timeZoneOffset, $this->getValidateTimezoneOffsetList())) {
            throw new InvalidParamException('Invalid Time Zone Offset');
        }

        $empNumber = $this->getLoggedInEmployeeNumber();

        if (!$this->checkValidEmployee($empNumber)) {
            throw new RecordNotFoundException('Employee id ' . $empNumber . ' Not Found');
        }
        $actionableStatesList = array(PluginAttendanceRecord::STATE_PUNCHED_IN);
        $attendanceRecord = $this->getAttendanceService()->getLastPunchRecord($empNumber, $actionableStatesList);

        if (is_null($attendanceRecord)) {
            throw new InvalidParamException('Cannot Proceed Punch Out Employee Already Punched Out');
        }
        $nextState = PluginAttendanceRecord::STATE_PUNCHED_OUT;

        //check overlapping
        $punchInUtcTime = $attendanceRecord->getPunchInUtcTime();
        $punchOutUtcTime = date('Y-m-d H:i:s', strtotime($dateTime) - $timeZoneOffset * 3600);
        if ($punchInUtcTime > $punchOutUtcTime) {
            throw new InvalidParamException('Punch Out Time Should Be Later Than Punch In Time');
        }
        $editable = $this->getAttendanceService()->getPunchTimeUserConfiguration();
        if (!$editable) {
            $utcNowTimeValue = strtotime($this->getCurrentUTCTime());
            $userEnterTimeUTCValue = strtotime($punchOutUtcTime);
            $diff = abs($utcNowTimeValue - $userEnterTimeUTCValue);
            if ($diff > 180) {
                throw new InvalidParamException('You Are Not Allowed To Change Current Date & Time');
            }
        }
        $isValid = $this->getAttendanceService()->checkForPunchOutOverLappingRecords(
            $punchInUtcTime,
            $punchOutUtcTime,
            $empNumber,
            $attendanceRecord->getId()
        );
        if ($isValid == '0') {
            throw new InvalidParamException('Overlapping Records Found');
        }
        $attendanceRecord = $this->setPunchOutRecord(
            $attendanceRecord,
            $nextState,
            $punchOutUtcTime,
            $dateTime,
            $timeZoneOffset,
            $punchOutNote
        );

        return new Response(
            array(
                'id' => $attendanceRecord->getId(),
                'punchInDateTime' => $attendanceRecord->getPunchInUserTime(),
                'punchInTimeZoneOffset' => $attendanceRecord->getPunchInTimeOffset(),
                'punchInNote' => $attendanceRecord->getPunchInNote(),
                'punchOutDateTime' => $attendanceRecord->getPunchOutUserTime(),
                'punchOutTimeZoneOffset' => $attendanceRecord->getPunchOutTimeOffset(),
                'punchOutNote' => $attendanceRecord->getPunchOutNote(),
            )
        );
    }

    public function getParameters()
    {
        $params = array();
        $params[parent::PARAMETER_TIME_ZONE] = $this->getRequestParams()->getPostParam(
            parent::PARAMETER_TIME_ZONE_OFFSET
        );
        $params[parent::PARAMETER_NOTE] = $this->getRequestParams()->getPostParam(parent::PARAMETER_NOTE);
        $params[parent::PARAMETER_DATE_TIME] = $this->getRequestParams()->getPostParam(parent::PARAMETER_DATE_TIME);
        return $params;
    }

    /**
     * @return array
     */
    public function getValidationRules()
    {
        return array(
            parent::PARAMETER_NOTE => ['StringType' => true, 'Length' => [1, 250]],
            parent::PARAMETER_DATE_TIME => ['Date' => ['Y-m-d H:i']],
            parent::PARAMETER_TIME_ZONE => ['NotEmpty' => true, 'Numeric' => true]
        );
    }
}
