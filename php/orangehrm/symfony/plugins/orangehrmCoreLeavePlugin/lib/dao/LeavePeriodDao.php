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
class LeavePeriodDao extends BaseDao{

	/**
	 * Saves the leave period
	 *
	 * @param LeavePeriod $leavePeriod
	 * @return boolean
	 */
	public function saveLeavePeriod (LeavePeriod $leavePeriod) {
		try {
			if ($leavePeriod->getLeavePeriodId() == '') {

				$idGenService = new IDGeneratorService();
				$idGenService->setEntity($leavePeriod);
				$leavePeriod->setLeavePeriodId($idGenService->getNextID());
			}

			$leavePeriod->save();

			return true ;

		} catch( Exception $e) {
			throw new DaoException( $e->getMessage());
		}
	}

	/**
	 * Returns an instance of LeavePeriod to which the passed timestamp belogs to
	 *
	 * @param int $timestamp
	 * @return LeavePeriod Object of LeavePeriod to which the passed timestamp belogs to
	 */
	public function filterByTimestamp($timestamp) {
		$date = date('Y-m-d', $timestamp);
		$q = Doctrine_Query::create()
		->select("*")
		->from("LeavePeriod lp")
		->where("lp.leave_period_start_date <= ?", $date)
		->andWhere("lp.leave_period_end_date >= ?", $date);

		$result = $q->fetchOne();
      if(!$result instanceof LeavePeriod) {
         return null;
      }
      return $result;
	}

	public function findLastLeavePeriod($date = null) {
		$date = empty($date) ? date('Y-m-d', time()) : $date;
		$q = Doctrine_Query::create()
		->select("*")
		->from("LeavePeriod lp")
		->where("lp.leave_period_end_date < ?", $date);

		
		$result = $q->execute();
		
		if ($result->count() > 0) {
			return $result->end();
		} else {
			return null;
		}
	}




	/**
	 * Get Leave Period list
	 * @return LeavePeriod Collection
	 */
	public function getLeavePeriodList() {

		try {

            $q = Doctrine_Query::create()
            ->from('LeavePeriod lp');

            return $q->execute();

        } catch( Exception $e) {
            throw new DaoException( $e->getMessage());
        }

	}

    public function readLeavePeriod($leavePeriodId) {

        try {
         return Doctrine::getTable('LeavePeriod')->find($leavePeriodId);
        } catch(Exception $e) {
         throw new DaoException($e->getMessage());
        }
        
    }



}
