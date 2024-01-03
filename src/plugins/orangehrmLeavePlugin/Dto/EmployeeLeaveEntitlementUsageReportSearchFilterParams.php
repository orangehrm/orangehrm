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

namespace OrangeHRM\Leave\Dto;

use InvalidArgumentException;

class EmployeeLeaveEntitlementUsageReportSearchFilterParams extends DateRangeSearchFilterParams
{
    public const ALLOWED_SORT_FIELDS = ['leaveType.name'];

    public const REPORT_TYPE_EMPLOYEE = 'employee';
    public const REPORT_TYPE_MY = 'my';

    private ?int $empNumber = null;

    private string $reportType = self::REPORT_TYPE_EMPLOYEE;

    public function __construct()
    {
        $this->setSortField('leaveType.name');
    }

    /**
     * @return int|null
     */
    public function getEmpNumber(): ?int
    {
        return $this->empNumber;
    }

    /**
     * @param int|null $empNumber
     */
    public function setEmpNumber(?int $empNumber): void
    {
        $this->empNumber = $empNumber;
    }

    /**
     * @return string
     */
    public function getReportType(): string
    {
        return $this->reportType;
    }

    /**
     * @param string $reportType
     */
    public function setReportType(string $reportType): void
    {
        if (!in_array($reportType, [self::REPORT_TYPE_EMPLOYEE, self::REPORT_TYPE_MY])) {
            throw new InvalidArgumentException();
        }
        $this->reportType = $reportType;
    }
}
