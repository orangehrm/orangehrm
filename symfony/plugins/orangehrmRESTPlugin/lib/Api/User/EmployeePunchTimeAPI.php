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

use Orangehrm\Rest\Api\Admin\Entity\User;
use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Http\Response;

class EmployeePunchTimeAPI extends EndPoint
{

    const PARAMETER_ID = 'id';
    const PARAMETER_TIME_ZONE = 'timezone';
    const PARAMETER_NOTE = 'note';
    const PARAMETER_DATE_TIME = 'datetime';

    protected $employeeService;
    protected $attendanceService;

    /**
     * @return \EmployeeService
     */
    public function getEmployeeService()
    {
        if (!$this->employeeService) {
            $this->employeeService = new \EmployeeService();
        }
        return $this->employeeService;
    }

    /**
     * @param $employeeService
     * @return $this
     */
    public function setEmployeeService($employeeService)
    {
        $this->employeeService = $employeeService;
        return $this;
    }

    /**
     * @return \AttendanceService
     */
    public function getAttendanceService()
    {
        if (is_null($this->attendanceService)) {
            $this->attendanceService = new \AttendanceService();
        }
        return $this->attendanceService;
    }

    /**
     * @param \AttendanceService $attendanceService
     */
    public function setAttendanceService(\AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }


    /**
     * @param $empNumber
     * @return \Employee
     */
    public function checkValidEmployee($empNumber)
    {
        try {
            return $this->getEmployeeService()->getEmployee($empNumber);
        } catch (\Exception $e) {
            new BadRequestException($e->getMessage());
        }
    }

    /**
     * @param $remote_tz
     * @param null $origin_tz
     * @return int
     */
    function getTimezoneOffset($remote_tz, $origin_tz = null)
    {
        if ($origin_tz === null) {
            if (!is_string($origin_tz = date_default_timezone_get())) {
                return false;
            }
        }
        $origin_dtz = new \DateTimeZone($origin_tz);
        $remote_dtz = new \DateTimeZone($remote_tz);
        $origin_dt = new \DateTime("now", $origin_dtz);
        $remote_dt = new \DateTime("now", $remote_dtz);
        $offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
        return $offset;
    }



    public function getDetailsForPunchIn()
    {
        $empNumber = $this->getAttendanceService()->GetLoggedInEmployeeNumber();
        if (!$this->checkValidEmployee($empNumber)) {
            throw new RecordNotFoundException('Employee Id' . $empNumber . ' Not Found');
        }
        $actionableStatesList = array(PluginAttendanceRecord::STATE_PUNCHED_IN);
        $attendanceRecord = $this->getAttendanceService()->getLastPunchRecord($empNumber, $actionableStatesList);
        if ($attendanceRecord) {
            throw new InvalidParamException('Cannot Proceed Punch In Employee Already Punched In');
        }
        $lastRecord = $this->getAttendanceService()->getLatestPunchInRecord(
            $empNumber,
            PluginAttendanceRecord::STATE_PUNCHED_OUT
        );
        $lastRecordId = null;
        $displayTimeZoneOffset = null;
        if ($lastRecord) {
            $lastRecordId = $lastRecord->getId();
            $lastRecordPunchOutTime = $lastRecord->getPunchOutUserTime();
            $punchOutTimeOffset = $lastRecord->getPunchOutTimeOffset();
            $displayTimeZoneOffset = $this->getAttendanceService()->getOriginDisplayTimeZoneOffset($punchOutTimeOffset);
        }


        $punchTimeEditableDetails = $this->getPunchTimeEditable();
        return new Response(
            array(
                'id' => $lastRecordId,
                'punchOutTime' => $lastRecordPunchOutTime,
                'punchOutTimezone' => $displayTimeZoneOffset,
                'dateTimeEditable' => $punchTimeEditableDetails['editable'],
                'currentUtcDateTime' => $punchTimeEditableDetails['serverUtcTime']
            )
        );
    }


    public function getDetailsForPunchOut()
    {
        $empNumber = $this->getAttendanceService()->GetLoggedInEmployeeNumber();
        if (!$this->checkValidEmployee($empNumber)) {
            throw new RecordNotFoundException('Employee Id' . $empNumber . ' Not Found');
        }
        $actionableStatesList = array(PluginAttendanceRecord::STATE_PUNCHED_IN);
        $attendanceRecord = $this->getAttendanceService()->getLastPunchRecord($empNumber, $actionableStatesList);
        if (is_null($attendanceRecord)) {
            throw new InvalidParamException('Cannot Proceed Punch Out Employee Already Punched Out');
        }
        $lastRecord = $this->getAttendanceService()->getLatestPunchInRecord(
            $empNumber,
            PluginAttendanceRecord::STATE_PUNCHED_IN
        );
        $lastRecordId = null;
        $displayTimeZoneOffset = null;
        if ($lastRecord) {
            $lastRecordId = $lastRecord->getId();
            $lastRecordPunchInTime = $lastRecord->getPunchInUserTime();
            $punchInTimeOffset = $lastRecord->getPunchInTimeOffset();
            $displayTimeZoneOffset = $this->getAttendanceService()->getOriginDisplayTimeZoneOffset($punchInTimeOffset);
        }

        $punchTimeEditableDetails = $this->getPunchTimeEditable();
        return new Response(
            array(
                'id' => $lastRecordId,
                'punchInTime' => $lastRecordPunchInTime,
                'punchInTimezone' => $displayTimeZoneOffset,
                'dateTimeEditable' => $punchTimeEditableDetails['editable'],
                'currentUtcDateTime' => $punchTimeEditableDetails['serverUtcTime']
            )
        );
    }

//    /**
//     * @dataProvider requestParamProvider
//     * @throws InvalidParamException
//     * @throws \Orangehrm\Rest\Api\Exception\RecordNotFoundException
//     */
//    public function testSavePunchInValidParams($id, $returnParamCallback,$expected)
//    {
//        $requestParams = $this->getMockBuilder('\Orangehrm\Rest\Http\RequestParams')
//            ->disableOriginalConstructor()
//            ->setMethods(['getUrlParam','getQueryParam'])
//            ->getMock();
//        $requestParams //->expects($this->once())
//            ->method('getUrlParam')
//            ->will($this->returnCallback($returnParamCallback));
//        $requestParams //->expects($this->exactly(6))
//            ->method('getQueryParam')
//            ->will($this->returnCallback($returnParamCallback));
//
//
//        $sfEvent = new sfEventDispatcher();
//        $sfRequest = new sfWebRequest($sfEvent);
//        $request = new Request($sfRequest);
//
//        $attendanceService = $this->getMockBuilder(
//            'Orangehrm\Attendance\AttendanceService'
//        )
//            ->setMethods(['getDateTimeEditable','getLastPunchRecord','validateTimezone','getCalculatedPunchInUtcTime','checkForPunchInOverLappingRecords','GetLoggedInEmployeeNumber'])
//            ->getMock();
//        $attendanceService->expects($this->once())
//            ->method('getLastPunchRecord')
//            ->with(1, \PluginAttendanceRecord::STATE_PUNCHED_IN)
//            ->will($this->returnValue(null));
//        $attendanceService->expects($this->once())
//            ->method('checkForPunchInOverLappingRecords')
//            ->with(1, \PluginAttendanceRecord::STATE_PUNCHED_IN)
//            ->will($this->returnValue(1));
//
//        $employeePunchInApi = $this->getMockBuilder(
//            'Orangehrm\Rest\Api\User\EmployeePunchInAPI'
//        )
//            ->setMethods(['setPunchInRecord','getTimezoneOffset'])
//            ->setConstructorArgs([$request])
//            ->getMock();
//
//        $employeePunchInApi->setRequestParams($requestParams);
//        $employeePunchInApi->expects($this->once())
//            ->method('GetLoggedInEmployeeNumber')
//            ->will($this->returnValue(1));
//
//
//        $actualResponse = $employeePunchInApi->savePunchIn();
//        $expectedResponse= new Response($expected,[]);
//        assertEquals($expectedResponse, $actualResponse);
//    }
//
//
//
//    /**
//     * @return Generator
//     */
//    public function requestParamProvider()
//    {
//        yield [
//            1,
//            function ($param) {
//                $params=[
//                    "datetime" => "2020-12-28 10:22",
//                    "note" => "PUNCH IN NOTE",
//                    "timezone" => "Asia/Colombo"
//                ];
//                return $params[$param];
//            },
//            [
//                "success" => "Successfully Punched In",
//                "id" =>  "24",
//                "datetime" => "2020-12-28 10:22",
//                "note" => "PUNCH IN NOTE",
//                "timezone" => "Asia/Colombo"
//            ]
//        ];
//    }
//
//    public function testGetLastPunchOutRecordDetailsValidParams()
//    {
//
//        $sfEvent = new sfEventDispatcher();
//        $sfRequest = new sfWebRequest($sfEvent);
//        $request = new Request($sfRequest);
//
//        $employeePunchInApi = $this->getMockBuilder(
//            'Orangehrm\Rest\Api\User\EmployeePunchInAPI'
//        )
//            ->setMethods(['GetLoggedInEmployeeNumber'])
//            ->setConstructorArgs([$request])
//            ->getMock();
//
//        $employeePunchInApi->expects($this->once())
//            ->method('GetLoggedInEmployeeNumber')
//            ->will($this->returnValue(1));
//
//        $actualResponse = $employeePunchInApi->getLastPunchRecordDetails();
//        $expectedResponse= $this->getTestCaseForGetLastRecordDetails();
//        $this->assertEquals($expectedResponse, $actualResponse);
//    }
//    public function getTestCaseForGetLastRecordDetails(){
//        return new Response(
//            [
//                "id"=> "10",
//                "punchOutTime"=> "2020-12-25 10:26:00",
//                "timezone"=> '3'
//            ]
//        );
//    }




}
