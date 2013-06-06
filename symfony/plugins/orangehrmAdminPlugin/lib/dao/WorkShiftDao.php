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

    public function getWorkShiftEmployeeNameListById($workShiftId) {

        try {
            $q = Doctrine_Query :: create()
                    ->select('w.emp_number as empNumber, e.firstName as firstName, e.lastName as lastName, e.middleName as middleName')
                    ->from('EmployeeWorkShift w')
                    ->leftJoin('w.Employee e')
                    ->where('work_shift_id = ?', $workShiftId);

            $employeeNames = $q->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

            return $employeeNames;

            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    public function getWorkShiftEmployeeList() {

        try {
            $q = Doctrine_Query :: create()
                    ->from('EmployeeWorkShift');
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function getWorkShiftEmployeeIdList() {

        try {
            $q = Doctrine_Query :: create()
                    ->select('emp_number')
                    ->from('EmployeeWorkShift');

            $employeeIds = $q->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

            if (is_string($employeeIds)) {
                $employeeIds = array($employeeIds);
            }

            return $employeeIds;

            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    public function updateWorkShift(WorkShift $workShift) {

		try {
			$q = Doctrine_Query:: create()->update('WorkShift')
				->set('name', '?', $workShift->name)
				->set('hours_per_day', '?', $workShift->hoursPerDay)
                                ->set('start_time', '?', $workShift->getStartTime())
                                ->set('end_time', '?', $workShift->getEndTime())
				->where('id = ?', $workShift->id);
			return $q->execute();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
    }

    public function saveEmployeeWorkShiftCollection(Doctrine_Collection $empWorkShiftCollection) {
        try {

            $empWorkShiftCollection->save();

            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

}

?>
