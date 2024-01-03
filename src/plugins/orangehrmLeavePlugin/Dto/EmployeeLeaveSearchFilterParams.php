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

use DateTime;
use OrangeHRM\Entity\Leave;
use InvalidArgumentException;
use OrangeHRM\Leave\Traits\Service\LeaveRequestServiceTrait;

class EmployeeLeaveSearchFilterParams extends LeaveSearchFilterParams
{
    use LeaveRequestServiceTrait;

    public const INCLUDE_EMPLOYEES_ONLY_CURRENT = 'onlyCurrent';
    public const INCLUDE_EMPLOYEES_ONLY_PAST = 'onlyPast';
    public const INCLUDE_EMPLOYEES_CURRENT_AND_PAST = 'currentAndPast';

    public const INCLUDE_EMPLOYEES = [
        self::INCLUDE_EMPLOYEES_ONLY_CURRENT,
        self::INCLUDE_EMPLOYEES_ONLY_PAST,
        self::INCLUDE_EMPLOYEES_CURRENT_AND_PAST,
    ];

    public const LEAVE_STATUSES = [
        Leave::LEAVE_STATUS_LEAVE_REJECTED,
        Leave::LEAVE_STATUS_LEAVE_CANCELLED,
        Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL,
        Leave::LEAVE_STATUS_LEAVE_APPROVED,
        Leave::LEAVE_STATUS_LEAVE_TAKEN,
    ];

    /**
     * @var DateTime|null
     */
    private ?DateTime $fromDate = null;

    /**
     * @var DateTime|null
     */
    private ?DateTime $toDate = null;

    /**
     * @var array|null
     */
    private ?array $statuses = null;

    /**
     * @var int|null
     */
    private ?int $empNumber = null;

    /**
     * @var string|null
     */
    private ?string $includeEmployees = self::INCLUDE_EMPLOYEES_ONLY_CURRENT;

    /**
     * @return string[]|null
     */
    public function getStatuses(): ?array
    {
        return $this->statuses;
    }

    /**
     * @param array|null $statuses
     */
    public function setStatuses(?array $statuses): void
    {
        if (!empty(array_diff($statuses, self::LEAVE_STATUSES))) {
            throw new InvalidArgumentException();
        }
        $statusMap = $this->getLeaveRequestService()->getAllLeaveStatusesAssoc();
        $this->statuses = [];
        foreach ($statuses as $status) {
            $this->statuses[] = $statusMap[$status];
        }
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
