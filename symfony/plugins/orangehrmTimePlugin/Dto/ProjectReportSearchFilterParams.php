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

use OrangeHRM\Leave\Dto\DateRangeSearchFilterParams;

class ProjectReportSearchFilterParams extends DateRangeSearchFilterParams
{
    public const ALLOWED_SORT_FIELDS = ['projectActivity.name'];

    /**
     * @var int|null
     */
    private ?int $projectId = null;

    public const INCLUDE_TIMESHEET_ALL = 'all';
    public const INCLUDE_TIMESHEET_ONLY_APPROVED = 'onlyApproved';

    public const INCLUDE_TIMESHEET = [
        self::INCLUDE_TIMESHEET_ALL,
        self::INCLUDE_TIMESHEET_ONLY_APPROVED,
    ];

    public const TIMESHEET_STATE_APPROVED = "APPROVED";

    /**
     * @var string|null
     */
    private ?string $includeApproveTimesheet = null;

    public function __construct()
    {
        $this->setSortField('projectActivity.name');
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
     * @return string|null
     */
    public function getIncludeApproveTimesheet(): ?string
    {
        return $this->includeApproveTimesheet;
    }

    /**
     * @param string|null $includeApproveTimesheet
     */
    public function setIncludeApproveTimesheet(?string $includeApproveTimesheet): void
    {
        $this->includeApproveTimesheet = $includeApproveTimesheet;
    }
}
