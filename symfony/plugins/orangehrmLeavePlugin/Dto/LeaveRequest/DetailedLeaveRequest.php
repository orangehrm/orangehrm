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

namespace OrangeHRM\Leave\Dto\LeaveRequest;

use DateTime;
use InvalidArgumentException;
use LogicException;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Entity\LeaveRequest;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Leave\Dto\LeaveDuration;
use OrangeHRM\Leave\Dto\LeavePeriod;
use OrangeHRM\Leave\Traits\Service\LeaveEntitlementServiceTrait;
use OrangeHRM\Leave\Traits\Service\LeaveRequestServiceTrait;

/**
 * This class mainly handle leave request related calculations
 * Leave entities related to this leave request, should add explicitly using DetailedLeaveRequest::addLeave(Leave $leave)
 * Otherwise use DetailedLeaveRequest::fetchLeaves() to fetch leaves before use any other functions
 */
class DetailedLeaveRequest
{
    use LeaveEntitlementServiceTrait;
    use LeaveRequestServiceTrait;
    use AuthUserTrait;

    /**
     * @var LeaveRequest
     */
    private LeaveRequest $leaveRequest;

    /**
     * @var Leave[]
     */
    private array $leaves;

    /**
     * @var DateTime[]
     */
    private array $leaveDates;

    /**
     * @var bool
     */
    private bool $sortedLeaves = false;

    /**
     * @var float|null
     */
    private ?float $noOfDays = null;

    /**
     * @var LeaveRequestDatesDetail|null
     */
    private ?LeaveRequestDatesDetail $dateDetail = null;

    /**
     * @var LeaveStatusWithLengthDays[]|null
     */
    private ?array $leaveBreakdown = null;

    /**
     * @var LeavePeriod[]|null
     */
    private ?array $leavePeriods = null;

    /**
     * @var Leave[]|null
     */
    private ?array $firstLeaveOfEachLeavePeriod = null;

    /**
     * @var LeaveBalanceWithLeavePeriod[]|null
     */
    private ?array $leaveBalances = null;

    /**
     * @var string[]|null
     */
    private ?array $allowedActions = null;

    /**
     * @var WorkflowStateMachine[]|null
     */
    private ?array $allowedWorkflows = null;

    /**
     * @param LeaveRequest $leaveRequest
     */
    public function __construct(LeaveRequest $leaveRequest)
    {
        $this->setLeaveRequest($leaveRequest);
    }

    private function reset(): void
    {
        $this->sortedLeaves = false;
        $this->noOfDays = null;
        $this->dateDetail = null;
        $this->leaveBreakdown = null;
        $this->leavePeriods = null;
        $this->firstLeaveOfEachLeavePeriod = null;
        $this->leaveBalances = null;
        $this->allowedActions = null;
        $this->allowedWorkflows = null;
    }

    /**
     * @return LeaveRequest
     */
    public function getLeaveRequest(): LeaveRequest
    {
        return $this->leaveRequest;
    }

    /**
     * @param LeaveRequest $leaveRequest
     */
    public function setLeaveRequest(LeaveRequest $leaveRequest): void
    {
        $this->reset();
        $this->leaveRequest = $leaveRequest;
    }

    /**
     * Fetch all leaves related to this leave request
     * By default DetailedLeaveRequest::class not fetching leaves in DetailedLeaveRequest::getLeaves
     */
    public function fetchLeaves(): void
    {
        $this->setLeaves($this->getLeaveRequest()->getLeaves());
    }

    /**
     * @return Leave[]
     */
    public function getLeaves(): array
    {
        if (!$this->sortedLeaves) {
            array_multisort($this->leaveDates, $this->leaves);
            $this->sortedLeaves = true;
        }
        return $this->leaves;
    }

    /**
     * @param Leave[] $leaves
     */
    public function setLeaves(iterable $leaves): void
    {
        if (empty($leaves)) {
            throw new InvalidArgumentException('Not excepting empty iterable');
        }
        $this->leaveDates = [];
        $this->leaves = [];
        foreach ($leaves as $leave) {
            $this->addLeave($leave);
        }
    }

    /**
     * @param Leave $leave
     */
    public function addLeave(Leave $leave): void
    {
        $this->reset();
        $this->leaveDates[] = $leave->getDate();
        $this->leaves[] = $leave;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return count($this->leaves);
    }

    /**
     * @return float
     */
    public function getNoOfDays(): float
    {
        if (!is_null($this->noOfDays)) {
            return $this->noOfDays;
        }
        $length = 0;
        foreach ($this->leaves as $leave) {
            $length += $leave->getLengthDays();
        }
        return $this->noOfDays = $length;
    }

    /**
     * @return LeaveRequestDatesDetail
     */
    public function getDatesDetail(): LeaveRequestDatesDetail
    {
        if (!is_null($this->dateDetail)) {
            return $this->dateDetail;
        }
        $leaves = $this->getLeaves();
        $count = count($leaves);
        $datesDetail = new LeaveRequestDatesDetail($leaves[0]->getDate());
        if ($count === 1) {
            $leave = $leaves[0];
            $duration = $leave->getDecorator()->getLeaveDuration();
            $datesDetail->setDurationTypeId($leave->getDurationType());
            $datesDetail->setDurationType($duration);
            if ($duration !== LeaveDuration::FULL_DAY) {
                $datesDetail->setStartTime($leave->getStartTime());
                $datesDetail->setEndTime($leave->getEndTime());
            }
        } else {
            $datesDetail->setToDate($leaves[$count - 1]->getDate());
        }

        return $this->dateDetail = $datesDetail;
    }

    /**
     * @return LeaveStatusWithLengthDays[]
     */
    public function getLeaveBreakdown(): array
    {
        if (!is_null($this->leaveBreakdown)) {
            return $this->leaveBreakdown;
        }
        $leaves = $this->getLeaves();
        /** @var array<int, LeaveStatusWithLengthDays> $statuses */
        $statuses = [];
        foreach ($leaves as $leave) {
            $status = $leave->getStatus();
            if (in_array($status, [Leave::LEAVE_STATUS_LEAVE_WEEKEND, Leave::LEAVE_STATUS_LEAVE_HOLIDAY])) {
                continue;
            }

            if (isset($statuses[$status])) {
                $statuses[$status]->addToLengthDays($leave->getLengthDays());
            } else {
                $statuses[$status] = new LeaveStatusWithLengthDays(
                    $status,
                    $leave->getDecorator()->getLeaveStatus(),
                    $leave->getLengthDays()
                );
            }
        }

        return $this->leaveBreakdown = array_values($statuses);
    }

    /**
     * @return LeaveBalanceWithLeavePeriod[]
     */
    public function getLeaveBalances(): array
    {
        if (!is_null($this->leaveBalances)) {
            return $this->leaveBalances;
        }
        $leavePeriods = $this->getLeavePeriods();
        $empNumber = $this->getLeaveRequest()->getEmployee()->getEmpNumber();
        $leaveTypeId = $this->getLeaveRequest()->getLeaveType()->getId();
        $leaveBalances = [];
        foreach ($leavePeriods as $i => $leavePeriod) {
            $asAtDate = $this->firstLeaveOfEachLeavePeriod[$i]->getDate();
            $endDate = $leavePeriod->getEndDate();
            $leaveBalance = $this->getLeaveEntitlementService()->getLeaveBalance(
                $empNumber,
                $leaveTypeId,
                $asAtDate,
                $endDate
            );
            $leaveBalances[] = new LeaveBalanceWithLeavePeriod($leaveBalance, $leavePeriod);
        }

        return $this->leaveBalances = $leaveBalances;
    }

    /**
     * @return LeavePeriod[]
     */
    public function getLeavePeriods(): array
    {
        if (!is_null($this->leavePeriods)) {
            return $this->leavePeriods;
        }
        $leaves = $this->getLeaves();
        $leaveEntitlementStrategy = $this->getLeaveEntitlementService()->getLeaveEntitlementStrategy();
        $empNumber = $this->getLeaveRequest()->getEmployee()->getEmpNumber();
        $leaveTypeId = $this->getLeaveRequest()->getLeaveType()->getId();
        $leavePeriods = [];
        $firstLeaveOfEachLeavePeriod = [];
        foreach ($leaves as $leave) {
            $leavePeriod = $leaveEntitlementStrategy->getLeavePeriod($leave->getDate(), $empNumber, $leaveTypeId);
            if (is_null($leavePeriod)) {
                continue;
            }
            $key = $leavePeriod->getYmdStartDate() . '_' . $leavePeriod->getYmdEndDate();
            if (!isset($leavePeriods[$key])) {
                $leavePeriods[$key] = $leavePeriod;
                $firstLeaveOfEachLeavePeriod[] = $leave;
            }
        }

        $this->firstLeaveOfEachLeavePeriod = $firstLeaveOfEachLeavePeriod;
        return $this->leavePeriods = array_values($leavePeriods);
    }

    /**
     * @return bool
     */
    public function hasMultipleStatus(): bool
    {
        return count($this->getLeaveBreakdown()) > 1;
    }

    /**
     * @return string[] e.g. array('APPROVE', 'REJECT', 'CANCEL')
     */
    public function getAllowedActions(): array
    {
        if (!is_null($this->allowedActions)) {
            return $this->allowedActions;
        }
        if ($this->hasMultipleStatus()) {
            return [];
        }
        $leaveStatus = $this->getLeaveRequestStatus();
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
                $this->getLeaveRequest()->getEmployee(),
                $this->getLeaveRequest()->getLeaveType(),
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
        if ($this->hasMultipleStatus()) {
            return false;
        }
        $leaveStatus = $this->getLeaveRequestStatus();
        return $this->getLeaveRequestService()->isLeaveRequestActionAllowed(
            $this->getLeaveRequest()->getEmployee(),
            $this->getLeaveRequest()->getLeaveType(),
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
        if ($this->hasMultipleStatus()) {
            return null;
        }
        $leaveStatus = $this->getLeaveRequestStatus();
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

    /**
     * @return string e.g. ['PENDING APPROVAL', 'SCHEDULED', 'TAKEN', 'REJECTED', 'CANCELLED']
     */
    protected function getLeaveRequestStatus(): string
    {
        if ($this->hasMultipleStatus()) {
            throw new LogicException('Cannot get leave request status, if it have multiple statuses');
        }
        return $this->getLeaveBreakdown()[0]->getName();
    }
}
