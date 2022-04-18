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
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Http\Response;
use Orangehrm\Rest\Api\Attendance\PunchInAPI;
use \PluginAttendanceRecord;
use \AttendanceRecord;


class EmployeePunchInAPI extends PunchInAPI
{
    /**
     * @return Response
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     */
    public function savePunchIn()
    {
        $params = $this->getParameters();
        $timeZoneOffset = $params[parent::PARAMETER_TIME_ZONE];
        $punchInNote = $params[parent::PARAMETER_NOTE];
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
            throw new BadRequestException('Employee Id ' . $empNumber . ' Not Found');
        }
        $actionableStatesList = array(PluginAttendanceRecord::STATE_PUNCHED_IN);
        $attendanceRecord = $this->getAttendanceService()->getLastPunchRecord(
            $empNumber,
            $actionableStatesList
        );
        if ($attendanceRecord) {
            throw new InvalidParamException('Cannot Proceed Punch In Employee Already Punched In');
        }
        $attendanceRecord = new AttendanceRecord();
        $attendanceRecord->setEmployeeId($empNumber);

        $nextState = PluginAttendanceRecord::STATE_PUNCHED_IN;

        //check overlapping
        $punchInUtcTime = date('Y-m-d H:i', strtotime($dateTime) - $timeZoneOffset * 3600);
        $editable = $this->getAttendanceService()->getPunchTimeUserConfiguration();
        if (!$editable) {
            $utcNowTimeValue = strtotime($this->getCurrentUTCTime());
            $userEnterTimeUTCValue = strtotime($punchInUtcTime);
            $diff = abs($utcNowTimeValue - $userEnterTimeUTCValue);
            if ($diff > 180) {
                throw new InvalidParamException('You Are Not Allowed To Change Current Date & Time');
            }
        }
        $isValid = $this->getAttendanceService()->checkForPunchInOverLappingRecords(
            $punchInUtcTime,
            $empNumber
        );
        if (!$isValid) {
            throw new InvalidParamException('Overlapping Records Found');
        }

        $attendanceRecord = $this->setPunchInRecord(
            $attendanceRecord,
            $nextState,
            $punchInUtcTime,
            $dateTime,
            $timeZoneOffset,
            $punchInNote
        );

        return new Response(
            array(
                'id' => $attendanceRecord->getId(),
                'datetime' => $attendanceRecord->getPunchInUserTime(),
                'timezoneOffset' => $attendanceRecord->getPunchInTimeOffset(),
                'note' => $attendanceRecord->getPunchInNote()
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
    public function getValidationRules(): array
    {
        return [
            parent::PARAMETER_NOTE => ['StringType' => true, 'Length' => [1, 250]],
            parent::PARAMETER_DATE_TIME => ['Date' => ['Y-m-d H:i']],
            parent::PARAMETER_TIME_ZONE => ['Numeric' => true, 'NotEmpty' => true]

        ];
    }
}
