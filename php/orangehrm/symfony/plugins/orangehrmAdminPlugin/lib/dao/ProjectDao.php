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

class ProjectDao extends BaseDao {
	
	public function getProjectList($limit=50, $offset=0, $sortField='name', $sortOrder='ASC') {

		$sortField = ($sortField == "") ? 'name' : $sortField;
		$sortOrder = ($sortOrder == "") ? 'ASC' : $sortOrder;
		try {
			$q = Doctrine_Query :: create()
				->from('Project')
				->where('deleted = ?', Project::ACTIVE_PROJECT)
				->orderBy($sortField . ' ' . $sortOrder)
				->offset($offset)
				->limit($limit);
			return $q->execute();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}
	
	public function getProjectCount() {

		try {
			$q = Doctrine_Query :: create()
				->from('Project')
				->where('deleted = ?', Project::ACTIVE_PROJECT);
			$count = $q->execute()->count();
			return $count;
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}
	
	public function deleteProject($projectId) {

		try {
			$project = Doctrine :: getTable('Project')->find($projectId);
			$project->setDeleted(Project::DELETED_PROJECT);
			$project->save();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}
	
	public function getProjectById($projectId) {

		try {
			return Doctrine :: getTable('Project')->find($projectId);
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}
}

?>
