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

namespace OrangeHRM\Leave\Service;

use InvalidArgumentException;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Entity\LeaveRequest;
use OrangeHRM\Entity\LeaveType;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Leave\Dao\LeaveRequestDao;
use OrangeHRM\Leave\Dto\LeaveRequest\DetailedLeave;
use OrangeHRM\Leave\Dto\LeaveRequest\DetailedLeaveRequest;
use OrangeHRM\Leave\Traits\Service\LeaveEntitlementServiceTrait;

class LeaveRequestService
{
    use UserRoleManagerTrait;
    use LeaveEntitlementServiceTrait;

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

    private $dispatcher;

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

    public function getDispatcher() {
        // TODO
        if(is_null($this->dispatcher)) {
            $this->dispatcher = sfContext::getInstance()->getEventDispatcher();
        }
        return $this->dispatcher;
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

    private function _notifyLeaveStatusChange($eventType, $workflow, $leaveList, $performerType, $performerId, $requestType) {
        // TODO
        $request = $leaveList[0]->getLeaveRequest();

        $eventData = array('days' => $leaveList,
                           'performerType' => $performerType,
                           'empNumber' => $performerId,
                           'requestType' => $requestType,
                           'request' => $request,
                           'workFlow' => $workflow);
        $this->getDispatcher()->notify(new sfEvent($this, $eventType, $eventData));
    }

    private function _notifyLeaveMultiStatusChange($allDays, $leaveList, $workFlows, $performerType, $performerId, $requestType) {
        // TODO
        $request = $allDays[0]->getLeaveRequest();

        $eventData = array('days' => $allDays,
                           'changes' => $leaveList,
                           'performerType' => $performerType,
                           'empNumber' => $performerId,
                           'requestType' => $requestType,
                           'request' => $request,
                           'workFlow' => $workFlows);
        $this->getDispatcher()->notify(new sfEvent($this, LeaveEvents::LEAVE_CHANGE, $eventData));
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
        // TODO
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
     * @param string $nextState e.g. 'SCHEDULED', 'CANCELLED', 'REJECTED'
     */
    public function changeLeaveRequestStatus(DetailedLeaveRequest $leaveRequest, string $nextState): void
    {
        $changedLeaves = $leaveRequest->getLeaves();
        $this->_changeLeaveStatus($changedLeaves, $this->getLeaveStatusByName($nextState));
        // TODO
//        $this->_notifyLeaveStatusChange(LeaveEvents::LEAVE_CHANGE, $workFlow, $changedLeave,
//            $actionPerformerUserType, $actionPerformerEmpNumber, 'request');
    }

    /**
     * @param Leave $leave
     * @param string $nextState
     */
    public function changeLeaveStatus(Leave $leave, string $nextState): void
    {
        $this->_changeLeaveStatus([$leave], $this->getLeaveStatusByName($nextState));
        // TODO
//        $this->_notifyLeaveStatusChange(LeaveEvents::LEAVE_CHANGE, $workFlow, $changedLeave,
//            $actionPerformerUserType, $actionPerformerEmpNumber, 'request');
    }

    /**
     * @param Leave[] $leaves
     * @param string $nextState
     */
    public function changeLeavesStatus(array $leaves, string $nextState): void
    {
        $this->_changeLeaveStatus($leaves, $this->getLeaveStatusByName($nextState));
        // TODO
//        $this->_notifyLeaveStatusChange(LeaveEvents::LEAVE_CHANGE, $workFlow, $changedLeave,
//            $actionPerformerUserType, $actionPerformerEmpNumber, 'request');
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
    public function getDetailedLeaves(array $leaves, array $allLeavesOfLeaveRequest): array
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
