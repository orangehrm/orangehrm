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
require_once ROOT_PATH . '/lib/common/LocaleUtil.php';

/**
 * Abstract super class managing employee history
 */
abstract class AbstractEmpHistory {

	const DB_FIELD_ID = 'id';
    const DB_FIELD_EMP_NUMBER = 'emp_number';
    const DB_FIELD_CODE = 'code';
    const DB_FIELD_NAME = 'name';
    const DB_FIELD_START_DATE = 'start_date';
    const DB_FIELD_END_DATE = 'end_date';

	private $id;
    private $empNumber;
    private $code;
    private $name;
    private $startDate;
    private $endDate;

    /**
     * Table name. Defined in the subclass
     */
    protected $tableName;
    protected $externalTable;
    protected $externalCodeField;
    protected $externalNameField;

    /**
     * Whether multiple current items are allowed
     * Eg: For job titles, this would be false, since only one job title is allowed at a time
     * But for locations, this would be true, since an employee can be assigned multiple locations
     */
    protected $allowMultipleCurrentItems = false;

	/**
	 * Constructor
	 */
	public function __construct() {
	}

    /**
     * Retrieves the value of id.
     * @return id
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Sets the value of id.
     * @param id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Retrieves the value of empNumber.
     * @return empNumber
     */
    public function getEmpNumber() {
        return $this->empNumber;
    }

    /**
     * Sets the value of empNumber.
     * @param empNumber
     */
    public function setEmpNumber($empNumber) {
        $this->empNumber = $empNumber;
    }

    /**
     * Retrieves the value of code.
     * @return code
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * Sets the value of code.
     * @param code
     */
    public function setCode($code) {
        $this->code = $code;
    }

    /**
     * Retrieves the value of name.
     * @return name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the value of name.
     * @param name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Retrieves the value of startDate.
     * @return startDate
     */
    public function getStartDate() {
        return $this->startDate;
    }

    /**
     * Sets the value of startDate.
     * @param startDate
     */
    public function setStartDate($startDate) {
        $this->startDate = $startDate;
    }

    /**
     * Retrieves the value of endDate.
     * @return endDate
     */
    public function getEndDate() {
        return $this->endDate;
    }

    /**
     * Sets the value of endDate.
     * @param endDate
     */
    public function setEndDate($endDate) {
        $this->endDate = $endDate;
    }

    /**
     * Update history table if needed
     * Checks the value of code and compares with current items in
     * history table for current employee and updates history if needed (if current item has changed)
     *
     * @param int $empNumber Employee number
     * @param mixed $code Value of code
     *
     * @return boolean true if history updated, false if not
     */
    public function updateHistory($empNum, $code, $remove = false) {

        if (!CommonFunctions::isValidId($empNum)) {
            throw new EmpHistoryException("Invalid emp number", EmpHistoryException::INVALID_PARAMETER);
        }

        if (!$this->validateCode($code)) {
            throw new EmpHistoryException("Code invalid", EmpHistoryException::INVALID_PARAMETER);
        }

        $added = false;

        // Get current items (end_date is null)
        $selectConditions[] = self::DB_FIELD_EMP_NUMBER . ' = ' . $empNum;
        $selectConditions[] = self::DB_FIELD_END_DATE . ' IS NULL ';
        $currentItems = $this->_getList($selectConditions);

        // Loop and look for code
        if (!$this->allowMultipleCurrentItems && count($currentItems) > 1) {
            throw new EmpHistoryException('Multiple current items not allowed', EmpHistoryException::MULTIPLE_CURRENT_ITEMS_NOT_ALLOWED);
        }

        $found = false;
        $foundItem = null;
        foreach($currentItems as $item) {
            if ($item->getCode() == $code) {
                $found = true;
                $foundItem = $item;
                break;
            }
        }

        // if not found, add end time to existing current item (only if there is on)
        if (!$found) {

            $now = date(LocaleUtil::STANDARD_TIMESTAMP_FORMAT);

            // add this item as current
            $className = get_class($this);
            $empHistory = new $className;
            $empHistory->setEmpNumber($empNum);
            $empHistory->setCode($code);
            $empHistory->setStartDate($now);
            $empHistory->save($now);

            $added = true;

            // If only one current item allowed set end time of existing current item, changing it to an history item
            if (!$this->allowMultipleCurrentItems && count($currentItems) == 1) {
                $currentItems[0]->setEndDate($now);
                $currentItems[0]->save();
            }
        } else if ($remove) {
            $now = date(LocaleUtil::STANDARD_TIMESTAMP_FORMAT);
            $foundItem->setEndDate($now);
            $foundItem->save();
        }

        return $added;
    }

    /**
     * Validate the code. To be implemented in sub class
     *
     * @param mixed $code Code
     * @return boolean true if code valid, false otherwise
     */
    abstract protected function validateCode($code);

    /**
     * Get history for this employee
     * Returns all records except the current one (identified by end_date not being set)
     *
     * @param int $empNumber Employee number
     * @return Array Array of history objects
     */
    public function getHistory($empNum) {

        if (!CommonFunctions::isValidId($empNum)) {
            throw new EmpHistoryException("Invalid empNum getHistory(): $empNum", EmpHistoryException::INVALID_PARAMETER);
        }

        $selectConditions[] = self::DB_FIELD_EMP_NUMBER . ' = ' . $empNum;
        $selectConditions[] = self::DB_FIELD_END_DATE . ' IS NOT NULL ';
        return $this->_getList($selectConditions);
    }


	/**
	 * Save this history item.
     * If no id is set, a new history item is created, otherwise,
     * the existing history item is updated.
	 */
    public function save() {
        $this->_validateParams();

        if (isset($this->id)) {
            return $this->_update();
        } else {
            return $this->_insert();
        }
    }

    /**
     * Delete given Employee history items
     * @param array $ids Array of Employee history item ids to delete
     */
	public function delete($ids) {

        $count = 0;
        if (!is_array($ids)) {
            throw new EmpHistoryException("Invalid parameter to delete(): ids should be an array", EmpHistoryException::INVALID_PARAMETER);
        }

        foreach ($ids as $id) {
            if (!CommonFunctions::isValidId($id)) {
                throw new EmpHistoryException("Invalid parameter to delete(): id = $id", EmpHistoryException::INVALID_PARAMETER);
            }
        }

        if (!empty($ids)) {

            $sql = sprintf("DELETE FROM %s WHERE %s IN (%s)", $this->tableName,
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
     * Insert new object to database
     */
    private function _insert() {

        // Update name if not set.
        if (empty($this->name)) {
            $this->_updateName();
        }

        $fields[0] = self::DB_FIELD_EMP_NUMBER;
        $fields[1] = self::DB_FIELD_CODE;
        $fields[2] = self::DB_FIELD_NAME;
        $fields[3] = self::DB_FIELD_START_DATE;
        $fields[4] = self::DB_FIELD_END_DATE;

        $values[0] = $this->empNumber;
        $values[1] = $this->code;
        $values[2] = isset($this->name) ? $this->name : 'null';
        $values[3] = $this->startDate;
        $values[4] = isset($this->endDate) ? $this->endDate : 'null';

        $sqlBuilder = new SQLQBuilder();
        $sqlBuilder->table_name = $this->tableName;
        $sqlBuilder->flg_insert = 'true';
        $sqlBuilder->arr_insert = $values;
        $sqlBuilder->arr_insertfield = $fields;

        $sql = $sqlBuilder->addNewRecordFeature2();

        $conn = new DMLFunctions();

        $result = $conn->executeQuery($sql);
        if (!$result || (mysql_affected_rows() != 1)) {
            throw new EmpHistoryException("Insert failed. ", EmpHistoryException::DB_ERROR);
        }

        $this->id = mysql_insert_id();
        return $this->id;
    }

    /**
     * Update existing object
     */
    private function _update() {

        // Update name if not set.
        if (empty($this->name)) {
            $this->_updateName();
        }

        $fields[0] = self::DB_FIELD_ID;
        $fields[1] = self::DB_FIELD_EMP_NUMBER;
        $fields[2] = self::DB_FIELD_CODE;
        $fields[3] = self::DB_FIELD_NAME;
        $fields[4] = self::DB_FIELD_START_DATE;
        $fields[5] = self::DB_FIELD_END_DATE;

        $values[0] = $this->id;
        $values[1] = $this->empNumber;
        $values[2] = $this->code;
        $values[3] = isset($this->name) ? $this->name : 'null';
        $values[4] = $this->startDate;
        $values[5] = isset($this->endDate) ? $this->endDate : 'null';

        $sqlBuilder = new SQLQBuilder();
        $sqlBuilder->table_name = $this->tableName;
        $sqlBuilder->flg_update = 'true';
        $sqlBuilder->arr_update = $fields;
        $sqlBuilder->arr_updateRecList = $values;

        $sql = $sqlBuilder->addUpdateRecord1(0);

        $conn = new DMLFunctions();
        $result = $conn->executeQuery($sql);

        // Here we don't check mysql_affected_rows because update may be called
        // without any changes.
        if (!$result) {
            throw new EmpHistoryException("Update failed. SQL=$sql", EmpHistoryException::DB_ERROR);
        }
        return $this->id;
    }

    /**
     * Update the name from the external table
     */
    private function _updateName() {

        if (!empty($this->code)) {
            $fields[0] = $this->externalNameField;
            $selectCondition[] = $this->externalCodeField . " = '" . $this->code . "'";

            $sqlBuilder = new SQLQBuilder();
            $sql = $sqlBuilder->simpleSelect($this->externalTable, $fields, $selectCondition);

            $conn = new DMLFunctions();
            $result = $conn->executeQuery($sql);

            if ($result && ($row = mysql_fetch_assoc($result))) {
                $this->name = $row[$this->externalNameField];
            }
        }
    }

    /**
     * Validates that the member variables are valid
     *
     * @throws EmpHistoryException if not valid
     */
    private function _validateParams() {
        if (!CommonFunctions::isValidId($this->empNumber)) {
            throw new EmpHistoryException("Invalid emp number", EmpHistoryException::INVALID_PARAMETER);
        }

        if (!$this->validateCode($this->code)) {
            throw new EmpHistoryException("Code invalid", EmpHistoryException::INVALID_PARAMETER);
        }

        if (!empty($this->id) && !CommonFunctions::isValidId($this->empNumber)) {
            throw new EmpHistoryException("Invalid ID", EmpHistoryException::INVALID_PARAMETER);
        }

        if (empty($this->startDate)) {
            throw new EmpHistoryException("Missing start date", EmpHistoryException::INVALID_PARAMETER);
        } else {
            $start = strtotime($this->startDate);

            if (!empty($this->endDate)) {
                $end = strtotime($this->endDate);
                if ($end < $start) {
                    throw new EmpHistoryException("Missing start date", EmpHistoryException::END_BEFORE_START);
                }
            }
        }

    }

	/**
	 * Get a list of History items with the given conditions.
	 *
	 * @param array   $selectCondition Array of select conditions to use.
	 * @return array  Array of History objects. Returns an empty (length zero) array if none found.
	 */
	private function _getList($selectCondition = null) {

        $fields[0] = self::DB_FIELD_ID;
        $fields[1] = self::DB_FIELD_EMP_NUMBER;
        $fields[2] = self::DB_FIELD_CODE;
        $fields[3] = self::DB_FIELD_NAME;
        $fields[4] = self::DB_FIELD_START_DATE;
        $fields[5] = self::DB_FIELD_END_DATE;

        $sqlBuilder = new SQLQBuilder();
        $sql = $sqlBuilder->simpleSelect($this->tableName, $fields, $selectCondition);

		$histList = array();

		$conn = new DMLFunctions();
		$result = $conn->executeQuery($sql);

		while ($result && ($row = mysql_fetch_assoc($result))) {
			$histList[] = self::_createFromRow($row);
		}

		return $histList;
	}

    /**
     * Creates a History object from a resultset row
     *
     * @param array $row Resultset row from the database.
     * @return History object
     */
    private function _createFromRow($row) {
        $className = get_class($this);
    	$empHistory = new $className;
        $empHistory->setId($row[self::DB_FIELD_ID]);
        $empHistory->setEmpNumber($row[self::DB_FIELD_EMP_NUMBER]);
        $empHistory->setCode($row[self::DB_FIELD_CODE]);
        $empHistory->setName($row[self::DB_FIELD_NAME]);
        $empHistory->setStartDate($row[self::DB_FIELD_START_DATE]);
        $empHistory->setEndDate($row[self::DB_FIELD_END_DATE]);
        return $empHistory;
    }

}

class EmpHistoryException extends Exception {
	const INVALID_PARAMETER = 0;
	const DB_ERROR = 1;
    const END_BEFORE_START = 2;
    const MULTIPLE_CURRENT_ITEMS_NOT_ALLOWED = 3;
}

?>
