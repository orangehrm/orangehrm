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

use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Http\RequestParams;
use Orangehrm\Rest\Http\Response;

use Orangehrm\Rest\Api\User\EmployeePunchOutAPI;

/**
 * @group API
 */
class ApiEmployeePunchOutAPITest extends PHPUnit\Framework\TestCase
{
    /**
     * @var Request
     */
    private $request = null;

    protected function setUp()
    {
        $sfEvent = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $this->request = new Request($sfRequest);
    }

    public function testSavePunchOutForOverlappingRecords()
    {
        $attendanceService = $this->getMockBuilder(
            'AttendanceService')
            ->setMethods(['getPunchTimeUserConfiguration','getLastPunchRecord','checkForPunchOutOverLappingRecords','checkForPunchInOverLappingRecords'])
            ->getMock();
        $attendanceService
            ->method('getPunchTimeUserConfiguration')
            ->will($this->returnValue(true));
        $attendanceService
            ->method('getLastPunchRecord')
            ->will($this->returnValue(null));
        $attendanceService
            ->method('checkForPunchOutOverLappingRecords')
            ->will($this->returnValue(1));

        $filters = ['timezone'=> 5.5,'datetime'=>"2020-12-29 19:34"];
        $employeePunchOutApi = $this->getMockBuilder('Orangehrm\Rest\Api\User\Attendance\EmployeePunchOutAPI')
            ->setMethods(['checkValidEmployee','getParameters'])
            ->setConstructorArgs([$this->request])
            ->getMock();
        $employeePunchOutApi->setAttendanceService($attendanceService);
        $employeePunchOutApi
            ->method('getParameters')
            ->will($this->returnValue($filters));
        $employeePunchOutApi
            ->method('checkValidEmployee')
            ->will($this->returnValue(true));
        $this->expectException(InvalidParamException::class);
        $employeePunchOutApi->savePunchOut();

    }


    public function testSavePunchOutForEmployeeAlreadyPunchedOut()
    {
        $attendanceService = $this->getMockBuilder(
            'AttendanceService')
            ->setMethods(['getPunchTimeUserConfiguration','getLastPunchRecord','getCalculatedPunchInUtcTime','checkForPunchOutOverLappingRecords'])
            ->getMock();
        $attendanceService
            ->method('getPunchTimeUserConfiguration')
            ->will($this->returnValue(true));
        $attendanceService
            ->method('getLastPunchRecord')
            ->will($this->returnValue(true));
        $attendanceService
            ->method('checkForPunchOutOverLappingRecords')
            ->will($this->returnValue(1));

        $filters = ['timezone'=> 5.5];
        $employeePunchOutApi = $this->getMockBuilder('Orangehrm\Rest\Api\User\Attendance\EmployeePunchOutAPI')
            ->setMethods(['checkValidEmployee','getParameters'])
            ->setConstructorArgs([$this->request])
            ->getMock();
        $employeePunchOutApi->setAttendanceService($attendanceService);
        $employeePunchOutApi
            ->method('getParameters')
            ->will($this->returnValue($filters));
        $employeePunchOutApi
            ->method('checkValidEmployee')
            ->will($this->returnValue(true));
        $this->expectException(InvalidParamException::class);
        $employeePunchOutApi->savePunchOut();

    }

    public function testSavePunchInForTimeZoneEmpty()
    {
        $attendanceService = $this->getMockBuilder(
            'AttendanceService')
            ->setMethods(['getPunchTimeUserConfiguration','getLastPunchRecord','getCalculatedPunchInUtcTime','checkForPunchOutOverLappingRecords'])
            ->getMock();
        $attendanceService
            ->method('getPunchTimeUserConfiguration')
            ->will($this->returnValue(true));
        $attendanceService
            ->method('getLastPunchRecord')
            ->will($this->returnValue(true));
        $attendanceService
            ->method('checkForPunchOutOverLappingRecords')
            ->will($this->returnValue(1));

        $filters = ['datetime'=>"2020-12-29 19:34"];
        $employeePunchOutApi = $this->getMockBuilder('Orangehrm\Rest\Api\User\Attendance\EmployeePunchOutAPI')
            ->setMethods(['checkValidEmployee','getParameters'])
            ->setConstructorArgs([$this->request])
            ->getMock();
        $employeePunchOutApi->setAttendanceService($attendanceService);
        $employeePunchOutApi
            ->method('getParameters')
            ->will($this->returnValue($filters));
        $employeePunchOutApi
            ->method('checkValidEmployee')
            ->will($this->returnValue(true));
        $this->expectException(InvalidParamException::class);
        $employeePunchOutApi->savePunchOut();
    }

    public function testSavePunchOutForTimeZoneInvalid()
    {
        $attendanceService = $this->getMockBuilder(
            'AttendanceService')
            ->setMethods(['getPunchTimeUserConfiguration','getLastPunchRecord','getCalculatedPunchInUtcTime','checkForPunchOutOverLappingRecords'])
            ->getMock();
        $attendanceService
            ->method('getPunchTimeUserConfiguration')
            ->will($this->returnValue(true));
        $attendanceService
            ->method('getLastPunchRecord')
            ->will($this->returnValue(true));
        $attendanceService
            ->method('checkForPunchOutOverLappingRecords')
            ->will($this->returnValue(0));

        $filters = ['datetime'=>"2020-12-29 19:34",'timezone'=>5.6];
        $employeePunchOutApi = $this->getMockBuilder('Orangehrm\Rest\Api\User\Attendance\EmployeePunchOutAPI')
            ->setMethods(['checkValidEmployee','getTimezoneOffset','getParameters'])
            ->setConstructorArgs([$this->request])
            ->getMock();
        $employeePunchOutApi->setAttendanceService($attendanceService);
        $employeePunchOutApi
            ->method('getParameters')
            ->will($this->returnValue($filters));
        $employeePunchOutApi
            ->method('checkValidEmployee')
            ->will($this->returnValue(true));
        $this->expectException(InvalidParamException::class);
        $employeePunchOutApi->savePunchOut();

    }
}
