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

namespace OrangeHRM\Leave\Dto;

use OrangeHRM\Pim\Dto\Traits\SubunitIdChainTrait;

class LeaveTypeLeaveEntitlementUsageReportSearchFilterParams extends DateRangeSearchFilterParams
{
    use SubunitIdChainTrait;

    public const ALLOWED_SORT_FIELDS = [
        'employee.lastName',
        'employee.firstName',
        'employee.middleName',
        'employee.empNumber',
        'employee.employeeId',
    ];

    public const INCLUDE_EMPLOYEES_ONLY_CURRENT = 'onlyCurrent';
    public const INCLUDE_EMPLOYEES_ONLY_PAST = 'onlyPast';
    public const INCLUDE_EMPLOYEES_CURRENT_AND_PAST = 'currentAndPast';

    public const INCLUDE_EMPLOYEES = [
        self::INCLUDE_EMPLOYEES_ONLY_CURRENT,
        self::INCLUDE_EMPLOYEES_ONLY_PAST,
        self::INCLUDE_EMPLOYEES_CURRENT_AND_PAST,
    ];

    /**
     * @var int|null
     */
    private ?int $leaveTypeId = null;

    /**
     * @var int|null
     */
    private ?int $jobTitleId = null;

    /**
     * @var int[]|null
     */
    private ?array $empNumbers = null;

    /**
     * @var int|null
     */
    private ?int $subunitId = null;

    /**
     * @var int|null
     */
    private ?int $locationId = null;

    /**
     * @var string|null
     */
    private ?string $includeEmployees = self::INCLUDE_EMPLOYEES_ONLY_CURRENT;


    public function __construct()
    {
        $this->setSortField('employee.lastName');
    }

    /**
     * @return int|null
     */
    public function getLeaveTypeId(): ?int
    {
        return $this->leaveTypeId;
    }

    /**
     * @param int|null $leaveTypeId
     */
    public function setLeaveTypeId(?int $leaveTypeId): void
    {
        $this->leaveTypeId = $leaveTypeId;
    }

    /**
     * @return int|null
     */
    public function getJobTitleId(): ?int
    {
        return $this->jobTitleId;
    }

    /**
     * @param int|null $jobTitleId
     */
    public function setJobTitleId(?int $jobTitleId): void
    {
        $this->jobTitleId = $jobTitleId;
    }

    /**
     * @return int[]|null
     */
    public function getEmpNumbers(): ?array
    {
        return $this->empNumbers;
    }

    /**
     * @param int[]|null $empNumbers
     */
    public function setEmpNumbers(?array $empNumbers): void
    {
        $this->empNumbers = $empNumbers;
    }

    /**
     * @return int|null
     */
    public function getSubunitId(): ?int
    {
        return $this->subunitId;
    }

    /**
     * @param int|null $subunitId
     */
    public function setSubunitId(?int $subunitId): void
    {
        $this->subunitId = $subunitId;
    }

    /**
     * @return int|null
     */
    public function getLocationId(): ?int
    {
        return $this->locationId;
    }

    /**
     * @param int|null $locationId
     */
    public function setLocationId(?int $locationId): void
    {
        $this->locationId = $locationId;
    }

    /**
     * @return string|null
     */
    public function getIncludeEmployees(): ?string
    {
        return $this->includeEmployees;
    }

    /**
     * @param string|null $includeEmployees
     */
    public function setIncludeEmployees(?string $includeEmployees): void
    {
        $this->includeEmployees = $includeEmployees;
    }
}
