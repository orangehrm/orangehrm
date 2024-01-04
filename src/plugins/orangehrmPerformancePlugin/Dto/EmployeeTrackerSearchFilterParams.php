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

namespace OrangeHRM\Performance\Dto;

use OrangeHRM\Core\Dto\FilterParams;
use OrangeHRM\ORM\ListSorter;

class EmployeeTrackerSearchFilterParams extends FilterParams
{
    public const ALLOWED_SORT_FIELDS = [
        'tracker.trackerName',
        'employee.lastName',
        'tracker.addedDate',
        'tracker.modifiedDate'
    ];

    public const INCLUDE_EMPLOYEES_ONLY_CURRENT = 'onlyCurrent';
    public const INCLUDE_EMPLOYEES_ONLY_PAST = 'onlyPast';
    public const INCLUDE_EMPLOYEES_CURRENT_AND_PAST = 'currentAndPast';

    public const INCLUDE_EMPLOYEES = [
        self::INCLUDE_EMPLOYEES_ONLY_CURRENT,
        self::INCLUDE_EMPLOYEES_ONLY_PAST,
        self::INCLUDE_EMPLOYEES_CURRENT_AND_PAST,
    ];

    private ?array $trackerIds = null;
    private ?int $empNumber = null;
    private string $includeEmployees = self::INCLUDE_EMPLOYEES_ONLY_CURRENT;

    public function __construct()
    {
        $this->setSortField('tracker.modifiedDate');
        $this->setSortOrder(ListSorter::DESCENDING);
    }

    /**
     * @return array|null
     */
    public function getTrackerIds(): ?array
    {
        return $this->trackerIds;
    }

    /**
     * @param array|null $trackerIds
     */
    public function setTrackerIds(?array $trackerIds): void
    {
        $this->trackerIds = $trackerIds;
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
     * @return string
     */
    public function getIncludeEmployees(): string
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
