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

    public function getProjectActivityById($activityId) {

        try {
            return Doctrine :: getTable('ProjectActivity')->find($activityId);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function getAllActiveProjects() {

        try {
            $q = Doctrine_Query :: create()
                            ->from('Project')
                            ->where('deleted = ?', Project::ACTIVE_PROJECT);
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function getActivityListByProjectId($projectId) {

        try {
            $q = Doctrine_Query :: create()
                            ->from('ProjectActivity')
                            ->where('deleted = ?', ProjectActivity::ACTIVE_PROJECT_ACTIVITY)
                            ->andWhere('project_id = ?', $projectId);
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
                            ->where('deleted = ?', 0)
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
     * Retrieve active projects given project ids.
     * @param integer[] $projectIdArray
     * @param string $orderField
     * @param string $orderBy
     * @return Project[]
     */
    public function getActiveProjectsByProjectIds($projectIdArray, $orderField='project_id', $orderBy='ASC') {
        try {
            $q = Doctrine_Query::create()
                            ->from('Project')
                            ->where('deleted = ?', 0)
                            ->andWhereIn('project_id', $projectIdArray)
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
     * Retrieve all projects given project ids.
     * @param integer[] $projectIdArray
     * @param string $orderField
     * @param string $orderBy
     * @return Project[]
     */
    public function getAllProjectsByProjectIds($projectIdArray, $orderField='project_id', $orderBy='ASC') {
        try {
            $q = Doctrine_Query::create()
                            ->from('Project')
                            ->whereIn('project_id', $projectIdArray)
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
    public function getProjectAdminRecordsByEmpNo($empNo) {

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

    public function getSearchProjectListCount($srchClues) {
        try {
            $q = $this->_buildSearchQuery($srchClues);
            return $q->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function searchProjects($srchClues) {

        $sortField = ($srchClues['sortField'] == "") ? 'name' : $srchClues['sortField'];
        $sortOrder = ($srchClues['sortOrder'] == "") ? 'ASC' : $srchClues['sortOrder'];
        $offset = ($srchClues['offset'] == "") ? 0 : $srchClues['offset'];
        $limit = ($srchClues['limit'] == "") ? 50 : $srchClues['limit'];

        try {
            $q = $this->_buildSearchQuery($srchClues);
            $q->orderBy($sortField . ' ' . $sortOrder)
                    ->addWhere('p.deleted = ?', Project::ACTIVE_PROJECT)
                    ->offset($offset)
                    ->limit($limit);

            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    private function _buildSearchQuery($srchClues) {

        $q = Doctrine_Query::create()
                        ->select('p.projectId, p.name, c.name')
                        ->addSelect('(SELECT GROUP_CONCAT(emp.firstName,\' \',emp.lastName) FROM Employee emp LEFT JOIN emp.projectAdmin pq ON (pq.emp_number = emp.emp_number) WHERE pq.projectId = p.projectId) AS projectAdmins')
                        ->from('Project p')
                        ->leftJoin('p.Customer c')
                        ->leftJoin('p.ProjectAdmin pa')
                        ->leftJoin('pa.Employee e');

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
    public function getProjectAdmin($projectId) {
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
     * Checking the existance of Project admin
     * @param String $projectId
     * @param String $empId
     * @returns boolean
     * @throws DaoException
     */
    public function isExistingProjectAdmin($projectId, $empId) {
        try {
            $q = Doctrine_Query::create()
                            ->from('ProjectAdmin pa')
                            ->where("pa.project_id = ?", $projectId)
                            ->andWhere("pa.emp_number =?", $empId);

            if ($q->count() > 0) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Retrieve Project by Id
     * @param int $id
     * @returns Project
     * @throws DaoException
     */
    public function readProject($id) {
        try {
            $q = Doctrine_Query::create()
                            ->from('Project')
                            ->where("project_id = ?", $id);

            return $q->fetchOne();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Retrieve project activity by projectId
     * @param String $projectId
     * @returns ProjectActivity
     * @throws DaoException
     */
    public function getProjectActivity($projectId) {
        try {
            $q = Doctrine_Query::create()
                            ->from('ProjectActivity pa')
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
    public function deleteProjectActivity($activitId) {

        try {
            $projectActivity = Doctrine :: getTable('ProjectActivity')->find($activitId);
            $projectActivity->setDeleted(ProjectActivity::DELETED_PROJECT_ACTIVITY);
            $projectActivity->save();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Save ProjectActivity
     * @param String $projectId
     * @param String $activity
     * @returns boolean
     * @throws DaoException
     */
    public function saveProjectActivity($projectId, $activity) {
        try {
            $projectActivity = new ProjectActivity();
            $idGenService = new IDGeneratorService();
            $idGenService->setEntity($projectActivity);
            $projectActivity->setActivityId($idGenService->getNextID());
            $projectActivity->setProjectId($projectId);
            $projectActivity->setName($activity);
            $projectActivity->save();

            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function isProjectHasTimesheetItems($projectId) {

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
                            ->where('deleted = ?', Project::ACTIVE_PROJECT)
                            ->andWhere('customer_id = ?', $customerId);
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

}

