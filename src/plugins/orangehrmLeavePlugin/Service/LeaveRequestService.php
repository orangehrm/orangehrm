<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Leave\Service;

use InvalidArgumentException;
use OrangeHRM\Core\Traits\EventDispatcherTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Entity\LeaveRequest;
use OrangeHRM\Entity\LeaveType;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Leave\Dao\LeaveRequestDao;
use OrangeHRM\Leave\Dto\LeaveRequest\DetailedLeave;
use OrangeHRM\Leave\Dto\LeaveRequest\DetailedLeaveRequest;
use OrangeHRM\Leave\Event\LeaveApprove;
use OrangeHRM\Leave\Event\LeaveCancel;
use OrangeHRM\Leave\Event\LeaveEvent;
use OrangeHRM\Leave\Event\LeaveReject;
use OrangeHRM\Leave\Traits\Service\LeaveEntitlementServiceTrait;

class LeaveRequestService
{
    use UserRoleManagerTrait;
    use LeaveEntitlementServiceTrait;
    use EventDispatcherTrait;

    public const WORKFLOW_LEAVE_TYPE_DELETED_STATUS_PREFIX = 'LEAVE TYPE DELETED';

    /**
     * @var LeaveRequestDao|null
     */
    private ?LeaveRequestDao $leaveRequestDao = null;

    /**
     * @var array|null
     * array(
     *     -1 => 'REJECTED',
     *     0 => 'CANCELLED',
     *     1 => 'PENDING APPROVAL',
     *     2 => 'SCHEDULED',
     *     3 => 'TAKEN',
     *     4 => 'WEEKEND',
     *     5 => 'HOLIDAY'
     * )
     */
    private ?array $leaveStatuses = null;

    /**
     * @return LeaveRequestDao
     */
    public function getLeaveRequestDao(): LeaveRequestDao
    {
        if (!($this->leaveRequestDao instanceof LeaveRequestDao)) {
            $this->leaveRequestDao = new LeaveRequestDao();
        }
        return $this->leaveRequestDao;
    }

    /**
     * @param Leave[] $leaveList
     * @param int $newState e.g. -1, 0, 2
     */
    protected function _changeLeaveStatus(iterable $leaveList, int $newState): void
    {
        foreach ($leaveList as $leave) {
            $currentState = $leave->getStatus();
            if ($currentState === Leave::LEAVE_STATUS_LEAVE_WEEKEND || $currentState === Leave::LEAVE_STATUS_LEAVE_HOLIDAY) {
                continue;
            }
            $entitlementChanges = null;

            $removeLinkedEntitlements = (($newState === Leave::LEAVE_STATUS_LEAVE_CANCELLED) ||
                ($newState === Leave::LEAVE_STATUS_LEAVE_REJECTED));

            $strategy = $this->getLeaveEntitlementService()->getLeaveEntitlementStrategy();
            if ($removeLinkedEntitlements) {
                $entitlementChanges = $strategy->handleLeaveCancel($leave);
            }

            $leave->setStatus($newState);

            $this->getLeaveRequestDao()->changeLeaveStatus($leave, $entitlementChanges, $removeLinkedEntitlements);
        }
    }

    /**
     * @param Leave[] $leaves
     * @param WorkflowStateMachine $workflow
     */
    private function _notifyLeaveStatusChange(array $leaves, WorkflowStateMachine $workflow)
    {
        $performer = $this->getUserRoleManager()->getUser();
        switch ($workflow->getAction()) {
            case 'APPROVE':
                $this->getEventDispatcher()->dispatch(
                    new LeaveApprove($leaves, $workflow, $performer),
                    LeaveEvent::APPROVE
                );
                break;
            case 'CANCEL':
                $this->getEventDispatcher()->dispatch(
                    new LeaveCancel($leaves, $workflow, $performer),
                    LeaveEvent::CANCEL
                );
                break;
            case 'REJECT':
                $this->getEventDispatcher()->dispatch(
                    new LeaveReject($leaves, $workflow, $performer),
                    LeaveEvent::REJECT
                );
                break;
        }
    }

    /**
     * @param Employee $employee
     * @param LeaveType $leaveType
     * @param string $leaveStatus e.g. 'PENDING APPROVAL', 'SCHEDULED', 'TAKEN'
     * @param int $loggedInEmpNumber
     * @return WorkflowStateMachine[]
     */
    public function getLeaveRequestAllowedWorkflows(
        Employee $employee,
        LeaveType $leaveType,
        string $leaveStatus,
        int $loggedInEmpNumber
    ): array {
        $includeRoles = $this->generateIncludeRolesForLeaveWorkflowByEmployee($employee, $loggedInEmpNumber);

        return $this->getUserRoleManager()->getAllowedActions(
            WorkflowStateMachine::FLOW_LEAVE,
            $this->generateLeaveStatusByLeaveType($leaveStatus, $leaveType),
            [],
            $includeRoles,
            [Employee::class => $employee->getEmpNumber()]
        );
    }

    /**
     * @param Employee $employee
     * @param LeaveType $leaveType
     * @param string $leaveStatus e.g. 'PENDING APPROVAL', 'SCHEDULED', 'TAKEN'
     * @param int $loggedInEmpNumber
     * @return bool
     */
    public function isLeaveRequestActionAllowed(
        Employee $employee,
        LeaveType $leaveType,
        string $leaveStatus,
        string $action,
        int $loggedInEmpNumber
    ): bool {
        $includeRoles = $this->generateIncludeRolesForLeaveWorkflowByEmployee($employee, $loggedInEmpNumber);

        return $this->getUserRoleManager()->isActionAllowed(
            WorkflowStateMachine::FLOW_LEAVE,
            $this->generateLeaveStatusByLeaveType($leaveStatus, $leaveType),
            $action,
            [],
            $includeRoles,
            [Employee::class => $employee->getEmpNumber()]
        );
    }

    /**
     * @param Employee $employee
     * @param int $loggedInEmpNumber
     * @return string[]
     */
    private function generateIncludeRolesForLeaveWorkflowByEmployee(Employee $employee, int $loggedInEmpNumber): array
    {
        $includeRoles = [];
        $empNumber = $employee->getEmpNumber();

        // If looking at own leave request, only consider ESS role
        if ($empNumber == $loggedInEmpNumber && ($this->getUserRoleManager()->essRightsToOwnWorkflow()
                || !$this->getUserRoleManager()->isEntityAccessible(Employee::class, $empNumber))) {
            $includeRoles = ['ESS'];
        }
        return $includeRoles;
    }

    /**
     * @param string $leaveStatus e.g. 'PENDING APPROVAL', 'SCHEDULED', 'TAKEN'
     * @param LeaveType $leaveType
     * @return string
     */
    private function generateLeaveStatusByLeaveType(string $leaveStatus, LeaveType $leaveType): string
    {
        if ($leaveType->isDeleted()) {
            $leaveStatus = self::WORKFLOW_LEAVE_TYPE_DELETED_STATUS_PREFIX . ' ' . $leaveStatus;
        }
        return $leaveStatus;
    }

    /**
     * @param LeaveRequest $leaveRequest
     * @param string $leaveStatus e.g. ['PENDING APPROVAL', 'SCHEDULED', 'TAKEN']
     * @param int $loggedInEmpNumber
     * @return array
     */
    public function getLeaveRequestActions(
        LeaveRequest $leaveRequest,
        string $leaveStatus,
        int $loggedInEmpNumber
    ): array {
        $workFlowItems = $this->getLeaveRequestAllowedWorkflows(
            $leaveRequest->getEmployee(),
            $leaveRequest->getLeaveType(),
            $leaveStatus,
            $loggedInEmpNumber
        );

        $actions = [];
        foreach ($workFlowItems as $item) {
            $name = $item->getAction();
            $actions[$item->getId()] = ucfirst(strtolower($name));
        }

        return $actions;
    }

    /**
     * Update leave request status (required prior access right validation)
     * @param DetailedLeaveRequest $leaveRequest
     * @param WorkflowStateMachine $workflow
     */
    public function changeLeaveRequestStatus(DetailedLeaveRequest $leaveRequest, WorkflowStateMachine $workflow): void
    {
        $changedLeaves = $leaveRequest->getLeaves();
        $this->_changeLeaveStatus($changedLeaves, $this->getLeaveStatusByName($workflow->getResultingState()));
        $this->_notifyLeaveStatusChange($changedLeaves, $workflow);
    }

    /**
     * @param Leave $leave
     * @param WorkflowStateMachine $workflow
     */
    public function changeLeaveStatus(Leave $leave, WorkflowStateMachine $workflow): void
    {
        $this->_changeLeaveStatus([$leave], $this->getLeaveStatusByName($workflow->getResultingState()));
        $this->_notifyLeaveStatusChange([$leave], $workflow);
    }

    /**
     * @param Leave[] $leaves
     * @param WorkflowStateMachine $workflow
     */
    public function changeLeavesStatus(array $leaves, WorkflowStateMachine $workflow): void
    {
        $this->_changeLeaveStatus($leaves, $this->getLeaveStatusByName($workflow->getResultingState()));
        $leaveRequests = $this->groupLeaves($leaves);
        foreach ($leaveRequests as $leaves) {
            $this->_notifyLeaveStatusChange($leaves, $workflow);
        }
    }

    /**
     * @param Leave[] $leaves
     * @return array<int, Leave[]>
     */
    private function groupLeaves(array $leaves): array
    {
        $leaveRequests = [];
        foreach ($leaves as $leave) {
            $leaveRequestId = $leave->getLeaveRequest()->getId();
            if (!isset($leaveRequests[$leaveRequestId])) {
                $leaveRequests[$leaveRequestId] = [];
            }
            $leaveRequests[$leaveRequestId][] = $leave;
        }
        return $leaveRequests;
    }

    /**
     * @param LeaveRequest[] $leaveRequests
     * @return DetailedLeaveRequest[]
     */
    public function getDetailedLeaveRequests(array $leaveRequests): array
    {
        $leaveRequestsMap = $this->getLeaveRequestsMap($leaveRequests);
        $leaveRequestsIds = array_keys($leaveRequestsMap);
        $leaves = $this->getLeaveRequestDao()->getLeavesByLeaveRequestIds($leaveRequestsIds);

        $detailedLeaveRequests = [];
        foreach ($leaves as $leave) {
            $leaveRequestId = $leave->getLeaveRequest()->getId();
            if (!isset($detailedLeaveRequests[$leaveRequestId])) {
                $detailedLeaveRequest = new DetailedLeaveRequest($leave->getLeaveRequest());
                $detailedLeaveRequests[$leaveRequestId] = $detailedLeaveRequest;
            }
            $detailedLeaveRequests[$leaveRequestId]->addLeave($leave);
        }
        $sortedDetailedLeaveRequests = [];
        foreach ($leaveRequestsIds as $leaveRequestId) {
            if (isset($detailedLeaveRequests[$leaveRequestId])) {
                $sortedDetailedLeaveRequests[] = $detailedLeaveRequests[$leaveRequestId];
            }
        }
        return $sortedDetailedLeaveRequests;
    }

    /**
     * @param LeaveRequest[] $leaveRequests
     * @return array<int, LeaveRequest>
     */
    private function getLeaveRequestsMap(array $leaveRequests): array
    {
        $leaveRequestsMap = [];
        foreach ($leaveRequests as $leaveRequest) {
            $leaveRequestsMap[$leaveRequest->getId()] = $leaveRequest;
        }
        return $leaveRequestsMap;
    }

    /**
     * @return array<int, string>
     */
    public function getAllLeaveStatusesAssoc(): ?array
    {
        if (is_null($this->leaveStatuses)) {
            foreach ($this->getLeaveRequestDao()->getAllLeaveStatuses() as $status) {
                $this->leaveStatuses[$status->getStatus()] = $status->getName();
            }
        }
        return $this->leaveStatuses;
    }

    /**
     * @param int $status e.g. -1, 1, 3
     * @return string e.g. 'REJECTED', 'PENDING APPROVAL', 'TAKEN'
     */
    public function getLeaveStatusNameByStatus(int $status): string
    {
        $leaveStatuses = $this->getAllLeaveStatusesAssoc();
        if (isset($leaveStatuses[$status])) {
            return $leaveStatuses[$status];
        }
        throw new InvalidArgumentException('Invalid status');
    }

    /**
     * @param string $name e.g. 'REJECTED', 'PENDING APPROVAL', 'TAKEN'
     * @return int e.g. -1, 1, 3
     */
    public function getLeaveStatusByName(string $name): int
    {
        $leaveStatuses = array_flip($this->getAllLeaveStatusesAssoc());
        if (isset($leaveStatuses[$name])) {
            return $leaveStatuses[$name];
        }
        throw new InvalidArgumentException('Invalid status name');
    }

    /**
     * @param string[] $names e.g. ['REJECTED', 'PENDING APPROVAL', 'TAKEN']
     * @return int[] e.g. [-1, 1, 3]
     */
    public function getLeaveStatusesByNames(array $names): array
    {
        $leaveStatuses = array_flip($this->getAllLeaveStatusesAssoc());
        return array_map(function (string $name) use ($leaveStatuses) {
            if (isset($leaveStatuses[$name])) {
                return $leaveStatuses[$name];
            }
            throw new InvalidArgumentException("Invalid status name $name");
        }, $names);
    }

    /**
     * @param Leave[] $leaves
     * @param Leave[] $allLeavesOfLeaveRequest
     * @return DetailedLeave[]
     */
    public function getDetailedLeaves(iterable $leaves, iterable $allLeavesOfLeaveRequest): array
    {
        $detailedLeaves = [];
        foreach ($leaves as $leave) {
            $detailedLeave = new DetailedLeave($leave);
            $detailedLeave->setLeaves($allLeavesOfLeaveRequest);
            $detailedLeaves[] = $detailedLeave;
        }
        return $detailedLeaves;
    }
}
