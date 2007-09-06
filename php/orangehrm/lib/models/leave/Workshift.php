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

require_once ROOT_PATH."/lib/dao/DMLFunctions.php";
require_once ROOT_PATH."/lib/dao/SQLQBuilder.php";
require_once ROOT_PATH."/lib/common/CommonFunctions.php";
require_once ROOT_PATH."/lib/models/hrfunct/EmpInfo.php";

class Workshift {

	const WORKSHIFT_TABLE = "hs_hr_workshift";
	const EMPLOYEE_WORKSHIFT_TABLE = "hs_hr_employee_workshift";
	const DB_FIELD_WORKSHIFT_ID = "workshift_id";
	const DB_FIELD_NAME = "name";
	const DB_FIELD_HOURS = "hours_per_day";
	const DB_FIELD_EMP_NUMBER = "emp_number";


	private $workshiftId;
	private $name;
	private $hoursPerDay;

	/**
	 * Construct workshift object
	 */
	public function __constructor() {

	}

	/**
	 * Get workshift Id
	 */
	public function getWorkshiftId() {
		return $this->workshiftId;
	}

	/**
	 * Set workshift Id
	 * @param int $workshiftId work shift id
	 */
	public function setWorkshiftId($workshiftId) {
		$this->workshiftId = $workshiftId;
	}

	/**
	 * Get name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Sets worksheet name
	 * @param string $name work sheet name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * Get hours per day
	 * @return hours per day
	 */
	public function getHoursPerDay() {
		return $this->hoursPerDay;
	}

	/**
	 * Set hours per day
	 * @param int $hoursPerDay hours per day
	 */
	public function setHoursPerDay($hoursPerDay) {
		$this->hoursPerDay = $hoursPerDay;
	}

	/**
	 * Save this workshift
	 * @return int number of rows changed (1 if update was done, 0 if not)
	 */
	public function save() {
		if (empty($this->hoursPerDay) || empty($this->name) || $this->hoursPerDay <= 0) {
			throw new WorkshiftException();
		}

		if (empty($this->workshiftId)) {
			return $this->_insert();
		} else {
			return $this->_update();
		}
	}

	private function _insert() {

		$fields[0] = self::DB_FIELD_WORKSHIFT_ID;
		$fields[1] = self::DB_FIELD_NAME;
		$fields[2] = self::DB_FIELD_HOURS;

		$this->workshiftId = UniqueIDGenerator::getInstance()->getNextID(self::WORKSHIFT_TABLE, self::DB_FIELD_WORKSHIFT_ID);
		$values[0] = $this->workshiftId;
		$values[1] = "'{$this->name}'";
		$values[2] = "'{$this->hoursPerDay}'";

		$sqlBuilder = new SQLQBuilder();
		$sqlBuilder->table_name = self::WORKSHIFT_TABLE;
		$sqlBuilder->flg_insert = 'true';
		$sqlBuilder->arr_insert = $values;
		$sqlBuilder->arr_insertfield = $fields;

		$sql = $sqlBuilder->addNewRecordFeature2();

		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($sql);
		if (!$result || mysql_affected_rows() != 1) {
			throw new WorkshiftException("Not inserted");
		}

		return mysql_affected_rows();
	}

	private function _update() {

		$updateTable = self::WORKSHIFT_TABLE;

		$fields[0] = self::DB_FIELD_NAME;
		$fields[1] = self::DB_FIELD_HOURS;

		$updateValues[0] = "'" . $this->name . "'";
		$updateValues[1] = $this->hoursPerDay;

		$updateConditions[0] = self::DB_FIELD_WORKSHIFT_ID . " = " . $this->workshiftId;

		$sqlBuilder = new SQLQBuilder();
		$query = $sqlBuilder->simpleUpdate($updateTable, $fields, $updateValues, $updateConditions);
		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);
		if ($result === false) {
			throw new WorkshiftException("Error in update");
		}

		return mysql_affected_rows();
	}

	/**
	 * Delete this workshift
	 */
	public function delete() {

		if (!CommonFunctions::isValidId($this->workshiftId)) {
			throw new WorkshiftException("Invalid id");
		}

		$arrList = array($this->workshiftId);
		$count = self::_deleteWorkshifts($arrList);

		if (mysql_affected_rows() !== 1) {
			throw new WorkshiftException("Error in Delete");
		}

	}

	private static function _deleteWorkshifts($ids) {

		$tableName = self::WORKSHIFT_TABLE;
		$arrFieldList[0] = self::DB_FIELD_WORKSHIFT_ID;

		$sqlBuilder = new SQLQBuilder();

		$sqlBuilder->table_name = $tableName;
		$sqlBuilder->flg_delete = 'true';
		$sqlBuilder->arr_delete = $arrFieldList;

		$arrList[] = $ids;
		$sqlQString = $sqlBuilder->deleteRecord($arrList);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($sqlQString);

		if ($result) {
			return mysql_affected_rows();
		} else {
			throw new WorkshiftException("Error in SQL Query");
		}
	}

	/**
	 * Assign employees to this workshift
	 * @param array $employeeIds Array of employee ids to assign
	 */
	public function assignEmployees($employeeIds) {

	}

	/**
	 * Remove all employees assigned to this workshift
	 */
	public function removeAssignedEmployees() {

	}

	/**
	 * Return list of employees who are not assigned to any workshift
	 * @return array Array of employees
	 */
	public static function getEmployeesWithoutWorkshift() {

		$fields[0] = EmpInfo::EMPLOYEE_FIELD_EMP_NUMBER;
		$fields[1] = EmpInfo::EMPLOYEE_FIELD_EMP_ID;
		$fields[2] = EmpInfo::EMPLOYEE_FIELD_FIRST_NAME;
		$fields[3] = EmpInfo::EMPLOYEE_FIELD_MIDDLE_NAME;
		$fields[4] = EmpInfo::EMPLOYEE_FIELD_LAST_NAME;

		$sqlBuilder = new SQLQBuilder();

		$sqlBuilder->table_name = EmpInfo::EMPLOYEE_TABLE_NAME;
		$sqlBuilder->flg_select = 'true';
		$sqlBuilder->arr_select = $fields;
		$sqlBuilder->field = EmpInfo::EMPLOYEE_FIELD_EMP_NUMBER;
		$sqlBuilder->field2 = self::DB_FIELD_EMP_NUMBER;
		$sqlBuilder->table2_name = self::EMPLOYEE_WORKSHIFT_TABLE;

		$sql = $sqlBuilder->selectFilter();

		$connection = new DMLFunctions();
		$result = $connection->executeQuery($sql);
		if ($result === false) {
			throw new WorkshiftException("Error in db query:" . $sql);
		}

		return self::_getEmployeesFromResults($result);

	}

	/**
	 * Return list of employees who are assigned to this workshift
	 * @return array Array of employees
	 */
	public function getAssignedEmployees() {

	}

	/**
	 * Get work shifts defined in the system
	 * @return array array of work shift objects
	 */
	public static function getWorkshifts() {

		$selectFields[0] = self::DB_FIELD_WORKSHIFT_ID;
		$selectFields[1] = self::DB_FIELD_NAME;
		$selectFields[2] = self::DB_FIELD_HOURS;

		$sqlBuilder = new SQLQBuilder();
		$query = $sqlBuilder->simpleSelect(self::WORKSHIFT_TABLE, $selectFields);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($query);

		if ($result === false) {
			throw new WorkshiftException("Error in db query:" . $query);
		}

		$workshifts = self::_getWorkshiftsFromResults($result);
		return $workshifts;
	}

	/**
	 * Get work shift with given id
	 * @param int $workshiftId Work shift id
	 * @return Workshift workshift object or null if not found
	 */
	public static function getWorkshift($workShiftId) {

		if (!CommonFunctions::isValidId($workShiftId)) {
			throw new WorkshiftException("Invalid id");
		}

		$sqlBuilder = new SQLQBuilder();

		$selectTable = self::WORKSHIFT_TABLE;

		$selectFields[0] = self::DB_FIELD_WORKSHIFT_ID;
		$selectFields[1] = self::DB_FIELD_NAME;
		$selectFields[2] = self::DB_FIELD_HOURS;

		$selectConditions[0] = self::DB_FIELD_WORKSHIFT_ID . " = " . $workShiftId;

		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($query);

		$numResults = mysql_num_rows($result);
		if ($numResults == 1) {
			$objs = self::_getWorkshiftsFromResults($result);
			return $objs[0];
		} else if ($numResults == 0) {
			return null;
		} else {
			throw new WorkshiftException("Invalid number of results returned.");
		}
	}

	/**
	 * Return array of workshift objects from the given results set
	 * @return array Array of Workshift objects
	 */
	private function _getWorkshiftsFromResults($results) {

		$workshiftArray = array();
		while($row = mysql_fetch_array($results)) {

			$workshiftObj = new Workshift();
			$workshiftObj->setWorkshiftId($row[self::DB_FIELD_WORKSHIFT_ID]);
			$workshiftObj->setName($row[self::DB_FIELD_NAME]);
			$workshiftObj->setHoursPerDay($row[self::DB_FIELD_HOURS]);

			$workshiftArray[] = $workshiftObj;
		}

		return $workshiftArray;
	}

	/**
	 * Return array of workshift objects from the given results set
	 * @return array Array of Employees from the results set
	 */
	private static function _getEmployeesFromResults($results) {

		$employees = array();
		while($row = mysql_fetch_array($results)) {
			$employees[] = $row;
		}

		return $employees;

	}

	/**
	 * Delete workshifts
	 * @param array $workShiftIds array of work sheet id's to delete
	 */
	public static function deleteWorkshifts($workshiftIds) {

		if (!is_array($workshiftIds) || empty($workshiftIds)) {
			throw new WorkshiftException("Invalid Parameter");
		}

		foreach($workshiftIds as $id) {
			if (!CommonFunctions::isValidId($id)) {
				throw new WorkshiftException("Invalid ID in array");
			}
		}

		self::_deleteWorkshifts($workshiftIds);
	}
}

class WorkshiftException extends Exception {
}
?>
