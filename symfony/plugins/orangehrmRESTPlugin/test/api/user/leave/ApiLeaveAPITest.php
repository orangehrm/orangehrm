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
class ApiLeaveAPITest extends PHPUnit\Framework\TestCase
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
        TestDataService::populate(sfConfig::get('sf_plugins_dir') . '/orangehrmLeavePlugin/test/fixtures/AttendanceLeaveDao.yml');
    }

    public function testGetLeaveRecords()
    {
        $leaveRecord = TestDataService::fetchObject('Leave', 10);
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
            ->will($this->returnValue(array($leaveRecord)));
        $params = [
            'fromDate' => "2020-12-23",
            'toDate' => "2020-12-29",
            'empNumber' => 1,
            'pendingApproval' => 'true',
            'scheduled' => 'true',
            'taken' => 'true'
        ];
        $leaveAPI = $this->getMockBuilder('Orangehrm\Rest\Api\User\Leave\LeaveAPI')
            ->setMethods(
                [
                    'getParameters',
                    'getLoggedInEmployeeNumber',
                    'getEmployeeDetails',
                    'getWorkHours',
                    'getAccessibleEmpNumbers',
                ]
            )
            ->setConstructorArgs([$this->request])
            ->getMock();
        $leaveAPI->setLeaveRequestService($leaveRequestService);
        $leaveAPI->expects($this->once())
            ->method('getParameters')
            ->will($this->returnValue($params));
        $leaveAPI->expects($this->once())
            ->method('getLoggedInEmployeeNumber')
            ->will($this->returnValue(1));
        $leaveAPI
            ->method('getWorkHours')
            ->will($this->returnValue([$leaveRecord->toArray()]));
        $leaveAPI
            ->method('getAccessibleEmpNumbers')
            ->will($this->returnValue([1]));
        $actual = $leaveAPI->getLeaveRecords();
        $expected = new Response(array(
            array(
                'id' => "10",
                'date' => "2010-09-21",
                'lengthHours' => "8.00",
                'lengthDays' => "1.0000",
                'leaveType' => array("id" => "2", "type" => "Medical"),
                'startTime' => null,
                'endTime' => null,
                'status' => "TAKEN"
            ))
        );
        $this->assertEquals($expected, $actual);
    }

    public function testGetLeaveRecordsForNotValidEmployee()
    {
        $leaveRecord = TestDataService::fetchObject('Leave', 10);
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
            ->will($this->returnValue(array($leaveRecord)));
        $params = [
            'fromDate' => "2020-12-29",
            'toDate' => "2020-12-29",
            'empNumber' => 10000,
            'pendingApproval' => 'true',
            'scheduled' => 'true',
            'taken' => 'true'
        ];
        $leaveAPI = $this->getMockBuilder('Orangehrm\Rest\Api\User\Leave\LeaveAPI')
            ->setMethods(
                [
                    'getParameters',
                    'getLoggedInEmployeeNumber',
                    'getEmployeeDetails',
                    'getWorkHours',
                    'getAccessibleEmpNumbers',
                ]
            )
            ->setConstructorArgs([$this->request])
            ->getMock();
        $leaveAPI->setLeaveRequestService($leaveRequestService);
        $leaveAPI->expects($this->once())
            ->method('getParameters')
            ->will($this->returnValue($params));
        $leaveAPI->expects($this->once())
            ->method('getLoggedInEmployeeNumber')
            ->will($this->returnValue(1));
        $leaveAPI
            ->method('getWorkHours')
            ->will($this->returnValue([$leaveRecord->toArray()]));
        $leaveAPI
            ->method('getAccessibleEmpNumbers')
            ->will($this->returnValue([]));
        $this->expectException(BadRequestException::class);
        $leaveAPI->getLeaveRecords();
    }
    public function testGetLeaveRecordsForNotValidDatePeriod()
    {
        $leaveRecord = TestDataService::fetchObject('Leave', 10);
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
            ->will($this->returnValue(array($leaveRecord)));
        $params = [
            'fromDate' => "2020-12-31",
            'toDate' => "2020-12-29",
            'empNumber' => 10000,
            'pendingApproval' => 'true',
            'scheduled' => 'true',
            'taken' => 'true'
        ];
        $leaveAPI = $this->getMockBuilder('Orangehrm\Rest\Api\User\Leave\LeaveAPI')
            ->setMethods(
                [
                    'getParameters',
                    'getLoggedInEmployeeNumber',
                    'getEmployeeDetails',
                    'getWorkHours',
                    'getAccessibleEmpNumbers',
                ]
            )
            ->setConstructorArgs([$this->request])
            ->getMock();
        $leaveAPI->setLeaveRequestService($leaveRequestService);
        $leaveAPI->expects($this->once())
            ->method('getParameters')
            ->will($this->returnValue($params));
        $leaveAPI->expects($this->once())
            ->method('getLoggedInEmployeeNumber')
            ->will($this->returnValue(1));
        $leaveAPI
            ->method('getWorkHours')
            ->will($this->returnValue([$leaveRecord->toArray()]));
        $leaveAPI
            ->method('getAccessibleEmpNumbers')
            ->will($this->returnValue([]));
        $this->expectException(InvalidParamException::class);
        $leaveAPI->getLeaveRecords();
    }
}
