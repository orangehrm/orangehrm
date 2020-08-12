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

use Orangehrm\Rest\Api\User\Service\APILeaveApplicationService;

/**
 * @group API
 */
class APILeaveApplicationServiceTest extends PHPUnit\Framework\TestCase
{
    public function testApplyLeaveEmptyData()
    {
        $apiLeaveApplicationService = new APILeaveApplicationService();

        $this->expectException(LeaveAllocationServiceException::class);
        $this->expectExceptionMessage('Invalid Employee');

        $leaveAssignmentData = new LeaveParameterObject([]);
        $apiLeaveApplicationService->applyLeave($leaveAssignmentData);
    }

    public function testApplyLeaveOverlappingLeave()
    {
        $apiLeaveApplicationService = $this->getMockBuilder(
            'Orangehrm\Rest\Api\User\Service\APILeaveApplicationService'
        )
            ->setMethods(['hasOverlapLeave'])
            ->getMock();
        $apiLeaveApplicationService->expects($this->once())
            ->method('hasOverlapLeave')
            ->will($this->returnValue(true));

        $this->expectException(LeaveAllocationServiceException::class);
        $this->expectExceptionMessage('Overlapping Leave Request Found.');

        $leaveAssignmentData = new LeaveParameterObject(['txtEmpID' => '1']);
        $apiLeaveApplicationService->applyLeave($leaveAssignmentData);
    }

    public function testApplyLeaveWorkShiftExceeded()
    {
        $apiLeaveApplicationService = $this->getMockBuilder(
            'Orangehrm\Rest\Api\User\Service\APILeaveApplicationService'
        )
            ->setMethods(['hasOverlapLeave', 'applyMoreThanAllowedForADay'])
            ->getMock();
        $apiLeaveApplicationService->expects($this->once())
            ->method('hasOverlapLeave')
            ->will($this->returnValue(false));
        $apiLeaveApplicationService->expects($this->once())
            ->method('applyMoreThanAllowedForADay')
            ->will($this->returnValue(true));

        $this->expectException(LeaveAllocationServiceException::class);
        $this->expectExceptionMessage('Work Shift Length Exceeded.');

        $leaveAssignmentData = new LeaveParameterObject(['txtEmpID' => '1']);
        $apiLeaveApplicationService->applyLeave($leaveAssignmentData);
    }

    public function testApplyLeave()
    {
        $apiLeaveApplicationService = $this->getMockBuilder(
            'Orangehrm\Rest\Api\User\Service\APILeaveApplicationService'
        )
            ->setMethods(['hasOverlapLeave', 'applyMoreThanAllowedForADay', 'saveLeaveRequest'])
            ->getMock();
        $apiLeaveApplicationService->expects($this->once())
            ->method('hasOverlapLeave')
            ->will($this->returnValue(false));
        $apiLeaveApplicationService->expects($this->once())
            ->method('applyMoreThanAllowedForADay')
            ->will($this->returnValue(false));
        $apiLeaveApplicationService->expects($this->once())
            ->method('saveLeaveRequest')
            ->will($this->returnValue(new LeaveRequest()));

        $leaveAssignmentData = new LeaveParameterObject(['txtEmpID' => '1']);
        $result = $apiLeaveApplicationService->applyLeave($leaveAssignmentData);
        $this->assertTrue($result instanceof LeaveRequest);
    }
}
