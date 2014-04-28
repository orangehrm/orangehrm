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
class TimesheetDao {

    /**
     * Get Timesheet by given Timehseet Id
     * @param $timesheetId
     * @return Timesheet
     */
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

    public function getTimesheetById($timesheetId) {

        try {
            $timesheet = Doctrine::getTable('Timesheet')
                    ->find($timesheetId);

            return $timesheet;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Get Timesheet by given Start Date
     * @param $starDate
     * @return Timesheet
     */
    public function getTimesheetByStartDate($startDate) {

        try {

            $query = Doctrine_Query::create()
                    ->from("Timesheet")
                    ->where("start_date = ?", $startDate);
            $results = $query->execute();
            if ($results[0]->getTimesheetId() == null) {

                return null;
            } else {
                return $results[0];
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Add or Save Timesheet
     * @param Timesheet $timesheet
     * @return Timesheet
     */
    public function saveTimesheet(Timesheet $timesheet) {

        try {

            if ($timesheet->getTimesheetId() == '') {
                $idGenService = new IDGeneratorService();
                $idGenService->setEntity($timesheet);
                $timesheet->setTimesheetId($idGenService->getNextID());
            }
            $timesheet->save();

            return $timesheet;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Get Timesheet Item by given Id
     * @param $timesheetItemId
     * @return TimesheetItem
     */
    public function getTimesheetItemById($timesheetItemId) {

        try {

            $timesheetItem = Doctrine::getTable("TimesheetItem")
                    ->find($timesheetItemId);

            return $timesheetItem;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Get Timesheet Item by given timesheetId and employeeId
     * @param $timesheetId , $employeeId
     * @return TimesheetItem
     */
    public function getTimesheetItem($timesheetId, $employeeId) {

        try {

            $query = Doctrine_Query::create()
                    ->from("TimesheetItem ti")
                    ->leftJoin("ti.Project p")
                    ->leftJoin("ti.ProjectActivity a")
                    ->where("ti.timesheetId = ?", $timesheetId)
                    ->andWhere("ti.employeeId = ?", $employeeId)
                    ->orderBy('ti.projectId ASC, ti.activityId ASC, ti.date ASC');


            return $query->execute()->getData();
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Get Timesheet Item by given timesheetId and employeeId
     * @param $timesheetId , $employeeId
     * @return TimesheetItem
     */
    public function getTimesheetItemByDateProjectId($timesheetId, $employeeId, $projectId, $activityId, $date) {

        try {

            $timesheetItem = Doctrine_Query::create()
                    ->from("TimesheetItem")
                    ->where("timesheetId = ?", $timesheetId)
                    ->andWhere("employeeId = ?", $employeeId)
                    ->andWhere("projectId = ?", $projectId)
                    ->andWhere("activityId = ?", $activityId)
                    ->andWhere("date = ?", $date);

            return $timesheetItem->execute();
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Add or Save TimesheetItem
     * @param $timesheetItem
     * @return $timesheetItem
     */
    public function saveTimesheetItem(TimesheetItem $timesheetItem) {

        try {

            if ($timesheetItem->getTimesheetItemId() == '') {
                $idGenService = new IDGeneratorService();

                $idGenService->setEntity($timesheetItem);
                $timesheetItem->setTimesheetItemId($idGenService->getNextID());
            }

            $timesheetItem->save();

            return $timesheetItem;
        } catch (Exception $ex) {

            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Add or Save TimesheetItem
     * @param $timesheetItem
     * @return $timesheetItem
     */
    public function deleteTimesheetItems($employeeId, $timesheetId, $projectId, $activityId) {
        try {

            $query = Doctrine_Query::create()
                    ->delete()
                    ->from("TimesheetItem")
                    ->where("timesheetId = ?", $timesheetId)
                    ->andWhere("employeeId = ?", $employeeId)
                    ->andWhere("projectId = ?", $projectId)
                    ->andWhere("activityId = ?", $activityId);

            $timesheetItemDeleted = $query->execute();
            if ($timesheetItemDeleted > 0) {
                return true;
            }

            return false;
        } catch (Exception $ex) {

            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Add or Save TimesheetActionLog
     * @param TimesheetActionLog $timesheetActionLog
     * @return $timesheetActionLog
     */
    public function saveTimesheetActionLog(TimesheetActionLog $timesheetActionLog) {

        try {

            if ($timesheetActionLog->getTimesheetActionLogId() == '') {
                $idGenService = new IDGeneratorService();
                $idGenService->setEntity($timesheetActionLog);
                $timesheetActionLog->setTimesheetActionLogId($idGenService->getNextID());
            }

            $timesheetActionLog->save();
            return $timesheetActionLog;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Get TimesheetActionLog by given TimesheetActionLog Id
     * @param $timesheetActionLogId
     * @return TimesheetActionLog
     */
    public function getTimesheetActionLogById($timesheetActionLogId) {

        try {

            $timesheetActionLog = Doctrine::getTable("TimesheetActionLog")
                    ->find($timesheetActionLogId);

            return $timesheetActionLog;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Get Timesheet by given Start Date And Employee Id
     * @param $starDate , $employeeId
     * @return Timesheet
     */
    public function getTimesheetByStartDateAndEmployeeId($startDate, $employeeId) {

        try {

            $query = Doctrine_Query::create()
                    ->from("Timesheet")
                    ->where("start_date = ?", $startDate)
                    ->andWhere("employee_id = ?", $employeeId);

            $results = $query->execute();
            if ($results[0]->getTimesheetId() == null) {

                return null;
            } else {
                return $results[0];
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Get TimesheetActionLog by given Timesheet Id
     * @param $timesheetActionLogId
     * @return TimesheetActionLog
     */
    public function getTimesheetActionLogByTimesheetId($timesheetId) {

        try {


            $query = Doctrine_Query::create()
                    ->from("TimesheetActionLog")
                    ->where("timesheetId = ?", $timesheetId)
                    ->orderBy('timesheetActionLogId');

            $results = $query->execute();
            if ($results[0]->getTimesheetActionLogId() == null) {

                return null;
            } else {
                return $results;
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Get start and end days of each timesheet
     * @param none
     * @return Start and end dates Array
     */
    public function getStartAndEndDatesList($employeeId) {

        $query = Doctrine_Query::create()
                ->select('a.start_date')
                ->from('Timesheet a')
                ->where("employeeId = ?", $employeeId)
                ->orderBy('a.start_date ASC');
        $results = $query->fetchArray();
        $query1 = Doctrine_Query::create()
                ->select('a.end_date')
                ->from('Timesheet a')
                ->where("employeeId = ?", $employeeId)
                ->orderBy('a.end_date ASC');

        $results1 = $query1->fetchArray();
        $resultArray = array($results, $results1);
        return $resultArray;
    }

    /**
     * Get Timesheet by given Employee Id
     * @param $employeeId
     * @return Timesheets
     */
    public function getTimesheetByEmployeeId($employeeId) {

        try {

            $query = Doctrine_Query::create()
                    ->from('Timesheet a')
                    ->where('employee_id = ?', $employeeId)
                    ->orderBy('a.start_date ASC');

            $results = $query->execute();

            if ($results[0]->getTimesheetId() == null) {

                return null;
            } else {
                return $results;
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Get Timesheet by given Employee Id and State
     * @param $employeeId
     * @return Timesheets
     */
    public function getTimesheetByEmployeeIdAndState($employeeId, $stateList) {

        try {

            $query = Doctrine_Query::create()
                    ->from('Timesheet')
                    ->where('employee_id = ?', $employeeId)
                    ->andWhereIn('state', $stateList);

            $results = $query->execute();

            if ($results[0]->getTimesheetId() == null) {

                return null;
            } else {
                return $results;
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }
    
    /**
     * Return an Array of Timesheets for given Employee Ids and States
     * 
     * @version 2.7.1
     * @param Array $employeeIdList Array of Employee Ids
     * @param Array $stateList Array of States
     * @param Integer $limit
     * @return Array of Timesheets
     */
    public function getTimesheetListByEmployeeIdAndState($employeeIdList, $stateList, $limit = 100) {

        try {
            
            if ((!empty($employeeIdList)) && (!empty($stateList))) {
                
                $employeeListEscapeString = implode(',', array_fill(0, count($employeeIdList), '?'));
                $stateListEscapeString = implode(',', array_fill(0, count($stateList), '?'));
    
                $q = "SELECT o.timesheet_id AS timesheetId, o.start_date AS timesheetStartday, o.end_date AS timesheetEndDate, o.employee_id AS employeeId, e.emp_firstname AS employeeFirstName, e.emp_lastname AS employeeLastName
    					FROM ohrm_timesheet o
    					LEFT JOIN  hs_hr_employee e ON o.employee_id = e.emp_number
    					WHERE 
    					o.employee_id IN ({$employeeListEscapeString}) AND
    					o.state IN({$stateListEscapeString})
    					ORDER BY e.emp_lastname ASC";
    			
    			if ($limit) {
    				$q .= " LIMIT 0, {$limit}";
    			}
                
                $escapeValueArray = array_merge($employeeIdList, $stateList);
                
                $pdo = Doctrine_Manager::connection()->getDbh();
                $query = $pdo->prepare($q);
                $query->execute($escapeValueArray);
                
                $results = $query->fetchAll(PDO::FETCH_ASSOC);
            }
            return $results;

        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Get Customer by customer name
     * @param $customerName
     * @return Customer
     */
    public function getCustomerByName($customerName) {

        try {

            $query = Doctrine_Query::create()
                    ->from("Customer")
                    ->where("name = ?", $customerName);

            $results = $query->execute();

            if ($results[0]->getCustomerId() == null) {

                return null;
            } else {
                return $results[0];
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * get Project By ProjectName And CustomerId
     * @param $projectName, $customerId
     * @return Project
     */
    public function getProjectByProjectNameAndCustomerId($projectName, $customerId) {

        try {

            $query = Doctrine_Query::create()
                    ->from('Project')
                    ->where('name = ?', $projectName)
                    ->andWhere('customer_id = ?', $customerId)
		    ->andWhere('is_deleted = ?', 0);

            $results = $query->execute();

            if ($results[0]->getProjectId() == null) {

                return null;
            } else {
                return $results[0];
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * get Project Activities By PorjectId
     * @param $projectId, $deleted
     * @return Project Activities
     */
    public function getProjectActivitiesByPorjectId($projectId, $deleted = false) {

        try {

            $query = Doctrine_Query::create()
                    ->from('ProjectActivity')
                    ->where('project_id = ?', $projectId);

            if (!$deleted) {
                // Only fetch active projects
                $query->andWhere('is_deleted = ?', ProjectActivity::ACTIVE_PROJECT_ACTIVITY);
            }

            $query->orderBy('name ASC');
            $results = $query->execute();

            if ($results[0]->getActivityId() == null) {
                return null;
            } else {
                return $results;
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }    
    
    /**
     * Return an Array of Project Names
     * 
     * @version 2.7.1
     * @param Boolean $excludeDeletedProjects Exclude deleted projects or not
     * @param String $orderField Sort order field
     * @param String $orderBy Sort order
     * @return Array of Project Names
     */
    public function getProjectNameList($excludeDeletedProjects = true, $orderField='project_id', $orderBy='ASC') {
        try {
            
            $q = "SELECT p.project_id AS projectId, p.name AS projectName, c.name AS customerName
            		FROM ohrm_project p
            		LEFT JOIN ohrm_customer c ON p.customer_id = c.customer_id";
            
            if($excludeDeletedProjects) {
                $q .= " WHERE p.is_deleted = 0";
            }
            
            if ($orderField) {
                $orderBy = (strcasecmp($orderBy, 'DESC') == 0) ? 'DESC' : 'ASC';
                $q .= " ORDER BY {$orderField} {$orderBy}";
            }
            
            $pdo = Doctrine_Manager::connection()->getDbh();
            $projectList = $pdo->query($q)->fetchAll(PDO::FETCH_ASSOC);

            return $projectList;
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }
    
    /**
     * Return an Array of Project Activities by Project Id
     * 
     * @version 2.7.1
     * @param Integer $projectId Project Id
     * @param Boolean $excludeDeletedActivities Exclude Deleted Project Activities or not
     * @return Array of Project Activities
     */
    public function getProjectActivityListByPorjectId($projectId, $excludeDeletedActivities = true) {

        try {

            $query = Doctrine_Query::create()
                    ->from('ProjectActivity')
                    ->where('project_id = ?', $projectId);

            if ($excludeDeletedActivities) {
                $query->andWhere('is_deleted = ?', ProjectActivity::ACTIVE_PROJECT_ACTIVITY);
            }
            $query->orderBy('name ASC');
            $results = $query->fetchArray();
            
            return $results;
        
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * get Project Activity By Project Id And ActivityName
     * @param $projectId, $activityName
     * @return Project Activities
     */
    public function getProjectActivityByProjectIdAndActivityName($projectId, $activityName) {

        try {

            $query = Doctrine_Query::create()
                    ->from('ProjectActivity')
                    ->where('project_id = ?', $projectId)
                    ->andWhere('name = ?', $activityName);

            $results = $query->execute();

            if ($results[0]->getActivityId() == null) {

                return null;
            } else {
                return $results[0];
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * get Project Activity By Activity Id
     * @param $activityId
     * @return Project Activities
     */
    public function getProjectActivityByActivityId($activityId) {

        try {

            $query = Doctrine_Query::create()
                    ->from('ProjectActivity')
                    ->where('activity_id = ?', $activityId);

            $results = $query->execute();

            if ($results[0]->getActivityId() == null) {

                return null;
            } else {
                return $results[0];
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * retrieve suppervisor approved timesheets
     * @param
     * @return $timesheets doctrine collection
     */
    public function getPendingApprovelTimesheetsForAdmin() {

        try {
            $query = Doctrine_Query::create()
                    ->from("Timesheet")
                    ->where("state = ?", "SUPERVISOR APPROVED");
            $results = $query->execute();
            if ($results[0]->getTimesheetId() == null) {

                return null;
            } else {

                return $results;
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Get Activity by given Activity Id
     * @param $activityId
     * @return ProjectActivity
     */
    public function getActivityByActivityId($activityId) {

        try {
            $activity = Doctrine::getTable('ProjectActivity')
                    ->find($activityId);

            return $activity;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * get Timesheet Time Format
     * @param 
     * @return Time Format
     */
    public function getTimesheetTimeFormat() {

        try {
            return $this->getConfigDao()->getValue(ConfigService::KEY_TIMESHEET_TIME_FORMAT);
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * get Project List
     * @param $orderField, $orderBy, $deleted
     * @return Projects
     */
    public function getProjectList($orderField='project_id', $orderBy='ASC', $deleted =0) {
        try {
            $orderBy = (strcasecmp($orderBy, 'DESC') == 0) ? 'DESC' : 'ASC';
            $q = Doctrine_Query::create()
                    ->from('Project')
                    ->andWhere('is_deleted = ?', $deleted)
                    ->orderBy($orderField . ' ' . $orderBy);

            $projectList = $q->execute();

            return $projectList;
        } catch (Exception $e) {
            throw new AdminServiceException($e->getMessage());
        }
    }

    /**
     * get Project List For Validation
     * @param $orderField, $orderBy,
     * @return Projects
     */
    public function getProjectListForValidation($orderField='project_id', $orderBy='ASC') {
        try {
            $orderBy = (strcasecmp($orderBy, 'DESC') == 0) ? 'DESC' : 'ASC';
            $q = Doctrine_Query::create()
                    ->from('Project')
                    ->orderBy($orderField . ' ' . $orderBy);

            $projectList = $q->execute();

            return $projectList;
        } catch (Exception $e) {
            throw new AdminServiceException($e->getMessage());
        }
    }

    /**
     * get Latest Timesheet EndDate
     * @param $employeeId
     * @return EndDate
     */
    public function getLatestTimesheetEndDate($employeeId) {

        try {

            $query = Doctrine_Query::create()
                    ->select('MAX(end_date)')
                    ->from("Timesheet")
                    ->where('employee_id = ?', $employeeId);

            $results = $query->execute();

            if ($results[0]['MAX'] != null) {

                return $results[0]['MAX'];
            } else {
                return null;
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * check For Overlapping Timesheets
     * @param $$startDate, $endDate, $employeeId
     * @return string 1,0
     */
    public function checkForOverlappingTimesheets($startDate, $endDate, $employeeId) {


        $isValid = "1";

        try {
            //case1=where the startDate is ok but the endDate comes in between some other timesheets startDate and endDate
            $query1 = Doctrine_Query::create()
                    ->from("Timesheet")
                    ->where("employee_id = ?", $employeeId)
                    ->andWhere("start_date >= ?", $startDate)
                    ->andWhere("end_date <= ?", $endDate);
            $records1 = $query1->execute();


            if ((count($records1) > 0)) {
                $isValid = "0";
            }

            //case2=this checks wether the timesheets startDate falls between some other timesheets startDate and enddate
            $query2 = Doctrine_Query::create()
                    ->from("Timesheet")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere("start_date <= ?", $startDate)
                    ->andWhere("end_date >= ?", $startDate);
            $records2 = $query2->execute();


            if ((count($records2) > 0)) {

                $isValid = "0";
            }

            //case3=this checks the case where new timesheet about to create totaly ovelapps a existing timesheet
            $query3 = Doctrine_Query::create()
                    ->from("Timesheet")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere("start_date >= ?", $startDate)
                    ->andWhere("start_date <= ?", $endDate);
            $records3 = $query3->execute();


            if ((count($records3) > 0)) {

                $isValid = "0";
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }

        return $isValid;
    }

    public function checkForMatchingTimesheetForCurrentDate($employeeId, $currentDate) {


        try {
            $query = Doctrine_Query::create()
                    ->from("Timesheet")
                    ->where("employee_id = ?", $employeeId)
                    ->andWhere("start_date <= ?", $currentDate)
                    ->andWhere("end_date >= ?", $currentDate);
            $record = $query->execute();
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }

        if ((count($record) > 0)) {

            return $record[0];
        } else {
            return null;
        }
    }

    /**
     *
     * @param type $employeeIds
     * @param type $dateFrom
     * @param type $dateTo
     * @param type $subDivision
     * @param type $employeementStatus
     * @return type array
     */
    public function searchTimesheetItems($employeeIds = null, $employeementStatus = null, $supervisorIds = null, $subDivision = null, $dateFrom = null, $dateTo = null) {

        $q = Doctrine_Query::create()
                ->select("e.emp_middle_name, e.termination_id , e.emp_lastname, e.emp_firstname, i.date, cust.name, prj.name, act.name, i.comment, SUM(i.duration) AS total_duration ")
                ->from("ProjectActivity act")
                ->leftJoin("act.Project prj")
                ->leftJoin("prj.Customer cust")
                ->leftJoin("act.TimesheetItem i")
                ->leftJoin("i.Employee e");
        
        $q->where("act.activity_id = i.activity_id ");
        
        if ($employeeIds != null) {
            if (is_array($employeeIds)) {
                $q->whereIn("e.emp_number", $employeeIds);
            } else {
                $q->andWhere(" e.emp_number = ?", $employeeIds);
            }
        }
        
        if (is_array($supervisorIds) && sizeof($supervisorIds)>0) {
            $q->whereIn("e.emp_number", $supervisorIds);
        }

        if( $employeementStatus > 0 ){
            $q->andWhere("e.emp_status = ?", $employeementStatus);
        } else {
            if($employeeIds <= 0){
                $q->andWhere("(e.termination_id IS NULL)");
            }            
        }        
        
        if( $subDivision > 0){
            
            $companyService = new CompanyStructureService();
            $subDivisions = $companyService->getCompanyStructureDao()->getSubunitById($subDivision);
           
            $subUnitIds = array($subDivision);
             if (!empty($subDivisions)) {
                $descendents = $subDivisions->getNode()->getDescendants();
                
                foreach($descendents as $descendent) {                
                    $subUnitIds[] = $descendent->id;
                }
            }

            $q->andWhereIn("e.work_station", $subUnitIds);            
        }

        if ($dateFrom != null) {
            $q->andWhere("i.date >=?", $dateFrom);
        }

        if ($dateTo != null) {
            $q->andWhere("i.date <=?", $dateTo);
        }

        $q->groupBy("e.emp_number, i.date, act.activity_id");
        $q->orderBy("e.lastName ASC, i.date DESC, cust.name, act.name ASC ");

        $result = $q->execute(array(), Doctrine::HYDRATE_SCALAR);

        return $result;
    }

}
