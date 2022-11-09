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

use Orangehrm\Rest\Api\Leave\Entity\LeaveRequest;
use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Http\Response;

/**
 * @group API
 */
class ApiLeaveRequestAPITest extends PHPUnit\Framework\TestCase
{
    public function testGetLeaveRequestById()
    {
        $requestParams = $this->getMockBuilder('\Orangehrm\Rest\Http\RequestParams')
            ->disableOriginalConstructor()
            ->setMethods(['getUrlParam'])
            ->getMock();
        $requestParams->expects($this->once())
            ->method('getUrlParam')
            ->will($this->returnValue(1));

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
        $leaveRequestEntity->setEmployeeName("Test Name");
        $leaveRequestEntity->setComments([]);
        $leaveRequestEntity->setDays([]);
        $leaveRequestEntity->setFromDate('2020-07-22');
        $leaveRequestEntity->setToDate('2020-07-22');
        $leaveRequestEntity->setNumberOfDays('0.50');
        $leaveRequestEntity->setLeaveBalance('10.00');
        $leaveRequestEntity->setLeaveBreakdown('Scheduled(0.50)');

        $sfEvent = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $leaveRequestApi = $this->getMockBuilder('Orangehrm\Rest\Api\Leave\LeaveRequestAPI')
            ->setMethods(['createLeaveRequestEntity', 'getAccessibleEmployeeIds','getUserAttribute'])
            ->setConstructorArgs(array($request))
            ->getMock();
        $leaveRequestApi->expects($this->once())
            ->method('createLeaveRequestEntity')
            ->will($this->returnValue($leaveRequestEntity));
        $leaveRequestApi->expects($this->once())
            ->method('getAccessibleEmployeeIds')
            ->will($this->returnValue([1, 2, 3, 32]));
        $leaveRequestApi->expects($this->once())
            ->method('getUserAttribute')
            ->will($this->returnValue('1'));

        $leaveRequestService = $this->getMockBuilder('LeaveRequestService')->getMock();
        $leaveRequestService->expects($this->once())
            ->method('fetchLeaveRequest')
            ->withAnyParameters()
            ->will($this->returnValue($leaveRequest));
        $leaveRequestService->expects($this->once())
            ->method('getLeaveRequestActions')
            ->withAnyParameters()
            ->will($this->returnValue(['Cancel']));

        $leaveRequestResponseArray = [
            "leaveRequestId" => 5,
            "fromDate" => "2020-07-22",
            "toDate" => "2020-07-22",
            "appliedDate" => "2020-06-20",
            "leaveType" => [
                'type' => "TestLeaveType",
                'id' => 10,
                'deleted' => '0',
                'situational' => null

            ],
            "numberOfDays" => "0.50",
            'leaveBalance' => '10.00',
            'leaveBreakdown' => 'Scheduled(0.50)',
            "comments" => [],
            "days" => [],
            'employeeId' => 32,
            'employeeName' => 'Test Name',
            'allowedActions' => ['Cancel']
        ];

        $leaveRequestApi->setRequestParams($requestParams);
        $leaveRequestApi->setLeaveRequestService($leaveRequestService);
        $leaveRequestResponse = $leaveRequestApi->getLeaveRequestById();

        $success = new Response($leaveRequestResponseArray, array());

        $this->assertEquals($success, $leaveRequestResponse);
    }
}
