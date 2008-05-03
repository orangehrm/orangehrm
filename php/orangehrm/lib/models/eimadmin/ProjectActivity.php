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
require_once ROOT_PATH.'/lib/common/UniqueIDGenerator.php';

class ProjectActivity {

	const TABLE_NAME           = 'hs_hr_project_activity';
	const DB_FIELD_NAME        = 'name';
	const DB_FIELD_PROJECT_ID  = 'project_id';
	const DB_FIELD_ACTIVITY_ID = 'activity_id';
	const DB_FIELD_DELETED     = 'deleted';
	const ACTIVITY_ATTENDANCE  = 0;

	/**
	 * Class Attributes
	 */
	protected $id = null;
	protected $projectId;
	protected $name;
	protected $deleted = false;

	public function getId() {
		return $this->id;
	}

	public function getProjectId() {
		return $this->projectId;
	}

	public function setProjectId($projectId) {
		$this->projectId = $projectId;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function isDeleted() {
		return $this->deleted;
	}

	public function setDeleted($deleted) {
		$this->deleted = $deleted;
	}

	/**
	 * Constructor
	 *
	 * @param int     $activityId Activity ID (can be null for newly created activities)
	 */
	public function __construct($activityId = null) {
		$this->id = $activityId;
	}

	/**
	 * Save the project activity to the database.
	 *
	 * If this is a new project activity a new entry is created. If not
	 * the exisiting entry is updated.
	 */
	public function save() {

		if (empty($this->name) || !CommonFunctions::isValidId($this->projectId)) {
			throw new ProjectActivityException("Attributes not set");
		}

		if (isset($this->id)) {
			$this->_update();
		} else {
			$this->_insert();
		}
	}

	private function _insert() {

		$fields[0] = self::DB_FIELD_ACTIVITY_ID;
		$fields[1] = self::DB_FIELD_NAME;
		$fields[2] = self::DB_FIELD_PROJECT_ID;
		$fields[3] = self::DB_FIELD_DELETED;

		$this->id = UniqueIDGenerator::getInstance()->getNextID(self::TABLE_NAME, self::DB_FIELD_ACTIVITY_ID);
		$values[0] = $this->id;
		$values[1] = "'{$this->name}'";
		$values[2] = "'{$this->projectId}'";
		$values[3] = "'". intval($this->deleted) ."'";

		$sqlBuilder = new SQLQBuilder();
		$sqlBuilder->table_name = self::TABLE_NAME;
		$sqlBuilder->flg_insert = 'true';
		$sqlBuilder->arr_insert = $values;
		$sqlBuilder->arr_insertfield = $fields;

		$sql = $sqlBuilder->addNewRecordFeature2();

		$conn = new DMLFunctions();

		$result = $conn->executeQuery($sql);
		if (!$result || (mysql_affected_rows() != 1)) {
			throw new ProjectActivityException("Insert failed. ");
		}
	}

	private function _update() {

		$fields[0] = self::DB_FIELD_ACTIVITY_ID;
		$fields[1] = self::DB_FIELD_NAME;
		$fields[2] = self::DB_FIELD_PROJECT_ID;
		$fields[3] = self::DB_FIELD_DELETED;

		$values[0] = "'{$this->id}'";
		$values[1] = "'{$this->name}'";
		$values[2] = "'{$this->projectId}'";
		$values[3] = "'". intval($this->deleted) ."'";

		$sqlBuilder = new SQLQBuilder();
		$sqlBuilder->table_name = self::TABLE_NAME;
		$sqlBuilder->flg_update = 'true';
		$sqlBuilder->arr_update = $fields;
		$sqlBuilder->arr_updateRecList = $values;

		$sql = $sqlBuilder->addUpdateRecord1(0);

		$conn = new DMLFunctions();
		$result = $conn->executeQuery($sql);

		// Here we don't check mysql_affected_rows because update may be called
		// without any changes.
		if (!$result) {
			throw new ProjectActivityException("Update failed. SQL=$sql");
		}
	}

	/**
	 * Get a list of project activities for the given project
	 *
	 * @param int     $projectId      The project ID
	 * @param boolean $includeDeleted Should deleted activities be included
	 * @return array  Array of ProjectActivity objects. Returns an empty (length zero) array if none found.
	 */
	public static function getActivityList($projectId, $includeDeleted = false) {

		if (!CommonFunctions::isValidId($projectId)) {
			throw new ProjectActivityException("Invalid parameters to getActivityList(): projectId = $projectId");
		}

		$selectCondition[] = self::DB_FIELD_PROJECT_ID . " = $projectId";
		$selectCondition[] = "`".self::DB_FIELD_ACTIVITY_ID."` != '".self::ACTIVITY_ATTENDANCE."'";

		if (!$includeDeleted) {
			$selectCondition[] = self::DB_FIELD_DELETED . " = 0";
		}

		$actList = self::_getList($selectCondition);
		return $actList;
	}


	/**
	 * Get project activity with given ID.
	 *
	 * @param int $activityId The activity ID of the activity to return
	 *
	 * @return ProjectActivity Project activity object with given Id or null if not found
	 */
	public static function getActivity($activityId) {

		if (!CommonFunctions::isValidId($activityId)) {
			throw new ProjectActivityException("Invalid parameters to getActivity(): activityId = $activityId");
		}

		$selectCondition[] = self::DB_FIELD_ACTIVITY_ID . " = $activityId";
		$selectCondition[] = "`".self::DB_FIELD_ACTIVITY_ID."` != '".self::ACTIVITY_ATTENDANCE."'";

		$actList = self::_getList($selectCondition);
		$obj = count($actList) == 0 ? null : $actList[0];
		return $obj;
	}

	/**
	 * Get project activities with given name
	 *
	 * @param int    $projectId    The project Id
	 * @param string $activityName The activity name
	 *
	 * @return array of project activities with given name.
	 */
	public static function getActivitiesWithName($projectId, $activityName, $includeDeleted = false) {

		if (!CommonFunctions::isValidId($projectId)) {
			throw new ProjectActivityException("Invalid parameters to getActivitiesWithName(): projectId = $projectId");
		}

		$activityName = mysql_real_escape_string($activityName);
		$selectCondition[] = self::DB_FIELD_NAME . " = '$activityName'";
		$selectCondition[] = self::DB_FIELD_PROJECT_ID . " = $projectId";
		if (!$includeDeleted) {
			$selectCondition[] = self::DB_FIELD_DELETED . " = 0";
		}

		$actList = self::_getList($selectCondition);
		return $actList;
	}

	/**
	 * Deletes the given activities
	 *
	 * @param int   projectId    If set, only activities of this project is affected.
	 * @param array $activityIds The list of activities to delete
	 *
	 * @return int Number of activites deleted.
	 */
	public static function deleteActivities($activityIds, $projectId = null) {

		$count = 0;

		if (!is_null($projectId) && !CommonFunctions::isValidId($projectId)) {
			throw new ProjectActivityException("Invalid parameters to deleteActivities(): projectId = $projectId");
		}

		if (!is_array($activityIds)) {
			throw new ProjectActivityException("Invalid parameter to deleteActivities(): activityIds should be an array");
		}

		foreach ($activityIds as $activityId) {
			if (!CommonFunctions::isValidId($activityId)) {
				throw new ProjectActivityException("Invalid parameter to deleteActivities(): activity id = $activityId");
			}
		}

		if (!empty($activityIds)) {

			$sql = sprintf("UPDATE %s SET %s = 1 WHERE %s IN (%s)", self::TABLE_NAME,
			                self::DB_FIELD_DELETED, self::DB_FIELD_ACTIVITY_ID, implode(",", $activityIds));

			if (!empty($projectId)) {
				$sql .= " AND " . self::DB_FIELD_PROJECT_ID . " = $projectId";
			}

			$conn = new DMLFunctions();
			$result = $conn->executeQuery($sql);
			if ($result) {
				$count = mysql_affected_rows();
			}
		}
		return $count;
	}

	/**
	 * Get a list of project activities with the given conditions.
	 *
	 * @param array   $selectCondition Array of select conditions to use.
	 * @return array  Array of ProjectActivity objects. Returns an empty (length zero) array if none found.
	 */
	private static function _getList($selectCondition = null) {

		$fields[0] = self::DB_FIELD_ACTIVITY_ID;
		$fields[1] = self::DB_FIELD_NAME;
		$fields[2] = self::DB_FIELD_PROJECT_ID;
		$fields[3] = self::DB_FIELD_DELETED;

		$sqlBuilder = new SQLQBuilder();
		$sql = $sqlBuilder->simpleSelect(self::TABLE_NAME, $fields, $selectCondition);

		$actList = array();

		$conn = new DMLFunctions();
		$result = $conn->executeQuery($sql);

		while ($result && ($row = mysql_fetch_assoc($result))) {
			$actList[] = self::_createFromRow($row);
		}

		return $actList;
	}

	/**
	 * Creates a ProjectActivity object from a resultset row
	 *
	 * @param array $row Resultset row from the database.
	 * @return ProjectActivity Project activity object.
	 */
	private static function _createFromRow($row) {

		$tmp = new ProjectActivity($row[self::DB_FIELD_ACTIVITY_ID]);
		$tmp->setProjectId($row[self::DB_FIELD_PROJECT_ID]);
		$tmp->setName($row[self::DB_FIELD_NAME]);
		$tmp->setDeleted((bool)$row[self::DB_FIELD_DELETED]);
		return $tmp;
	}

}

class ProjectActivityException extends Exception {
}

?>
