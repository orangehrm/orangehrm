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

namespace OrangeHRM\Time\Report;

use OrangeHRM\Attendance\Traits\Service\AttendanceServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Report\ReportData;
use OrangeHRM\Core\Traits\Service\NumberHelperTrait;
use OrangeHRM\Time\Dto\AttendanceReportSearchFilterParams;

class AttendanceReportData implements ReportData
{
    use AttendanceServiceTrait;
    use NumberHelperTrait;

    /**
     * @var AttendanceReportSearchFilterParams
     */
    private AttendanceReportSearchFilterParams $filterParams;

    public function __construct(AttendanceReportSearchFilterParams $filterParams)
    {
        $this->filterParams = $filterParams;
    }

    /**
     * @inheritDoc
     */
    public function normalize(): array
    {
        $employeeAttendanceRecords = $this->getAttendanceService()
            ->getAttendanceDao()
            ->getAttendanceReportCriteriaList($this->filterParams);

        $result = [];
        foreach ($employeeAttendanceRecords as $employeeAttendanceRecord) {
            $termination = $employeeAttendanceRecord['terminationId'];
            $result[] = [
                AttendanceReport::PARAMETER_EMPLOYEE_NAME => $termination === null ? $employeeAttendanceRecord['fullName'] : $employeeAttendanceRecord['fullName'] . ' (Past employee)',
                AttendanceReport::PARAMETER_TIME => $this->getNumberHelper()
                    ->numberFormat((float)$employeeAttendanceRecord['total'] / 3600, 2)
            ];
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getMeta(): ?ParameterBag
    {
        $total = $this->getAttendanceService()
            ->getAttendanceDao()
            ->getTotalAttendanceDuration($this->filterParams);

        return new ParameterBag(
            [
                CommonParams::PARAMETER_TOTAL => $this->getAttendanceService()
                    ->getAttendanceDao()
                    ->getAttendanceReportCriteriaListCount($this->filterParams),
                'sum' => [
                    'hours' => floor($total / 3600),
                    'minutes' => ($total / 60) % 60,
                    'label' => $this->getNumberHelper()->numberFormat($total / 3600, 2),
                ],
            ]
        );
    }
}
