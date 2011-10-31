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

class ProjectService extends BaseService {
	
	private $projectDao;

	/**
	 * Construct
	 */
	public function __construct() {
		$this->projectDao = new ProjectDao();
	}

	/**
	 *
	 * @return <type>
	 */
	public function getProjectDao() {
		return $this->projectDao;
	}

	/**
	 *
	 * @param UbCoursesDao $UbCoursesDao 
	 */
	public function setProjectDao(ProjectDao $projectDao) {
		$this->projectDao = $projectDao;
	}

	/**
	 *
	 * @param type $noOfRecords
	 * @param type $offset
	 * @param type $sortField
	 * @param type $sortOrder
	 * @return type 
	 */
	public function getProjectList($noOfRecords, $offset, $sortField, $sortOrder){
		return $this->projectDao->getProjectList($noOfRecords, $offset, $sortField, $sortOrder);
	}
	
	public function getProjectCount(){
		return $this->projectDao->getProjectCount();
	}
	
	public function deleteProject($projectId){
		return $this->projectDao->deleteProject($projectId);
	}
}

?>
