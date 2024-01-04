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

namespace OrangeHRM\Leave\Dto\LeaveRequest;

use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Leave\Dto\LeaveDuration;
use OrangeHRM\Leave\Dto\LeavePeriod;
use OrangeHRM\Leave\Traits\Service\LeaveEntitlementServiceTrait;
use OrangeHRM\Leave\Traits\Service\LeaveRequestServiceTrait;

class DetailedLeave
{
    use LeaveEntitlementServiceTrait;
    use LeaveRequestServiceTrait;
    use AuthUserTrait;

    /**
     * @var Leave
     */
    private Leave $leave;

    /**
     * @var Leave|null
     */
    private ?Leave $firstLeaveOfLeavePeriod = null;

    /**
     * @var Leave[]
     */
    private iterable $leaves;

    /**
     * @var LeaveRequestDatesDetail|null
     */
    private ?LeaveRequestDatesDetail $dateDetail = null;

    /**
     * @var LeaveStatusWithLengthDays|null
     */
    private ?LeaveStatusWithLengthDays $status = null;

    /**
     * @var LeavePeriod|null
     */
    private ?LeavePeriod $leavePeriod = null;

    /**
     * @var LeaveBalanceWithLeavePeriod|null
     */
    private ?LeaveBalanceWithLeavePeriod $leaveBalance = null;

    /**
     * @var string[]|null
     */
    private ?array $allowedActions = null;

    /**
     * @var WorkflowStateMachine[]|null
     */
    private ?array $allowedWorkflows = null;

    /**
     * @param Leave $leave
     */
    public function __construct(Leave $leave)
    {
        $this->setLeave($leave);
    }

    private function reset(): void
    {
        $this->dateDetail = null;
        $this->leavePeriod = null;
        $this->status = null;
        $this->leaveBalance = null;
        $this->allowedActions = null;
        $this->allowedWorkflows = null;
    }

    /**
     * @return Leave
     */
    public function getLeave(): Leave
    {
        return $this->leave;
    }

    /**
     * @param Leave $leave
     */
    public function setLeave(Leave $leave): void
    {
        $this->reset();
        $this->leave = $leave;
    }

    /**
     * @param Leave[] $leaves
     */
    public function setLeaves(iterable $leaves): void
    {
        $this->reset();
        $this->leaves = $leaves;
    }

    /**
     * @return Leave[]
     */
    public function getLeaves(): iterable
    {
        return $this->leaves;
    }

    /**
     * @return LeaveRequestDatesDetail|null
     */
    public function getDatesDetail(): ?LeaveRequestDatesDetail
    {
        if (!is_null($this->dateDetail)) {
            return $this->dateDetail;
        }
        $datesDetail = new LeaveRequestDatesDetail($this->getLeave()->getDate());
        $duration = $this->getLeave()->getDecorator()->getLeaveDuration();
        $datesDetail->setDurationTypeId($this->getLeave()->getDurationType());
        $datesDetail->setDurationType($duration);
        if ($duration !== LeaveDuration::FULL_DAY) {
            $datesDetail->setStartTime($this->getLeave()->getStartTime());
            $datesDetail->setEndTime($this->getLeave()->getEndTime());
        }
        return $this->dateDetail = $datesDetail;
    }

    /**
     * @return LeaveStatusWithLengthDays
     */
    public function getLeaveStatus(): LeaveStatusWithLengthDays
    {
        if (!is_null($this->status)) {
            return $this->status;
        }

        $leave = $this->getLeave();
        $status = $leave->getStatus();

        return $this->status = new LeaveStatusWithLengthDays(
            $status,
            $leave->getDecorator()->getLeaveStatus(),
            $leave->getLengthDays()
        );
    }

    /**
     * @return LeaveBalanceWithLeavePeriod
     */
    public function getLeaveBalance(): LeaveBalanceWithLeavePeriod
    {
        if (!is_null($this->leaveBalance)) {
            return $this->leaveBalance;
        }
        $leavePeriod = $this->getLeavePeriod();
        $empNumber = $this->getLeave()->getEmployee()->getEmpNumber();
        $leaveTypeId = $this->getLeave()->getLeaveType()->getId();
        $asAtDate = $this->firstLeaveOfLeavePeriod->getDate();
        $endDate = $leavePeriod->getEndDate();
        $leaveBalance = $this->getLeaveEntitlementService()->getLeaveBalance(
            $empNumber,
            $leaveTypeId,
            $asAtDate,
            $endDate
        );
        return $this->leaveBalance = new LeaveBalanceWithLeavePeriod($leaveBalance, $leavePeriod);
    }

    /**
     * @return LeavePeriod
     */
    public function getLeavePeriod(): LeavePeriod
    {
        if (!is_null($this->leavePeriod)) {
            return $this->leavePeriod;
        }
        $leaves = $this->getLeaves();
        $leaveEntitlementStrategy = $this->getLeaveEntitlementService()->getLeaveEntitlementStrategy();
        $empNumber = $this->getLeave()->getEmployee()->getEmpNumber();
        $leaveTypeId = $this->getLeave()->getLeaveType()->getId();
        foreach ($leaves as $leave) {
            if ($this->firstLeaveOfLeavePeriod == null) {
                $this->firstLeaveOfLeavePeriod = $leave;
            }
        }

        return $this->leavePeriod = $leaveEntitlementStrategy->getLeavePeriod(
            $this->getLeave()->getDate(),
            $empNumber,
            $leaveTypeId
        );
    }

    /**
     * @return string[] e.g. array('APPROVE', 'REJECT', 'CANCEL')
     */
    public function getAllowedActions(): array
    {
        if (!is_null($this->allowedActions)) {
            return $this->allowedActions;
        }
        $leaveStatus = $this->getLeave()->getDecorator()->getLeaveStatusName();
        $allowedWorkflows = $this->getAllowedWorkflows($leaveStatus);
        return $this->allowedActions = array_values(
            array_map(
                fn (WorkflowStateMachine $workflow) => $workflow->getAction(),
                $allowedWorkflows
            )
        );
    }

    /**
     * @param string $leaveStatus e.g. 'PENDING APPROVAL', 'SCHEDULED', 'TAKEN'
     * @return WorkflowStateMachine[]
     */
    private function getAllowedWorkflows(string $leaveStatus): array
    {
        if (is_null($this->allowedWorkflows)) {
            $this->allowedWorkflows = $this->getLeaveRequestService()->getLeaveRequestAllowedWorkflows(
                $this->getLeave()->getEmployee(),
                $this->getLeave()->getLeaveType(),
                $leaveStatus,
                $this->getAuthUser()->getEmpNumber()
            );
        }
        return $this->allowedWorkflows;
    }

    /**
     * @param string $action e.g. 'APPROVE', 'REJECT', 'CANCEL'
     * @return bool
     */
    public function isActionAllowed(string $action): bool
    {
        $leaveStatus = $this->getLeave()->getDecorator()->getLeaveStatusName();
        return $this->getLeaveRequestService()->isLeaveRequestActionAllowed(
            $this->getLeave()->getEmployee(),
            $this->getLeave()->getLeaveType(),
            $leaveStatus,
            $action,
            $this->getAuthUser()->getEmpNumber()
        );
    }

    /**
     * @param string $action e.g. 'APPROVE', 'REJECT', 'CANCEL'
     * @return WorkflowStateMachine|null
     */
    public function getWorkflowForAction(string $action): ?WorkflowStateMachine
    {
        $leaveStatus = $this->getLeave()->getDecorator()->getLeaveStatusName();
        $allowedWorkflows = $this->getAllowedWorkflows($leaveStatus);
        $workflow = null;
        foreach ($allowedWorkflows as $allowedWorkflow) {
            if ($allowedWorkflow->getAction() === $action) {
                $workflow = $allowedWorkflow;
                break;
            }
        }
        return $workflow;
    }
}
