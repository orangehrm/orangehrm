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
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Core\Traits\Service\NumberHelperTrait;
use OrangeHRM\Entity\EmployeeTerminationRecord;
use OrangeHRM\Leave\Api\Model\LeaveTypeModel;
use OrangeHRM\Leave\Dto\LeaveTypeLeaveEntitlementUsageReportSearchFilterParams;
use OrangeHRM\Leave\Traits\Service\LeaveEntitlementServiceTrait;
use OrangeHRM\Leave\Traits\Service\LeaveTypeServiceTrait;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

class LeaveTypeLeaveEntitlementUsageReportData implements ReportData
{
    use LeaveEntitlementServiceTrait;
    use LeaveTypeServiceTrait;
    use EmployeeServiceTrait;
    use DateTimeHelperTrait;
    use NormalizerServiceTrait;
    use NumberHelperTrait;

    private LeaveTypeLeaveEntitlementUsageReportSearchFilterParams $filterParams;

    public function __construct(LeaveTypeLeaveEntitlementUsageReportSearchFilterParams $filterParams)
    {
        $this->filterParams = $filterParams;
    }

    /**
     * @inheritDoc
     */
    public function normalize(): array
    {
        $employees = $this->getLeaveEntitlementService()
            ->getLeaveEntitlementDao()
            ->getEmployeesForEntitlementUsageReport($this->filterParams);

        $fromDateYmd = $this->getDateTimeHelper()->formatDateTimeToYmd($this->filterParams->getFromDate());
        $toDateYmd = $this->getDateTimeHelper()->formatDateTimeToYmd($this->filterParams->getToDate());
        $result = [];

        $leaveTypeId = $this->filterParams->getLeaveTypeId();
        foreach ($employees as $employee) {
            $empNumber = $employee->getEmpNumber();
            $balance = $this->getLeaveEntitlementService()
                ->getLeaveBalance(
                    $employee->getEmpNumber(),
                    $leaveTypeId,
                    $this->filterParams->getFromDate(),
                    $this->filterParams->getToDate()
                );

            $employeeName = $employee->getDecorator()->getFirstAndLastNames();
            if ($employee->getEmployeeTerminationRecord() instanceof EmployeeTerminationRecord) {
                // TODO:: Need to handle localization
                $employeeName .= ' (Past Employee)';
            }
            $result[] = [
                LeaveTypeLeaveEntitlementUsageReport::PARAMETER_EMPLOYEE_NAME => $employeeName,
                LeaveTypeLeaveEntitlementUsageReport::PARAMETER_ENTITLEMENT_DAYS => $this->getNumberHelper()
                    ->numberFormatWithGroupedThousands($balance->getEntitled(), 2),
                LeaveTypeLeaveEntitlementUsageReport::PARAMETER_PENDING_APPROVAL_DAYS => $this->getNumberHelper()
                    ->numberFormatWithGroupedThousands($balance->getPending(), 2),
                LeaveTypeLeaveEntitlementUsageReport::PARAMETER_SCHEDULED_DAYS => $this->getNumberHelper()
                    ->numberFormatWithGroupedThousands($balance->getScheduled(), 2),
                LeaveTypeLeaveEntitlementUsageReport::PARAMETER_TAKEN_DAYS => $this->getNumberHelper()
                    ->numberFormatWithGroupedThousands($balance->getTaken(), 2),
                LeaveTypeLeaveEntitlementUsageReport::PARAMETER_BALANCE_DAYS => $this->getNumberHelper()
                    ->numberFormatWithGroupedThousands($balance->getBalance(), 2),
                'terminationId' => $employee->getEmployeeTerminationRecord() ?
                    $employee->getEmployeeTerminationRecord()->getId() : null,
                '_url' => [
                    EmployeeLeaveEntitlementUsageReport::PARAMETER_ENTITLEMENT_DAYS => '/leave/viewLeaveEntitlements' .
                        "?empNumber=$empNumber" .
                        "&fromDate=$fromDateYmd" .
                        "&toDate=$toDateYmd" .
                        "&leaveTypeId=$leaveTypeId",
                    EmployeeLeaveEntitlementUsageReport::PARAMETER_PENDING_APPROVAL_DAYS => '/leave/viewLeaveList' .
                        "?empNumber=$empNumber" .
                        "&fromDate=$fromDateYmd" .
                        "&toDate=$toDateYmd" .
                        "&leaveTypeId=$leaveTypeId" .
                        '&status=1',
                    EmployeeLeaveEntitlementUsageReport::PARAMETER_SCHEDULED_DAYS => '/leave/viewLeaveList' .
                        "?empNumber=$empNumber" .
                        "&fromDate=$fromDateYmd" .
                        "&toDate=$toDateYmd" .
                        "&leaveTypeId=$leaveTypeId" .
                        '&status=2',
                    EmployeeLeaveEntitlementUsageReport::PARAMETER_TAKEN_DAYS => '/leave/viewLeaveList' .
                        "?empNumber=$empNumber" .
                        "&fromDate=$fromDateYmd" .
                        "&toDate=$toDateYmd" .
                        "&leaveTypeId=$leaveTypeId" .
                        '&status=3'
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
                    ->getEmployeesCountForEntitlementUsageReport($this->filterParams),
                'leaveType' => $this->getNormalizedLeaveType($this->filterParams->getLeaveTypeId()),
            ]
        );
    }

    /**
     * @param int $leaveTypeId
     * @return array|null
     */
    private function getNormalizedLeaveType(int $leaveTypeId): ?array
    {
        $leaveType = $this->getLeaveTypeService()->getLeaveTypeDao()->getLeaveTypeById($leaveTypeId);
        return $this->getNormalizerService()->normalize(LeaveTypeModel::class, $leaveType);
    }
}
