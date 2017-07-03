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

namespace Orangehrm\Rest\Api\Attendance;


use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Http\Response;


class PunchInAPI extends PunchTimeAPI
{

    /**
     * @return Response
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     */
    public function savePunchIn()
    {
        $empNumber = $this->getRequestParams()->getUrlParam(parent::PARAMETER_ID);
        $timeZone = $this->getRequestParams()->getPostParam(parent::PARAMETER_TIME_ZONE);
        $punchInNote = $this->getRequestParams()->getPostParam(parent::PARAMETER_NOTE);
        $dateTime = $this->getRequestParams()->getPostParam(parent::PARAMETER_DATE_TIME);
        if(empty($dateTime)) {
            throw new InvalidParamException('Datetime Cannot Be Empty');
        }

        if($this->checkValidEmployee($empNumber)){
            $actionableStatesList = array(\PluginAttendanceRecord::STATE_PUNCHED_IN);
            $attendanceRecord = $this->getAttendanceService()->getLastPunchRecord($empNumber, $actionableStatesList);

            if (is_null($attendanceRecord)) {
                $attendanceRecord = new \AttendanceRecord();
                $attendanceRecord->setEmployeeId($empNumber);

                $nextState = \PluginAttendanceRecord::STATE_PUNCHED_IN;
                if($timeZone){
                    $zoneList = timezone_identifiers_list();
                    if(in_array($timeZone,$zoneList)) {
                        $timeZone_dtz = new \DateTimeZone($timeZone);
                        $origin_dt = new \DateTime($dateTime, $timeZone_dtz);
                        $punchIndateTime = $origin_dt->format('Y-m-d H:i');
                        $timeZoneOffset = $this->getTimezoneOffset('UTC', $timeZone);
                    }else{
                        throw new InvalidParamException('Invalid Time Zone');
                    }
                }else {

                    $punchIndateTime = date($dateTime,'Y-m-d H:i');
                    $timeZoneOffset = $this->getTimezoneOffset('UTC');
                }
                try {
                    $attendanceRecord = $this->setPunchInRecord(
                        $attendanceRecord,
                        $nextState,
                        date('Y-m-d H:i:s', strtotime($punchIndateTime) + ((-1) * $timeZoneOffset)),
                        $punchIndateTime,
                        $timeZoneOffset / 3600,
                        $punchInNote
                    );
                    return new Response(array('success' => 'Successfully Punched In','id'=>$attendanceRecord->getId()));

                }catch (Exception $e) {
                    new BadRequestException($e->getMessage());
                }

            } else {
                throw new InvalidParamException('Cannot Proceed Punch In Employee Already Punched In');
            }

        }else{
            throw new RecordNotFoundException('Employee Id '.$empNumber.' Not Found');
        }
    }


    /**
     * @param $attendanceRecord
     * @param $state
     * @param $punchInUtcTime
     * @param $punchInUserTime
     * @param $punchInTimezoneOffset
     * @param $punchInNote
     * @return \AttendanceRecord
     */
    public function setPunchInRecord($attendanceRecord, $state, $punchInUtcTime, $punchInUserTime, $punchInTimezoneOffset, $punchInNote) {

        $attendanceRecord->setState($state);
        $attendanceRecord->setPunchInUtcTime($punchInUtcTime);
        $attendanceRecord->setPunchInUserTime($punchInUserTime);
        $attendanceRecord->setPunchInNote($punchInNote);
        $attendanceRecord->setPunchInTimeOffset($punchInTimezoneOffset);
        return $this->getAttendanceService()->savePunchRecord($attendanceRecord);
    }

    /**
     * @return array
     */
    public function getValidationRules()
    {
        return array(
            self::PARAMETER_NOTE => array('StringType' => true, 'Length' => array(1, 250)),
            self::PARAMETER_DATE_TIME => array('Date' => array('Y-m-d H:i'))
        );
    }
}