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
 * Description of EntitlementConsumptionStrategy
 */
interface EntitlementConsumptionStrategy {
    
    
    // Deprecated, no longer in use
    public function getAvailableEntitlements($empNumber, $leaveType, $leaveDates, $allowNoEntitlements = false);
    
    public function handleLeaveCreate($empNumber, $leaveType, $leaveDates, $allowNoEntitlements = false);
    
    public function handleLeaveCancel($leave);
    
    public function handleEntitlementStatusChange();
    
    public function handleLeavePeriodChange($leavePeriodForToday, $oldStartMonth, $oldStartDay, $newStartMonth, $newStartDay);
    
    /**
     * Get date limits for considering leave without entitlements in leave balance for the given start, end date.
     * 
     * @param String $balanceStartDate Date string for balance start date
     * @param String $balanceEndDate Date string for balance end date
     * @param int $empNumber
     * @param int $leaveTypeId
     * 
     * @return Mixed Array with two dates giving period inside which leave without entitlements should count towards the leave balance.
     *               If false is returned, leave without entitlements are not considered for leave balance.
     * 
     */
    public function getLeaveWithoutEntitlementDateLimitsForLeaveBalance($balanceStartDate, $balanceEndDate, $empNumber = null, $leaveTypeId = null);
    
    /**
     * Get leave period
     * 
     * @param string $date Date for which leave period is required
     * @param int $empNumber Employee Number
     * @param int $leaveTypeId Leave Type ID
     * @return array Array with start date at index 0 and end date at index 1
     */
    public function getLeavePeriod($date, $empNumber = null, $leaveTypeId = null);
}
