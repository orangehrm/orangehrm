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

namespace OrangeHRM\Recruitment\Dto;

use OrangeHRM\Core\Dto\FilterParams;

class VacancySearchFilterParams extends FilterParams
{
    public const ALLOWED_SORT_FIELDS = [
        'jobTitle.id',
        'vacancy.id',
        'employee.id',
        'vacancy.status',
    ];

    /**
     * @var int|null
     */
    protected ?int $jobTitleId = null;

    /**
     * @var int|null
     */
    protected ?int $vacancyId = null;

    /**
     * @var int|null
     */
    protected ?int $employeeId = null;
    
    /**
     * @var int|null
     */
    protected ?int $status = 1;

    /**
     * @return int|null
     */
    public function getJobTitleId(): ?int
    {
        return $this->jobTitleId;
    }

    /**
     * @param  int|null  $jobTitleId
     */
    public function setJobTitleId(?int $jobTitleId): void
    {
        $this->jobTitleId = $jobTitleId;
    }

    /**
     * @return int|null
     */
    public function getVacancyId(): ?int
    {
        return $this->vacancyId;
    }

    /**
     * @param  int|null  $vacancyId
     */
    public function setVacancyId(?int $vacancyId): void
    {
        $this->vacancyId = $vacancyId;
    }

    /**
     * @return int|null
     */
    public function getEmployeeId(): ?int
    {
        return $this->employeeId;
    }

    /**
     * @param  int|null  $employeeId
     */
    public function setEmployeeId(?int $employeeId): void
    {
        $this->employeeId = $employeeId;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param  int|null  $status
     */
    public function setStatus(?int $status): void
    {
        $this->status = $status;
    }

}