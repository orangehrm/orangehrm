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
 *
 */
require_once ROOT_PATH.'/lib/dao/DMLFunctions.php';
require_once ROOT_PATH.'/lib/dao/SQLQBuilder.php';
require_once ROOT_PATH.'/lib/confs/sysConf.php';
require_once ROOT_PATH.'/lib/common/CommonFunctions.php';
require_once ROOT_PATH.'/lib/models/time/ProjectActivityTime.php';
require_once ROOT_PATH.'/lib/models/time/EmployeeActivityTime.php';

/**
 * Generates project reports
 */
 class ProjectReport {

	protected $pageSize;

	/**
	 * Constructor
	 */
	public function __construct() {
		$sysConf = new sysConf();
		$this->pageSize = $sysConf->itemsPerPage;
	}

 	/**
 	 * Gets time spent on activities for the given project for the given period
 	 * Deleted activities are only included if they have time against them.
 	 *
 	 * @param int $projectId The project Id
 	 * @param string $startDate valid start date
 	 * @param string $endDate valid end date
 	 *
 	 * @return array Array of ProjectActivityTime objects
 	 */
 	 public function getProjectActivityTime($projectId, $startDate = null, $endDate = null) {

        if (empty($startDate)) {
			$startDate = "1970-01-01";
        }
        if (empty($endDate)) {
			$endDate = date("Y-m-d", strtotime("+1 year"));
        }

        $dayStart = $startDate . " 00:00:00";
        $nextDay = date("Y-m-d", strtotime("+1 day", strtotime($endDate)));
        $dayEnd =  $nextDay . " 00:00:00";

		$sql = "SELECT  a.activity_id as activity_id, " .
                       "a.name as activity_name, " .
                       "a.project_id as project_id, " .
			           "if(start_time < '$dayStart', '$dayStart', start_time) AS start, " .
                       "if(end_time > '$dayEnd', '$dayEnd', end_time) AS end " .
               "FROM hs_hr_project_activity a LEFT JOIN hs_hr_time_event e on " .
                        "(a.activity_id = e.activity_id) AND " .
                        "(" .
                           "(start_time BETWEEN '$dayStart' AND '$dayEnd') OR " .
                           "(end_time BETWEEN '$dayStart' AND '$dayEnd') OR " .
                           "('$dayStart' BETWEEN start_time AND end_time) " .
                         ")" .
               "WHERE   a.project_id = '$projectId' AND " .
                       "(a.deleted <> 1 OR e.activity_id IS NOT NULL)";


		$sql = "SELECT activity_id, activity_name, project_id, " .
                       "COALESCE(sum(time_to_sec(timediff(end, start))), 0) AS duration " .
               "FROM (" . $sql . ") AS s GROUP BY activity_id";

		$conn = new DMLFunctions();
		$result = $conn->executeQuery($sql);

		$activityTimeList = array();

		while ($result && ($row = mysql_fetch_assoc($result))) {
			$activityTimeList[] = new ProjectActivityTime($row['activity_id'], $row['activity_name'], $row['duration'], $row['project_id']);
		}

		return $activityTimeList;
 	 }

	/**
	 * Gets time spent by each employee for the given project activity during the given period
	 *
 	 * @param int $projectId The project Id
 	 * @param int $activityId The activity Id
 	 * @param string $startDate valid start date
 	 * @param string $endDate valid end date
 	 * @param int    If given and greater than zero, only one page of results are returned.
 	 *
 	 * @return array Array of ProjectActivityTime objects. An empty array is returned if
 	 *               no employee has time against the given activity.
	 */
 	 public function getEmployeeActivityTime($projectId, $activityId, $startDate = null, $endDate = null, $pageNo = 0) {

        if (empty($startDate)) {
			$startDate = "1970-01-01";
        }
        if (empty($endDate)) {
			$endDate = date("Y-m-d", strtotime("+1 year"));
        }

        $dayStart = $startDate . " 00:00:00";
        $nextDay = date("Y-m-d", strtotime("+1 day", strtotime($endDate)));
        $dayEnd =  $nextDay . " 00:00:00";

        $limit = "";
        if ($pageNo > 0) {
        	$startFrom = ($pageNo - 1) * $this->pageSize;
        	$limit = "LIMIT " . $startFrom . ", " . $this->pageSize;
        }

		$sql = "SELECT  a.activity_id as activity_id, " .
                       "a.name as activity_name, " .
                       "e.emp_number as emp_number, " .
                       "e.emp_firstname as firstname, " .
                       "e.emp_lastname as lastname, " .
			           "if(start_time < '$dayStart', '$dayStart', start_time) AS start, " .
                       "if(end_time > '$dayEnd', '$dayEnd', end_time) AS end " .
               "FROM hs_hr_time_event te LEFT JOIN hs_hr_project_activity a on (a.activity_id = te.activity_id) " .
               "     LEFT JOIN hs_hr_employee e on (te.employee_id = e.emp_number) " .
               "WHERE " .
                      " te.activity_id = $activityId AND (" .
                           "(start_time BETWEEN '$dayStart' AND '$dayEnd') OR " .
                           "(end_time BETWEEN '$dayStart' AND '$dayEnd') OR " .
                           "('$dayStart' BETWEEN start_time AND end_time) )";


		$sql = "SELECT activity_id, activity_name, emp_number, firstname, lastname, " .
                       "COALESCE(sum(time_to_sec(timediff(end, start))), 0) AS duration " .
               "FROM (" . $sql . ") AS s GROUP BY emp_number $limit";

		$conn = new DMLFunctions();
		$result = $conn->executeQuery($sql);

		$empTimeList = array();

		while ($result && ($row = mysql_fetch_assoc($result))) {
			$empTimeList[] = new EmployeeActivityTime($row['emp_number'], $row['firstname'], $row['lastname'],
			                          $row['activity_id'], $row['activity_name'], $row['duration']);
		}

		return $empTimeList;
 	 }

	/**
	 * Gets count of employees who had worked on given project activity
	 * during given period
	 *
 	 * @param int $projectId The project Id
 	 * @param int $activityId The activity Id
 	 * @param string $startDate valid start date
 	 * @param string $endDate valid end date
 	 *
 	 * @return array Array containing count of employees
	 */
 	 public function countEmployeesInActivity($projectId, $activityId, $startDate = null, $endDate = null) {

        if (empty($startDate)) {
			$startDate = "1970-01-01";
        }
        if (empty($endDate)) {
			$endDate = date("Y-m-d", strtotime("+1 year"));
        }

        $dayStart = $startDate . " 00:00:00";
        $nextDay = date("Y-m-d", strtotime("+1 day", strtotime($endDate)));
        $dayEnd =  $nextDay . " 00:00:00";

		$sql = "SELECT count(DISTINCT employee_id) " .
               "FROM hs_hr_time_event " .
               "WHERE   activity_id = $activityId AND (" .
                      "(start_time BETWEEN '$dayStart' AND '$dayEnd') OR " .
                      "(end_time BETWEEN '$dayStart' AND '$dayEnd') OR " .
                      "('$dayStart' BETWEEN start_time AND end_time) )";


		$conn = new DMLFunctions();
		$result = $conn->executeQuery($sql);

		$count = 0;
		if ($result) {
			$row = mysql_fetch_array($result, MYSQL_NUM);
			$count = $row[0];
		}
		return $count;
 	 }
}

class ProjectReportException extends Exception {

}

?>
