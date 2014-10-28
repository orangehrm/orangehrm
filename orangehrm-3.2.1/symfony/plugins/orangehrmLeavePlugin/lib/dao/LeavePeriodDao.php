<?php

/*
 *
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

class LeavePeriodDao extends BaseDao {



    /**
     * Save Leave Period History
     * 
     * @param LeavePeriodHistory $leavePeriodHistory
     * @return \LeavePeriodHistory
     * @throws DaoException
     */
    public function saveLeavePeriodHistory(LeavePeriodHistory $leavePeriodHistory) {
        try {

            $leavePeriodHistory->save();

            return $leavePeriodHistory;

            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Return latest record of leave period history 
     * 
     * @return LeavePeriodHistory leavePeriodHistory
     * @throws DaoException
     */
    public function getCurrentLeavePeriodStartDateAndMonth() {
        try {
            $q = Doctrine_Query::create()
                    ->from("LeavePeriodHistory lph")
                    ->addOrderBy("lph.created_at DESC")
                    ->addOrderBy("id DESC");

            
            return $q->fetchOne();

            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }
    
    /**
     * Get All Leave period list
     */
    public function getLeavePeriodHistoryList( ){
        try {
            $q = Doctrine_Query::create()
                    ->from("LeavePeriodHistory lph")
                    ->addOrderBy("lph.created_at")
                    ->addOrderBy("id ");

           
            return $q->execute();

            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

}
