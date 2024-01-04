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

namespace OrangeHRM\CorporateDirectory\Dto;

use OrangeHRM\Core\Dto\FilterParams;

class EmployeeDirectorySearchFilterParams extends FilterParams
{
    public const ALLOWED_SORT_FIELDS = [];

    /**
     * @var int[]|null
     */
    protected ?array $empNumbers = null;
    /**
     * @var string|null
     */
    protected ?string $nameOrId = null;
    /**
     * @var int|null
     */
    protected ?int $jobTitleId = null;
    /**
     * @var int|null
     */
    protected ?int $locationId = null;

    public function __construct()
    {
        $this->setSortField('employee.lastName');
    }

    /**
     * @return int[]|null
     */
    public function getEmpNumbers(): ?array
    {
        return $this->empNumbers;
    }

    /**
     * @param int[]|null $empNumbers
     */
    public function setEmpNumbers(?array $empNumbers): void
    {
        $this->empNumbers = $empNumbers;
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
