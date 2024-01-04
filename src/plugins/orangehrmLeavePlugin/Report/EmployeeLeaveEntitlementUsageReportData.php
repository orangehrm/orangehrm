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
use OrangeHRM\Leave\Dto\EmployeeLeaveEntitlementUsageReportSearchFilterParams;
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
        $isMyReportType = $this->filterParams->getReportType() ===
            EmployeeLeaveEntitlementUsageReportSearchFilterParams::REPORT_TYPE_MY;
        $result = [];
        foreach ($leaveTypes as $leaveType) {
            $balance = $this->getLeaveEntitlementService()
                ->getLeaveBalance(
                    $empNumber,
                    $leaveType->getId(),
                    $this->filterParams->getFromDate(),
                    $this->filterParams->getToDate()
                );
            $leaveTypeName = $leaveType->getName();
            if ($leaveType->isDeleted()) {
                $leaveTypeName .= $this->getI18NHelper()->transBySource(' (Deleted)');
            }

            $leaveEntitlementUrl = $isMyReportType ? '/leave/viewMyLeaveEntitlements' : '/leave/viewLeaveEntitlements';
            $leaveListUrl = $isMyReportType ? '/leave/viewMyLeaveList' : '/leave/viewLeaveList';
            $result[] = [
                EmployeeLeaveEntitlementUsageReport::PARAMETER_LEAVE_TYPE_NAME => $leaveTypeName,
                EmployeeLeaveEntitlementUsageReport::PARAMETER_ENTITLEMENT_DAYS => $this->getNumberHelper()
                    ->numberFormatWithGroupedThousands($balance->getEntitled(), 2),
                EmployeeLeaveEntitlementUsageReport::PARAMETER_PENDING_APPROVAL_DAYS => $this->getNumberHelper()
                    ->numberFormatWithGroupedThousands($balance->getPending(), 2),
                EmployeeLeaveEntitlementUsageReport::PARAMETER_SCHEDULED_DAYS => $this->getNumberHelper()
                    ->numberFormatWithGroupedThousands($balance->getScheduled(), 2),
                EmployeeLeaveEntitlementUsageReport::PARAMETER_TAKEN_DAYS => $this->getNumberHelper()
                    ->numberFormatWithGroupedThousands($balance->getTaken(), 2),
                EmployeeLeaveEntitlementUsageReport::PARAMETER_BALANCE_DAYS => $this->getNumberHelper()
                    ->numberFormatWithGroupedThousands($balance->getBalance(), 2),
                'leaveTypeDeleted' => $leaveType->isDeleted(),
                '_url' => [
                    EmployeeLeaveEntitlementUsageReport::PARAMETER_ENTITLEMENT_DAYS => $leaveEntitlementUrl .
                        "?empNumber=$empNumber" .
                        "&fromDate=$fromDateYmd" .
                        "&toDate=$toDateYmd" .
                        '&leaveTypeId=' . $leaveType->getId(),
                    EmployeeLeaveEntitlementUsageReport::PARAMETER_PENDING_APPROVAL_DAYS => $leaveListUrl .
                        "?empNumber=$empNumber" .
                        "&fromDate=$fromDateYmd" .
                        "&toDate=$toDateYmd" .
                        '&leaveTypeId=' . $leaveType->getId() . '&status=' . Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL,
                    EmployeeLeaveEntitlementUsageReport::PARAMETER_SCHEDULED_DAYS => $leaveListUrl .
                        "?empNumber=$empNumber" .
                        "&fromDate=$fromDateYmd" .
                        "&toDate=$toDateYmd" .
                        '&leaveTypeId=' . $leaveType->getId() . '&status=' . Leave::LEAVE_STATUS_LEAVE_APPROVED,
                    EmployeeLeaveEntitlementUsageReport::PARAMETER_TAKEN_DAYS => $leaveListUrl .
                        "?empNumber=$empNumber" .
                        "&fromDate=$fromDateYmd" .
                        "&toDate=$toDateYmd" .
                        '&leaveTypeId=' . $leaveType->getId() . '&status=' . Leave::LEAVE_STATUS_LEAVE_TAKEN,
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
