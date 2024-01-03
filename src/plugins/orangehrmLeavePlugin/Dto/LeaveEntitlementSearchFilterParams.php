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

class LeaveEntitlementSearchFilterParams extends DateRangeSearchFilterParams
{
    public const ALLOWED_SORT_FIELDS = ['entitlement.fromDate', 'leaveType.name'];

    private ?int $empNumber = null;

    private ?array $empNumbers = null;

    private ?int $leaveTypeId = null;

    private ?bool $leaveTypeDeleted = null;

    public function __construct()
    {
        $this->setSortField('entitlement.fromDate');
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
     * @return int|null
     */
    public function getLeaveTypeId(): ?int
    {
        return $this->leaveTypeId;
    }

    /**
     * @param int|null $leaveTypeId
     */
    public function setLeaveTypeId(?int $leaveTypeId): void
    {
        $this->leaveTypeId = $leaveTypeId;
    }

    /**
     * @return bool|null
     */
    public function getLeaveTypeDeleted(): ?bool
    {
        return $this->leaveTypeDeleted;
    }

    /**
     * @param bool|null $leaveTypeDeleted
     */
    public function setLeaveTypeDeleted(?bool $leaveTypeDeleted): void
    {
        $this->leaveTypeDeleted = $leaveTypeDeleted;
    }
}
