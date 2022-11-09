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

namespace OrangeHRM\Leave\Exception;

use Exception;

class LeaveAllocationServiceException extends Exception
{
    /**
     * @return static
     */
    public static function overlappingLeavesFound(): self
    {
        return new self('Overlapping Leave Request Found');
    }

    /**
     * @return static
     */
    public static function workShiftLengthExceeded(): self
    {
        return new self('Work Shift Length Exceeded');
    }

    /**
     * @return static
     */
    public static function leaveBalanceExceeded(): self
    {
        return new self('Leave Balance Exceeded');
    }

    /**
     * @return static
     */
    public static function leaveQuotaWillExceed(): self
    {
        return new self('Leave Quota will Exceed');
    }

    /**
     * @return static
     */
    public static function noWorkingDaysSelected(): self
    {
        return new self('Failed to Submit: No Working Days Selected');
    }

    /**
     * @return static
     */
    public static function cannotApplyLeaveBeyondMaxAllowedLeavePeriodEndDate(string $endDate): self
    {
        return new self("Cannot Apply Leave Beyond $endDate");
    }

    /**
     * @return static
     */
    public static function cannotAssignLeaveBeyondMaxAllowedLeavePeriodEndDate(string $endDate): self
    {
        return new self("Cannot Assign Leave Beyond $endDate");
    }
}
