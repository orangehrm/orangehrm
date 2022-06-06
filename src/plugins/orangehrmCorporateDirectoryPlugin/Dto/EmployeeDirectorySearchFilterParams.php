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

namespace OrangeHRM\CorporateDirectory\Dto;

use OrangeHRM\Core\Dto\FilterParams;
use OrangeHRM\Core\Exception\SearchParamException;

class EmployeeDirectorySearchFilterParams extends FilterParams
{

    public const ALLOWED_SORT_FIELDS = [
        'employee.lastName',
        'employee.firstName',
        'employee.middleName',
        'employee.empNumber',
        'jobTitle.jobTitleName',
        'subunit.name',
        'location.name'
    ];

    public const INCLUDE_EMPLOYEES_ONLY_CURRENT = 'onlyCurrent';
    public const INCLUDE_EMPLOYEES_ONLY_PAST = 'onlyPast';
    public const INCLUDE_EMPLOYEES_CURRENT_AND_PAST = 'currentAndPast';

    public const INCLUDE_EMPLOYEES_MAP = [
        1 => self::INCLUDE_EMPLOYEES_ONLY_CURRENT,
        2 => self::INCLUDE_EMPLOYEES_CURRENT_AND_PAST,
        3 => self::INCLUDE_EMPLOYEES_ONLY_PAST,
    ];

    /**
     * @var string|null
     */
    protected ?string $includeEmployees = self::INCLUDE_EMPLOYEES_ONLY_CURRENT;
    /**
     * @var string|null
     */
    protected ?string $name = null;
    /**
     * @var int[]|null
     */
    protected ?array $empNumber = null;
    /**
     * @var int|null
     */
    protected ?int $jobTitleId = null;
    /**
     * @var int|null
     */
    protected ?int $subunitId = null;
    /**
     * @var int|null
     */
    protected ?int $locationId = null;

    public function __construct()
    {
        $this->setSortField('employee.lastName');
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
     * @throws SearchParamException
     */
    public function setIncludeEmployees(?string $includeEmployees): void
    {
        if (in_array($includeEmployees, array_keys(self::INCLUDE_EMPLOYEES_MAP))) {
            $includeEmployees = self::INCLUDE_EMPLOYEES_MAP[$includeEmployees] ?? null;
        }
        if (!is_null($includeEmployees) && !in_array($includeEmployees, array_values(self::INCLUDE_EMPLOYEES_MAP))) {
            throw new SearchParamException('Invalid parameter');
        }
        $this->includeEmployees = $includeEmployees;
    }

    /**
     * @return int[]|null
     */
    public function getEmpNumber(): ?array
    {
        return $this->empNumber;
    }

    /**
     * @param int[]|null $empNumber
     */
    public function setEmpNumber(?array $empNumber): void
    {
        $this->empNumber = $empNumber;
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
    public function getLocationId(): ?int
    {
        return $this->locationId;
    }

    /**
     * @param int|null $locationId
     */
    public function setLocationId(?int $locationId): void
    {
        $this->locationId = $locationId;
    }
}
