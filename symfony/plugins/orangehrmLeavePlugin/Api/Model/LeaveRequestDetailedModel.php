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

namespace OrangeHRM\Leave\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Leave\Dto\LeaveRequest\DetailedLeaveRequest;

class LeaveRequestDetailedModel implements Normalizable
{
    use DateTimeHelperTrait;
    use NormalizerServiceTrait;

    private DetailedLeaveRequest $leaveRequest;

    /**
     * @param DetailedLeaveRequest $leaveRequest
     */
    public function __construct(DetailedLeaveRequest $leaveRequest)
    {
        $this->leaveRequest = $leaveRequest;
    }

    public function toArray(): array
    {
        $detailedLeaveRequest = $this->leaveRequest;
        $employee = $detailedLeaveRequest->getLeaveRequest()->getEmployee();
        $leaveType = $detailedLeaveRequest->getLeaveRequest()->getLeaveType();
        $lastComment = $detailedLeaveRequest->getLeaveRequest()->getDecorator()->getLastComment();
        $dates = $detailedLeaveRequest->getDatesDetail();

        $leaveBreakdown = [];
        foreach ($detailedLeaveRequest->getLeaveBreakdown() as $leaveStatusWithLengthDays) {
            $leaveBreakdownItem = [
                'id' => $leaveStatusWithLengthDays->getId(),
                'name' => $leaveStatusWithLengthDays->getName(),
                'lengthDays' => $leaveStatusWithLengthDays->getLengthDays(),
            ];
            $leaveBreakdown[] = $leaveBreakdownItem;
        }

        $allowedActions = [];
        foreach ($detailedLeaveRequest->getAllowedActions() as $action) {
            $allowedActions[] = [
                'action' => $action,
                'name' => ucwords(strtolower($action)),
            ];
        }

        $leaveBalances = [];
        foreach ($detailedLeaveRequest->getLeaveBalances() as $leaveBalanceWithLeavePeriod) {
            $leaveBalance = [
                'period' => $this->getNormalizerService()
                    ->normalize(LeavePeriodModel::class, $leaveBalanceWithLeavePeriod->getLeavePeriod()),
                'balance' => $this->getNormalizerService()
                    ->normalize(LeaveBalanceModel::class, $leaveBalanceWithLeavePeriod->getLeaveBalance())
            ];
            $leaveBalances[] = $leaveBalance;
        }

        return [
            'id' => $detailedLeaveRequest->getLeaveRequest()->getId(),
            'dates' => [
                'fromDate' => $this->getDateTimeHelper()->formatDateTimeToYmd($dates->getFromDate()),
                'toDate' => $this->getDateTimeHelper()->formatDateTimeToYmd($dates->getToDate()),
                'durationType' => [
                    'id' => $dates->getDurationTypeId(),
                    'type' => $dates->getDurationType(),
                ],
                'startTime' => $this->getDateTimeHelper()->formatDateTimeToTimeString($dates->getStartTime()),
                'endTime' => $this->getDateTimeHelper()->formatDateTimeToTimeString($dates->getEndTime()),
            ],
            'noOfDays' => $detailedLeaveRequest->getNoOfDays(),
            'leaveBalances' => $leaveBalances,
            'multiPeriod' => count($leaveBalances) > 1,
            'leaveBreakdown' => $leaveBreakdown,
            'allowedActions' => $allowedActions,
            'hasMultipleStatus' => $detailedLeaveRequest->hasMultipleStatus(),
            'employee' => [
                'empNumber' => $employee->getEmpNumber(),
                'lastName' => $employee->getLastName(),
                'firstName' => $employee->getFirstName(),
                'middleName' => $employee->getMiddleName(),
                'employeeId' => $employee->getEmployeeId(),
                'terminationId' => $employee->getEmployeeTerminationRecord() ?
                    $employee->getEmployeeTerminationRecord()->getId() : null,
            ],
            'leaveType' => [
                'id' => $leaveType->getId(),
                'name' => $leaveType->getName(),
                'deleted' => $leaveType->isDeleted(),
            ],
            'lastComment' => $lastComment ? [
                'id' => $lastComment->getId(),
                'comment' => $lastComment->getComment(),
                'date' => $lastComment->getDecorator()->getCreatedAtDate(),
                'time' => $lastComment->getDecorator()->getCreatedAtTime(),
            ] : null,
        ];
    }
}
