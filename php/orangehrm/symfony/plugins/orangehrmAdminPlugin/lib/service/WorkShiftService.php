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
class WorkShiftService extends BaseService {
	
	private $workShiftDao;

	/**
	 * Construct
	 */
	public function __construct() {
		$this->workShiftDao = new WorkShiftDao();
	}

	/**
	 *
	 * @return <type>
	 */
	public function getWorkShiftDao() {
		return $this->workShiftDao;
	}

	/**
	 *
	 * @param WorkShiftDao $workShiftDao 
	 */
	public function setWorkShiftDao(WorkShiftDao $workShiftDao) {
		$this->workShiftDao = $workShiftDao;
	}
	
	public function getWorkShiftList(){
		return $this->workShiftDao->getWorkShiftList();
	}
	
	public function getWorkShiftById($workShiftId){
		return $this->workShiftDao->getWorkShiftById($workShiftId);
	}
	
	public function getWorkShiftEmployeeListById($workShiftId){
		return $this->workShiftDao->getWorkShiftEmployeeListById($workShiftId);
	}
	
    public function getWorkShiftEmployeeNameListById($workShiftId) {
        return $this->workShiftDao->getWorkShiftEmployeeNameListById($workShiftId);
	}
	
	public function getWorkShiftEmployeeList(){
		return $this->workShiftDao->getWorkShiftEmployeeList();
	}
	
	public function updateWorkShift($workShift) {
		return $this->workShiftDao->updateWorkShift($workShift);
	}
	
    public function getWorkShiftEmployeeIdList(){
        return $this->workShiftDao->getWorkShiftEmployeeIdList();
    }
    
    public function saveEmployeeWorkShiftCollection(Doctrine_Collection $empWorkShiftCollection) {
        $this->workShiftDao->saveEmployeeWorkShiftCollection($empWorkShiftCollection);
    }
}

?>
