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



	public function getDefinedTimesheetPeriod() {

		try {
			$query = Doctrine_Query::create()
					->from('Config')
					->where('key = ?', 'timesheet_period_and_start_date');
			$xmlString = $query->execute();

			return $xmlString[0]->getValue();
		} catch (Exception $ex) {
			throw new DaoException($ex->getMessage());
		}
	}

	public function isTimesheetPeriodDefined() {

		try {
			$query = Doctrine_Query::create()
					->from('Config')
					->where('key = ?', 'timesheet_period_set');
			
			$isAllowed = $query->execute();
		
			return $isAllowed[0]->getValue();
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

