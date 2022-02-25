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

namespace OrangeHRM\Time\Dto;

use InvalidArgumentException;
use OrangeHRM\Leave\Dto\DateRangeSearchFilterParams;

class EmployeeReportsSearchFilterParams extends DateRangeSearchFilterParams
{
    public const ALLOWED_SORT_FIELDS = [
        'customer.name',
        'project.name',
        'projectActivity.name'
    ];

    public const INCLUDE_TIMESHEETS_APPROVED_ONLY = 'onlyApproved';
    public const INCLUDE_TIMESHEETS_ALL = 'all';

    public const INCLUDE_TIMESHEETS = [
        self::INCLUDE_TIMESHEETS_APPROVED_ONLY,
        self::INCLUDE_TIMESHEETS_ALL,
    ];

    public const TIMESHEET_APPROVED_STATE = 'APPROVED';

    /**
     * @var int
     */
    private int $empNumber;

    /**
     * @var int|null
     */
    private ?int $projectId = null;

    /**
     * @var int|null
     */
    private ?int $activityId = null;

    /**
     * @var string
     */
    private string $includeTimesheets = self::INCLUDE_TIMESHEETS_ALL;

    public function __construct()
    {
        $this->setSortField('customer.name');
    }

    /**
     * @return int
     */
    public function getEmpNumber(): int
    {
        return $this->empNumber;
    }

    /**
     * @param int $empNumber
     */
    public function setEmpNumber(int $empNumber): void
    {
        $this->empNumber = $empNumber;
    }

    /**
     * @return int|null
     */
    public function getProjectId(): ?int
    {
        return $this->projectId;
    }

    /**
     * @param int|null $projectId
     */
    public function setProjectId(?int $projectId): void
    {
        $this->projectId = $projectId;
    }

    /**
     * @return int|null
     */
    public function getActivityId(): ?int
    {
        return $this->activityId;
    }

    /**
     * @param int|null $activityId
     */
    public function setActivityId(?int $activityId): void
    {
        $this->activityId = $activityId;
    }

    /**
     * @return string
     */
    public function getIncludeTimesheets(): string
    {
        return $this->includeTimesheets;
    }

    /**
     * @param string|null $includeTimesheets
     * @return void
     */
    public function setIncludeTimesheets(?string $includeTimesheets): void
    {
        if (is_null($includeTimesheets)) {
            return;
        }
        if (!in_array($includeTimesheets, self::INCLUDE_TIMESHEETS)) {
            throw new InvalidArgumentException();
        }
        $this->includeTimesheets = $includeTimesheets;
    }
}
