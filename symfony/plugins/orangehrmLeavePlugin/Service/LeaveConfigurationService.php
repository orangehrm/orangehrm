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

namespace OrangeHRM\Leave\Service;

use OrangeHRM\Core\Service\ConfigService;

class LeaveConfigurationService extends ConfigService
{
    public const KEY_LEAVE_ENTITLEMENT_CONSUMPTION_STRATEGY = "leave.entitlement_consumption_algorithm";
    public const KEY_LEAVE_WORK_SCHEDULE_IMPLEMENTATION = "leave.work_schedule_implementation";
    public const KEY_INCLUDE_PENDING_LEAVE_IN_BALANCE = 'leave.include_pending_leave_in_balance';
    public const KEY_LEAVE_PERIOD_STATUS = 'leave.leavePeriodStatus';
    public const KEY_LEAVE_PERIOD_DEFINED = "leave_period_defined";

    /**
     * @return string
     */
    public function getLeaveEntitlementConsumptionStrategy(): string
    {
        return $this->_getConfigValue(self::KEY_LEAVE_ENTITLEMENT_CONSUMPTION_STRATEGY);
    }

    /**
     * @return string
     */
    public function getWorkScheduleImplementation(): string
    {
        return $this->_getConfigValue(self::KEY_LEAVE_WORK_SCHEDULE_IMPLEMENTATION);
    }

    /**
     * @return bool
     */
    public function includePendingLeaveInBalance(): bool
    {
        $includePendingLeaveInBalance = $this->_getConfigValue(self::KEY_INCLUDE_PENDING_LEAVE_IN_BALANCE);
        return $includePendingLeaveInBalance !== '0';
    }

    /**
     * @param int $value
     */
    public function setLeavePeriodStatus(int $value): void
    {
        $this->_setConfigValue(self::KEY_LEAVE_PERIOD_STATUS, $value);
    }

    /**
     * @return int
     *
     * @see LeavePeriodService::LEAVE_PERIOD_STATUS_FORCED
     * @see LeavePeriodService::LEAVE_PERIOD_STATUS_NOT_FORCED
     * @see LeavePeriodService::LEAVE_PERIOD_STATUS_NOT_APPLICABLE
     */
    public function getLeavePeriodStatus(): int
    {
        return $this->_getConfigValue(self::KEY_LEAVE_PERIOD_STATUS);
    }

    /**
     * @param bool $value
     */
    public function setLeavePeriodDefined(bool $value): void
    {
        $this->_setConfigValue(self::KEY_LEAVE_PERIOD_DEFINED, $value ? 'Yes' : 'No');
    }

    /**
     * Get Value: Whether leave period has been set
     * @return bool Returns true if leave period has been set
     */
    public function isLeavePeriodDefined(): bool
    {
        $val = $this->_getConfigValue(self::KEY_LEAVE_PERIOD_DEFINED);
        return ($val == 'Yes');
    }
}
