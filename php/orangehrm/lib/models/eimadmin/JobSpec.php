<?php
/*
// OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
// all the essential functionalities required for any enterprise.
// Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com

// OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
// the GNU General Public License as published by the Free Software Foundation; either
// version 2 of the License, or (at your option) any later version.

// OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
// without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU General Public License for more details.

// You should have received a copy of the GNU General Public License along with this program;
// if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
// Boston, MA  02110-1301, USA
*/

require_once ROOT_PATH . '/lib/confs/Conf.php';
require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';
require_once ROOT_PATH . '/lib/dao/SQLQBuilder.php';
require_once ROOT_PATH . '/lib/common/CommonFunctions.php';
require_once ROOT_PATH . '/lib/common/UniqueIDGenerator.php';

class JobSpec {

	const TABLE_NAME = 'hs_hr_job_spec';
	const DB_FIELD_NAME = 'jobspec_name';
	const DB_FIELD_DESC = 'jobspec_desc';
	const DB_FIELD_ID = 'jobspec_id';
	const DB_FIELD_DUTIES = 'jobspec_duties';

	private $id;
	private $name;
	private $desc;
    private $duties;

	/**
	 * Constructor
	 *
	 * @param int     $jobSpecId Job Spec ID (can be null for newly created activities)
	 */
	public function __construct($jobSpecId = null) {
		$this->id = $jobSpecId;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function setDesc($desc) {
		$this->desc = $desc;
    }

	public function setDuties($duties) {
		$this->duties = $duties;
    }

	public function getId() {
		return $this->id;
	}

	public function getName() {
		return $this->name;
	}

	public function getDesc() {
		return $this->desc;
    }

	public function getDuties() {
		return $this->duties;
    }

	/**
	 * Save JobSpec object to database
	 * @return int Returns the ID of the JobSpec
	 */
    public function save() {
		if (empty($this->name)) {
			throw new JobSpecException("Attributes not set", JobSpecException::INVALID_PARAMETER);
		}

		if (isset($this->id)) {
			return $this->_update();
		} else {
			return $this->_insert();
		}
    }

	/**
	 * Get list of job specs in a format suitable for view.php
	 */
	public static function getListForView($pageNO = 0, $schStr = '', $mode = -1, $sortField = 0, $sortOrder = 'ASC') {

		$arrFieldList[0] = self::DB_FIELD_ID;
		$arrFieldList[1] = self::DB_FIELD_NAME;
		$arrFieldList[2] = self::DB_FIELD_DESC;

		$sqlBuilder = new SQLQBuilder();

		$sqlBuilder->table_name = self::TABLE_NAME;
		$sqlBuilder->flg_select = 'true';
		$sqlBuilder->arr_select = $arrFieldList;
		$sqlQString = $sqlBuilder->passResultSetMessage($pageNO, $schStr, $mode, $sortField, $sortOrder);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($sqlQString);

		$i = 0;
		$arrayDispList = null;
		while ($line = mysql_fetch_assoc($result)) {
			$arrayDispList[$i][0] = $line[self::DB_FIELD_ID];
	    	$arrayDispList[$i][1] = $line[self::DB_FIELD_NAME];
	    	$arrayDispList[$i][2] = $line[self::DB_FIELD_DESC];
	    	$i++;
	     }

		return $arrayDispList;
	}

	/**
	 * Count job specs with given search conditions
	 * @param string $schStr Search string
	 * @param string $mode Integer giving which field to search on
	 */
	public static function getCount($schStr = '', $mode = -1) {

		$count = 0;

		$arrFieldList[0] = self::DB_FIELD_ID;
		$arrFieldList[1] = self::DB_FIELD_NAME;
		$arrFieldList[2] = self::DB_FIELD_DESC;

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = self::TABLE_NAME;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->countResultset($schStr,$mode);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($sqlQString);

		if ($result) {
			$line = mysql_fetch_array($result, MYSQL_NUM);
			$count = $line[0];
		}

	    return $count;
	}

	/**
	 * Delete given job titles
	 * @param array $ids Array of job titles to delete
	 */
	public static function delete($ids) {

		$count = 0;
		if (!is_array($ids)) {
			throw new JobSpecException("Invalid parameter to delete(): ids should be an array", JobSpecException::INVALID_PARAMETER);
		}

		foreach ($ids as $id) {
			if (!CommonFunctions::isValidId($id)) {
				throw new JobSpecException("Invalid parameter to delete(): id = $id", JobSpecException::INVALID_PARAMETER);
			}
		}

		if (!empty($ids)) {

			$sql = sprintf("DELETE FROM %s WHERE %s IN (%s)", self::TABLE_NAME,
			                self::DB_FIELD_ID, implode(",", $ids));

			$conn = new DMLFunctions();
			$result = $conn->executeQuery($sql);
			if ($result) {
				$count = mysql_affected_rows();
			}
		}
		return $count;
	}


	/**
	 * Get all job specs available in the system
	 *
	 * @return array Array of JobSpec objects
	 */
	public static function getAll() {
		return self::_getList();
	}

	/**
	 * Get job spec with given ID
	 * @param int $id The job spec ID
	 * @return JobSpec Job Spec object with given id or null if not found
	 */
	public static function getJobSpec($id) {

		if (!CommonFunctions::isValidId($id)) {
			throw new JobSpecException("Invalid parameters to getJobSpec(): id = $id", JobSpecException::INVALID_PARAMETER);
		}

		$selectCondition[] = self::DB_FIELD_ID . " = $id";
		$actList = self::_getList($selectCondition);
		$obj = count($actList) == 0 ? null : $actList[0];
		return $obj;
	}


	/**
	 * Get a list of jobs specs with the given conditions.
	 *
	 * @param array   $selectCondition Array of select conditions to use.
	 * @return array  Array of JobSpec objects. Returns an empty (length zero) array if none found.
	 */
	private static function _getList($selectCondition = null) {

		$fields[0] = self::DB_FIELD_ID;
		$fields[1] = self::DB_FIELD_NAME;
		$fields[2] = self::DB_FIELD_DESC;
		$fields[3] = self::DB_FIELD_DUTIES;

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
	 * Insert new object to database
	 */
	private function _insert() {

		$fields[0] = self::DB_FIELD_ID;
		$fields[1] = self::DB_FIELD_NAME;
		$fields[2] = self::DB_FIELD_DESC;
		$fields[3] = self::DB_FIELD_DUTIES;

		$this->id = UniqueIDGenerator::getInstance()->getNextID(self::TABLE_NAME, self::DB_FIELD_ID);
		$values[0] = $this->id;
		$values[1] = "'{$this->name}'";
		$values[2] = "'{$this->desc}'";
		$values[3] = "'{$this->duties}'";

		$sqlBuilder = new SQLQBuilder();
		$sqlBuilder->table_name = self::TABLE_NAME;
		$sqlBuilder->flg_insert = 'true';
		$sqlBuilder->arr_insert = $values;
		$sqlBuilder->arr_insertfield = $fields;

		$sql = $sqlBuilder->addNewRecordFeature2();

		$conn = new DMLFunctions();

		$result = $conn->executeQuery($sql);
		if (!$result || (mysql_affected_rows() != 1)) {
			throw new JobSpecException("Insert failed. ", JobSpecException::DB_ERROR);
		}

		return $this->id;
	}

	/**
	 * Update existing object
	 */
	private function _update() {

		$fields[0] = self::DB_FIELD_ID;
		$fields[1] = self::DB_FIELD_NAME;
		$fields[2] = self::DB_FIELD_DESC;
		$fields[3] = self::DB_FIELD_DUTIES;

		$values[0] = $this->id;
		$values[1] = "'{$this->name}'";
		$values[2] = "'{$this->desc}'";
		$values[3] = "'{$this->duties}'";

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
			throw new JobSpecException("Update failed. SQL=$sql", JobSpecException::DB_ERROR);
		}
		return $this->id;
	}

    /**
     * Creates a JobSpec object from a resultset row
     *
     * @param array $row Resultset row from the database.
     * @return JobSpec JobSpec object.
     */
    private static function _createFromRow($row) {
    	$spec = new JobSpec($row[self::DB_FIELD_ID]);
        $spec->setName($row[self::DB_FIELD_NAME]);
        $spec->setDesc($row[self::DB_FIELD_DESC]);
        $spec->setDuties($row[self::DB_FIELD_DUTIES]);
        return $spec;
    }

}

class JobSpecException extends Exception {
	const INVALID_PARAMETER = 0;
	const DB_ERROR = 1;
}

?>
