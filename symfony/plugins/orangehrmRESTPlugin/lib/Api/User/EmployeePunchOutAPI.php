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

use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Http\Response;
use Orangehrm\Rest\Api\Attendance\PunchOutAPI;
use \PluginAttendanceRecord;
use \DateTimeZone;
use \DateTime;
use \Exception;


class EmployeePunchOutAPI extends PunchOutAPI
{
    /**
     * @return Response
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     */
    public function savePunchOut()
    {
        $filters = $this->filterParameters();
        $timeZone = $filters['timeZone'];
        $punchOutNote = $filters['punchOutNote'];
        $dateTime = $filters['dateTime'];

        $editable = $this->getAttendanceService()->getDateTimeEditable();
        if ($editable && empty($dateTime)) {
            throw new InvalidParamException('Datetime Cannot Be Empty');
        } else {
            if (!$editable && !empty($dateTime)) {
                throw new InvalidParamException('You Are Not Allowed To Change Current Date & Time');
            }
        }
        $empNumber = $this->getAttendanceService()->GetLoggedInEmployeeNumber();

        if (!$this->checkValidEmployee($empNumber)) {
            throw new RecordNotFoundException('Employee id ' . $empNumber . ' Not Found');
        }
        $actionableStatesList = array(PluginAttendanceRecord::STATE_PUNCHED_IN);
        $attendanceRecord = $this->getAttendanceService()->getLastPunchRecord($empNumber, $actionableStatesList);

        if (is_null($attendanceRecord)) {
            throw new InvalidParamException('Cannot Proceed Punch Out Employee Already Punched Out');
        }
        $nextState = PluginAttendanceRecord::STATE_PUNCHED_OUT;

        if (empty($timeZone)) {
            throw new InvalidParamException('Datetime Cannot Be Empty');
        }
        if (!$this->getAttendanceService()->validateTimezone($timeZone)) {
            throw new InvalidParamException('Invalid Time Zone');
        }
        $timeZoneDTZ = new DateTimeZone($timeZone);
        $originDT = new DateTime($dateTime, $timeZoneDTZ);
        $punchOutdateTime = $originDT->format('Y-m-d H:i');
        $timeZoneOffset = $this->getTimezoneOffset('UTC', $timeZone);

        //check overlapping
        $punchOutUtcTime = $this->getAttendanceService()->getCalculatedPunchInUtcTime(
            $punchOutdateTime,
            $timeZoneOffset
        );
        $isValid = $this->getAttendanceService()->checkForPunchOutOverLappingRecords(
            $attendanceRecord->getPunchInUtcTime(),
            $punchOutUtcTime,
            $empNumber,
            $attendanceRecord->getId()
        );
        if (!$isValid) {
            throw new InvalidParamException('Overlapping Records Found');
        }
        try {
            $attendanceRecord = $this->setPunchOutRecord(
                $attendanceRecord,
                $nextState,
                $punchOutUtcTime,
                $punchOutdateTime,
                $timeZoneOffset / 3600,
                $punchOutNote
            );

            $displayPunchInTimeZoneOffset = $this->getAttendanceService()->getOriginDisplayTimeZoneOffset(
                $attendanceRecord->getPunchInTimeOffset()
            );
            $displayPunchOutTimeZoneOffset = $this->getAttendanceService()->getOriginDisplayTimeZoneOffset(
                $timeZoneOffset / 3600
            );

            return new Response(
                array(
                    'success' => 'Successfully Punched Out',
                    'id' => $attendanceRecord->getId(),
                    'punchInDateTime' => $attendanceRecord->getPunchInUserTime(),
                    'punchInTimeZone' => $displayPunchInTimeZoneOffset,
                    'punchInNote' => $attendanceRecord->getPunchInNote(),
                    'punchOutDateTime' => $attendanceRecord->getPunchOutUserTime(),
                    'punchOutTimeZone' => $displayPunchOutTimeZoneOffset,
                    'punchOutNote' => $attendanceRecord->getPunchOutNote(),
                )
            );
        } catch (Exception $e) {
            new BadRequestException($e->getMessage());
        }
    }

    public function filterParameters()
    {
        $filters = array();
        $filters['timeZone'] = $this->getRequestParams()->getPostParam(parent::PARAMETER_TIME_ZONE);
        $filters['punchInNote'] = $this->getRequestParams()->getPostParam(parent::PARAMETER_NOTE);
        $filters['dateTime'] = $this->getRequestParams()->getPostParam(parent::PARAMETER_DATE_TIME);
        return $filters;
    }

    /**
     * @return array
     */
    public function getValidationRules()
    {
        return array(
            parent::PARAMETER_NOTE => ['StringType' => true, 'Length' => [1, 250]],
            parent::PARAMETER_DATE_TIME => ['Date' => ['Y-m-d H:i']],
            parent::PARAMETER_TIME_ZONE => ['NotEmpty' => true, 'IntegerType' => true]
        );
    }
}
