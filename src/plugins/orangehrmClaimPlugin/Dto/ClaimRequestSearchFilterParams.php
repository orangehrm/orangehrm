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

namespace OrangeHRM\Claim\Dto;

use DateTime;
use OrangeHRM\Core\Dto\FilterParams;
use OrangeHRM\ORM\ListSorter;

class ClaimRequestSearchFilterParams extends FilterParams
{
    public const INCLUDE_EMPLOYEES_ONLY_CURRENT = 'onlyCurrent';
    public const INCLUDE_EMPLOYEES_ONLY_PAST = 'onlyPast';
    public const INCLUDE_EMPLOYEES_CURRENT_AND_PAST = 'currentAndPast';

    public const INCLUDE_EMPLOYEES = [
        self::INCLUDE_EMPLOYEES_ONLY_CURRENT,
        self::INCLUDE_EMPLOYEES_ONLY_PAST,
        self::INCLUDE_EMPLOYEES_CURRENT_AND_PAST,
    ];
    public const ALLOWED_SORT_FIELDS = [
        'claimRequest.referenceId',
        'claimRequest.status',
        'claimEvent.name',
        'claimRequest.submittedDate'
    ];

    /**
     * @var string|null
     */
    private ?string $includeEmployees = self::INCLUDE_EMPLOYEES_ONLY_CURRENT;

    /**
     * @var string|null
     */
    protected ?string $referenceId = null;

    /**
     * @var int[]|null
     */
    protected ?array $empNumbers = null;

    /**
     * @var int|null
     */
    protected ?int $eventId = null;

    /**
     * @var string|null
     */
    protected ?string $status = null;

    /**
     * @var DateTime|null
     */
    protected ?DateTime $fromDate = null;

    /**
     * @var DateTime|null
     */
    protected ?DateTime $toDate = null;

    public function __construct()
    {
        $this->setSortField('claimRequest.referenceId');
        $this->setSortOrder(ListSorter::DESCENDING);
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

    /**
     * @return string|null
     */
    public function getReferenceId(): ?string
    {
        return $this->referenceId;
    }

    /**
     * @param string|null $referenceId
     */
    public function setReferenceId(?string $referenceId): void
    {
        $this->referenceId = $referenceId;
    }

    /**
     * @return int|null
     */
    public function getEventId(): ?int
    {
        return $this->eventId;
    }

    /**
     * @param int|null $eventId
     */
    public function setEventId(?int $eventId): void
    {
        $this->eventId = $eventId;
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
        if ($toDate instanceof DateTime) {
            $toDate->setTime(23, 59, 59);
        }
        $this->toDate = $toDate;
    }

    /**
     * @return array|null
     */
    public function getEmpNumbers(): ?array
    {
        return $this->empNumbers;
    }

    /**
     * @param array|null $empNumbers
     */
    public function setEmpNumbers(?array $empNumbers): void
    {
        $this->empNumbers = $empNumbers;
    }
}
