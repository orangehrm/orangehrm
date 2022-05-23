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
use OrangeHRM\Pim\Dto\Traits\SubunitIdChainTrait;

class PerformanceReviewSearchFilterParams extends FilterParams
{
    use SubunitIdChainTrait;

    public const REVIEW_LIST_ALLOWED_SORT_FIELDS = ['employee.lastName', 'performanceReview.workPeriodStart',  'performanceReview.dueDate', 'performanceReview.statusId'];
    public const PERFORMANCE_REVIEW_ALLOWED_SORT_FIELDS = [...self::REVIEW_LIST_ALLOWED_SORT_FIELDS, 'jobTitle.jobTitleName', 'reviewerEmployee.lastName'];

    public const STATUS_ID_INACTIVE = 1;
    public const STATUS_ID_ACTIVATED = 2;
    public const STATUS_ID_IN_PROGRESS = 3;
    public const STATUS_ID_COMPLETED = 4;

    public const REVIEW_LIST_STATUSES = [
        self::STATUS_ID_ACTIVATED,
        self::STATUS_ID_IN_PROGRESS,
        self::STATUS_ID_COMPLETED
    ];

    public const PERFORMANCE_REVIEW_STATUSES = [
        self::STATUS_ID_INACTIVE,
        ...self::REVIEW_LIST_STATUSES
    ];

    public const INCLUDE_EMPLOYEES_ONLY_CURRENT = 'onlyCurrent';
    public const INCLUDE_EMPLOYEES_ONLY_PAST = 'onlyPast';
    public const INCLUDE_EMPLOYEES_CURRENT_AND_PAST = 'currentAndPast';

    public const INCLUDE_EMPLOYEES = [
        self::INCLUDE_EMPLOYEES_ONLY_CURRENT,
        self::INCLUDE_EMPLOYEES_ONLY_PAST,
        self::INCLUDE_EMPLOYEES_CURRENT_AND_PAST,
    ];

    protected ?int $empNumber = null;
    protected ?int $reviewerEmpNumber = null;
    protected ?int $jobTitleId = null;
    protected ?int $subunitId = null;
    protected ?int $statusId = null;
    protected ?DateTime $fromDate = null;
    protected ?DateTime $toDate = null;
    protected bool $excludeInactiveReviews = false;
    protected string $includeEmployees = self::INCLUDE_EMPLOYEES_ONLY_CURRENT;

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
     * @return int|null
     */
    public function getReviewerEmpNumber(): ?int
    {
        return $this->reviewerEmpNumber;
    }

    /**
     * @param int|null $reviewerEmpNumber
     */
    public function setReviewerEmpNumber(?int $reviewerEmpNumber): void
    {
        $this->reviewerEmpNumber = $reviewerEmpNumber;
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

    /**
     * @return bool
     */
    public function isExcludeInactiveReviews(): bool
    {
        return $this->excludeInactiveReviews;
    }

    /**
     * @param bool $excludeInactiveReviews
     */
    public function setExcludeInactiveReviews(bool $excludeInactiveReviews): void
    {
        $this->excludeInactiveReviews = $excludeInactiveReviews;
    }

    /**
     * @return string
     */
    public function getIncludeEmployees(): string
    {
        return $this->includeEmployees;
    }

    /**
     * @param string $includeEmployees
     */
    public function setIncludeEmployees(string $includeEmployees): void
    {
        $this->includeEmployees = $includeEmployees;
    }
}
