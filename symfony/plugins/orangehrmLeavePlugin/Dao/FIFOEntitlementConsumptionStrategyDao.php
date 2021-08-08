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

namespace OrangeHRM\Leave\Dao;

use DateInterval;
use DateTime;
use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Leave\Dto\LeavePeriod;

class FIFOEntitlementConsumptionStrategyDao extends BaseDao
{
    use DateTimeHelperTrait;

    /**
     * @param LeavePeriod $leavePeriodForToday
     * @param int $oldMonth
     * @param int $oldDay
     * @param int $newMonth
     * @param int $newDay
     * @throws DaoException
     */
    public function handleLeavePeriodChange(
        LeavePeriod $leavePeriodForToday,
        int $oldMonth,
        int $oldDay,
        int $newMonth,
        int $newDay
    ): void {
        try {
            // TODO:: move queries to doctrine query language
            $conn = $this->getEntityManager()->getConnection();

            $leavePeriodStartDate = $leavePeriodForToday->getStartDate();
            $leavePeriodEndDate = $leavePeriodForToday->getEndDate();

            // If current leave period start date is 1/1 and new date is 1/1, 
            if ($leavePeriodStartDate->format('n') == 1 &&
                $leavePeriodStartDate->format('j') == 1 &&
                $newMonth == 1 && $newDay == 1) {
                $newEndDateForCurrentPeriod = $leavePeriodStartDate->format('Y') . '-12-31';
            } else {
                $tmp = new DateTime();
                $tmp->setDate((int)$leavePeriodStartDate->format('Y') + 1, $newMonth, $newDay);
                $tmp->sub(new DateInterval('P1D'));
                $newEndDateForCurrentPeriod = $tmp->format('Y-m-d');
            }

            $queryCurrentPeriod = 'UPDATE ohrm_leave_entitlement e SET ' .
                "e.to_date = :new_end_date " .
                "WHERE e.deleted = 0 AND e.from_date = :fromDate AND e.to_date = :toDate ";

            $stmt = $conn->prepare($queryCurrentPeriod);
            $stmt->executeQuery(
                [
                    ':new_end_date' => $newEndDateForCurrentPeriod,
                    ':fromDate' => $this->getDateTimeHelper()
                        ->formatDateTimeToYmd($leavePeriodForToday->getStartDate()),
                    ':toDate' => $this->getDateTimeHelper()->formatDateTimeToYmd($leavePeriodForToday->getEndDate()),
                ]
            );

            $queryFuturePeriods = 'UPDATE ohrm_leave_entitlement e SET ' .
                "e.from_date = CONCAT(YEAR(e.from_date), '-',:newMonth , '-', :newDay), " .
                "e.to_date = DATE_SUB(CONCAT(YEAR(e.from_date) + 1, '-', :newMonth, '-', :newDay), INTERVAL 1 DAY) " .
                "WHERE e.deleted = 0 AND MONTH(e.from_date) = :oldMonth AND DAY(e.from_date) = :oldDay AND e.from_date > :fromDate ";

            $stmt = $conn->prepare($queryFuturePeriods);
            $stmt->executeQuery(
                [
                    ':newMonth' => $newMonth,
                    ':newDay' => $newDay,
                    ':oldMonth' => $oldMonth,
                    ':oldDay' => $oldDay,
                    ':fromDate' => $this->getDateTimeHelper()->formatDateTimeToYmd($leavePeriodForToday->getEndDate()),
                ]
            );
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), 0, $e);
        }
    }
}
