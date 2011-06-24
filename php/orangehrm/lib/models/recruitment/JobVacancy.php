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
require_once ROOT_PATH . '/lib/common/SearchObject.php';

class JobVacancy {

	const TABLE_NAME = 'hs_hr_job_vacancy';

	/** Database fields */
	const DB_FIELD_VACANCY_ID = 'vacancy_id';
	const DB_FIELD_JOBTITLE_CODE = 'jobtit_code';
	const DB_FIELD_MANAGER_ID = 'manager_id';
	const DB_FIELD_ACTIVE = 'active';
	const DB_FIELD_DESCRIPTION = 'description';

	const FIELD_JOB_TITLE_NAME = 'job_title_name';
	const FIELD_MANAGER_NAME = 'manager_name';

	const STATUS_ACTIVE = 1;
	const STATUS_INACTIVE = 0;

	/** Field order */
	const SORT_FIELD_NONE = -1;
	const SORT_FIELD_VACANCY_ID = 0;
	const SORT_FIELD_JOBTITLE_NAME = 1;
	const SORT_FIELD_MANAGER_NAME = 2;
	const SORT_FIELD_ACTIVE = 3;
	const SORT_FIELD_DESCRIPTION = 4;

	private $id;
	private $jobTitleCode;
	private $managerId;
    private $active;
    private $description;

    private $managerName;
    private $jobTitleName;

	/**
	 * Constructor
	 *
	 * @param int $vacancyId Vacancy ID (can be null for newly created vacancies)
	 */
	public function __construct($vacancyId = null) {
		$this->id = $vacancyId;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function setJobTitleCode($jobTitleCode) {
		$this->jobTitleCode = $jobTitleCode;
	}

	public function setManagerId($managerId) {
		$this->managerId = $managerId;
	}

	public function setActive($active) {
		$this->active = (bool)$active;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	public function setManagerName($name) {
	    $this->managerName = $name;
	}

	public function setJobTitleName($name) {
	    $this->jobTitleName = $name;
	}

	public function getId() {
		return $this->id;
	}

	public function getJobTitleCode() {
		return $this->jobTitleCode;
	}

	public function getManagerId() {
		return $this->managerId;
	}

	public function isActive() {
		return $this->active;
	}

	public function getDescription() {
		return $this->description;
	}

	public function getManagerName() {
	    return $this->managerName;
	}

	public function getJobTitleName() {
	    return $this->jobTitleName;
	}

	/**
	 * Save JobVacancy object to database
	 * @return int Returns the ID of the JobVacancy
	 */
    public function save() {
		if (isset($this->id)) {
			return $this->_update();
		} else {
			return $this->_insert();
		}
    }

	/**
	 * Get list of job vacancies in a format suitable for view.php
	 * TODO: May have to be imporoved to support arrays for schStr and mode
	 *
	 * @param int $pageNo The page number. 0 to fetch all
	 * @param string $searchStr The search string
	 * @param int $searchfieldNo which field to search on
	 * @param int $sortField The field to sort by
	 * @param string $sortOrder Sort Order (one of ASC or DESC)
	 */
	public static function getListForView($pageNO = 0, $searchStr = '', $searchFieldNo = self::SORT_FIELD_NONE, $sortField = self::SORT_FIELD_VACANCY_ID, $sortOrder = 'ASC') {

		$count = 0;
		$fields[0] = "a." . self::DB_FIELD_VACANCY_ID;
		$fields[1] = "c.jobtit_name";
		$fields[2] = "CONCAT(b.`emp_firstname`, ' ', b.`emp_lastname`)";
		$fields[3] = "a." . self::DB_FIELD_ACTIVE;
		$fields[4] = "a." . self::DB_FIELD_DESCRIPTION;

		$tables[0] = self::TABLE_NAME . ' a';
		$tables[1] = 'hs_hr_employee b';
		$tables[2] = 'hs_hr_job_title c';

		$joinConditions[1] = 'a.' . self::DB_FIELD_MANAGER_ID . ' = b.emp_number';
		$joinConditions[2] = 'a.jobtit_code = c.jobtit_code';

		$sysConst = new sysConf();
		$limit = null;
		if ($pageNO > 0) {
			$pageNO--;
			$pageNO *= $sysConst->itemsPerPage;
			$limit = "{$pageNO}, {$sysConst->itemsPerPage}";
		}

		$sortBy = null;
		if (($sortField >= 0) && ($sortField < count($fields))) {
			$sortBy = $fields[$sortField];
		}

		$selectConditions = null;
        if (($searchFieldNo >= 0) && ($searchFieldNo < count($fields)) && (trim($searchStr) != '')) {

            if ($searchFieldNo == self::SORT_FIELD_ACTIVE) {
            	$active = ($searchStr) ? self::STATUS_ACTIVE : self::STATUS_INACTIVE;
                $selectConditions[] = "{$fields[$searchFieldNo]} = " . $active;
            } else {
            	$filteredSearch = mysql_real_escape_string($searchStr);
            	$selectConditions[] = "{$fields[$searchFieldNo]} LIKE '" . $filteredSearch . "%'";
            }
        }

		$sqlBuilder = new SQLQBuilder();
		$sqlQString = $sqlBuilder->selectFromMultipleTable($fields, $tables, $joinConditions, $selectConditions, null, $sortBy, $sortOrder, $limit);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($sqlQString);

		$i = 0;
		$arrayDispList = null;
		while ($line = mysql_fetch_array($result)) {
			$arrayDispList[$i][0] = $line[self::SORT_FIELD_VACANCY_ID];
	    	$arrayDispList[$i][1] = $line[self::SORT_FIELD_JOBTITLE_NAME];
	    	$arrayDispList[$i][2] = $line[self::SORT_FIELD_MANAGER_NAME];
	    	$arrayDispList[$i][3] = $line[self::SORT_FIELD_ACTIVE];
			$arrayDispList[$i][4] = $line[self::SORT_FIELD_DESCRIPTION];
	    	$i++;
	     }

		return $arrayDispList;
	}

	/**
	 * Count job vacancys with given search conditions
	 * @param string $searchStr Search string
	 * @param string $searchFieldNo Integer giving which field to search on
	 */
	public static function getCount($searchStr = '', $searchFieldNo = self::SORT_FIELD_NONE) {

		$count = 0;
		$fields[0] = "a." . self::DB_FIELD_VACANCY_ID;
		$fields[1] = "c.jobtit_name";
		$fields[2] = "CONCAT(b.`emp_firstname`, ' ', b.`emp_lastname`)";
		$fields[3] = "a." . self::DB_FIELD_ACTIVE;
		$fields[4] = "a." . self::DB_FIELD_DESCRIPTION;

		$tables[0] = self::TABLE_NAME . ' a';
		$tables[1] = 'hs_hr_employee b';
		$tables[2] = 'hs_hr_job_title c';

		$joinConditions[1] = 'a.' . self::DB_FIELD_MANAGER_ID . ' = b.emp_number';
		$joinConditions[2] = 'a.jobtit_code = c.jobtit_code';

		$selectConditions = null;
        if (($searchFieldNo >= 0) && ($searchFieldNo < count($fields)) && (trim($searchStr) != '')) {

            if ($searchFieldNo == self::SORT_FIELD_ACTIVE) {
            	$active = ($searchStr) ? self::STATUS_ACTIVE : self::STATUS_INACTIVE;
                $selectConditions[] = "{$fields[$searchFieldNo]} = " . $active;
            } else {
            	$filteredSearch = mysql_real_escape_string($searchStr);
            	$selectConditions[] = "{$fields[$searchFieldNo]} LIKE '" . $filteredSearch . "%'";
            }
        }

		$sqlBuilder = new SQLQBuilder();
		$sql = $sqlBuilder->countFromMultipleTables($tables, $joinConditions, $selectConditions);
		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($sql);

		if ($result) {
			$line = mysql_fetch_array($result, MYSQL_NUM);
			$count = $line[0];
		}

	    return $count;
	}

	/**
	 * Delete given job vacancies
	 * @param array $ids Array of job vacancy ID's to delete
	 */
	public static function delete($ids) {

		$count = 0;
		if (!is_array($ids)) {
			throw new JobVacancyException("Invalid parameter to delete(): ids should be an array", JobVacancyException::INVALID_PARAMETER);
		}

		foreach ($ids as $id) {
			if (!CommonFunctions::isValidId($id)) {
				throw new JobVacancyException("Invalid parameter to delete(): id = $id", JobVacancyException::INVALID_PARAMETER);
			}
		}

		if (!empty($ids)) {

			$sql = sprintf("DELETE FROM %s WHERE %s IN (%s)", self::TABLE_NAME,
			                self::DB_FIELD_VACANCY_ID, implode(",", $ids));

			$conn = new DMLFunctions();
                        $conn->executeQuery("SET @orangehrm_action_name = 'DELETE JOB VACANCY';");
			$result = $conn->executeQuery($sql);
			if ($result) {
				$count = mysql_affected_rows();
			}
		}
		return $count;
	}


	/**
	 * Get all job vacancies available in the system
	 *
	 * @return array Array of JobVacancy objects
	 */
	public static function getAll() {
		return self::_getList();
	}

	/**
	 * Get active job vacancies available in the system
	 *
	 * @return array Array of active JobVacancy objects
	 */
	public static function getActive() {
		$selectCondition[] = self::DB_FIELD_ACTIVE . ' = ' . self::STATUS_ACTIVE;
		return self::_getList($selectCondition);
	}

	/**
	 * Get job vacancy with given ID
	 * @param int $id The job vacancy ID
	 * @return JobVacancy Job Vacancy object with given id or null if not found
	 */
	public static function getJobVacancy($id) {

		if (!CommonFunctions::isValidId($id)) {
			throw new JobVacancyException("Invalid parameters to getJobVacancy(): id = $id", JobVacancyException::INVALID_PARAMETER);
		}

		$selectCondition[] = self::DB_FIELD_VACANCY_ID . " = $id";
		$actList = self::_getList($selectCondition);
		$obj = count($actList) == 0 ? null : $actList[0];
		return $obj;
	}


	/**
	 * Get a list of jobs vacancies with the given conditions.
	 *
	 * @param array   $selectCondition Array of select conditions to use.
	 * @return array  Array of JobVacancy objects. Returns an empty (length zero) array if none found.
	 */
	private static function _getList($selectCondition = null) {

		$fields[0] = "a. " . self::DB_FIELD_VACANCY_ID;
		$fields[1] = "a. " . self::DB_FIELD_JOBTITLE_CODE;
		$fields[2] = "c.jobtit_name AS " . self::FIELD_JOB_TITLE_NAME;
		$fields[3] = "a. " . self::DB_FIELD_MANAGER_ID;
		$fields[4] = "CONCAT(b.`emp_firstname`, ' ', b.`emp_lastname`) AS " . self::FIELD_MANAGER_NAME;
		$fields[5] = "a. " . self::DB_FIELD_ACTIVE;
		$fields[6] = "a. " . self::DB_FIELD_DESCRIPTION;

		$tables[0] = self::TABLE_NAME . ' a';
		$tables[1] = 'hs_hr_employee b';
		$tables[2] = 'hs_hr_job_title c';

		$joinConditions[1] = 'a.' . self::DB_FIELD_MANAGER_ID . ' = b.emp_number';
		$joinConditions[2] = 'a.jobtit_code = c.jobtit_code';

		$sqlBuilder = new SQLQBuilder();
		$sql = $sqlBuilder->selectFromMultipleTable($fields, $tables, $joinConditions, $selectCondition);

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

		$this->id = UniqueIDGenerator::getInstance()->getNextID(self::TABLE_NAME, self::DB_FIELD_VACANCY_ID);
		$fields[0] = self::DB_FIELD_VACANCY_ID;
		$fields[1] = self::DB_FIELD_JOBTITLE_CODE;
		$fields[2] = self::DB_FIELD_MANAGER_ID;
		$fields[3] = self::DB_FIELD_ACTIVE;
		$fields[4] = self::DB_FIELD_DESCRIPTION;

		$values[0] = $this->id;
		$values[1] = "'{$this->jobTitleCode}'";
		$values[2] = $this->managerId;
		$values[3] = ($this->active) ? self::STATUS_ACTIVE : self::STATUS_INACTIVE;
		$values[4] = "'{$this->description}'";

		$sqlBuilder = new SQLQBuilder();
		$sqlBuilder->table_name = self::TABLE_NAME;
		$sqlBuilder->flg_insert = 'true';
		$sqlBuilder->arr_insert = $values;
		$sqlBuilder->arr_insertfield = $fields;

		$sql = $sqlBuilder->addNewRecordFeature2();

		$conn = new DMLFunctions();
        $conn->executeQuery("SET @orangehrm_action_name = 'ADD JOB VACANCY';");

        $result = $conn->executeQuery($sql);

		if (!$result || (mysql_affected_rows() != 1)) {
			throw new JobVacancyException("Insert failed. ", JobVacancyException::DB_ERROR);
		}

		return $this->id;
	}

	/**
	 * Update existing object
	 */
	private function _update() {

		$fields[0] = self::DB_FIELD_VACANCY_ID;
		$fields[1] = self::DB_FIELD_JOBTITLE_CODE;
		$fields[2] = self::DB_FIELD_MANAGER_ID;
		$fields[3] = self::DB_FIELD_ACTIVE;
		$fields[4] = self::DB_FIELD_DESCRIPTION;

		$values[0] = $this->id;
		$values[1] = "'{$this->jobTitleCode}'";
		$values[2] = $this->managerId;
		$values[3] = ($this->active) ? self::STATUS_ACTIVE : self::STATUS_INACTIVE;
		$values[4] = "'{$this->description}'";

		$sqlBuilder = new SQLQBuilder();
		$sqlBuilder->table_name = self::TABLE_NAME;
		$sqlBuilder->flg_update = 'true';
		$sqlBuilder->arr_update = $fields;
		$sqlBuilder->arr_updateRecList = $values;

		$sql = $sqlBuilder->addUpdateRecord1(0);

		$conn = new DMLFunctions();
                $conn->executeQuery("SET @orangehrm_action_name = 'CHANGE JOB VACANCY';");
		$result = $conn->executeQuery($sql);

		// Here we don't check mysql_affected_rows because update may be called
		// without any changes.
		if (!$result) {
			throw new JobVacancyException("Update failed. SQL=$sql", JobVacancyException::DB_ERROR);
		}
		return $this->id;
	}

    /**
     * Creates a JobVacancy object from a resultset row
     *
     * @param array $row Resultset row from the database.
     * @return JobVacancy JobVacancy object.
     */
    private static function _createFromRow($row) {

    	$vacancy = new JobVacancy($row[self::DB_FIELD_VACANCY_ID]);
		$vacancy->setJobTitleCode($row[self::DB_FIELD_JOBTITLE_CODE]);
		$vacancy->setManagerId($row[self::DB_FIELD_MANAGER_ID]);
		$vacancy->setActive((bool)$row[self::DB_FIELD_ACTIVE]);
		$vacancy->setDescription($row[self::DB_FIELD_DESCRIPTION]);

		if (isset($row[self::FIELD_JOB_TITLE_NAME])) {
			$vacancy->setJobTitleName($row[self::FIELD_JOB_TITLE_NAME]);
		}

		if (isset($row[self::FIELD_MANAGER_NAME])) {
		    $vacancy->setManagerName($row[self::FIELD_MANAGER_NAME]);
		}
	    return $vacancy;
    }

}

class JobVacancyException extends Exception {
	const INVALID_PARAMETER = 0;
	const DB_ERROR = 1;
}

?>
