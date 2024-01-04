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

class LocationEmployeeCount
{
    private int $locationId;
    private string $locationName;
    private int $employeeCount;

    /**
     * @param int $locationId
     * @param string $locationName
     * @param int $employeeCount
     */
    public function __construct(
        int $locationId,
        string $locationName,
        int $employeeCount
    ) {
        $this->locationId = $locationId;
        $this->locationName = $locationName;
        $this->employeeCount = $employeeCount;
    }

    /**
     * @return int
     */
    public function getLocationId(): int
    {
        return $this->locationId;
    }

    /**
     * @return string
     */
    public function getLocationName(): string
    {
        return $this->locationName;
    }

    /**
     * @return int
     */
    public function getEmployeeCount(): int
    {
        return $this->employeeCount;
    }
}
