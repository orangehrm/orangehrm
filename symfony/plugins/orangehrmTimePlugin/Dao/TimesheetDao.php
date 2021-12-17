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

namespace OrangeHRM\Time\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Timesheet;
use OrangeHRM\Entity\TimesheetActionLog;
use OrangeHRM\Entity\TimesheetItem;
use DateTime;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Time\Dto\TimesheetActionLogSearchFilterParams;

class TimesheetDao extends BaseDao
{
    /**
     * @param int $timesheetId
     * @return Timesheet|null
     */
    public function getTimesheetById(int $timesheetId): ?Timesheet
    {
        return $this->getRepository(Timesheet::class)->find($timesheetId);
    }

    /**
     * Get Timesheet by given Start Date
     * @param $starDate
     * @return Timesheet
     */
    public function getTimesheetByStartDate($startDate)
    {
        // TODO
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
     * @param Timesheet $timesheet
     * @return Timesheet
     */
    public function saveTimesheet(Timesheet $timesheet): Timesheet
    {
        $this->persist($timesheet);
        return $timesheet;
    }

    /**
     * Get Timesheet Item by given Id
     * @param $timesheetItemId
     * @return TimesheetItem
     */
    public function getTimesheetItemById($timesheetItemId)
    {
        // TODO
        try {
            $timesheetItem = Doctrine::getTable("TimesheetItem")
                    ->find($timesheetItemId);

            return $timesheetItem;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * @param int $timesheetId
     * @return TimesheetItem[]
     */
    public function getTimesheetItemsByTimesheetId(int $timesheetId): array
    {
        $q = $this->createQueryBuilder(TimesheetItem::class, 'timesheetItem')
            ->andWhere('IDENTITY(timesheetItem.timesheet) = :timesheetId')
            ->setParameter('timesheetId', $timesheetId);

        return $q->getQuery()->execute();
    }

    /**
     * Get Timesheet Item by given timesheetId and employeeId
     * @param $timesheetId , $employeeId
     * @return TimesheetItem
     */
    public function getTimesheetItemByDateProjectId($timesheetId, $employeeId, $projectId, $activityId, $date)
    {
        // TODO
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
    public function saveTimesheetItem(TimesheetItem $timesheetItem)
    {
        // TODO
        try {
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
    public function deleteTimesheetItems($employeeId, $timesheetId, $projectId, $activityId)
    {
        // TODO
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
     * @param EmployeeID $employeeId
     * @param TimeSheetId $timesheetId
     * @return bool
     * @throws DaoException
     */
    public function deleteTimesheetItemsByTimesheetId($employeeId, $timesheetId)
    {
        // TODO
        try {
            $query = Doctrine_Query::create()
                ->delete()
                ->from("TimesheetItem")
                ->where("timesheetId = ?", $timesheetId)
                ->andWhere("employeeId = ?", $employeeId);

            return $timesheetItemDeleted = $query->execute();

            // @codeCoverageIgnoreStart
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
        //@codeCoverageIgnoreEnd
    }

    /**
     * Add or Save TimesheetActionLog
     * @param TimesheetActionLog $timesheetActionLog
     * @return $timesheetActionLog
     */
    public function saveTimesheetActionLog(TimesheetActionLog $timesheetActionLog)
    {
        // TODO
        try {
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
    public function getTimesheetActionLogById($timesheetActionLogId)
    {
        // TODO
        try {
            $timesheetActionLog = Doctrine::getTable("TimesheetActionLog")
                    ->find($timesheetActionLogId);

            return $timesheetActionLog;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Get TimesheetActionLog by given Timesheet Id
     * @param $timesheetActionLogId
     * @return TimesheetActionLog
     */
    public function getTimesheetActionLogByTimesheetId($timesheetId)
    {
        // TODO
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
    public function getStartAndEndDatesList($employeeId)
    {
        // TODO
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
        $resultArray = [$results, $results1];
        return $resultArray;
    }

    /**
     * Get Timesheet by given Employee Id
     * @param $employeeId
     * @return Timesheets
     */
    public function getTimesheetByEmployeeId($employeeId)
    {
        // TODO
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
    public function getTimesheetByEmployeeIdAndState($employeeId, $stateList)
    {
        // TODO
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
    public function getTimesheetListByEmployeeIdAndState($employeeIdList, $stateList, $limit = 100)
    {
        // TODO
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
    public function getCustomerByName($customerName)
    {
        // TODO
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
    public function getProjectByProjectNameAndCustomerId($projectName, $customerId)
    {
        // TODO
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
    public function getProjectActivitiesByPorjectId($projectId, $deleted = false)
    {
        // TODO
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
    public function getProjectNameList($excludeDeletedProjects = true, $orderField='project_id', $orderBy='ASC')
    {
        // TODO
        try {
            $q = "SELECT p.project_id AS projectId, p.name AS projectName, c.name AS customerName
            		FROM ohrm_project p
            		LEFT JOIN ohrm_customer c ON p.customer_id = c.customer_id";

            if ($excludeDeletedProjects) {
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
    public function getProjectActivityListByPorjectId($projectId, $excludeDeletedActivities = true)
    {
        // TODO
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
    public function getProjectActivityByProjectIdAndActivityName($projectId, $activityName)
    {
        // TODO
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
    public function getProjectActivityByActivityId($activityId)
    {
        // TODO
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
    public function getPendingApprovelTimesheetsForAdmin()
    {
        // TODO
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
    public function getActivityByActivityId($activityId)
    {
        // TODO
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
    public function getTimesheetTimeFormat()
    {
        // TODO
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
    public function getProjectList($orderField='project_id', $orderBy='ASC', $deleted =0)
    {
        // TODO
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
    public function getProjectListForValidation($orderField='project_id', $orderBy='ASC')
    {
        // TODO
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
    public function getLatestTimesheetEndDate($employeeId)
    {
        // TODO
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
    public function checkForOverlappingTimesheets($startDate, $endDate, $employeeId)
    {
        // TODO

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

    public function checkForMatchingTimesheetForCurrentDate($employeeId, $currentDate)
    {
        // TODO

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
     * @param DateTime $date
     * @param int|null $employeeNumber
     * @return bool
     */
    public function hasTimesheetForStartDate(int $employeeNumber, DateTime $date): bool
    {
        $q = $this->createQueryBuilder(Timesheet::class, 'timesheet');
        $q->andWhere('timesheet.startDate = :date');
        $q->andWhere('timesheet.employee = :employeeNumber');
        $q->setParameter('date', $date);
        $q->setParameter('employeeNumber', $employeeNumber);

        return $this->getPaginator($q)->count() > 0;
    }

    /**
     * @param  int  $timesheetId
     * @param  TimesheetActionLogSearchFilterParams  $timesheetActionLogParamHolder
     * @return TimesheetActionLog[]
     */
    public function getTimesheetActionLogs(
        int $timesheetId,
        TimesheetActionLogSearchFilterParams $timesheetActionLogParamHolder
    ): array {
        $qb = $this->getTimesheetActionLogsPaginator($timesheetId, $timesheetActionLogParamHolder);
        return $qb->getQuery()->execute();
    }

    /**
     * @param  int  $timesheetId
     * @param  TimesheetActionLogSearchFilterParams  $timesheetActionLogParamHolder
     * @return Paginator
     */
    protected function getTimesheetActionLogsPaginator(
        int $timesheetId,
        TimesheetActionLogSearchFilterParams $timesheetActionLogParamHolder
    ): Paginator {
        $qb = $this->createQueryBuilder(TimesheetActionLog::class, 'timesheetActionLog');
        $qb->leftJoin('timesheetActionLog.timesheet', 'timesheet');

        $this->setSortingAndPaginationParams($qb, $timesheetActionLogParamHolder);

        $qb->andWhere('timesheet.id = :timesheetId')
            ->setParameter('timesheetId', $timesheetId);
        return $this->getPaginator($qb);
    }

    /**
     * @param $timesheetId
     * @param  TimesheetActionLogSearchFilterParams  $timesheetActionLogParamHolder
     * @return int
     */
    public function getTimesheetActionLogsCount(
        $timesheetId,
        TimesheetActionLogSearchFilterParams $timesheetActionLogParamHolder
    ): int {
        return $this->getTimesheetActionLogsPaginator($timesheetId, $timesheetActionLogParamHolder)->count();
    }
}
