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

/**
 * Class representing employee locations
 */
class EmpLocation {

	const TABLE_NAME = 'hs_hr_emp_locations';
	const DB_FIELD_EMP_NUMBER = 'emp_number';
	const DB_FIELD_LOC_CODE = 'loc_code';

    /** Not a field in hs_hr_emp_locations */
    const FIELD_LOCATION_NAME = 'loc_name';

	private $empNumber;
    private $location;

    private $locationName;

	/**
	 * Constructor
     * @param int $empNumber Employee number
     * @param String $locCode Location Code
	 */
	public function __construct($empNumber = null, $locCode = null) {
        $this->empNumber = $empNumber;
        $this->location = $locCode;
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
     * Retrieves the value of location.
     * @return location
     */
    public function getLocation() {
        return $this->location;
    }

    /**
     * Sets the value of location.
     * @param location
     */
    public function setLocation($location) {
        $this->location = $location;
    }

    /**
     * Sets the location name
     * @param String $locationName Location Name
     */
    public function setLocationName($locationName) {
        $this->locationName = $locationName;
    }

    /**
     * Gets the location name
     * @return String Location Name
     */
    public function getLocationName() {
        return $this->locationName;
    }

	/**
	 * Save a employee Location to the database.
     * If this location has already been assigned to the employee, no change is done.
	 */
    public function save() {
        $this->_validateParams();

        $fields[0] = self::DB_FIELD_EMP_NUMBER;
        $fields[1] = self::DB_FIELD_LOC_CODE;

        $values[0] = $this->empNumber;
        $values[1] = "'{$this->location}'";

        $sqlBuilder = new SQLQBuilder();
        $sqlBuilder->table_name = self::TABLE_NAME;
        $sqlBuilder->flg_insert = 'true';
        $sqlBuilder->arr_insert = $values;
        $sqlBuilder->arr_insertfield = $fields;

        $sql = $sqlBuilder->addNewRecordFeature2(true, false, true);

        $conn = new DMLFunctions();

        $result = $conn->executeQuery($sql);
        if (!$result) {
            throw new EmpLocationException("Insert failed. ", EmpLocationException::DB_ERROR);
        }
    }

	/**
	 * Delete Employee Location
	 */
	public function delete() {

        $this->_validateParams();

        $conn = new DMLFunctions();
    	$sql = sprintf("DELETE FROM %s WHERE %s = %d AND %s = '%s'", self::TABLE_NAME,
            self::DB_FIELD_EMP_NUMBER, mysql_real_escape_string($this->empNumber),
            self::DB_FIELD_LOC_CODE, mysql_real_escape_string($this->location));
		$result = $conn->executeQuery($sql);

        if (!$result) {
            throw new EmpLocationException("Insert failed. ", EmpLocationException::DB_ERROR);
        }
	}


	/**
	 * Get Locations assigned to given employee
	 * @param int $empNum The Emp number of the employee
	 * @return Array of EmpLocation objects assigned to given employee (empty array if non assigned)
	 */
	public static function getEmpLocations($empNum) {

		if (!CommonFunctions::isValidId($empNum)) {
			throw new EmpLocationException("Invalid empNum getEmpLocations(): $empNum", EmpLocationException::INVALID_PARAMETER);
		}

		$selectCondition[] = self::DB_FIELD_EMP_NUMBER . " = $empNum";
		return self::_getList($selectCondition);
	}

    /**
     * Get Locations not yet assigned to given employee
     * @param int $empNum The Emp number of the employee
     * @return Array of EmpLocation objects not assigned to given employee (empty array if all assigned)
     */
    public static function getUnassignedLocations($empNum) {

        if (!CommonFunctions::isValidId($empNum)) {
            throw new EmpLocationException("Invalid empNum getEmpLocations(): $empNum", EmpLocationException::INVALID_PARAMETER);
        }

        $fields[0] = "a." . self::DB_FIELD_LOC_CODE;
        $fields[1] = "a.loc_name AS " . self::FIELD_LOCATION_NAME;
        $fields[2] = "b." . self::DB_FIELD_EMP_NUMBER;

        $tables[0] = 'hs_hr_location a';
        $tables[1] = self::TABLE_NAME . ' b';

        $joinConditions[1] = '((a.' . self::DB_FIELD_LOC_CODE . ' = b.loc_code) AND ' .
                              '(b.' .self::DB_FIELD_EMP_NUMBER.' = '.$empNum.'))';

        $selectCondition[] = 'b.' . self::DB_FIELD_EMP_NUMBER . ' IS NULL';
        $sqlBuilder = new SQLQBuilder();
        $sql = $sqlBuilder->selectFromMultipleTable($fields, $tables, $joinConditions, $selectCondition);

        $locList = array();

        $conn = new DMLFunctions();
        $result = $conn->executeQuery($sql);

        while ($result && ($row = mysql_fetch_assoc($result))) {
            $locList[] = array($row['loc_code'], $row['loc_name']);
        }

        return $locList;
    }

    /**
     * Validates that the empNumber and location are set and valid
     *
     * @throws EmpLocationException if not valid
     */
    private function _validateParams() {
        if (!CommonFunctions::isValidId($this->empNumber)) {
            throw new EmpLocationException("Invalid emp number", EmpLocationException::INVALID_PARAMETER);
        }

        if (!CommonFunctions::isValidId($this->location, 'LOC')) {
            throw new EmpLocationException("Location code invalid", EmpLocationException::INVALID_PARAMETER);
        }
    }

	/**
	 * Get a list of Employee locations with the given conditions.
	 *
	 * @param array   $selectCondition Array of select conditions to use.
	 * @return array  Array of EmpLocation objects. Returns an empty (length zero) array if none found.
	 */
	private static function _getList($selectCondition = null) {

        $fields[0] = "a." . self::DB_FIELD_EMP_NUMBER;
        $fields[1] = "a." . self::DB_FIELD_LOC_CODE;
        $fields[2] = "b.loc_name AS " . self::FIELD_LOCATION_NAME;

        $tables[0] = self::TABLE_NAME . ' a';
        $tables[1] = 'hs_hr_location b';

        $joinConditions[1] = 'a.' . self::DB_FIELD_LOC_CODE . ' = b.loc_code';

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
     * Creates a EmpLocation object from a resultset row
     *
     * @param array $row Resultset row from the database.
     * @return EmpLocation EmpLocation object.
     */
    private static function _createFromRow($row) {
    	$empLocation = new EmpLocation();
        $empLocation->setEmpNumber($row[self::DB_FIELD_EMP_NUMBER]);
        $empLocation->setLocation($row[self::DB_FIELD_LOC_CODE]);
        $empLocation->setLocationName($row[self::FIELD_LOCATION_NAME]);

        return $empLocation;
    }

}

class EmpLocationException extends Exception {
	const INVALID_PARAMETER = 0;
	const DB_ERROR = 1;
}

?>
