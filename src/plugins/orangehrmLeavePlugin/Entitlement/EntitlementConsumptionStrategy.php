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

namespace OrangeHRM\Leave\Entitlement;

use DateTime;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Leave\Dto\CurrentAndChangeEntitlement;
use OrangeHRM\Leave\Dto\LeavePeriod;

interface EntitlementConsumptionStrategy
{
    /**
     * @param int $empNumber
     * @param int $leaveTypeId
     * @param array $leaveDates
     * @param bool $allowNoEntitlements
     * @return CurrentAndChangeEntitlement|null
     */
    public function handleLeaveCreate(
        int $empNumber,
        int $leaveTypeId,
        array $leaveDates,
        bool $allowNoEntitlements = false
    ): ?CurrentAndChangeEntitlement;

    public function handleLeaveCancel(Leave $leave): CurrentAndChangeEntitlement;

    public function handleEntitlementStatusChange();

    /**
     * @param LeavePeriod $leavePeriodForToday
     * @param int $oldStartMonth
     * @param int $oldStartDay
     * @param int $newStartMonth
     * @param int $newStartDay
     */
    public function handleLeavePeriodChange(
        LeavePeriod $leavePeriodForToday,
        int $oldStartMonth,
        int $oldStartDay,
        int $newStartMonth,
        int $newStartDay
    ): void;

    /**
     * Get date limits for considering leave without entitlements in leave balance for the given start, end date.
     *
     * @param DateTime $balanceStartDate Date string for balance start date
     * @param DateTime|null $balanceEndDate Date string for balance end date
     * @param int|null $empNumber
     * @param int|null $leaveTypeId
     *
     * @return Mixed Array with two dates giving period inside which leave without entitlements should count towards the leave balance.
     *               If false is returned, leave without entitlements are not considered for leave balance.
     */
    public function getLeaveWithoutEntitlementDateLimitsForLeaveBalance(
        DateTime $balanceStartDate,
        ?DateTime $balanceEndDate = null,
        ?int $empNumber = null,
        ?int $leaveTypeId = null
    );

    /**
     * Get leave period
     *
     * @param DateTime $date Date for which leave period is required
     * @param int|null $empNumber Employee Number
     * @param int|null $leaveTypeId Leave Type ID
     * @return LeavePeriod|null
     */
    public function getLeavePeriod(DateTime $date, ?int $empNumber = null, ?int $leaveTypeId = null): ?LeavePeriod;
}
