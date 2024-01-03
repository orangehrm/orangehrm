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

namespace OrangeHRM\Time\Dto;

use OrangeHRM\Core\Dto\FilterParams;
use OrangeHRM\Core\Exception\SearchParamException;
use OrangeHRM\ORM\ListSorter;

class EmployeeTimesheetListSearchFilterParams extends FilterParams
{
    public const ALLOWED_SORT_FIELDS = ['timesheet.startDate'];

    /**
     * @var int[]|null
     */
    protected ?array $employeeNumbers = null;

    /**
     * @var array|null
     */
    protected ?array $actionableStatesList = null;

    /**
     * @throws SearchParamException
     */
    public function __construct()
    {
        $this->setSortField('timesheet.startDate');
        $this->setSortOrder(ListSorter::DESCENDING);
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
     * @return array|null
     */
    public function getActionableStatesList(): ?array
    {
        return $this->actionableStatesList;
    }

    /**
     * @param array|null $actionableStatesList
     */
    public function setActionableStatesList(?array $actionableStatesList): void
    {
        $this->actionableStatesList = $actionableStatesList;
    }
}
