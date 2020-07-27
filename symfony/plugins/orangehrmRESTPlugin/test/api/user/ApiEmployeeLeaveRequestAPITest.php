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
class ApiEmployeeLeaveRequestAPITest extends PHPUnit\Framework\TestCase
{
    public function testChangeLeaveRequestStatus()
    {
        $sfEvent = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $employeeLeaveRequestApi = $this->getMockBuilder('Orangehrm\Rest\Api\User\EmployeeLeaveRequestAPI')
            ->setMethods(['getUserAttribute', 'getAccessibleEmployeeIds', 'filterChangeLeaveRequestStatusParameters'])
            ->setConstructorArgs([$request])
            ->getMock();
        $employeeLeaveRequestApi->expects($this->exactly(2))
            ->method('getUserAttribute')
            ->willReturnCallback(function ($name) {
                if ($name == 'auth.isSupervisor') {
                    return 'Supervisor';
                } else {
                    return 1;
                }
            });
        $employeeLeaveRequestApi->expects($this->once())
            ->method('filterChangeLeaveRequestStatusParameters')
            ->will($this->returnValue([
                'id' => 5,
                'status' => 'Cancel'
            ]));
        $employeeLeaveRequestApi->expects($this->once())
            ->method('getAccessibleEmployeeIds')
            ->will($this->returnValue([1, 2, 3, 32]));

        $leaveRequestService = $this->getMockBuilder('LeaveRequestService')->getMock();
        $leaveRequestService->expects($this->once())
            ->method('fetchLeaveRequest')
            ->withAnyParameters()
            ->will($this->returnValue($this->getLeaveRequest()));
        $leaveRequestService->expects($this->once())
            ->method('getLeaveRequestActions')
            ->withAnyParameters()
            ->will($this->returnValue(['Cancel']));
        $leaveRequestService->expects($this->once())
            ->method('changeLeaveRequestStatus');

        $leaveRequestResponseArray = [
            "success" => 'Successfully Saved'];

        $employeeLeaveRequestApi->setLeaveRequestService($leaveRequestService);
        $employeeLeaveRequestResponse = $employeeLeaveRequestApi->changeLeaveRequestStatus();

        $success = new Response($leaveRequestResponseArray, []);

        $this->assertEquals($success, $employeeLeaveRequestResponse);
    }

    public function testSaveLeaveRequestComment()
    {
        $sfEvent = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $employeeLeaveRequestApi = $this->getMockBuilder('Orangehrm\Rest\Api\User\EmployeeLeaveRequestAPI')
            ->setMethods(['getUserAttribute', 'filterCommentActionParameters', 'getCommentPermissions'])
            ->setConstructorArgs([$request])
            ->getMock();
        $employeeLeaveRequestApi->expects($this->exactly(3))
            ->method('getUserAttribute')
            ->willReturnCallback(function ($name) {
                if ($name == 'auth.userId') {
                    return 1;
                } else if ($name = 'auth.empNumber') {
                    return 32;
                } else {
                    return 'auth.firstName';
                }
            });
        $employeeLeaveRequestApi->expects($this->once())
            ->method('filterCommentActionParameters')
            ->will($this->returnValue([
                'id' => 5,
                'comment' => 'Test Comment'
            ]));
        $employeeLeaveRequestApi->expects($this->once())
            ->method('getCommentPermissions')
            ->will($this->returnValue(new \ResourcePermission(false, true, false, false)));

        $leaveRequestService = $this->getMockBuilder('LeaveRequestService')->getMock();
        $leaveRequestService->expects($this->once())
            ->method('fetchLeaveRequest')
            ->withAnyParameters()
            ->will($this->returnValue($this->getLeaveRequest()));
        $leaveRequestService->expects($this->once())
            ->method('saveLeaveRequestComment')
            ->withAnyParameters()
            ->will($this->returnValue(new \LeaveRequestComment()));

        $employeeService = $this->getMockBuilder('EmployeeService')->getMock();
        $employeeService->expects($this->once())
            ->method('getEmployee')
            ->withAnyParameters()
            ->will($this->returnValue(new Employee()));

        $leaveRequestResponseArray = [
            "success" => 'Successfully Saved'];

        $employeeLeaveRequestApi->setLeaveRequestService($leaveRequestService);
        $employeeLeaveRequestApi->setEmployeeService($employeeService);
        $employeeLeaveRequestResponse = $employeeLeaveRequestApi->saveLeaveRequestComment();

        $success = new Response($leaveRequestResponseArray, []);

        $this->assertEquals($success, $employeeLeaveRequestResponse);
    }

    private function getLeaveRequest()
    {
        $leaveType = new \LeaveType();
        $leaveType->setId(10);
        $leaveType->setName('TestLeaveType');

        $leaveRequest = new \LeaveRequest();
        $leaveRequest->setLeaveTypeId(10);
        $leaveRequest->setLeaveType($leaveType);
        $leaveRequest->setEmpNumber(32);
        $leaveRequest->setDateApplied('2020-06-20');
        $leaveRequest->setId(5);
        return $leaveRequest;
    }
}
