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

namespace OrangeHRM\Leave\Report;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Report\ReportData;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\Service\NumberHelperTrait;
use OrangeHRM\Entity\Leave;
use OrangeHRM\I18N\Traits\Service\I18NHelperTrait;
use OrangeHRM\Leave\Dao\LeaveRequestDao;
use OrangeHRM\Leave\Dto\EmployeeLeaveEntitlementUsageReportSearchFilterParams;
use OrangeHRM\Leave\Dto\LeaveRequestSearchFilterParams;
use OrangeHRM\Leave\Traits\Service\LeaveEntitlementServiceTrait;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

class EmployeeLeaveEntitlementUsageReportData implements ReportData
{
    use LeaveEntitlementServiceTrait;
    use EmployeeServiceTrait;
    use DateTimeHelperTrait;
    use NumberHelperTrait;
    use I18NHelperTrait;

    public const META_PARAMETER_EMPLOYEE = 'employee';

    private EmployeeLeaveEntitlementUsageReportSearchFilterParams $filterParams;

    private LeaveRequestDao $leaveRequestDao;

    public function __construct(
        EmployeeLeaveEntitlementUsageReportSearchFilterParams $filterParams
    ) {
        $this->filterParams = $filterParams;
        $this->leaveRequestDao = new LeaveRequestDao();
    }

    /**
     * @inheritDoc
     */
    public function normalize(): array
    {
        $empNumber = $this->filterParams->getEmpNumber();

        $employee = $this->getEmployeeService()->getEmployeeAsArray($empNumber);

        $employeeName = trim(
            ($employee['firstName'] ?? '') . ' ' . ($employee['lastName'] ?? '')
        );

        $leaveFilter = new LeaveRequestSearchFilterParams();
        $leaveFilter->setEmpNumber($empNumber);
        $leaveFilter->setFromDate($this->filterParams->getFromDate());
        $leaveFilter->setToDate($this->filterParams->getToDate());

        if ($this->filterParams->getLeaveTypeId()) {
            $leaveFilter->setLeaveTypeId($this->filterParams->getLeaveTypeId());
        }

        $leaveRequests = $this->leaveRequestDao->getLeaveRequests($leaveFilter);

        $result = [];

        foreach ($leaveRequests as $leaveRequest) {
            foreach ($leaveRequest->getLeaves() as $leave) {

                $date = $leave->getDate();

                if (
                    $date < $this->filterParams->getFromDate() ||
                    $date > $this->filterParams->getToDate()
                ) {
                    continue;
                }

                $leaveType = $leave->getLeaveType();

                $balance = $this->getLeaveEntitlementService()->getLeaveBalance(
                    $empNumber,
                    $leaveType->getId(),
                    $this->filterParams->getFromDate(),
                    $this->filterParams->getToDate()
                );

                $result[] = [
                    'employeeName'        => $employeeName,
                    'leaveFromDate'       => $this->getDateTimeHelper()
                        ->formatDateTimeToYmd($date),
                    'leaveToDate'         => $this->getDateTimeHelper()
                        ->formatDateTimeToYmd($date),
                    'leaveTypeName'       => $leaveType->getName(),
                    'entitlementDays'     => $balance->getEntitled(),
                    'pendingApprovalDays' => $balance->getPending(),
                    'scheduledDays'       => $balance->getScheduled(),
                    'takenDays'           => $balance->getTaken(),
                    'balanceDays'         => $balance->getBalance(),
                ];
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getMeta(): ?ParameterBag
    {
        return new ParameterBag([
            CommonParams::PARAMETER_TOTAL =>
                $this->getLeaveEntitlementService()
                    ->getLeaveEntitlementDao()
                    ->getLeaveTypesCountForEntitlementUsageReport($this->filterParams),

            self::META_PARAMETER_EMPLOYEE =>
                $this->getEmployeeService()
                    ->getEmployeeAsArray($this->filterParams->getEmpNumber()),
        ]);
    }
}