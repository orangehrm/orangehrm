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

namespace OrangeHRM\Tests\Leave\Service;

use DateTime;
use InvalidArgumentException;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Entity\LeaveRequest;
use OrangeHRM\Entity\LeaveStatus;
use OrangeHRM\Entity\LeaveType;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Leave\Dao\LeaveRequestDao;
use OrangeHRM\Leave\Dto\LeaveRequest\DetailedLeave;
use OrangeHRM\Leave\Dto\LeaveRequest\DetailedLeaveRequest;
use OrangeHRM\Leave\Service\LeaveRequestService;
use OrangeHRM\Tests\Util\Mock\MockUserRoleManager;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Leave
 * @group Service
 */
class LeaveRequestServiceTest extends TestCase
{
    public function testGetLeaveRequestDao(): void
    {
        $service = new LeaveRequestService();
        $this->assertTrue($service->getLeaveRequestDao() instanceof LeaveRequestDao);
    }

    public function testGetAllLeaveStatusesAssoc(): void
    {
        $status1 = new LeaveStatus();
        $status1->setId(1);
        $status1->setStatus(1);
        $status1->setName('PENDING APPROVAL');
        $status2 = new LeaveStatus();
        $status2->setId(2);
        $status2->setStatus(2);
        $status2->setName('SCHEDULED');
        $statuses = [$status1, $status2];

        $dao = $this->getMockBuilder(LeaveRequestDao::class)
            ->onlyMethods(['getAllLeaveStatuses'])
            ->getMock();
        $dao->expects($this->once())
            ->method('getAllLeaveStatuses')
            ->willReturn($statuses);

        $service = $this->getMockBuilder(LeaveRequestService::class)
            ->onlyMethods(['getLeaveRequestDao'])
            ->getMock();
        $service->expects($this->once())
            ->method('getLeaveRequestDao')
            ->willReturn($dao);
        $this->assertEquals([1 => 'PENDING APPROVAL', 2 => 'SCHEDULED'], $service->getAllLeaveStatusesAssoc());
        $service->getAllLeaveStatusesAssoc();
    }

    public function testGetLeaveStatusNameByStatus(): void
    {
        $service = $this->getMockBuilder(LeaveRequestService::class)
            ->onlyMethods(['getAllLeaveStatusesAssoc'])
            ->getMock();
        $service->expects($this->exactly(2))
            ->method('getAllLeaveStatusesAssoc')
            ->willReturn([1 => 'PENDING APPROVAL', 2 => 'SCHEDULED']);
        $this->assertEquals('PENDING APPROVAL', $service->getLeaveStatusNameByStatus(1));

        $this->expectException(InvalidArgumentException::class);
        $service->getLeaveStatusNameByStatus(50);
    }

    public function testGetLeaveStatusByName(): void
    {
        $service = $this->getMockBuilder(LeaveRequestService::class)
            ->onlyMethods(['getAllLeaveStatusesAssoc'])
            ->getMock();
        $service->expects($this->exactly(2))
            ->method('getAllLeaveStatusesAssoc')
            ->willReturn([1 => 'PENDING APPROVAL', 2 => 'SCHEDULED']);
        $this->assertEquals(2, $service->getLeaveStatusByName('SCHEDULED'));

        $this->expectException(InvalidArgumentException::class);
        $service->getLeaveStatusByName('INVALID STATUS NAME');
    }

    public function testGetLeaveStatusByNames(): void
    {
        $service = $this->getMockBuilder(LeaveRequestService::class)
            ->onlyMethods(['getAllLeaveStatusesAssoc'])
            ->getMock();
        $service->expects($this->exactly(2))
            ->method('getAllLeaveStatusesAssoc')
            ->willReturn([1 => 'PENDING APPROVAL', 2 => 'SCHEDULED']);
        $this->assertEquals([2], $service->getLeaveStatusesByNames(['SCHEDULED']));

        $this->expectException(InvalidArgumentException::class);
        $service->getLeaveStatusesByNames(['INVALID STATUS NAME']);
    }

    public function testGetDetailedLeaveRequests(): void
    {
        $leaveRequest1 = new LeaveRequest();
        $leaveRequest1->setId(1);
        $leaveRequest2 = new LeaveRequest();
        $leaveRequest2->setId(2);
        $leaveRequest3 = new LeaveRequest();
        $leaveRequest3->setId(3);

        $leave1 = new Leave();
        $leave1->setLeaveRequest($leaveRequest1);
        $leave1->setDate(new DateTime('2021-08-01'));
        $leave2 = new Leave();
        $leave2->setLeaveRequest($leaveRequest1);
        $leave2->setDate(new DateTime('2021-08-03'));
        $leave3 = new Leave();
        $leave3->setLeaveRequest($leaveRequest1);
        $leave3->setDate(new DateTime('2021-08-02'));
        $leave4 = new Leave();
        $leave4->setLeaveRequest($leaveRequest2);
        $leave4->setDate(new DateTime('2021-10-30'));
        $leave5 = new Leave();
        $leave5->setLeaveRequest($leaveRequest2);
        $leave5->setDate(new DateTime('2021-10-31'));
        $leave6 = new Leave();
        $leave6->setLeaveRequest($leaveRequest3);
        $leave6->setDate(new DateTime('2120-02-27'));
        $leave7 = new Leave();
        $leave7->setLeaveRequest($leaveRequest3);
        $leave7->setDate(new DateTime('2120-02-28'));
        $leave8 = new Leave();
        $leave8->setLeaveRequest($leaveRequest3);
        $leave8->setDate(new DateTime('2120-02-29'));

        $leaves = [$leave1, $leave3, $leave4, $leave5, $leave6, $leave8, $leave7, $leave2];
        $dao = $this->getMockBuilder(LeaveRequestDao::class)
            ->onlyMethods(['getLeavesByLeaveRequestIds'])
            ->getMock();
        $dao->expects($this->once())
            ->method('getLeavesByLeaveRequestIds')
            ->with([3, 1, 2])
            ->willReturn($leaves);

        $service = $this->getMockBuilder(LeaveRequestService::class)
            ->onlyMethods(['getLeaveRequestDao'])
            ->getMock();
        $service->expects($this->once())
            ->method('getLeaveRequestDao')
            ->willReturn($dao);

        $detailedLeaveRequests = $service->getDetailedLeaveRequests([$leaveRequest3, $leaveRequest1, $leaveRequest2]);
        $this->assertCount(3, $detailedLeaveRequests);
        $this->assertEquals(
            [3, 1, 2],
            array_map(
                fn (DetailedLeaveRequest $detailedLeaveRequest) => $detailedLeaveRequest->getLeaveRequest()->getId(),
                $detailedLeaveRequests
            )
        );
        $this->assertEquals(
            [
                ['2120-02-27', '2120-02-28', '2120-02-29'],
                ['2021-08-01', '2021-08-02', '2021-08-03'],
                ['2021-10-30', '2021-10-31']
            ],
            array_map(
                fn (DetailedLeaveRequest $detailedLeaveRequest) => array_map(
                    fn (Leave $leave) => $leave->getDate()->format('Y-m-d'),
                    $detailedLeaveRequest->getLeaves()
                ),
                $detailedLeaveRequests
            )
        );
    }

    public function testGetLeaveRequestAllowedWorkflows(): void
    {
        $userRoleManager = $this->getMockBuilder(MockUserRoleManager::class)
            ->onlyMethods(['isEntityAccessible', 'getAllowedActions', /*'essRightsToOwnWorkflow'*/])
            ->getMock();
        $userRoleManager->expects($this->never())
            ->method('isEntityAccessible');
        $userRoleManager->expects($this->once())
            ->method('getAllowedActions')
            ->with(WorkflowStateMachine::FLOW_LEAVE, 'PENDING APPROVAL', [], ['ESS'], [Employee::class => 1])
            ->willReturn($this->getMockWorkflowActions());

        $service = $this->getMockBuilder(LeaveRequestService::class)
            ->onlyMethods(['getUserRoleManager'])
            ->getMock();
        $service->expects($this->exactly(2))
            ->method('getUserRoleManager')
            ->willReturn($userRoleManager);

        $employee = new Employee();
        $employee->setEmpNumber(1);
        $leaveType = new LeaveType();
        $leaveType->setId(1);
        $allowedWorkflows = $service->getLeaveRequestAllowedWorkflows($employee, $leaveType, 'PENDING APPROVAL', 1);
        $this->assertCount(3, $allowedWorkflows);
    }

    public function testGetLeaveRequestAllowedWorkflowsWithDeletedLeaveType(): void
    {
        $userRoleManager = $this->getMockBuilder(MockUserRoleManager::class)
            ->onlyMethods(['isEntityAccessible', 'getAllowedActions', /*'essRightsToOwnWorkflow'*/])
            ->getMock();
        $userRoleManager->expects($this->never())
            ->method('isEntityAccessible');
        $userRoleManager->expects($this->once())
            ->method('getAllowedActions')
            ->with(
                WorkflowStateMachine::FLOW_LEAVE,
                'LEAVE TYPE DELETED PENDING APPROVAL',
                [],
                [],
                [Employee::class => 1]
            )
            ->willReturn(
                $this->getMockWorkflowActions(
                    LeaveRequestService::WORKFLOW_LEAVE_TYPE_DELETED_STATUS_PREFIX . ' PENDING APPROVAL'
                )
            );

        $service = $this->getMockBuilder(LeaveRequestService::class)
            ->onlyMethods(['getUserRoleManager'])
            ->getMock();
        $service->expects($this->once())
            ->method('getUserRoleManager')
            ->willReturn($userRoleManager);

        $employee = new Employee();
        $employee->setEmpNumber(1);
        $leaveType = new LeaveType();
        $leaveType->setId(1);
        $leaveType->setDeleted(true);
        $allowedWorkflows = $service->getLeaveRequestAllowedWorkflows($employee, $leaveType, 'PENDING APPROVAL', 2);
        $this->assertCount(3, $allowedWorkflows);
    }

    /**
     * @param string $state
     * @return WorkflowStateMachine[]
     */
    private function getMockWorkflowActions(string $state = 'PENDING APPROVAL'): array
    {
        $action1 = new WorkflowStateMachine();
        $action1->setId(1);
        $action1->setWorkflow(WorkflowStateMachine::FLOW_LEAVE);
        $action1->setState($state);
        $action1->setRole('ADMIN');
        $action1->setAction('APPROVE');
        $action1->setResultingState('SCHEDULED');

        $action2 = new WorkflowStateMachine();
        $action2->setId(2);
        $action2->setWorkflow(WorkflowStateMachine::FLOW_LEAVE);
        $action2->setState($state);
        $action2->setRole('ADMIN');
        $action2->setAction('CANCEL');
        $action2->setResultingState('CANCELLED');

        $action3 = new WorkflowStateMachine();
        $action3->setId(3);
        $action3->setWorkflow(WorkflowStateMachine::FLOW_LEAVE);
        $action3->setState($state);
        $action3->setRole('ADMIN');
        $action3->setAction('REJECT');
        $action3->setResultingState('REJECTED');

        return [$action1, $action3, $action2];
    }

    public function testGetDetailedLeaves()
    {
        $leaveRequest1 = new LeaveRequest();
        $leaveRequest1->setId(1);
        $leaveRequest2 = new LeaveRequest();
        $leaveRequest2->setId(2);
        $leaveRequest3 = new LeaveRequest();
        $leaveRequest3->setId(3);

        $leave1 = new Leave();
        $leave1->setLeaveRequest($leaveRequest1);
        $leave1->setDate(new DateTime('2021-08-01'));
        $leave2 = new Leave();
        $leave2->setLeaveRequest($leaveRequest1);
        $leave2->setDate(new DateTime('2021-08-03'));
        $leave3 = new Leave();
        $leave3->setLeaveRequest($leaveRequest1);
        $leave3->setDate(new DateTime('2021-08-02'));
        $leave4 = new Leave();
        $leave4->setLeaveRequest($leaveRequest2);
        $leave4->setDate(new DateTime('2021-10-30'));
        $leave5 = new Leave();
        $leave5->setLeaveRequest($leaveRequest2);
        $leave5->setDate(new DateTime('2021-10-31'));
        $leave6 = new Leave();
        $leave6->setLeaveRequest($leaveRequest3);
        $leave6->setDate(new DateTime('2120-02-27'));
        $leave7 = new Leave();
        $leave7->setLeaveRequest($leaveRequest3);
        $leave7->setDate(new DateTime('2120-02-28'));
        $leave8 = new Leave();
        $leave8->setLeaveRequest($leaveRequest3);
        $leave8->setDate(new DateTime('2120-02-29'));
        $leaves = [$leave1, $leave3, $leave4, $leave5, $leave6, $leave8, $leave7, $leave2];
        $service = new LeaveRequestService();

        $detailedLeaves = [];
        foreach ($leaves as $leave) {
            $detailedLeave = new DetailedLeave($leave);
            $detailedLeave->setLeaves($leaves);
            $detailedLeaves[] = $detailedLeave;
        }
        $this->assertEquals($detailedLeaves, $service->getDetailedLeaves($leaves, $leaves));
    }
}
