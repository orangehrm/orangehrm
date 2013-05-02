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
 *
 */

/**
 * Description of LeaveConfigurationService
 */
class LeaveConfigurationService extends ConfigService {
    
    const KEY_LEAVE_ENTITLEMENT_CONSUMPTION_STRATEGY = "leave.entitlement_consumption_algorithm";
    const KEY_LEAVE_WORK_SCHEDULE_IMPLEMENTATION = "leave.work_schedule_implementation";
    const KEY_INCLUDE_PENDING_LEAVE_IN_BALANCE = 'leave.include_pending_leave_in_balance';
    
    protected static $includePendingLeaveInBalance = null;
    
    protected static $leaveConsumptionStrategy = null;
    
    public function getLeaveEntitlementConsumptionStrategy($forceReload = false) {
        if ($forceReload || is_null(self::$leaveConsumptionStrategy)) {
            self::$leaveConsumptionStrategy = $this->_getConfigValue(self::KEY_LEAVE_ENTITLEMENT_CONSUMPTION_STRATEGY);
        }
        
        return self::$leaveConsumptionStrategy;        
    }
    
    public function getWorkScheduleImplementation() {
        return $this->_getConfigValue(self::KEY_LEAVE_WORK_SCHEDULE_IMPLEMENTATION);
    }
    
    public function includePendingLeaveInBalance($forceReload = false) {
        $include = true;
        
        if ($forceReload || is_null(self::$includePendingLeaveInBalance)) {
            self::$includePendingLeaveInBalance = $this->_getConfigValue(self::KEY_INCLUDE_PENDING_LEAVE_IN_BALANCE);
        }
        
        if (self::$includePendingLeaveInBalance == 0) {
            $include = false;
        }
        
        return $include;
    }
}
