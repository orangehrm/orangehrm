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
class WorkShiftDao extends BaseDao {

	/**
	 *
	 * @return type 
	 */
	public function getWorkShiftList() {

		try {
			$q = Doctrine_Query :: create()
				->from('WorkShift')
				->orderBy('name ASC');
			return $q->execute();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	public function getWorkShiftById($workShiftId) {

		try {
			return Doctrine :: getTable('WorkShift')->find($workShiftId);
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	public function getWorkShiftEmployeeListById($workShiftId) {

		try {
			$q = Doctrine_Query :: create()
				->from('EmployeeWorkShift')
				->where('work_shift_id = ?', $workShiftId);
			return $q->execute();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}
	
	public function getWorkShiftEmployeeList(){

		try {
			$q = Doctrine_Query :: create()
				->from('EmployeeWorkShift');
			return $q->execute();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	public function updateWorkShift(WorkShift $workShift) {
		
		try {
			$q = Doctrine_Query:: create()->update('WorkShift')
				->set('name', '?', $workShift->name)
				->set('hours_per_day', '?', $workShift->hoursPerDay)
				->where('id = ?', $workShift->id);
			return $q->execute();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}
}

?>
