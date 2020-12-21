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
use Orangehrm\Rest\Http\Response;

/**
 * @group API
 */
class ApiAttendanceAPITest extends PHPUnit\Framework\TestCase
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
        TestDataService::truncateSpecificTables(array('AttendanceRecord','Employee','LeaveType','Leave'));
        TestDataService::populate(sfConfig::get('sf_plugins_dir') . '/orangehrmAttendancePlugin/test/fixtures/AttendanceService.yml');
    }


    public function testGetAttendanceFinalDetails()
    {
        $attendanceRecord = TestDataService::fetchObject('AttendanceRecord', 10);
        $params = ['fromDate' => "2020-12-29", 'toDate' => "2020-12-29", 'empNumber' => 1];
        $attendanceSummaryAPI = $this->getMockBuilder('Orangehrm\Rest\Api\User\Attendance\AttendanceAPI')
            ->setMethods(['getParameters', 'getAccessibleEmpNumbers','getLoggedInEmployeeNumber', 'getEmployeeDetails','getWorkHours'])
            ->setConstructorArgs([$this->request])
            ->getMock();
        $attendanceSummaryAPI->expects($this->once())
            ->method('getParameters')
            ->will($this->returnValue($params));
        $attendanceSummaryAPI->expects($this->once())
            ->method('getLoggedInEmployeeNumber')
            ->will($this->returnValue(1));
        $attendanceSummaryAPI->expects($this->once())
            ->method('getWorkHours')
            ->will($this->returnValue([$attendanceRecord->toArray()]));
        $attendanceSummaryAPI
            ->method('getAccessibleEmpNumbers')
            ->will($this->returnValue([1]));
        $actual = $attendanceSummaryAPI->getAttendanceRecords();
        $expected= new Response(array($attendanceRecord->toArray()));
        $this->assertEquals($expected, $actual);
    }

    public function testGetAttendanceFinalDetailsForNotValidEmployee()
    {
        $attendanceRecord = TestDataService::fetchObject('AttendanceRecord', 10);
        $params = ['fromDate' => "2020-12-29", 'toDate' => "2020-12-29", 'empNumber' => 1000];
        $attendanceSummaryAPI = $this->getMockBuilder('Orangehrm\Rest\Api\User\Attendance\AttendanceAPI')
            ->setMethods(['getParameters','getAccessibleEmpNumbers', 'getLoggedInEmployeeNumber', 'getEmployeeDetails','getWorkHours'])
            ->setConstructorArgs([$this->request])
            ->getMock();
        $attendanceSummaryAPI->expects($this->once())
            ->method('getParameters')
            ->will($this->returnValue($params));
        $attendanceSummaryAPI->expects($this->once())
            ->method('getLoggedInEmployeeNumber')
            ->will($this->returnValue(1));
        $attendanceSummaryAPI
            ->method('getWorkHours')
            ->will($this->returnValue([$attendanceRecord->toArray()]));
        $attendanceSummaryAPI
            ->method('getAccessibleEmpNumbers')
            ->will($this->returnValue([]));
        $this->expectException(BadRequestException::class);
        $attendanceSummaryAPI->getAttendanceRecords();
    }

    public function testGetAttendanceFinalDetailsForNotValidDatePeriod()
    {
        $attendanceRecord = TestDataService::fetchObject('AttendanceRecord', 10);
        $params = ['fromDate' => "2020-12-31", 'toDate' => "2020-12-29", 'empNumber' => 1000];
        $attendanceSummaryAPI = $this->getMockBuilder('Orangehrm\Rest\Api\User\Attendance\AttendanceAPI')
            ->setMethods(['getParameters','getAccessibleEmpNumbers', 'getLoggedInEmployeeNumber', 'getEmployeeDetails','getWorkHours'])
            ->setConstructorArgs([$this->request])
            ->getMock();
        $attendanceSummaryAPI->expects($this->once())
            ->method('getParameters')
            ->will($this->returnValue($params));
        $attendanceSummaryAPI->expects($this->once())
            ->method('getLoggedInEmployeeNumber')
            ->will($this->returnValue(1));
        $attendanceSummaryAPI
            ->method('getWorkHours')
            ->will($this->returnValue([$attendanceRecord->toArray()]));
        $attendanceSummaryAPI
            ->method('getAccessibleEmpNumbers')
            ->will($this->returnValue([]));
        $this->expectException(InvalidParamException::class);
        $attendanceSummaryAPI->getAttendanceRecords();
    }
}
