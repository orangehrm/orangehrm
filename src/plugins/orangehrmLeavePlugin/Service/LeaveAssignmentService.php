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

use DateTime;
use Exception;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Entity\LeaveRequest;
use OrangeHRM\Entity\LeaveRequestComment;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Leave\Dto\LeaveParameterObject;
use OrangeHRM\Leave\Event\LeaveAssign;
use OrangeHRM\Leave\Event\LeaveEvent;
use OrangeHRM\Leave\Exception\LeaveAllocationServiceException;
use OrangeHRM\Leave\Traits\Service\LeaveEntitlementServiceTrait;
use OrangeHRM\Leave\Traits\Service\LeaveRequestServiceTrait;

class LeaveAssignmentService extends AbstractLeaveAllocationService
{
    use LeaveEntitlementServiceTrait;
    use LeaveRequestServiceTrait;
    use AuthUserTrait;

    protected ?WorkflowStateMachine $assignWorkflowItem = null;

    /**
     * @param LeaveParameterObject $leaveAssignmentData
     * @return LeaveRequest|null
     * @throws LeaveAllocationServiceException
     */
    public function assignLeave(LeaveParameterObject $leaveAssignmentData)
    {
        $maxAllowedLeavePeriodEndDate = $this->getLeavePeriodService()->getMaxAllowedLeavePeriodEndDate();
        if ($leaveAssignmentData->getToDate() > $maxAllowedLeavePeriodEndDate) {
            throw LeaveAllocationServiceException::cannotAssignLeaveBeyondMaxAllowedLeavePeriodEndDate(
                $this->getDateTimeHelper()->formatDateTimeToYmd($maxAllowedLeavePeriodEndDate)
            );
        }
        if ($this->hasOverlapLeaves($leaveAssignmentData)) {
            throw LeaveAllocationServiceException::overlappingLeavesFound();
        }
        if ($this->isWorkShiftLengthExceeded($leaveAssignmentData)) {
            throw LeaveAllocationServiceException::workShiftLengthExceeded();
        }

        return $this->saveLeaveRequest($leaveAssignmentData);
    }

    /**
     * Saves Leave Request and Sends Notification
     *
     * @param LeaveParameterObject $leaveAssignmentData
     * @return LeaveRequest|null True if leave request is saved else false
     * @throws LeaveAllocationServiceException
     */
    protected function saveLeaveRequest(LeaveParameterObject $leaveAssignmentData): ?LeaveRequest
    {
        $leaveRequest = $this->generateLeaveRequest($leaveAssignmentData);
        $leaveType = $this->getLeaveTypeService()->getLeaveTypeDao()->getLeaveTypeById(
            $leaveAssignmentData->getLeaveType()
        );
        $leaveDays = $this->createLeaveObjectListForAppliedRange($leaveAssignmentData);
        $empNumber = $leaveAssignmentData->getEmployeeNumber();
        $nonHolidayLeaveDays = [];
        $holidayCount = 0;
        $holidays = [Leave::LEAVE_STATUS_LEAVE_WEEKEND, Leave::LEAVE_STATUS_LEAVE_HOLIDAY];
        foreach ($leaveDays as $k => $leave) {
            if (in_array($leave->getStatus(), $holidays)) {
                $holidayCount++;
            } else {
                $nonHolidayLeaveDays[] = $leave;
            }
        }
        if (count($nonHolidayLeaveDays) > 0) {
            $strategy = $this->getLeaveEntitlementService()->getLeaveEntitlementStrategy();
            $entitlements = $strategy->handleLeaveCreate($empNumber, $leaveType->getId(), $nonHolidayLeaveDays, true);
            if ($entitlements == false) {
                throw LeaveAllocationServiceException::leaveBalanceExceeded();
            }
        }
        // TODO
        /* This is to see whether employee applies leave only during weekends or standard holidays */
        if ($holidayCount != count($leaveDays)) {
            if ($this->isEmployeeAllowedToApply($leaveType)) { // TODO: Should this be checked on Assign??
                try {
                    $loggedInUserId = $this->getAuthUser()->getUserId();
                    $loggedInEmpNumber = $this->getAuthUser()->getEmpNumber();
                    $leaveRequest = $this->getLeaveRequestService()
                        ->getLeaveRequestDao()
                        ->saveLeaveRequest($leaveRequest, $leaveDays, $entitlements);

                    if (!empty($leaveAssignmentData->getComment())) {
                        $leaveRequestComment = new LeaveRequestComment();
                        $leaveRequestComment->setLeaveRequest($leaveRequest);
                        $leaveRequestComment->getDecorator()->setCreatedByUserById($loggedInUserId);
                        $leaveRequestComment->getDecorator()->setCreatedByEmployeeByEmpNumber($loggedInEmpNumber);
                        $leaveRequestComment->setComment($leaveAssignmentData->getComment());
                        $this->getLeaveRequestService()
                            ->getLeaveRequestDao()
                            ->saveLeaveRequestComment($leaveRequestComment);
                    }

                    $workFlowItem = $this->getWorkflowItemForAssignAction($leaveAssignmentData);
                    $this->getEventDispatcher()->dispatch(
                        new LeaveAssign($leaveRequest, $workFlowItem, $this->getUserRoleManager()->getUser()),
                        LeaveEvent::ASSIGN
                    );

                    return $leaveRequest;
                } catch (Exception $e) {
                    $this->getLogger()->error('Exception while saving leave:' . $e->getMessage());
                    throw LeaveAllocationServiceException::leaveQuotaWillExceed();
                }
            }
        } else {
            throw LeaveAllocationServiceException::noWorkingDaysSelected();
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getLeaveRequestStatus(
        bool $isWeekend,
        bool $isHoliday,
        DateTime $leaveDate,
        LeaveParameterObject $leaveAssignmentData
    ): int {
        // TODO: Change here for leave workflow

        $status = null;

        if ($isWeekend) {
            return Leave::LEAVE_STATUS_LEAVE_WEEKEND;
        }

        if ($isHoliday) {
            return Leave::LEAVE_STATUS_LEAVE_HOLIDAY;
        }

        if (is_null($status)) {
            $workFlowItem = $this->getWorkflowItemForAssignAction($leaveAssignmentData);
            $status = Leave::LEAVE_STATUS_LEAVE_APPROVED;
            if ($workFlowItem instanceof WorkflowStateMachine) {
                $status = $this->getLeaveRequestService()->getLeaveStatusByName($workFlowItem->getResultingState());
            }
        }
        return $status;
    }

    /**
     * @inheritDoc
     */
    protected function allowToExceedLeaveBalance(): bool
    {
        return true;
    }

    /**
     * @param LeaveParameterObject $leaveAssignmentData
     * @return WorkflowStateMachine|null
     */
    protected function getWorkflowItemForAssignAction(LeaveParameterObject $leaveAssignmentData): ?WorkflowStateMachine
    {
        if (is_null($this->assignWorkflowItem)) {
            $empNumber = $leaveAssignmentData->getEmployeeNumber();
            $workFlowItems = $this->getUserRoleManager()
                ->getAllowedActions(
                    WorkflowStateMachine::FLOW_LEAVE,
                    'INITIAL',
                    [],
                    [],
                    [Employee::class => $empNumber]
                );
            // get apply action
            foreach ($workFlowItems as $item) {
                if ($item->getAction() == 'ASSIGN') {
                    $this->assignWorkflowItem = $item;
                    break;
                }
            }
        }
        if (is_null($this->assignWorkflowItem)) {
            $this->getLogger()->error("No workflow item found for ASSIGN leave action!");
        }
        return $this->assignWorkflowItem;
    }
}
