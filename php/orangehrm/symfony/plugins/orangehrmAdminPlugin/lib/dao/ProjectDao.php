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

	public function getProjectCount($activeOnly = true) {

		try {
			$q = Doctrine_Query :: create()
				->from('Project');
			if ($activeOnly == true) {
				$q->addWhere('is_deleted = ?', Project::ACTIVE_PROJECT);
			}
			$count = $q->execute()->count();
			return $count;
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	public function deleteProject($projectId) {

		try {
			$project = Doctrine :: getTable('Project')->find($projectId);
			$project->setIsDeleted(Project::DELETED_PROJECT);
			$project->save();
			$this->_deleteRelativeProjectActivitiesForProject($projectId);
			$this->_deleteRelativeProjectAdminsForProject($projectId);
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	private function _deleteRelativeProjectActivitiesForProject($projectId) {

		try {
			$q = Doctrine_Query :: create()
				->from('ProjectActivity')
				->where('is_deleted = ?', ProjectActivity::ACTIVE_PROJECT_ACTIVITY)
				->andWhere('project_id = ?', $projectId);
			$projectActivities = $q->execute();

			foreach ($projectActivities as $projectActivity) {
				$projectActivity->setIsDeleted(ProjectActivity::DELETED_PROJECT_ACTIVITY);
				$projectActivity->save();
			}
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	private function _deleteRelativeProjectAdminsForProject($projectId) {

		try {
			$q = Doctrine_Query :: create()
				->delete('ProjectAdmin pa')
				->where('pa.project_id = ?', $projectId);
			$q->execute();
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

	public function getProjectActivityById($activityId) {

		try {
			return Doctrine :: getTable('ProjectActivity')->find($activityId);
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	public function getAllProjects($activeOnly = true) {

		try {
			$q = Doctrine_Query :: create()
				->from('Project');
			if ($activeOnly == true) {
				$q->addWhere('is_deleted = ?', Project::ACTIVE_PROJECT);
			}
			return $q->execute();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	public function getActivityListByProjectId($projectId) {

		try {
			$q = Doctrine_Query :: create()
				->from('ProjectActivity')
				->where('is_deleted = ?', ProjectActivity::ACTIVE_PROJECT_ACTIVITY)
				->andWhere('project_id = ?', $projectId)
                                ->orderBy('name ASC');
			return $q->execute();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	/**
	 * Retrieve Active Projects
	 * @param string $orderField
	 * @param string $orderBy
	 * @return Project[]
	 */
	public function getActiveProjectList($orderField='project_id', $orderBy='ASC') {
		try {
			$q = Doctrine_Query::create()
				->from('Project')
				->where('is_deleted = ?', Project::ACTIVE_PROJECT)
				->orderBy($orderField . ' ' . $orderBy);

			$projectList = $q->execute();

			if ($projectList[0]->getName() == null) {
				return null;
			}

			return $projectList;
		} catch (Exception $e) {
			throw new AdminServiceException($e->getMessage());
		}
	}
        
    /**
     *Get list of active projects, ordered by customer name, project name.
     * 
     * @return Doctrine_Collection of Project objects. Empty collection if no
     *         active projects available.
     * @throws AdminServiceException 
     */
    public function getActiveProjectsOrderedByCustomer() {
        
        try {
            $q = Doctrine_Query::create()
                    ->from('Project p')
                    ->leftJoin('p.Customer c')
                    ->where('p.is_deleted = ?', Project::ACTIVE_PROJECT)
                    ->orderBy('c.name ASC, p.name ASC');

            $projectList = $q->execute();
            return $projectList;
            
        } catch (Exception $e) {
            throw new AdminServiceException($e->getMessage());
        }
    }

	/**
	 * Retrieve active projects given project ids.
	 * @param integer[] $projectIdArray
	 * @param string $orderField
	 * @param string $orderBy
	 * @return Project[]
	 */
	public function getProjectsByProjectIds($projectIdArray, $orderField='project_id', $orderBy='ASC', $activeOnly = true) {
		try {
			$q = Doctrine_Query::create()
				->from('Project');
			if ($activeOnly == true) {
				$q->addWhere('is_deleted = ?', Project::ACTIVE_PROJECT);
			}
			$q->andWhereIn('project_id', $projectIdArray)
				->orderBy($orderField . ' ' . $orderBy);

			$projectList = $q->execute();

			if ($projectList[0]->getName() == null) {
				return null;
			}

			return $projectList;
		} catch (Exception $e) {
			throw new AdminServiceException($e->getMessage());
		}
	}

	/**
	 * Retrieves records from project admin table given employee number.
	 * @param integer $empNo
	 * @return ProjectAdmin[]
	 */
	public function getProjectAdminByEmpNumber($empNo) {

		try {
			$q = Doctrine_Query::create()
				->from('ProjectAdmin')
				->where('emp_number = ?', $empNo);
			$projectAdmin = $q->execute();

			if ($projectAdmin[0]->getProjectId() == null) {
				return null;
			}

			return $projectAdmin;
		} catch (Exception $e) {
			throw new AdminServiceException($e->getMessage());
		}
	}

	public function getSearchProjectListCount($srchClues, $allowedProjectList) {

		try {
			$q = $this->_buildSearchQuery($srchClues, $allowedProjectList);
			return $q->count();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	public function searchProjects($srchClues, $allowedProjectList) {

		$sortField = ($srchClues['sortField'] == "") ? 'name' : $srchClues['sortField'];
		$sortOrder = ($srchClues['sortOrder'] == "") ? 'ASC' : $srchClues['sortOrder'];
		$offset = ($srchClues['offset'] == "") ? 0 : $srchClues['offset'];
		$limit = ($srchClues['limit'] == "") ? 50 : $srchClues['limit'];

		try {
			$q = $this->_buildSearchQuery($srchClues, $allowedProjectList);
			$q->orderBy($sortField . ' ' . $sortOrder)
				->addWhere('p.is_deleted = ?', Project::ACTIVE_PROJECT)
				->offset($offset)
				->limit($limit);
			return $q->execute();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	private function _buildSearchQuery($srchClues, $allowedProjectList) {

		$q = Doctrine_Query::create()
			->select('p.projectId, p.name, c.name')
			->addSelect('(SELECT GROUP_CONCAT(emp.firstName,\' \',emp.lastName) FROM Employee emp LEFT JOIN emp.projectAdmin pq ON (pq.emp_number = emp.emp_number) WHERE pq.projectId = p.projectId) AS projectAdmins')
			->from('Project p')
			->leftJoin('p.Customer c')
			->leftJoin('p.ProjectAdmin pa')
			->leftJoin('pa.Employee e');

		if (!empty($allowedProjectList)) {
			$q->whereIn('p.projectId', $allowedProjectList);
		}
		if (!empty($srchClues['customer'])) {
			$q->addWhere('c.name = ?', trim($srchClues['customer']));
		}
		if (!empty($srchClues['project'])) {
			$q->addWhere('p.name = ?', trim($srchClues['project']));
		}
		if (!empty($srchClues['projectAdmin'])) {
			$projectAdmin = preg_replace('!\s+!', '%', trim($srchClues['projectAdmin']));
			$projectAdmin = "%" . $projectAdmin . "%";
			$q->addWhere("concat_ws(' ', e.emp_firstname, e.emp_middle_name, e.emp_lastname) LIKE ?", $projectAdmin);
		}

		return $q;
	}

	/**
	 * Returns ProjectAdmin for a given project
	 * @param String $projectId
	 * @returns ProjectAdmin
	 * @throws DaoException
	 */
	public function getProjectAdminByProjectId($projectId) {
		try {
			$q = Doctrine_Query::create()
				->from('ProjectAdmin pa')
				->leftJoin('pa.Employee emp')
				->where("pa.project_id = ?", $projectId);

			return $q->execute();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	/**
	 *
	 * @param type $activitId
	 */
	public function deleteProjectActivities($activitId) {

		try {
			$projectActivity = Doctrine :: getTable('ProjectActivity')->find($activitId);
			$projectActivity->setIsDeleted(ProjectActivity::DELETED_PROJECT_ACTIVITY);
			$projectActivity->save();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	public function hasProjectGotTimesheetItems($projectId) {

		try {
			$q = Doctrine_Query :: create()
				->select("COUNT(*)")
				->from('TimesheetItem ti')
				->leftJoin('ti.Project p')
				->where('p.projectId = ?', $projectId);
			$count = $q->fetchOne(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
			return ($count > 0);
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	public function hasActivityGotTimesheetItems($activityId) {

		try {
			$q = Doctrine_Query :: create()
				->select("COUNT(*)")
				->from('TimesheetItem ti')
				->leftJoin('ti.ProjectActivity p')
				->where('p.activityId = ?', $activityId);
			$count = $q->fetchOne(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
			return ($count > 0);
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	/**
	 *
	 * @param type $customerId
	 * @return type 
	 */
	public function getProjectsByCustomerId($customerId) {

		try {
			$q = Doctrine_Query :: create()
				->from('Project')
				->where('is_deleted = ?', Project::ACTIVE_PROJECT)
				->andWhere('customer_id = ?', $customerId);
			return $q->execute();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	public function getProjectListForUserRole($role, $empNumber) {

		try {
			$q = Doctrine_Query :: create()
				->select('p.projectId')
				->from('Project p');
			if ($role == ProjectAdminUserRoleDecorator::PROJECT_ADMIN_USER) {
				$q->leftJoin('p.ProjectAdmin pa')
					->where('pa.emp_number = ?', $empNumber);
			}

			$result = $q->fetchArray();
			$idList = array();
			foreach ($result as $item) {
				$idList[] = $item['projectId'];
			}
			return $idList;
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

}

