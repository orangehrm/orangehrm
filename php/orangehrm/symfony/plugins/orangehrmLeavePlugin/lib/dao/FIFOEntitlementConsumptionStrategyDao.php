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
 * Description of FIFOEntitlementConsumptionStrategyDao
 */
class FIFOEntitlementConsumptionStrategyDao extends BaseDao {
    
    public function handleLeavePeriodChange($leavePeriodForToday, $oldMonth, $oldDay, $newMonth, $newDay) {
        try {
            
            $pdo = Doctrine_Manager::connection()->getDbh();

            $leavePeriodStartDate = new DateTime($leavePeriodForToday[0]);
            $leavePeriodEndDate = new DateTime($leavePeriodForToday[1]);
            
            // If current leave period start date is 1/1 and new date is 1/1, 
            if ($leavePeriodStartDate->format('n') == 1 && $leavePeriodStartDate->format('j') == 1 && $newMonth == 1 && $newDay == 1) {
                $newEndDateForCurrentPeriod = $leavePeriodStartDate->format('Y') . '-12-31';
            } else {
                $tmp = new DateTime();
                $tmp->setDate($leavePeriodStartDate->format('Y') + 1, $newMonth, $newDay);
                $tmp->sub(new DateInterval('P1D'));
                $newEndDateForCurrentPeriod = $tmp->format('Y-m-d');
            }
                        
         $queryCurrentPeriod = 'UPDATE ohrm_leave_entitlement e SET ' .
                    "e.to_date = :new_end_date " .                    
                    "WHERE e.deleted = 0 AND e.from_date = :fromDate AND e.to_date = :toDate ";
                    
            $stmt = $pdo->prepare($queryCurrentPeriod);                        
            $stmt->execute(array(':new_end_date' => $newEndDateForCurrentPeriod, ':fromDate'=> $leavePeriodForToday[0],':toDate'=>$leavePeriodForToday[1]));
                        
           $queryFuturePeriods = 'UPDATE ohrm_leave_entitlement e SET ' .
                    "e.from_date = CONCAT(YEAR(e.from_date), '-',:newMonth , '-', :newDay), " .
                    "e.to_date = DATE_SUB(CONCAT(YEAR(e.from_date) + 1, '-', :newMonth, '-', :newDay), INTERVAL 1 DAY) " .                    
                    "WHERE e.deleted = 0 AND MONTH(e.from_date) = :oldMonth AND DAY(e.from_date) = :oldDay AND e.from_date > :fromDate ";
           
            $stmt = $pdo->prepare($queryFuturePeriods);                        
            $stmt->execute(array(':newMonth'=>$newMonth,':newDay'=>$newDay,':oldMonth'=>$oldMonth,':oldDay'=>$oldDay,':fromDate'=>$leavePeriodForToday[1]));


        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), 0, $e);
        }        
    }
}
