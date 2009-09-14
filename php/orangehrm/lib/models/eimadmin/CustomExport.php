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
require_once ROOT_PATH . '/lib/common/UniqueIDGenerator.php';
require_once ROOT_PATH . '/lib/models/eimadmin/CustomFields.php';

class CustomExport {

	/**
	 * Table Name
	 */
	const TABLE_NAME = 'hs_hr_custom_export';

	const DB_FIELDS_ID = 'export_id';
	const DB_FIELDS_NAME = 'name';
	const DB_FIELDS_FIELDS = 'fields';
	const DB_FIELDS_HEADINGS = 'headings';

	/** CSV Field name constants */
	const FIELD_EMPID = 'empId';
        const FIELD_LASTNAME = 'lastName';
	const FIELD_FIRSTNAME =  'firstName';
	const FIELD_MIDDLENAME = 'middleName';
	const FIELD_STREET1 = 'street1';
	const FIELD_STREET2 = 'street2';
	const FIELD_CITY = 'city';
	const FIELD_STATE = 'state';
	const FIELD_ZIP = 'zip';
	const FIELD_GENDER = 'gender';
	const FIELD_BIRTHDATE = 'birthDate';
	const FIELD_SSN = 'ssn';
	const FIELD_EMPSTATUS = 'empStatus';
	const FIELD_JOINEDDATE = 'joinedDate';
	const FIELD_WORKSTATION = 'workStation';
	const FIELD_LOCATION = 'location';
	const FIELD_CUSTOM1 = 'custom1';
	const FIELD_CUSTOM2 = 'custom2';
	const FIELD_CUSTOM3 = 'custom3';
	const FIELD_CUSTOM4 = 'custom4';
	const FIELD_CUSTOM5 = 'custom5';
	const FIELD_CUSTOM6 = 'custom6';
	const FIELD_CUSTOM7 = 'custom7';
	const FIELD_CUSTOM8 = 'custom8';
	const FIELD_CUSTOM9 = 'custom9';
	const FIELD_CUSTOM10 = 'custom10';
	const FIELD_WORKSTATE = 'workState';
	const FIELD_SALARY = 'salary';
	const FIELD_PAYFREQUENCY = 'payFrequency';
	const FIELD_FITWSTATUS = 'FITWStatus';
	const FIELD_FITWEXCEMPTIONS = 'FITWExemptions';
	const FIELD_SITWSTATE = 'SITWState';
	const FIELD_SITWSTATUS = 'SITWStatus';
	const FIELD_SITWEXCEMPTIONS = 'SITWExemptions';
	const FIELD_SUISTATE = 'SUIState';
	const FIELD_DD1ROUTING = 'DD1Routing';
	const FIELD_DD1ACCOUNT = 'DD1Account';
	const FIELD_DD1AMOUNT = 'DD1Amount';
	const FIELD_DD1AMOUNTCODE = 'DD1AmountCode';
	const FIELD_DD1CHECKING = 'DD1Checking';
	const FIELD_DD2ROUTING = 'DD2Routing';
	const FIELD_DD2ACCOUNT = 'DD2Account';
	const FIELD_DD2AMOUNT = 'DD2Amount';
	const FIELD_DD2AMOUNTCODE = 'DD2AmountCode';
	const FIELD_DD2CHECKING = 'DD2Checking';

	/**
	 * Class Attributes
	 */
	private $id;
	private $name;
	private $assignedFields;
	private $headings;

	/**
	 *	Setter method followed by getter method for each
	 *	attribute
	 */
	public function setId($id) {
			$this->id = $id;
	}

	public function getId() {
		return $this->id;
	}

	public function setName($name){
		$this->name = $name;
	}

	public function getName(){
		return $this->name;
	}

	public function setAssignedFields($fields) {
		$this->assignedFields = $fields;
	}

	public function getAssignedFields() {
		return $this->assignedFields;
	}

	public function setHeadings($headings) {
		return $this->headings = $headings;
	}

	public function getHeadings() {
		return $this->headings;
	}

	public function __construct() {
	}

	/**
	 * Get CustomExport with given Id
	 *
	 * @param int Custom export id
	 * @return CustomExport Custom Export object if found or null if not
	 */
	public static function getCustomExport($id) {

		if (!CommonFunctions::isValidId($id)) {
			throw new CustomExportException("Invalid parameters to getCustomExport(): id = $id", CustomExportException::INVALID_PARAMETERS);
		}

		$selectCondition[] = self::DB_FIELDS_ID . " = $id";
		$list = self::_getList($selectCondition);
		$export = count($list) == 0 ? null : $list[0];
		return $export;
	}

	public static function getAllFields() {
		$allFields = array(self::FIELD_EMPID, self::FIELD_LASTNAME, self::FIELD_FIRSTNAME, self::FIELD_MIDDLENAME,
			self::FIELD_STREET1, self::FIELD_STREET2, self::FIELD_CITY, self::FIELD_STATE,
			self::FIELD_ZIP, self::FIELD_GENDER, self::FIELD_BIRTHDATE, self::FIELD_SSN,
			self::FIELD_EMPSTATUS, self::FIELD_JOINEDDATE, self::FIELD_WORKSTATION, self::FIELD_LOCATION);

		$restOfAllFields = array(self::FIELD_WORKSTATE, self::FIELD_SALARY, self::FIELD_PAYFREQUENCY, self::FIELD_FITWSTATUS,
			self::FIELD_FITWEXCEMPTIONS, self::FIELD_SITWSTATE, self::FIELD_SITWSTATUS, self::FIELD_SITWEXCEMPTIONS,
			self::FIELD_SUISTATE, self::FIELD_DD1ROUTING, self::FIELD_DD1ACCOUNT, self::FIELD_DD1AMOUNT,
			self::FIELD_DD1AMOUNTCODE, self::FIELD_DD1CHECKING, self::FIELD_DD2ROUTING, self::FIELD_DD2ACCOUNT,
			self::FIELD_DD2AMOUNT, self::FIELD_DD2AMOUNTCODE, self::FIELD_DD2CHECKING);

			$availableCustomFields = CustomFields::getCustomFieldList();

			$customFields = array();

			foreach($availableCustomFields as $fieldObj) {
			    $customFields[] = 'custom' . $fieldObj->getFieldNumber();
			}

			$allFields = array_merge($allFields, $customFields, $restOfAllFields);

		return $allFields;
	}

	/**
	 * Get list of Custom Export objects in the database
	 * @return Array Array of CustomExport objects
	 */
	public static function getCustomExportList() {
		return self::_getList();
	}

	/**
	 * Get list of defined Custom Exports in format suitable for view.php
	 * @return Array 2D array representing custom export objects defined in database.
	 */
    public static function getCustomExportListForView($pageNO,$schStr,$mode,$sortField = 0, $sortOrder = 'ASC') {
                                                       
        $tableName = self::TABLE_NAME;
        $arrFieldList[0] = self::DB_FIELDS_ID;
        $arrFieldList[1] = self::DB_FIELDS_NAME;
        $arrFieldList[2] = self::DB_FIELDS_FIELDS;
        $arrFieldList[3] = self::DB_FIELDS_HEADINGS;
        
        
        $sqlBuilder = new SQLQBuilder();

        $sqlBuilder->table_name = $tableName;
        $sqlBuilder->flg_select = 'true';
        $sqlBuilder->arr_select = $arrFieldList;

        $sqlQString = $sqlBuilder->passResultSetMessage($pageNO,$schStr,$mode, $sortField, $sortOrder);

        
        $dbConnection = new DMLFunctions();
        $result = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

        $i=0;

         while ($line = mysql_fetch_array($result, MYSQL_NUM)) {

            $arrayDispList[$i][0] = $line[0];
            $arrayDispList[$i][1] = $line[1];
            $arrayDispList[$i][2] = $line[2];
            $i++;

         }

         if (isset($arrayDispList)) {

            return $arrayDispList;

        } else {

            $arrayDispList = '';
            return $arrayDispList;

        }
         
    }

	/**
	 * Get the available fields (fields not yet assigned to this CustomExport)
	 *
	 * @return array Array of fields not yet assigned to this CustomExport object
	 */
	public function getAvailableFields() {
		$allFields = CustomExport::getAllFields();
		$available = array_diff($allFields, $this->assignedFields);
		return $available;
	}

	/**
	 * Delete custom exports with the given ids
	 *
	 * @param array $ids Array of export id's
	 * @return int the number of CustomExport's actually deleted
	 */
	public static function deleteExports($ids) {

		$count = 0;

		if (!is_array($ids)) {
			throw new CustomExportException("Invalid parameter to deleteExports(): ids should be an array", CustomExportException::INVALID_PARAMETERS);
		}

		foreach ($ids as $id) {
			if (!CommonFunctions::isValidId($id)) {
				throw new CustomExportException("Invalid parameter to deleteExports(): id = $id", CustomExportException::INVALID_PARAMETERS);
			}
		}

		if (!empty($ids)) {

			$sql = sprintf("DELETE FROM %s WHERE `%s` IN (%s)", self::TABLE_NAME,
			                self::DB_FIELDS_ID, implode(",", $ids));

			$conn = new DMLFunctions();
			$result = $conn->executeQuery($sql);
			if ($result) {
				$count = mysql_affected_rows();
			}
		}
		return $count;
	}

	/**
	 * Save this CustomExport Object.
	 * If an id is available the existing values are updated, if not a new
	 * id is assigned and a new CustomExport is saved
	 *
	 */
	public function save() {

		// Validate fieleds
		if (empty($this->name)) {
			throw new CustomExportException("Empty name", CustomExportException::EMPTY_EXPORT_NAME);
		}

		if ($this->_isNameInUse()) {
			throw new CustomExportException("Duplicate name", CustomExportException::DUPLICATE_EXPORT_NAME);
		}

		if (empty($this->assignedFields) || !is_array($this->assignedFields)) {
			throw new CustomExportException("No valid Assigned fields", CustomExportException::NO_ASSIGNED_FIELDS);
		}

		$allFields = self::getAllFields();
		foreach ($this->assignedFields as $field) {
			if (!in_array($field, $allFields)) {
				throw new CustomExportException("Invalid field name", CustomExportException::INVALID_FIELD_NAME);
			}
		}

		if (!empty($this->headings)) {
			if (count($this->assignedFields) != count($this->headings)) {
				throw new CustomExportException("Header count should match field count", CustomExportException::HEADER_COUNT_DOESNT_MATCH_FIELD_COUNT);
			}

			foreach ($this->headings as $heading) {
				if (strpos($heading, ",") !== false) {
					throw new CustomExportException("Invalid heading name", CustomExportException::INVALID_HEADER_NAME);
				}
			}
		}

		if (empty($this->id)) {
			$this->_insert();
		} else {
			$this->_update();
		}
	}

	/**
	 * Add new CustomExport object to database
	 */
	private function _insert() {

		$fields[0] = self::DB_FIELDS_ID;
		$fields[1] = self::DB_FIELDS_NAME;
		$fields[2] = self::DB_FIELDS_FIELDS;
		$fields[3] = self::DB_FIELDS_HEADINGS;

		$this->id = UniqueIDGenerator::getInstance()->getNextID(self::TABLE_NAME, self::DB_FIELDS_ID);
		$values[0] = $this->id;
		$values[1] = "'{$this->name}'";
		$values[2] = "'" . implode(",", $this->assignedFields) . "'";
		$values[3] = empty($this->headings) ? "''" : "'". implode(",", $this->headings) . "'";

		$sqlBuilder = new SQLQBuilder();
		$sqlBuilder->table_name = self::TABLE_NAME;
		$sqlBuilder->flg_insert = 'true';
		$sqlBuilder->arr_insert = $values;
		$sqlBuilder->arr_insertfield = $fields;

		$sql = $sqlBuilder->addNewRecordFeature2();

		$conn = new DMLFunctions();

		$result = $conn->executeQuery($sql);
		if (!$result || (mysql_affected_rows() != 1)) {
			throw new CustomExportException("Insert failed. $sql", CustomExportException::DB_EXCEPTION);
		}
	}

	/**
	 * Update existing CustomExport data
	 */
	private function _update() {

		$fields[0] = self::DB_FIELDS_ID;
		$fields[1] = self::DB_FIELDS_NAME;
		$fields[2] = self::DB_FIELDS_FIELDS;
		$fields[3] = self::DB_FIELDS_HEADINGS;

		$values[0] = $this->id;
		$values[1] = "'{$this->name}'";
		$values[2] = "'" . implode(",", $this->assignedFields) . "'";
		$values[3] = empty($this->headings) ? "''" : "'". implode(",", $this->headings) . "'";

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
			throw new CustomExportException("Update failed. SQL=$sql", CustomExportException::DB_EXCEPTION);
		}
	}

	/**
	 * Check if this objects name is in use
	 *
	 * @return boolean true if that name is in use, false otherwise
	 */
	private function _isNameInUse()  {
		$sql = 'SELECT COUNT(*) FROM ' . self::TABLE_NAME . ' WHERE ' . self::DB_FIELDS_NAME . " = '" . $this->name . "'";

		// exclude this object
		if (!empty($this->id)) {
			$sql .= ' AND ' . self::DB_FIELDS_ID . ' <> ' . $this->id;
		}
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result, MYSQL_NUM);
        $count = $row[0];
		return ($count != 0);
	}

	/**
	 * Get a list of custom export objects with the given conditions.
	 *
	 * @param array   $selectCondition Array of select conditions to use.
	 * @return array  Array of CustomExport objects. Returns an empty (length zero) array if none found.
	 */
	private static function _getList($selectCondition = null) {

		$fields[0] = self::DB_FIELDS_ID;
		$fields[1] = self::DB_FIELDS_NAME;
		$fields[2] = self::DB_FIELDS_FIELDS;
		$fields[3] = self::DB_FIELDS_HEADINGS;

		$sqlBuilder = new SQLQBuilder();
		$sql = $sqlBuilder->simpleSelect(self::TABLE_NAME, $fields, $selectCondition, $fields[1], "ASC");

		$actList = array();

		$conn = new DMLFunctions();
		$result = $conn->executeQuery($sql);

		while ($result && ($row = mysql_fetch_assoc($result))) {
			$actList[] = self::_createFromRow($row);
		}

		return $actList;
	}

	/**
	 * Creates a CustomExport object from a resultset row
	 *
	 * @param array $row Resultset row from the database.
	 * @return CustomExport Custom Export object.
	 */
	private static function _createFromRow($row) {

		$tmp = new CustomExport();
		$tmp->setId($row[self::DB_FIELDS_ID]);
		$tmp->setName($row[self::DB_FIELDS_NAME]);

		$assignedFields = $row[self::DB_FIELDS_FIELDS];
		if (!empty($assignedFields)) {
			$tmp->setAssignedFields(explode(",", $assignedFields));
		} else {
			$tmp->setAssignedFields(array());
		}

		$headers = $row[self::DB_FIELDS_HEADINGS];
		if (!empty($headers)) {
			$tmp->setHeadings(explode(",", $headers));
		} else {
			$tmp->setHeadings(array());
		}

		return $tmp;
	}

}

class CustomExportException extends Exception {

	const INVALID_FIELD_NAME = 0;
	const INVALID_HEADER_NAME = 1;
	const HEADER_COUNT_DOESNT_MATCH_FIELD_COUNT = 2;
	const NO_ASSIGNED_FIELDS = 3;
	const DUPLICATE_EXPORT_NAME = 4;
	const EMPTY_EXPORT_NAME = 5;
	const DB_EXCEPTION = 6;
	const INVALID_PARAMETERS = 7;
	const ID_NOT_FOUND = 8;
}

?>
