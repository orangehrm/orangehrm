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

/**
 * Employee class
 */
class Employee extends BaseEmployee {
	
	/**
	 * Returns the full name of employee, (first middle last)
	 * 
	 * @return String Full Name 
	 */
	public function getFullName() {
		
	    $fullName = trim($this->firstName) . " " . trim($this->middleName);
	    $fullName = trim( trim($fullName) . " " . trim($this->lastName) ); 
		
		return $fullName;
	}

	/**
	 * Returns the first and last names of employee
	 *
	 * @return String
	 */
	public function getFirstAndLastNames() {

	    $fullName = trim($this->firstName) . " " . trim($this->lastName);

		return $fullName;
	}
	
	/**
	 * Gets the names of all the supervisors of this employee as a comma separated string
	 * Only the first and last name are used.
	 * 
	 * @return String String containing comma separated list of supervisor names. 
	 *                Empty string if employee has no supervisors
	 */
	public function getSupervisorNames() {
	    $supervisorNames = array();
	    
	    foreach ($this->supervisors as $supervisor ){
	        $supervisorNames[] = trim($supervisor->firstName . ' ' . $supervisor->lastName); 
	    }
	    
	    return implode(',', $supervisorNames);
	}
}