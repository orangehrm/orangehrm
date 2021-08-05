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
}
