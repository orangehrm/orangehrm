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

namespace OrangeHRM\Pim\Dto;

use OrangeHRM\Core\Dto\FilterParams;

class EmployeeSearchFilterParams extends FilterParams
{
    public const ALLOWED_SORT_FIELDS = [
        'employee.lastName',
        'employee.firstName',
        'employee.middleName',
        'employee.empNumber',
        'employee.employeeId',
        'jobTitle.jobTitleName',
        'empStatus.name',
        'subunit.name',
    ];

    public const INCLUDE_EMPLOYEES_ONLY_CURRENT = 'onlyCurrent';
    public const INCLUDE_EMPLOYEES_ONLY_PAST = 'onlyPast';
    public const INCLUDE_EMPLOYEES_CURRENT_AND_PAST = 'currentAndPast';

    /**
     * @var string|null
     */
    protected ?string $includeEmployees = self::INCLUDE_EMPLOYEES_ONLY_CURRENT;
    /**
     * @var string|null
     */
    protected ?string $name = null;
    /**
     * @var string|null
     */
    protected ?string $nameOrId = null;
    /**
     * @var int[]|null
     */
    protected ?array $employeeNumbers = null;
    /**
     * @var string|null
     */
    protected ?string $employeeId = null;
    /**
     * @var int|null
     */
    protected ?int $empStatusId = null;
    /**
     * @var int|null
     */
    protected ?int $jobTitleId = null;
    /**
     * @var int|null
     */
    protected ?int $subunitId = null;
    /**
     * @var int[]|null
     */
    protected ?array $supervisorEmpNumbers = null;

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
     */
    public function setIncludeEmployees(?string $includeEmployees): void
    {
        $this->includeEmployees = $includeEmployees;
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
     * @return int[]|null
     */
    public function getEmployeeNumbers(): ?array
    {
        return $this->employeeNumbers;
    }

    /**
     * @param int[]|null $employeeNumbers
     */
    public function setEmployeeNumbers(?array $employeeNumbers): void
    {
        $this->employeeNumbers = $employeeNumbers;
    }

    /**
     * @return string|null
     */
    public function getEmployeeId(): ?string
    {
        return $this->employeeId;
    }

    /**
     * @param string|null $employeeId
     */
    public function setEmployeeId(?string $employeeId): void
    {
        $this->employeeId = $employeeId;
    }

    /**
     * @return int|null
     */
    public function getEmpStatusId(): ?int
    {
        return $this->empStatusId;
    }

    /**
     * @param int|null $empStatusId
     */
    public function setEmpStatusId(?int $empStatusId): void
    {
        $this->empStatusId = $empStatusId;
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
     * @return int[]|null
     */
    public function getSupervisorEmpNumbers(): ?array
    {
        return $this->supervisorEmpNumbers;
    }

    /**
     * @param int[]|null $supervisorEmpNumbers
     */
    public function setSupervisorEmpNumbers(?array $supervisorEmpNumbers): void
    {
        $this->supervisorEmpNumbers = $supervisorEmpNumbers;
    }
}
