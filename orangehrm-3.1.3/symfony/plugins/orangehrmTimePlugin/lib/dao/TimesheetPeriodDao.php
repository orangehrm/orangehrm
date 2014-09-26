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
class TimesheetPeriodDao {

    protected $configDao;
    
    public function setConfigDao($configDao) {
        $this->configDao = $configDao;
    }
    
    public function getConfigDao() {
        
        if (is_null($this->configDao)) {
            $this->configDao = new ConfigDao();
        }
        
        return $this->configDao;
        
    }

	public function getDefinedTimesheetPeriod() {

		try {
            return $this->getConfigDao()->getValue(ConfigService::KEY_TIMESHEET_PERIOD_AND_START_DATE);
 		} catch (Exception $ex) {
			throw new DaoException($ex->getMessage());
		}
	}

	public function isTimesheetPeriodDefined() {

		try {
            return $this->getConfigDao()->getValue(ConfigService::KEY_TIMESHEET_PERIOD_SET);
		} catch (Exception $ex) {
			throw new DaoException($ex->getMessage());
		}
	}

	public function setTimesheetPeriod() {

		try {
			$query = Doctrine_Query::create()
					->update('Config')
					->set("`value`",'?','Yes')
					->where("`key` ='timesheet_period_set' ");
	
			$query->execute();
			return true;
			
		} catch (Exception $ex) {
			throw new DaoException($ex->getMessage());
		}
	}

	public function setTimesheetPeriodAndStartDate($xml) {

		try {
			$query = Doctrine_Query::create()
					->update('Config')
					->set('`value`', '?', $xml)
					->where("`key` ='timesheet_period_and_start_date' ");
			$query->execute();
			return true;
			
		} catch (Exception $ex) {
			throw new DaoException($ex->getMessage());
		}
	}

}

