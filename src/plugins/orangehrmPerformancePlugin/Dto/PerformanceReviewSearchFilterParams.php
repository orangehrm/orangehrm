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

namespace OrangeHRM\Performance\Dto;

use DateTime;
use OrangeHRM\Core\Dto\FilterParams;

class PerformanceReviewSearchFilterParams extends FilterParams
{
    public const ALLOWED_SORT_FIELDS = ['employee.firstName', 'performanceReview.workPeriodStart',  'performanceReview.dueDate', 'performanceReview.statusId'];

    protected ?int $empNumber = null;
    protected ?string $nameOrId = null;
    protected ?int $jobTitleId = null;
    protected ?int $subUnitId = null;
    protected ?int $statusId = null;
    protected ?DateTime $fromDate = null;
    protected ?DateTime $toDate = null;

    public function __construct()
    {
        $this->setSortField('performanceReview.statusId');
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
     * @return string|null
     */
    public function getNameOrId(): ?string
    {
        return $this->nameOrId;
    }

    /**
     * @param string|null $nameOrId
     */
    public function setNameOrId(?string $nameOrId): void
    {
        $this->nameOrId = $nameOrId;
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
     * @return int|null
     */
    public function getSubUnitId(): ?int
    {
        return $this->subUnitId;
    }

    /**
     * @param int|null $subUnitId
     */
    public function setSubUnitId(?int $subUnitId): void
    {
        $this->subUnitId = $subUnitId;
    }

    /**
     * @return int|null
     */
    public function getStatusId(): ?int
    {
        return $this->statusId;
    }

    /**
     * @param int|null $statusId
     */
    public function setStatusId(?int $statusId): void
    {
        $this->statusId = $statusId;
    }

    /**
     * @return DateTime|null
     */
    public function getFromDate(): ?DateTime
    {
        return $this->fromDate;
    }

    /**
     * @param DateTime|null $fromDate
     */
    public function setFromDate(?DateTime $fromDate): void
    {
        $this->fromDate = $fromDate;
    }

    /**
     * @return DateTime|null
     */
    public function getToDate(): ?DateTime
    {
        return $this->toDate;
    }

    /**
     * @param DateTime|null $toDate
     */
    public function setToDate(?DateTime $toDate): void
    {
        $this->toDate = $toDate;
    }
}
