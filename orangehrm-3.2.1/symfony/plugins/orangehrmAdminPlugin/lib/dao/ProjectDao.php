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
	
    /**
     * Return an array of Customer Ids for Lits of Project Ids
     * 
     * @version 2.7.1
     * @param Array $projectIdList List of Project Ids
     * @return Array of Customer Ids
     */
    public function getCustomerIdListByProjectId($projectIdList) {
        try {
            
            if (!empty($projectIdList)) {
                
                $escapeString = implode(',', array_fill(0, count($projectIdList), '?'));
                $q = "SELECT p.customer_id 
               			FROM ohrm_project AS p
                		WHERE p.project_id IN ({$escapeString})";
                
                $pdo = Doctrine_Manager::connection()->getDbh();
                $query = $pdo->prepare($q); 
                $query->execute($projectIdList);
                $results = $query->fetchAll(PDO::FETCH_COLUMN, 0);
                
            }
            
            return $results;

        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Return an Array of Project Names
     * 
     * @version 2.7.1
     * @param Array $projectIdList List of Project Ids
     * @param Boolean $excludeDeletedProjects Exclude Deleted Projects or not
     * @return Array of Project Names
     */
    public function getProjectNameList($projectIdList, $excludeDeletedProjects = true) {
 
        try {
            
            if (!empty($projectIdList)) {
                
                $escapeString = implode(',', array_fill(0, count($projectIdList), '?'));
                
                $q = "SELECT p.project_id AS projectId, p.name
                			FROM ohrm_project AS p
                			WHERE p.project_id IN ({$escapeString})";
            
                
                if ($excludeDeletedProjects) {
                    $q .= ' AND p.is_deleted = ' . Project::ACTIVE_PROJECT;
                }

                $pdo = Doctrine_Manager::connection()->getDbh();
                $query = $pdo->prepare($q); 
                $query->execute($projectIdList);
                $results = $query->fetchAll(PDO::FETCH_ASSOC);
            }
            
        	return $results;

        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
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
                        $orderBy = (strcasecmp($orderBy, 'DESC') == 0) ? 'DESC' : 'ASC';
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
                        $orderBy = (strcasecmp($orderBy, 'DESC') == 0) ? 'DESC' : 'ASC';
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

    /**
     * Return Count of Projects
     * 
     * @version 2.7.1
     * @param Array $searchClues Array of Search Clues
     * @param Array $projectIdList List of allowed project Ids
     * @return Count of Projects
     */
    public function getSearchProjectListCount($searchClues, $projectIdList) {
        
        try {
            $projectIdEscapeString = implode(',', array_fill(0, count($projectIdList), '?'));
            
            $q = "SELECT p.project_id AS projectIds
            		FROM ohrm_project p
            		LEFT JOIN ohrm_customer c ON p.customer_id = c.customer_id
            		LEFT JOIN ohrm_project_admin pa ON p.project_id = pa.project_id
            		LEFT JOIN hs_hr_employee e ON pa.emp_number = e.emp_number";
            
            
            $escapeValueArray = array();
            
            if (!empty($projectIdList)) {
                $q .= " WHERE p.project_id IN ({$projectIdEscapeString})";
                $escapeValueArray = array_merge($escapeValueArray, $projectIdList);
            }
            
            if (!empty($searchClues['customer'])) {
                $q .= " AND c.name = ? ";
                $escapeValueArray[] = trim($searchClues['customer']);
            }
            if (!empty($searchClues['project'])) {
                $q .= " AND p.name = ? ";
                $escapeValueArray[] = trim($searchClues['project']);
            }
            
            if (!empty($searchClues['projectAdmin'])) {
                $projectAdmin = preg_replace('!\s+!', '%', trim($searchClues['projectAdmin']));
                $projectAdmin = "%" . $projectAdmin . "%";
                $q .= " AND concat_ws(' ', e.emp_firstname, e.emp_middle_name, e.emp_lastname) LIKE ?";
                $escapeValueArray[] = $projectAdmin;
            }
            $escapeValueArray[] = Project::ACTIVE_PROJECT;
            $q .= " AND p.is_deleted = ? GROUP BY p.project_id";
            
            $pdo = Doctrine_Manager::connection()->getDbh();
            $query = $pdo->prepare($q);
            $query->execute($escapeValueArray);
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            $count = count($results);
            
            return $count;
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Return List of Projects
     * 
     * @version 2.7.1
     * @param Array $searchClues Array of Search Clues
     * @param Array $projectIdList List of allowed project Ids
     * @return List of Projects
     */
    public function searchProjects($searchClues, $allowedProjectList) {
        try {
            
            $sortField = $this->_getSortField($searchClues['sortField']);
            $sortOrder = ($searchClues['sortOrder'] == "") ? 'ASC' : $searchClues['sortOrder'];
            $offset = ($searchClues['offset'] == "") ? 0 : $searchClues['offset'];
            $limit = ($searchClues['limit'] == "") ? 50 : $searchClues['limit'];

        
            $allowedProjectEscapeString = implode(',', array_fill(0, count($allowedProjectList), '?'));
            
            $q = "SELECT p.project_id As projectId, p.name AS projectName, c.customer_id AS customerId, c.name AS customerName, 
            		(SELECT GROUP_CONCAT(emp.emp_firstname,' ',emp.emp_lastname) FROM hs_hr_employee emp LEFT JOIN ohrm_project_admin pq ON (pq.emp_number = emp.emp_number) WHERE pq.project_id = p.project_id) AS projectAdminName
            		
            		FROM ohrm_project p
            		LEFT JOIN ohrm_customer c ON p.customer_id = c.customer_id
            		LEFT JOIN ohrm_project_admin pa ON p.project_id = pa.project_id
            		LEFT JOIN hs_hr_employee e ON pa.emp_number = e.emp_number";
            
            
            $escapeValueArray = array();
            
            if (!empty($allowedProjectList)) {
                $q .= " WHERE p.project_id IN ({$allowedProjectEscapeString})";
                $escapeValueArray = array_merge($escapeValueArray, $allowedProjectList);
            }
            
            if (!empty($searchClues['customer'])) {
                $q .= " AND c.name = ? ";
                $escapeValueArray[] = trim($searchClues['customer']);
            }
            if (!empty($searchClues['project'])) {
                $q .= " AND p.name = ? ";
                $escapeValueArray[] = trim($searchClues['project']);
            }
            
            if (!empty($searchClues['projectAdmin'])) {
                $projectAdmin = preg_replace('!\s+!', '%', trim($searchClues['projectAdmin']));
                $projectAdmin = "%" . $projectAdmin . "%";
                $q .= " AND concat_ws(' ', e.emp_firstname, e.emp_middle_name, e.emp_lastname) LIKE ?";
                $escapeValueArray[] = $projectAdmin;
            }

            $q .= " AND p.is_deleted = ? GROUP BY p.project_id ORDER BY {$sortField}  {$sortOrder}
            		limit {$offset}, {$limit}";
            
            $escapeValueArray[] = Project::ACTIVE_PROJECT;

            $pdo = Doctrine_Manager::connection()->getDbh();
            $query = $pdo->prepare($q);
            $query->execute($escapeValueArray);
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            
            return $results;

        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
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
    
    /**
     * Return an array of Project Ids
     * 
     * @version 2.7.1
     * @param $role User Role
     * @param Integer $empNumber Employee Number
     * @return Array of Project Ids
     */
    public function getProjectListForUserRole($role, $empNumber) {

        try {
            
            $q = "SELECT p.project_id AS project_id FROM ohrm_project p";
            $escapeValueArray = array();
            
            if ($role == ProjectAdminUserRoleDecorator::PROJECT_ADMIN_USER) {
                $q .= " LEFT JOIN ohrm_project_admin pa 
                		ON p.project_id = pa.project_id WHERE (pa.emp_number = ?)";
                $escapeValueArray[] = $empNumber;
            }
            
            $pdo = Doctrine_Manager::connection()->getDbh();
            $query = $pdo->prepare($q);
            $query->execute($escapeValueArray);
            $results = $query->fetchAll(PDO::FETCH_COLUMN, 0);
            
            return $results;

        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd

    }
    
    /**
     * Get count of project activities in the sytem
     * 
     * @param bool $includeDeleted If true, count will include deleted activities
     * @return int number of activities
     * @throws DaoException
     */
    public function getProjectActivityCount($includeDeleted = false) {
        try {

            $query = Doctrine_Query::create()
                    ->from('ProjectActivity');

            if (!$includeDeleted) {
                $query->andWhere('is_deleted = ?', ProjectActivity::ACTIVE_PROJECT_ACTIVITY);
            }

            $count = $query->count();
            return $count;

        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }        
    }    
    
    /**
     * Returns corresponding sort field
     * 
     * @version 2.7.1
     * @param string $sortFieldName 
     * @return string 
     */
    private function _getSortField($sortFieldName){
        
        $sortField = 'p.name';
        if($sortFieldName === 'customerName') {
            $sortField = 'c.name';
        } else if ($sortFieldName === 'projectName') {
            $sortField = 'p.name';
        }
        
        return $sortField;
        
    }
}

