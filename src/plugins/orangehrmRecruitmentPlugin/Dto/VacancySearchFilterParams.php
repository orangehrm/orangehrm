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

namespace OrangeHRM\Recruitment\Dto;

use OrangeHRM\Core\Dto\FilterParams;

class VacancySearchFilterParams extends FilterParams
{
    public const ALLOWED_SORT_FIELDS = [
        'vacancy.name',
        'vacancy.status',
        'jobTitle.jobTitleName',
        'hiringManager.lastName',
    ];

    /**
     * @var int|null
     */
    private ?int $jobTitleId = null;

    /**
     * @var int|null
     */
    private ?int $empNumber = null;

    /**
     * @var bool|null
     */
    private ?bool $status = null;

    /**
     * @var string|null
     */
    private ?string $name = null;

    /**
     * @var bool|null
     */
    private ?bool $isPublished = null;

    /**
     * @var array|null
     */
    private ?array $vacancyIds = null;

    public function __construct()
    {
        $this->setSortField('vacancy.name');
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
     * @return bool|null
     */
    public function getStatus(): ?bool
    {
        return $this->status;
    }

    /**
     * @param bool|null $status
     */
    public function setStatus(?bool $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return bool|null
     */
    public function isPublished(): ?bool
    {
        return $this->isPublished;
    }

    /**
     * @param bool|null $isPublished
     */
    public function setIsPublished(?bool $isPublished): void
    {
        $this->isPublished = $isPublished;
    }

    /**
     * @return array|null
     */
    public function getVacancyIds(): ?array
    {
        return $this->vacancyIds;
    }

    /**
     * @param array|null $vacancyIds
     */
    public function setVacancyIds(?array $vacancyIds): void
    {
        $this->vacancyIds = $vacancyIds;
    }
}
