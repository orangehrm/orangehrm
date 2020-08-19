<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http=>//www.orangehrm.com
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

use Orangehrm\Rest\Api\Leave\Entity\LeaveRequest;
use Orangehrm\Rest\Api\User\MyLeaveRequestAPI;
use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Http\Response;

/**
 * @group API
 */
class ApiMyLeaveRequestAPITest extends PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
    }

    public function testGetMyLeaveRequests()
    {
        $sfEvent = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $leaveType = new \LeaveType();
        $leaveType->setId(10);
        $leaveType->setName('TestLeaveType');

        $leaveRequest = new \LeaveRequest();
        $leaveRequest->setLeaveTypeId(10);
        $leaveRequest->setLeaveType($leaveType);
        $leaveRequest->setEmpNumber(32);
        $leaveRequest->setDateApplied('2020-06-20');
        $leaveRequest->setId(5);
        $leaveRequestEntity = new LeaveRequest($leaveRequest->getId(), $leaveType->getName());
        $leaveRequestEntity->setAppliedDate($leaveRequest->getDateApplied());
        $leaveRequestEntity->setEmpId($leaveRequest->getEmpNumber());

        $leaveRequestsCollection = new Doctrine_Collection('LeaveRequest');
        $leaveRequestsCollection[] = $leaveRequest;

        $myLeaveRequestApi = $this->getMockBuilder('Orangehrm\Rest\Api\User\MyLeaveRequestAPI')
            ->setMethods(array('createLeaveRequestEntity'))
            ->setConstructorArgs(array($request))
            ->getMock();
        $myLeaveRequestApi->expects($this->once())
            ->method('createLeaveRequestEntity')
            ->will($this->returnValue($leaveRequestEntity));

        $leaveRequestService = $this->getMockBuilder('LeaveRequestService')->getMock();
        $leaveRequestService->expects($this->once())
            ->method('searchLeaveRequests')
            ->withAnyParameters()
            ->will($this->returnValue($leaveRequestsCollection));

        $myLeaveRequestApi->setLeaveRequestService($leaveRequestService);
        $leaveRequests = $myLeaveRequestApi->getMyLeaveRequests(1, []);

        $this->assertEquals($leaveRequest->getId(), $leaveRequests[0]['id']);
        $this->assertEquals($leaveRequest->getDateApplied(), $leaveRequests[0]['appliedDate']);
        $this->assertEquals($leaveType->getName(), $leaveRequests[0]['leaveType']['type']);
    }

    public function testGetMyLeaveDetails()
    {
        $sfEvent = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $leaveRequest = [
            [
                "id" => "2",
                "fromDate" => "2020-07-22",
                "toDate" => "2020-07-22",
                "appliedDate" => "2020-07-22",
                "leaveType" => "Casual",
                "numberOfDays" => "0.50",
                "comments" => [],
                "days" => [
                    [
                        "date" => "2020-07-22",
                        "status" => "SCHEDULED",
                        "duration" => "4.00",
                        "durationString" => "(09:00 - 13:00)",
                        "comments" => []
                    ]
                ]
            ]
        ];

        $myLeaveRequestApi = $this->getMockBuilder('Orangehrm\Rest\Api\User\MyLeaveRequestAPI')
            ->setMethods(['getMyLeaveEntitlement', 'getMyLeaveRequests', 'getFilters'])
            ->setConstructorArgs(array($request))
            ->getMock();
        $myLeaveRequestApi->expects($this->once())
            ->method('getMyLeaveRequests')
            ->will($this->returnValue($leaveRequest));
        $myLeaveRequestApi->expects($this->once())
            ->method('getFilters')
            ->will($this->returnValue([]));

        $leaveDetailsResponse = $myLeaveRequestApi->getMyLeaveDetails(1);

        $success = new Response($leaveRequest, array());

        $this->assertEquals($success, $leaveDetailsResponse);
    }

    /**
     * @dataProvider requestParamProvider
     * @param $id
     * @param $returnParamCallback
     * @param $fromDate
     * @param $toDate
     * @throws DaoException
     * @throws Doctrine_Connection_Exception
     * @throws Doctrine_Record_Exception
     * @throws \Orangehrm\Rest\Api\Exception\InvalidParamException
     * @throws \Orangehrm\Rest\Api\Exception\RecordNotFoundException
     */
    public function testGetFilters($id, $returnParamCallback, $fromDate, $toDate)
    {
        $requestParams = $this->getMockBuilder('\Orangehrm\Rest\Http\RequestParams')
            ->disableOriginalConstructor()
            ->setMethods(['getUrlParam'])
            ->getMock();
        $requestParams->expects($this->exactly(5))
            ->method('getUrlParam')
            ->will($this->returnCallback($returnParamCallback));

        $sfEvent = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $myLeaveRequestApi = new MyLeaveRequestAPI($request);
        $myLeaveRequestApi->setRequestParams($requestParams);

        $employeeService = $this->getMockBuilder('EmployeeService')->getMock();
        $employeeService->expects($this->once())
            ->method('getEmployee')
            ->withAnyParameters()
            ->will($this->returnValue(new \Employee()));
        $myLeaveRequestApi->setEmployeeService($employeeService);

        if ($id == 1) {
            $leavePeriodService = $this->getMockBuilder('LeavePeriodService')->getMock();
            $leavePeriodService->expects($this->once())
                ->method('getCurrentLeavePeriodByDate')
                ->withAnyParameters()
                ->will($this->returnValue(['2021-01-01', '2021-12-31']));

            $myLeaveRequestApi->setLeavePeriodService($leavePeriodService);
        }

        $filters = $myLeaveRequestApi->getFilters(1);

        $this->assertEquals($fromDate, $filters['fromDate']);
        $this->assertEquals($toDate, $filters['toDate']);
    }

    /**
     * @return \Generator
     */
    public function requestParamProvider()
    {
        yield [1, function ($param) {
            return null;
        }, '2021-01-01', '2021-12-31'];
        yield [2, function ($param) {
            if ($param == 'fromDate') {
                return '2020-01-01';
            } else if ($param == 'toDate') {
                return '2020-12-31';
            }
            return null;
        }, '2020-01-01', '2020-12-31'];
    }
}
