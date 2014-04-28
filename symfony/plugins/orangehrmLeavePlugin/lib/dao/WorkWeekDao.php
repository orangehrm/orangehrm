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

class WorkWeekDao extends BaseDao {

    /**
     * Add and Update WorkWeek
     * @param Weekends $dayOff
     * @return boolean
     */
    public function saveWorkWeek(WorkWeek $workWeek) {
        try {
            $workWeek->save();
            return $workWeek;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Read WorkWeek
     * @param $day
     * @return WorkWeek
     */
    public function readWorkWeek($operationalCountryId = null) {
        try {
            $query = Doctrine_Query::create()
                    ->from('WorkWeek');
            $query = $this->addOperationalCountryFilter($query, $operationalCountryId);

            $workWeek = $query->execute()->get(0);

            return $workWeek;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Delete WorkWeek
     * @param int $id
     * @return boolean
     */
    public function deleteWorkWeek($id) {
        try {
            $q = Doctrine_Query::create()
                    ->delete('WorkWeek')
                    ->where('id = ?', $id);
            
            $affectedRecords = $q->execute();

            return ($affectedRecords > 0);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Get WorkWeek List
     * @return WorkWeek Collection
     */
    public function getWorkWeekList($offset = 0, $limit = 10) {
        try {
            $query = Doctrine_Query::create()
                    ->from('WorkWeek')
                    ->offset($offset)
                    ->limit($limit);

            $WorkWeekList = $query->execute();
            return $WorkWeekList;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Check whether the given date is a weekend.
     * @param date $date
     * @param bool $isFullDay
     * @param int $operationalCountryId
     * @return bool true on success and false on failiure
     */
    public function isWeekend($date, $isFullDay = true, $operationalCountryId = null) {
        try {
            $query = Doctrine_Query::create()
                    ->from('WorkWeek');

            $query = $this->addOperationalCountryFilter($query, $operationalCountryId);

            $workWeek = $query->execute()->get(0);

            $dayNumber = date('N', strtotime($date));

            if ($isFullDay) {
                return ($workWeek->getLength($dayNumber) == WorkWeek::WORKWEEK_LENGTH_WEEKEND);
            } else {
                return ($workWeek->getLength($dayNumber) == WorkWeek::WORKWEEK_LENGTH_HALF_DAY);
            }
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param int $operationalCountryId
     * @return WorkWeek
     */
    public function searchWorkWeek($searchParams = array()) {
        try {
            $query = Doctrine_Query::create()
                    ->from('WorkWeek');
            
            foreach ($searchParams as $field => $value) {
                if ($field == 'operational_country_id') {
                    $query = $this->addOperationalCountryFilter($query, $value);
                } else {
                    $query->addWhere("{$field} = ?", $value);
                }
            }

            return $query->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param type $operationalCountryId 
     * @return Doctrine_Query
     */
    protected function addOperationalCountryFilter(Doctrine_Query $query, $operationalCountryId) {
        if (is_null($operationalCountryId)) {
            $query->addWhere('id = ?', WorkWeek::DEFAULT_WORK_WEEK_ID);
        } else {
            $query->where('operational_country_id = ?', $operationalCountryId);
        }
        
        return $query;
    }

}
