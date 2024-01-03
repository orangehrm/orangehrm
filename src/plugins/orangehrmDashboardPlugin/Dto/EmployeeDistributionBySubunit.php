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

namespace OrangeHRM\Dashboard\Dto;

class EmployeeDistributionBySubunit
{
    private array  $subunitCountPairs;
    private int $otherEmployeeCount;
    private int $totalSubunitCount;
    private int $unassignedEmployeeCount;
    private int $limit;

    /**
     * @param array $subunitCountPairs
     * @param int   $otherEmployeeCount
     * @param int   $totalSubunitCount
     * @param int   $unassignedEmployeeCount
     * @param int   $limit
     */
    public function __construct(
        array $subunitCountPairs,
        int $otherEmployeeCount,
        int $totalSubunitCount,
        int $unassignedEmployeeCount,
        int $limit
    ) {
        $this->subunitCountPairs = $subunitCountPairs;
        $this->otherEmployeeCount = $otherEmployeeCount;
        $this->totalSubunitCount = $totalSubunitCount;
        $this->unassignedEmployeeCount = $unassignedEmployeeCount;
        $this->limit = $limit;
    }

    /**
     * @return array
     */
    public function getSubunitCountPairs(): array
    {
        return $this->subunitCountPairs;
    }

    /**
     * @return int
     */
    public function getOtherEmployeeCount(): int
    {
        return $this->otherEmployeeCount;
    }

    /**
     * @return int
     */
    public function getUnassignedEmployeeCount(): int
    {
        return $this->unassignedEmployeeCount;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getTotalSubunitCount(): int
    {
        return $this->totalSubunitCount;
    }
}
