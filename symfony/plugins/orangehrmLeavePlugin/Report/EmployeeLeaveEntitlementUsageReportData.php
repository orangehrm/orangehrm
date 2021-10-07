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

namespace OrangeHRM\Leave\Report;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Report\ReportData;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Leave\Dto\EmployeeLeaveEntitlementUsageReportSearchFilterParams;
use OrangeHRM\Leave\Traits\Service\LeaveEntitlementServiceTrait;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

class EmployeeLeaveEntitlementUsageReportData implements ReportData
{
    use LeaveEntitlementServiceTrait;
    use EmployeeServiceTrait;
    use DateTimeHelperTrait;

    public const META_PARAMETER_EMPLOYEE = 'employee';

    private EmployeeLeaveEntitlementUsageReportSearchFilterParams $filterParams;

    public function __construct(EmployeeLeaveEntitlementUsageReportSearchFilterParams $filterParams)
    {
        $this->filterParams = $filterParams;
    }

    /**
     * @inheritDoc
     */
    public function normalize(): array
    {
        $leaveTypes = $this->getLeaveEntitlementService()
            ->getLeaveEntitlementDao()
            ->getLeaveTypesForEntitlementUsageReport($this->filterParams);

        $empNumber = $this->filterParams->getEmpNumber();
        $fromDateYmd = $this->getDateTimeHelper()->formatDateTimeToYmd($this->filterParams->getFromDate());
        $toDateYmd = $this->getDateTimeHelper()->formatDateTimeToYmd($this->filterParams->getToDate());
        $result = [];
        foreach ($leaveTypes as $leaveType) {
            $balance = $this->getLeaveEntitlementService()
                ->getLeaveBalance(
                    $empNumber,
                    $leaveType->getId(),
                    $this->filterParams->getFromDate(),
                    $this->filterParams->getToDate()
                );
            $result[] = [
                EmployeeLeaveEntitlementUsageReport::PARAMETER_LEAVE_TYPE_NAME => $leaveType->getName(),
                EmployeeLeaveEntitlementUsageReport::PARAMETER_ENTITLEMENT_DAYS => $balance->getEntitled(),
                EmployeeLeaveEntitlementUsageReport::PARAMETER_PENDING_APPROVAL_DAYS => $balance->getPending(),
                EmployeeLeaveEntitlementUsageReport::PARAMETER_SCHEDULED_DAYS => $balance->getScheduled(),
                EmployeeLeaveEntitlementUsageReport::PARAMETER_TAKEN_DAYS => $balance->getTaken(),
                EmployeeLeaveEntitlementUsageReport::PARAMETER_BALANCE_DAYS => $balance->getBalance(),
                'leaveTypeDeleted' => $leaveType->isDeleted(),
                '_url' => [
                    EmployeeLeaveEntitlementUsageReport::PARAMETER_ENTITLEMENT_DAYS => '/leave/viewLeaveEntitlements' .
                        "?empNumber=$empNumber" .
                        "&fromDate=$fromDateYmd" .
                        "&toDate=$toDateYmd" .
                        '&leaveTypeId=' . $leaveType->getId(),
                    EmployeeLeaveEntitlementUsageReport::PARAMETER_PENDING_APPROVAL_DAYS => '/leave/viewLeaveList' .
                        "?empNumber=$empNumber" .
                        "&fromDate=$fromDateYmd" .
                        "&toDate=$toDateYmd" .
                        '&leaveTypeId=' . $leaveType->getId() . '&status=1',
                    EmployeeLeaveEntitlementUsageReport::PARAMETER_SCHEDULED_DAYS => '/leave/viewLeaveList' .
                        "?empNumber=$empNumber" .
                        "&fromDate=$fromDateYmd" .
                        "&toDate=$toDateYmd" .
                        '&leaveTypeId=' . $leaveType->getId() . '&status=2',
                    EmployeeLeaveEntitlementUsageReport::PARAMETER_TAKEN_DAYS => '/leave/viewLeaveList' .
                        "?empNumber=$empNumber" .
                        "&fromDate=$fromDateYmd" .
                        "&toDate=$toDateYmd" .
                        '&leaveTypeId=' . $leaveType->getId() . '&status=3'
                ],
            ];
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getMeta(): ?ParameterBag
    {
        return new ParameterBag(
            [
                CommonParams::PARAMETER_TOTAL => $this->getLeaveEntitlementService()
                    ->getLeaveEntitlementDao()
                    ->getLeaveTypesCountForEntitlementUsageReport($this->filterParams),
                self::META_PARAMETER_EMPLOYEE => $this->getEmployeeService()->getEmployeeAsArray(
                    $this->filterParams->getEmpNumber()
                )
            ]
        );
    }
}
