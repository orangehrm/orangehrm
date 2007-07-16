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
require_once ROOT_PATH.'/lib/models/hrfunct/EmpInfo.php';
require_once ROOT_PATH .'/lib/models/eimadmin/Projects.php';

/**
 * This class implements the table gateway pattern and provides
 * methods to manipulate project admins
 */
class ProjectAdminGateway {

	const TABLE_NAME                      = 'hs_hr_project_admin';
	const PROJECT_ADMIN_FIELD_PROJECT_ID  = 'project_id';
	const PROJECT_ADMIN_FIELD_EMP_NUMBER  = 'emp_number';

	const EMPLOYEE_TABLE_NAME             = 'hs_hr_employee';
	const EMPLOYEE_FIELD_EMP_NUMBER       = 'emp_number';
	const EMPLOYEE_FIELD_FIRST_NAME       = 'emp_firstname';
	const EMPLOYEE_FIELD_LAST_NAME        = 'emp_lastname';
	const EMPLOYEE_FIELD_EMP_ID           = 'employee_id';

	/**
	 * Constructor
	 *
	 */
	public function __construct() {
	}

	/**
	 * Adds the given employee as a admin to the given project.
	 * If the employee is already an admin, the request is ignored.
	 *
	 * @param int $projectId The project ID
	 * @param int $empNumber The employee number
	 * @return true if successful
	 */
	public function addAdmin($projectId, $empNumber) {

		if (!CommonFunctions::isValidId($projectId) || !CommonFunctions::isValidId($empNumber)) {
			throw new ProjectAdminException("Invalid parameters to addAdmin(): emp_number = $empNumber , " .
											"projectId = $projectId");
		}

		$result = true;
		if (!$this->isAdmin($empNumber, $projectId)) {

			$fields[0] = self::PROJECT_ADMIN_FIELD_PROJECT_ID;
			$fields[1] = self::PROJECT_ADMIN_FIELD_EMP_NUMBER;

			$values[0] = "'{$projectId}'";
			$values[1] = "'{$empNumber}'";

			$sqlBuilder = new SQLQBuilder();
			$sqlBuilder->table_name = self::TABLE_NAME;
			$sqlBuilder->flg_insert = 'true';
			$sqlBuilder->arr_insert = $values;
			$sqlBuilder->arr_insertfield = $fields;

			$sql = $sqlBuilder->addNewRecordFeature2();

			$conn = new DMLFunctions();
			$result = $conn->executeQuery($sql);
			if (!$result || (mysql_affected_rows() != 1)) {
				$result = false;
			} else {
				$this->id = mysql_insert_id();
			}
		}
		return $result;

	}

	/**
	 * Removes the given employee as an admin from the given project.
	 *
	 * @param int $projectId The project ID
	 * @param int $empNumber The employee number
	 *
	 * @return bool true if removed, false otherwise.
	 */
	public function removeAdmin($projectId, $empNumber) {

		if (!CommonFunctions::isValidId($projectId) || !CommonFunctions::isValidId($empNumber)) {
			throw new ProjectAdminException("Invalid parameters to removeAdmin(): emp_number = $empNumber , " .
											"projectId = $projectId");
		}

		$num = $this->removeAdmins($projectId, array($empNumber));

		if ($num > 1) {
			throw new ProjectAdminException("Duplicate entries removed for admin. emp_number = $empNumber , " .
											"projectId = $projectId");
		}

		return ($num == 1);
	}

	/**
	 * Removes the given employees as admins from the given project.
	 *
	 * @param int   $projectId The project ID
	 * @param array $empList Array of employee numbers to remove
	 *
	 * @return int  Number of admins actually removed.
	 */
	public function removeAdmins($projectId, $empList) {

		if (!CommonFunctions::isValidId($projectId)) {
			throw new ProjectAdminException("Invalid parameter to removeAdmins(): projectId = $projectId");
		}

		if (!is_array($empList)) {
			throw new ProjectAdminException("Invalid parameter to removeAdmins(): empList");
		}

		foreach ($empList as $employee) {
			if (!CommonFunctions::isValidId($employee)) {
				throw new ProjectAdminException("Invalid parameter to removeAdmins(): employee id = $employee");
			}
		}

		$count = 0;
		if (!empty($empList)) {
			$sql = sprintf("DELETE FROM %s WHERE %s = %d AND %s IN (%s)", self::TABLE_NAME,
			                self::PROJECT_ADMIN_FIELD_PROJECT_ID, $projectId,
			                self::PROJECT_ADMIN_FIELD_EMP_NUMBER, implode(",", $empList));

			$conn = new DMLFunctions();
			$result = $conn->executeQuery($sql);
			if ($result) {
				$count = mysql_affected_rows();
			}
		}

		return $count;
	}

	/**
	 * Gets a list of admins for the given project.
	 *
	 * @param int $projectId The project ID
	 */
	public function getAdmins($projectId) {

		if (!CommonFunctions::isValidId($projectId)) {
			throw new ProjectAdminException("Invalid parameters to getAdmins(): projectId = $projectId");
		}

		$fields[0] = "a.`" . self::PROJECT_ADMIN_FIELD_EMP_NUMBER . "`";
		$fields[1] = "a.`" . self::PROJECT_ADMIN_FIELD_PROJECT_ID . "`";
		$fields[2] = "b.`" . self::EMPLOYEE_FIELD_FIRST_NAME . "`";
		$fields[3] = "b.`" . self::EMPLOYEE_FIELD_LAST_NAME . "`";

		$tables[0] = "`" . self::TABLE_NAME. "` a ";
		$tables[1] = "`" . self::EMPLOYEE_TABLE_NAME . "` b ";

		$joinConditions[1] = "a.`" . self::PROJECT_ADMIN_FIELD_EMP_NUMBER .
							 "` = b.`" . self::EMPLOYEE_FIELD_EMP_NUMBER . "`";

		$selectConditions[0] = "a.`" . self::PROJECT_ADMIN_FIELD_PROJECT_ID . "`= $projectId ";

		$sqlBuilder = new SQLQBuilder();
		$sql = $sqlBuilder->selectFromMultipleTable($fields, $tables, $joinConditions, $selectConditions);

		$conn = new DMLFunctions();
		$results = $conn->executeQuery($sql);

		$admins = array();

		if ($results) {
			while ($row = mysql_fetch_assoc($results)) {
		    	$admins[] = $this->_createFromRow($row);
		    }
		}

	     return $admins;
	}

	/**
     * Checks whether the given employee is a project admin for the given project
	 *
	 * @param int $empNumber The employee number
	 * @param int $projectId The project ID (If null all projects are checked)
	 * @return boolean True if an admin, false otherwise
	 */
	public function isAdmin($empNumber, $projectId = null) {

		if (!CommonFunctions::isValidId($empNumber)) {
			throw new ProjectAdminException("Invalid empNumber");
		}

		if (!CommonFunctions::isValidId($empNumber) || (!is_null($projectId) && !CommonFunctions::isValidId($projectId)) ) {
			throw new ProjectAdminException("Invalid parameters to isAdmin(): emp_number = $empNumber , " .
											"projectId = " . is_null($projectId));
		}

		$admin = false;

		// Include deleted if project ID given
		$includeDeleted = !empty($projectId);
		$results = $this->_getProjects($empNumber, $projectId, $includeDeleted);

		if ($results) {
			$numRows = mysql_num_rows($results);
			$admin = ($numRows >= 1);
		}

		return $admin;
	}

	/**
	 * Gets a array of projects for which the given employee is a project admin
	 *
	 * @param int  $empNumber       The employee number
	 * @param bool $includeDeleted Included deleted projects
	 * @return array Array of Projects objects
	 */
	public function getProjectsForAdmin($empNumber, $includeDeleted = false) {

		if (!CommonFunctions::isValidId($empNumber)) {
			throw new ProjectAdminException("Invalid parameters to getProjectsForAdmin(): empNumber = $empNumber");
		}

		$results = $this->_getProjects($empNumber, null, $includeDeleted);

		if ($results) {
		    $projects = Projects::projectObjArr($results);
		}

		if (empty($projects)) {
			$projects = array();
		}

	     return $projects;
	}

	/**
	 * Queries the hs_hr_project_admin and hs_hr_project tables with the given conditions
	 *
	 * @param int  $empNumber The employee number to filter by
	 * @param int  $projectId The project ID to filter by
	 * @param bool $includeDeleted Whether deleted projects should be included
	 *
	 * @return resource Resource returned from the database.
	 */
	private function _getProjects($empNumber, $projectId = null, $includeDeleted = false) {

		$fields[0] = "b.`" . Projects::PROJECT_DB_FIELD_PROJECT_ID . "`";
		$fields[1] = "b.`" . Projects::PROJECT_DB_FIELD_CUSTOMER_ID . "`";
		$fields[2] = "b.`" . Projects::PROJECT_DB_FIELD_NAME . "`";
		$fields[3] = "b.`" . Projects::PROJECT_DB_FIELD_DESCRIPTION . "`";
		$fields[4] = "b.`" . Projects::PROJECT_DB_FIELD_DELETED . "`";

		$tables[0] = "`" . self::TABLE_NAME. "` a ";
		$tables[1] = "`" . Projects::PROJECT_DB_TABLE."` b ";

		$selectConditions[] = "a.`" . self::PROJECT_ADMIN_FIELD_EMP_NUMBER . "`= $empNumber ";

		if (!$includeDeleted) {
			$selectConditions[] = "b.`" . Projects::PROJECT_DB_FIELD_DELETED . "`= " . Projects::PROJECT_NOT_DELETED;
		}

		if (!empty($projectId)) {
			$selectConditions[] = "a.`" . self::PROJECT_ADMIN_FIELD_PROJECT_ID . "`= $projectId ";
		}

		$joinConditions[1] = "a.`" . self::PROJECT_ADMIN_FIELD_PROJECT_ID .
							 "` = b.`" . Projects::PROJECT_DB_FIELD_PROJECT_ID . "`";

		$sqlBuilder = new SQLQBuilder();
		$sql = $sqlBuilder->selectFromMultipleTable($fields, $tables, $joinConditions, $selectConditions);

		$conn = new DMLFunctions();
		$results = $conn->executeQuery($sql);
	     return $results;
	}

	/**
	 * Creates a ProjectAdmin object from a resultset row
	 *
	 * @param array $row Resultset row from the database.
	 * @return ProjectAdmin ProjectAdmin object.
	 */
	private function _createFromRow($row) {

		$tmp = new ProjectAdmin();
		$tmp->setEmpNumber($row[self::PROJECT_ADMIN_FIELD_EMP_NUMBER]);
		$tmp->setLastName($row[self::PROJECT_ADMIN_FIELD_PROJECT_ID]);
		$tmp->setFirstName($row[self::EMPLOYEE_FIELD_FIRST_NAME]);
		$tmp->setLastName($row[self::EMPLOYEE_FIELD_LAST_NAME]);

		return $tmp;
	}

}

class ProjectAdminException extends Exception {

}
?>

