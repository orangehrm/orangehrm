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

use Orangehrm\Rest\Api\User\EmployeePunchInAPI;


/**
 * @group API
 */
class ApiEmployeePunchInAPITest extends PHPUnit\Framework\TestCase
{
    /**
     * @var Request
     */
    private $request = null;

    protected function setUp()
    {
        TestDataService::truncateSpecificTables(array('AttendanceRecord', 'Employee'));
        TestDataService::populate(
            sfConfig::get('sf_plugins_dir') . '/orangehrmRESTPlugin/test/fixtures/punchInRecords.yml'
        );

        $sfEvent = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $this->request = new Request($sfRequest);
    }

    public function testSavePunchOutForOverlappingRecords()
    {
        $attendanceService = $this->getMockBuilder(
            'AttendanceService'
        )
            ->setMethods(
                [
                    'getDateTimeEditable',
                    'getLastPunchRecord',
                    'validateTimezone',
                    'getCalculatedPunchInUtcTime',
                    'checkForPunchInOverLappingRecords'
                ]
            )
            ->getMock();
        $attendanceService->expects($this->once())
            ->method('getDateTimeEditable')
            ->will($this->returnValue(true));
        $attendanceService->expects($this->once())
            ->method('getLastPunchRecord')
            ->will($this->returnValue(null));
        $attendanceService->expects($this->once())
            ->method('validateTimezone')
            ->will($this->returnValue(true));
        $attendanceService->expects($this->once())
            ->method('checkForPunchInOverLappingRecords')
            ->will($this->returnValue(0));

        $filters = ['timeZone' => 'Asia/Colombo', 'dateTime' => "2020-12-29 19:34"];
        $employeePunchInApi = $this->getMockBuilder('Orangehrm\Rest\Api\User\EmployeePunchInAPI')
            ->setMethods(['checkValidEmployee', 'getTimezoneOffset', 'filterParameters'])
            ->setConstructorArgs([$this->request])
            ->getMock();
        $employeePunchInApi->setAttendanceService($attendanceService);
        $employeePunchInApi->expects($this->once())
            ->method('filterParameters')
            ->will($this->returnValue($filters));
        $employeePunchInApi->expects($this->once())
            ->method('checkValidEmployee')
            ->will($this->returnValue(true));
        $employeePunchInApi->expects($this->once())
            ->method('getTimezoneOffset')
            ->with('UTC', 'Asia/Colombo')
            ->will($this->returnValue(5.5));
        $this->expectException(InvalidParamException::class);
        $employeePunchInApi->savePunchIn();
    }

    public function testSavePunchInForDateTimeEmptyWhenEditable()
    {
        $attendanceService = $this->getMockBuilder(
            'AttendanceService'
        )
            ->setMethods(
                [
                    'getDateTimeEditable',
                    'getLastPunchRecord',
                    'validateTimezone',
                    'getCalculatedPunchInUtcTime',
                    'checkForPunchInOverLappingRecords'
                ]
            )
            ->getMock();
        $attendanceService
            ->method('getDateTimeEditable')
            ->will($this->returnValue(true));
        $attendanceService
            ->method('getLastPunchRecord')
            ->will($this->returnValue(null));
        $attendanceService
            ->method('validateTimezone')
            ->will($this->returnValue(true));
        $attendanceService
            ->method('checkForPunchInOverLappingRecords')
            ->will($this->returnValue(0));

        $filters = ['timeZone' => 'Asia/Colombo'];
        $employeePunchInApi = $this->getMockBuilder('Orangehrm\Rest\Api\User\EmployeePunchInAPI')
            ->setMethods(['checkValidEmployee', 'getTimezoneOffset', 'filterParameters'])
            ->setConstructorArgs([$this->request])
            ->getMock();
        $employeePunchInApi->setAttendanceService($attendanceService);
        $employeePunchInApi
            ->method('filterParameters')
            ->will($this->returnValue($filters));
        $employeePunchInApi
            ->method('checkValidEmployee')
            ->will($this->returnValue(true));
        $employeePunchInApi
            ->method('getTimezoneOffset')
            ->with('UTC', 'Asia/Colombo')
            ->will($this->returnValue(5.5));
        $this->expectException(InvalidParamException::class);
        $employeePunchInApi->savePunchIn();
    }

    public function testSavePunchInForEmployeeAlreadyPunchedIn()
    {
        $attendanceService = $this->getMockBuilder(
            'AttendanceService'
        )
            ->setMethods(
                [
                    'getDateTimeEditable',
                    'getLastPunchRecord',
                    'validateTimezone',
                    'getCalculatedPunchInUtcTime',
                    'checkForPunchInOverLappingRecords'
                ]
            )
            ->getMock();
        $attendanceService
            ->method('getDateTimeEditable')
            ->will($this->returnValue(true));
        $attendanceService
            ->method('getLastPunchRecord')
            ->will($this->returnValue(true));
        $attendanceService
            ->method('validateTimezone')
            ->will($this->returnValue(true));
        $attendanceService
            ->method('checkForPunchInOverLappingRecords')
            ->will($this->returnValue(0));

        $filters = ['timeZone' => 'Asia/Colombo'];
        $employeePunchInApi = $this->getMockBuilder('Orangehrm\Rest\Api\User\EmployeePunchInAPI')
            ->setMethods(['checkValidEmployee', 'getTimezoneOffset', 'filterParameters'])
            ->setConstructorArgs([$this->request])
            ->getMock();
        $employeePunchInApi->setAttendanceService($attendanceService);
        $employeePunchInApi
            ->method('filterParameters')
            ->will($this->returnValue($filters));
        $employeePunchInApi
            ->method('checkValidEmployee')
            ->will($this->returnValue(true));
        $employeePunchInApi
            ->method('getTimezoneOffset')
            ->with('UTC', 'Asia/Colombo')
            ->will($this->returnValue(5.5));
        $this->expectException(InvalidParamException::class);
        $employeePunchInApi->savePunchIn();
    }

    public function testSavePunchInForTimeZoneEmpty()
    {
        $attendanceService = $this->getMockBuilder(
            'AttendanceService'
        )
            ->setMethods(
                [
                    'getDateTimeEditable',
                    'getLastPunchRecord',
                    'validateTimezone',
                    'getCalculatedPunchInUtcTime',
                    'checkForPunchInOverLappingRecords'
                ]
            )
            ->getMock();
        $attendanceService
            ->method('getDateTimeEditable')
            ->will($this->returnValue(true));
        $attendanceService
            ->method('getLastPunchRecord')
            ->will($this->returnValue(true));
        $attendanceService
            ->method('validateTimezone')
            ->will($this->returnValue(true));
        $attendanceService
            ->method('checkForPunchInOverLappingRecords')
            ->will($this->returnValue(0));

        $filters = ['dateTime' => "2020-12-29 19:34"];
        $employeePunchInApi = $this->getMockBuilder('Orangehrm\Rest\Api\User\EmployeePunchInAPI')
            ->setMethods(['checkValidEmployee', 'getTimezoneOffset', 'filterParameters'])
            ->setConstructorArgs([$this->request])
            ->getMock();
        $employeePunchInApi->setAttendanceService($attendanceService);
        $employeePunchInApi
            ->method('filterParameters')
            ->will($this->returnValue($filters));
        $employeePunchInApi
            ->method('checkValidEmployee')
            ->will($this->returnValue(true));
        $employeePunchInApi
            ->method('getTimezoneOffset')
            ->with('UTC', 'Asia/Colombo')
            ->will($this->returnValue(5.5));
        $this->expectException(InvalidParamException::class);
        $employeePunchInApi->savePunchIn();
    }

    public function testSavePunchInForTimeZoneInvalid()
    {
        $attendanceService = $this->getMockBuilder(
            'AttendanceService'
        )
            ->setMethods(
                [
                    'getDateTimeEditable',
                    'getLastPunchRecord',
                    'validateTimezone',
                    'getCalculatedPunchInUtcTime',
                    'checkForPunchInOverLappingRecords'
                ]
            )
            ->getMock();
        $attendanceService
            ->method('getDateTimeEditable')
            ->will($this->returnValue(true));
        $attendanceService
            ->method('getLastPunchRecord')
            ->will($this->returnValue(true));
        $attendanceService
            ->method('validateTimezone')
            ->will($this->returnValue(false));
        $attendanceService
            ->method('checkForPunchInOverLappingRecords')
            ->will($this->returnValue(0));

        $filters = ['dateTime' => "2020-12-29 19:34"];
        $employeePunchInApi = $this->getMockBuilder('Orangehrm\Rest\Api\User\EmployeePunchInAPI')
            ->setMethods(['checkValidEmployee', 'getTimezoneOffset', 'filterParameters'])
            ->setConstructorArgs([$this->request])
            ->getMock();
        $employeePunchInApi->setAttendanceService($attendanceService);
        $employeePunchInApi
            ->method('filterParameters')
            ->will($this->returnValue($filters));
        $employeePunchInApi
            ->method('checkValidEmployee')
            ->will($this->returnValue(true));
        $employeePunchInApi
            ->method('getTimezoneOffset')
            ->with('UTC', 'Asia/Colombo')
            ->will($this->returnValue(5.5));
        $this->expectException(InvalidParamException::class);
        $employeePunchInApi->savePunchIn();
    }

}
