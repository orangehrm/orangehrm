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

use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Http\Response;

/**
 * @group API
 */
class ApiAttendanceSummaryAPITest extends PHPUnit\Framework\TestCase
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
        TestDataService::populate(sfConfig::get('sf_plugins_dir') . '/orangehrmLeavePlugin/test/fixtures/AttendanceSummary.yml');
    }

    public function testGetAttendanceSummary()
    {
        $leaveRecord1 = TestDataService::fetchObject('Leave', 6);
        $leaveRecord2 = TestDataService::fetchObject('Leave', 7);
        $leaveType2 = TestDataService::fetchObject('LeaveType', 2);
        $leaveRecord1->setLeaveType($leaveType2);
        $leaveRecord2->setLeaveType($leaveType2);
        $leaveRequestService = $this->getMockBuilder(
            'LeaveRequestService'
        )
            ->setMethods(
                [
                    'getLeaveRecordsBetweenTwoDays',
                ]
            )
            ->getMock();
        $leaveRequestService->expects($this->once())
            ->method('getLeaveRecordsBetweenTwoDays')
            ->will($this->returnValue(array($leaveRecord1, $leaveRecord2)));

        $attendanceRecord1 = TestDataService::fetchObject('AttendanceRecord', 4);
        $attendanceRecord2 = TestDataService::fetchObject('AttendanceRecord', 5);
        $attendanceService = $this->getMockBuilder(
            'AttendanceService'
        )
            ->setMethods(
                [
                    'getAttendanceRecordsBetweenTwoDays',
                ]
            )
            ->getMock();
        $attendanceService->expects($this->once())
            ->method('getAttendanceRecordsBetweenTwoDays')
            ->will($this->returnValue(array($attendanceRecord1, $attendanceRecord2)));
        $params = [
            'fromDate' => "2020-12-18",
            'toDate' => "2020-12-24",
            'empNumber' => 1,
            'pendingApproval' => 'true',
            'scheduled' => 'true',
            'taken' => 'true'
        ];
        $attendanceGraphAPI = $this->getMockBuilder('Orangehrm\Rest\Api\User\Attendance\AttendanceSummaryAPI')
            ->setMethods(
                [
                    'getParameters',
                    'getLoggedInEmployeeNumber',
                    'getEmployeeDetails',
                    'getAccessibleEmpNumbers',
                ]
            )
            ->setConstructorArgs([$this->request])
            ->getMock();
        $attendanceGraphAPI->setLeaveRequestService($leaveRequestService);
        $attendanceGraphAPI->setAttendanceService($attendanceService);
        $attendanceGraphAPI->expects($this->once())
            ->method('getParameters')
            ->will($this->returnValue($params));
        $attendanceGraphAPI->expects($this->once())
            ->method('getLoggedInEmployeeNumber')
            ->will($this->returnValue(1));
        $attendanceGraphAPI
            ->method('getAccessibleEmpNumbers')
            ->will($this->returnValue([1]));
        $actual = $attendanceGraphAPI->getAttendanceSummary();
        $expected = new Response(
            array(
                "totalWorkHours"=> "0.25",
                "totalLeaveHours"=> "16.00",
                "totalLeaveTypeHours"=> [
                    array(
                        "typeId"=> "2",
                        "type"=> "Medical",
                        "hours"=> "16.00"
                    ),
                ],
                "workSummary"=> array(
                    "sunday"=> array(
                        "workHours"=> 0,
                        "leave"=> []
                    ),
                    "monday"=> array(
                        "workHours"=> 0,
                        "leave"=> [

                        ]
                    ),
                    "tuesday"=> array(
                        "workHours"=> 0,
                        "leave"=> [
                            array(
                                "typeId"=> "2",
                                "type"=> "Medical",
                                "hours"=> "8.00"
                            )
                        ]
                    ),
                    "wednesday"=> array(
                        "workHours"=> 0,
                        "leave"=> [
                            array(
                                "typeId"=> "2",
                                "type"=> "Medical",
                                "hours"=> "8.00"
                            )
                        ]
                    ),
                    "thursday"=> array(
                        "workHours"=> 0,
                        "leave"=> [

                        ]
                    ),
                    "friday"=> array(
                        "workHours"=> '0.25',
                        "leave"=> [

                        ]
                    ),
                    "saturday"=> array(
                        "workHours"=> "0",
                        "leave"=> [

                        ]
                    )
                )
            )
        );
        $this->assertEquals($expected, $actual);
    }

    public function testGetAttendanceSummaryForDateRangeNotHavingSevenDaysGap()
    {
        $leaveRecord1 = TestDataService::fetchObject('Leave', 6);
        $leaveRecord2 = TestDataService::fetchObject('Leave', 7);
        $leaveType2 = TestDataService::fetchObject('LeaveType', 2);
        $leaveRecord1->setLeaveType($leaveType2);
        $leaveRecord2->setLeaveType($leaveType2);
        $leaveRequestService = $this->getMockBuilder(
            'LeaveRequestService'
        )
            ->setMethods(
                [
                    'getLeaveRecordsBetweenTwoDays',
                ]
            )
            ->getMock();
        $leaveRequestService
            ->method('getLeaveRecordsBetweenTwoDays')
            ->will($this->returnValue(array($leaveRecord1, $leaveRecord2)));

        $attendanceRecord1 = TestDataService::fetchObject('AttendanceRecord', 4);
        $attendanceRecord2 = TestDataService::fetchObject('AttendanceRecord', 5);
        $attendanceService = $this->getMockBuilder(
            'AttendanceService'
        )
            ->setMethods(
                [
                    'getAttendanceRecordsBetweenTwoDays',
                ]
            )
            ->getMock();
        $attendanceService
            ->method('getAttendanceRecordsBetweenTwoDays')
            ->will($this->returnValue(array($attendanceRecord1, $attendanceRecord2)));
        $params = [
            'fromDate' => "2020-12-15",
            'toDate' => "2020-12-24",
            'empNumber' => 1,
            'pendingApproval' => 'true',
            'scheduled' => 'true',
            'taken' => 'true'
        ];
        $attendanceGraphAPI = $this->getMockBuilder('Orangehrm\Rest\Api\User\Attendance\AttendanceSummaryAPI')
            ->setMethods(
                [
                    'getParameters',
                    'getLoggedInEmployeeNumber',
                    'getEmployeeDetails',
                    'getAccessibleEmpNumbers'
                ]
            )
            ->setConstructorArgs([$this->request])
            ->getMock();
        $attendanceGraphAPI->setLeaveRequestService($leaveRequestService);
        $attendanceGraphAPI->setAttendanceService($attendanceService);
        $attendanceGraphAPI->expects($this->once())
            ->method('getParameters')
            ->will($this->returnValue($params));
        $attendanceGraphAPI->expects($this->once())
            ->method('getLoggedInEmployeeNumber')
            ->will($this->returnValue(1));
        $attendanceGraphAPI
            ->method('getAccessibleEmpNumbers')
            ->will($this->returnValue([1]));
        $this->expectException(InvalidParamException::class);
        $attendanceGraphAPI->getAttendanceSummary();
    }

    public function testGetAttendanceSummaryForInvalidEmployee()
    {
        $leaveRecord1 = TestDataService::fetchObject('Leave', 6);
        $leaveRecord2 = TestDataService::fetchObject('Leave', 7);
        $leaveType2 = TestDataService::fetchObject('LeaveType', 2);
        $leaveRecord1->setLeaveType($leaveType2);
        $leaveRecord2->setLeaveType($leaveType2);
        $leaveRequestService = $this->getMockBuilder(
            'LeaveRequestService'
        )
            ->setMethods(
                [
                    'getLeaveRecordsBetweenTwoDays',
                ]
            )
            ->getMock();
        $leaveRequestService
            ->method('getLeaveRecordsBetweenTwoDays')
            ->will($this->returnValue(array($leaveRecord1, $leaveRecord2)));

        $attendanceRecord1 = TestDataService::fetchObject('AttendanceRecord', 4);
        $attendanceRecord2 = TestDataService::fetchObject('AttendanceRecord', 5);
        $attendanceService = $this->getMockBuilder(
            'AttendanceService'
        )
            ->setMethods(
                [
                    'getAttendanceRecordsBetweenTwoDays',
                ]
            )
            ->getMock();
        $attendanceService
            ->method('getAttendanceRecordsBetweenTwoDays')
            ->will($this->returnValue(array($attendanceRecord1, $attendanceRecord2)));
        $params = [
            'fromDate' => "2020-12-18",
            'toDate' => "2020-12-24",
            'empNumber' => 1000,
            'pendingApproval' => 'true',
            'scheduled' => 'true',
            'taken' => 'true'
        ];
        $attendanceGraphAPI = $this->getMockBuilder('Orangehrm\Rest\Api\User\Attendance\AttendanceSummaryAPI')
            ->setMethods(
                [
                    'getParameters',
                    'getLoggedInEmployeeNumber',
                    'getEmployeeDetails',
                    'getAccessibleEmpNumbers'
                ]
            )
            ->setConstructorArgs([$this->request])
            ->getMock();
        $attendanceGraphAPI->setLeaveRequestService($leaveRequestService);
        $attendanceGraphAPI->setAttendanceService($attendanceService);
        $attendanceGraphAPI->expects($this->once())
            ->method('getParameters')
            ->will($this->returnValue($params));
        $attendanceGraphAPI->expects($this->once())
            ->method('getLoggedInEmployeeNumber')
            ->will($this->returnValue(1));
        $attendanceGraphAPI
            ->method('getAccessibleEmpNumbers')
            ->will($this->returnValue([]));
        $this->expectException(BadRequestException::class);
        $attendanceGraphAPI->getAttendanceSummary();
    }

    public function testGetAttendanceSummaryForInvalidDatePeriod()
    {
        $leaveRecord1 = TestDataService::fetchObject('Leave', 6);
        $leaveRecord2 = TestDataService::fetchObject('Leave', 7);
        $leaveType2 = TestDataService::fetchObject('LeaveType', 2);
        $leaveRecord1->setLeaveType($leaveType2);
        $leaveRecord2->setLeaveType($leaveType2);
        $leaveRequestService = $this->getMockBuilder(
            'LeaveRequestService'
        )
            ->setMethods(
                [
                    'getLeaveRecordsBetweenTwoDays',
                ]
            )
            ->getMock();
        $leaveRequestService
            ->method('getLeaveRecordsBetweenTwoDays')
            ->will($this->returnValue(array($leaveRecord1, $leaveRecord2)));

        $attendanceRecord1 = TestDataService::fetchObject('AttendanceRecord', 4);
        $attendanceRecord2 = TestDataService::fetchObject('AttendanceRecord', 5);
        $attendanceService = $this->getMockBuilder(
            'AttendanceService'
        )
            ->setMethods(
                [
                    'getAttendanceRecordsBetweenTwoDays',
                ]
            )
            ->getMock();
        $attendanceService
            ->method('getAttendanceRecordsBetweenTwoDays')
            ->will($this->returnValue(array($attendanceRecord1, $attendanceRecord2)));
        $params = [
            'fromDate' => "2020-12-31",
            'toDate' => "2020-12-24",
            'empNumber' => 1,
            'pendingApproval' => 'true',
            'scheduled' => 'true',
            'taken' => 'true'
        ];
        $attendanceGraphAPI = $this->getMockBuilder('Orangehrm\Rest\Api\User\Attendance\AttendanceSummaryAPI')
            ->setMethods(
                [
                    'getParameters',
                    'getLoggedInEmployeeNumber',
                    'getEmployeeDetails',
                    'getAccessibleEmpNumbers'
                ]
            )
            ->setConstructorArgs([$this->request])
            ->getMock();
        $attendanceGraphAPI->setLeaveRequestService($leaveRequestService);
        $attendanceGraphAPI->setAttendanceService($attendanceService);
        $attendanceGraphAPI->expects($this->once())
            ->method('getParameters')
            ->will($this->returnValue($params));
        $attendanceGraphAPI->expects($this->once())
            ->method('getLoggedInEmployeeNumber')
            ->will($this->returnValue(1));
        $attendanceGraphAPI
            ->method('getAccessibleEmpNumbers')
            ->will($this->returnValue([1]));
        $this->expectException(InvalidParamException::class);
        $attendanceGraphAPI->getAttendanceSummary();
    }
}
