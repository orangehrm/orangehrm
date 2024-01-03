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

use DateTime;
use OrangeHRM\Core\Dto\FilterParams;
use OrangeHRM\ORM\ListSorter;

class CandidateSearchFilterParams extends FilterParams
{
    public const ALLOWED_SORT_FIELDS = [
        'vacancy.name',
        'candidate.lastName',
        'hiringManager.lastName',
        'candidate.dateOfApplication',
        'candidateVacancy.status'
    ];

    /**
     * @var int|null
     */
    protected ?int $candidateId = null;

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
    protected ?int $jobTitleId = null;

    /**
     * @var int|null
     */
    protected ?int $hiringManagerId = null;

    /**
     * @var string|null
     */
    protected ?string $status = null;

    /**
     * @var int|null
     */
    protected ?int $methodOfApplication = null;

    /**
     * @var DateTime|null
     */
    protected ?DateTime $fromDate = null;

    /**
     * @var DateTime|null
     */
    protected ?DateTime $toDate = null;

    /**
     * @var string|null
     */
    protected ?string $keywords = null;

    /**
     * @var int|null
     */
    protected ?int $candidateVacancy = null;

    /**
     * @var string|null
     */
    protected ?string $candidateName = null;

    /**
     * @var array|null
     */
    protected ?array $candidateIds = null;

    /**
     * @var bool|null
     */
    protected ?bool $consentToKeepData = null;

    public function __construct()
    {
        $this->setSortField('candidate.dateOfApplication');
        $this->setSortOrder(ListSorter::DESCENDING);
    }

    /**
     * @return int|null
     */
    public function getCandidateId(): ?int
    {
        return $this->candidateId;
    }

    /**
     * @param int|null $candidateId
     */
    public function setCandidateId(?int $candidateId): void
    {
        $this->candidateId = $candidateId;
    }

    /**
     * @return int|null
     */
    public function getVacancyId(): ?int
    {
        return $this->vacancyId;
    }

    /**
     * @param int|null $vacancyId
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
     * @param int|null $employeeId
     */
    public function setEmployeeId(?int $employeeId): void
    {
        $this->employeeId = $employeeId;
    }

    /**
     * @return int|null
     */
    public function getCandidateVacancy(): ?int
    {
        return $this->candidateVacancy;
    }

    /**
     * @param int|null $candidateVacancy
     */
    public function setCandidateVacancy(?int $candidateVacancy): void
    {
        $this->candidateVacancy = $candidateVacancy;
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
    public function getHiringManagerId(): ?int
    {
        return $this->hiringManagerId;
    }

    /**
     * @param int|null $hiringManagerId
     */
    public function setHiringManagerId(?int $hiringManagerId): void
    {
        $this->hiringManagerId = $hiringManagerId;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     */
    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return int|null
     */
    public function getMethodOfApplication(): ?int
    {
        return $this->methodOfApplication;
    }

    /**
     * @param int|null $methodOfApplication
     */
    public function setMethodOfApplication(?int $methodOfApplication): void
    {
        $this->methodOfApplication = $methodOfApplication;
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
     * @return string|null
     */
    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    /**
     * @param string|null $keywords
     */
    public function setKeywords(?string $keywords): void
    {
        $this->keywords = $keywords;
    }

    /**
     * @return string|null
     */
    public function getCandidateName(): ?string
    {
        return $this->candidateName;
    }

    /**
     * @param string|null $candidateName
     */
    public function setCandidateName(?string $candidateName): void
    {
        $this->candidateName = $candidateName;
    }

    /**
     * @return array|null
     */
    public function getCandidateIds(): ?array
    {
        return $this->candidateIds;
    }

    /**
     * @param array|null $candidateIds
     */
    public function setCandidateIds(?array $candidateIds): void
    {
        $this->candidateIds = $candidateIds;
    }

    /**
     * @return bool|null
     */
    public function isConsentToKeepData(): ?bool
    {
        return $this->consentToKeepData;
    }

    /**
     * @param bool|null $consentToKeepData
     */
    public function setConsentToKeepData(?bool $consentToKeepData): void
    {
        $this->consentToKeepData = $consentToKeepData;
    }
}
