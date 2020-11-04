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

namespace Orangehrm\Rest\Api\User;

use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Http\Response;
use Orangehrm\Rest\Api\Attendance\PunchInAPI;


class EmployeePunchInAPI extends PunchInAPI
{

    /**
     * @return Response
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     */
    public function savePunchIn()
    {
        $timeZone = $this->getRequestParams()->getPostParam(parent::PARAMETER_TIME_ZONE);
        $punchInNote = $this->getRequestParams()->getPostParam(parent::PARAMETER_NOTE);
        $dateTime = $this->getRequestParams()->getPostParam(parent::PARAMETER_DATE_TIME);
        if(empty($dateTime)) {
            throw new InvalidParamException('Datetime Cannot Be Empty');
        }
        $empNumber = \sfContext::getInstance()->getUser()->getAttribute("auth.empNumber");
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
                        //check overlapping
                        $punchInUtcTime= Date('Y-m-d H:i:s', strtotime($punchIndateTime) + ((-1) * $timeZoneOffset));
                        $isValid = $this->getAttendanceService()->checkForPunchInOverLappingRecords($punchInUtcTime,$empNumber);
                        if(!$isValid){
                            throw new InvalidParamException('Overlapping Records Found');
                        }

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
                        $punchInUtcTime,
                        $punchIndateTime,
                        $timeZoneOffset / 3600,
                        $punchInNote
                    );
                    $origin_dtz = new \DateTimeZone($timeZone);
                    $origin_dt = new \DateTime("now", $origin_dtz);
                    $originTimeZoneOffset=$origin_dtz->getOffset($origin_dt)/3600;
                    return new Response(array('success' => 'Successfully Punched In',
                        'id'=>$attendanceRecord->getId(),
                        'datetime' => $attendanceRecord->getPunchInUserTime(),
                        'timezone' => $originTimeZoneOffset,
                        'note' => $attendanceRecord->getPunchInNote()
                    ));

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

    public function getLastPunchRecordDetails(){
        $empNumber = \sfContext::getInstance()->getUser()->getAttribute("auth.empNumber");
        if($this->checkValidEmployee($empNumber)) {

            $actionableStatesList = array(\PluginAttendanceRecord::STATE_PUNCHED_IN);
            $attendanceRecord = $this->getAttendanceService()->getLastPunchRecord($empNumber, $actionableStatesList);
            if (is_null($attendanceRecord)) {

                $lastRecord = $this->getAttendanceService()->getLastPunchRecordDetails($empNumber);
                if ($lastRecord) {
                    $punchOutTime=$lastRecord->getPunchOutUserTime();
                    $punchInTimeOffset=$lastRecord->getPunchInTimeOffset();
                    $remote_dtz = new \DateTimeZone('UTC');
                    $remote_dt = new \DateTime("now", $remote_dtz);
                    $timeZoneOffset=$punchInTimeOffset+($remote_dtz->getOffset($remote_dt))/3600;
                    return new Response(array('punchOutTime'=>$punchOutTime,'timezone'=>$timeZoneOffset));
                }
                else{
                    return new Response(array('punchOutTime'=>'0'));
                }
            } else {
                throw new InvalidParamException('Cannot Proceed Punch In Employee Already Punched In');
            }
        } else{
            throw new RecordNotFoundException('Employee Id '.$empNumber.' Not Found');
        }
    }
}
