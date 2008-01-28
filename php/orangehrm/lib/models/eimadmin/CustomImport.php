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

class CustomImport {

	/**
	 * Table Name
	 */
	const TABLE_NAME = 'hs_hr_custom_import';

	const DB_FIELDS_ID = 'import_id';
	const DB_FIELDS_NAME = 'name';
	const DB_FIELDS_FIELDS = 'fields';
	const DB_FIELDS_HAS_HEADING = 'has_heading';

	const NO_HEADING = 0;
	const HAS_HEADING = 1;

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
	const FIELD_JOINEDDATE = 'joinedDate';
	const FIELD_WORKSTATION = 'workStation';
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

	const FIELD_HOME_PHONE = 'HomePhone';
	const FIELD_MOBILE_PHONE = 'MobilePhone';
	const FIELD_WORK_PHONE = 'WorkPhone';
	const FIELD_WORK_EMAIL = 'WorkEmail';
	const FIELD_OTHER_EMAIL = 'OtherEmail';
	const FIELD_DRIVING_LIC = 'DrivingLic';

	/**
	 * Class Attributes
	 */
	private $id;
	private $name;
	private $assignedFields;
	private $containsHeader;
	private $maxFieldLengths;

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

	public function setContainsHeader($containsHeader) {
		return $this->containsHeader = $containsHeader;
	}

	public function getContainsHeader() {
		return $this->containsHeader;
	}

	public function getFieldCount() {
		return count($this->assignedFields);
	}

	public function __construct() {
		$this->maxFieldLengths = self::getMaxFieldLengths();
	}

	/**
	 * Get CustomImport with given Id
	 *
	 * @param int Custom import id
	 * @return CustomImport Custom Import object if found or null if not
	 */
	public static function getCustomImport($id) {

		if (!CommonFunctions::isValidId($id)) {
			throw new CustomImportException("Invalid parameters to getCustomImport(): id = $id", CustomImportException::INVALID_PARAMETERS);
		}

		$selectCondition[] = self::DB_FIELDS_ID . " = $id";
		$list = self::_getList($selectCondition);
		$import = count($list) == 0 ? null : $list[0];
		return $import;
	}

	public static function getAllFields() {
		$allFields = array(self::FIELD_EMPID, self::FIELD_LASTNAME, self::FIELD_FIRSTNAME, self::FIELD_MIDDLENAME,
			self::FIELD_HOME_PHONE, self::FIELD_MOBILE_PHONE, self::FIELD_WORK_PHONE, self::FIELD_WORK_EMAIL,
			self::FIELD_OTHER_EMAIL, self::FIELD_DRIVING_LIC,
			self::FIELD_STREET1, self::FIELD_STREET2, self::FIELD_CITY, self::FIELD_STATE,
			self::FIELD_ZIP, self::FIELD_GENDER, self::FIELD_BIRTHDATE, self::FIELD_SSN,
			self::FIELD_JOINEDDATE, self::FIELD_WORKSTATION, self::FIELD_CUSTOM1, self::FIELD_CUSTOM2,
			self::FIELD_CUSTOM3, self::FIELD_CUSTOM4, self::FIELD_CUSTOM5, self::FIELD_CUSTOM6,
			self::FIELD_CUSTOM7, self::FIELD_CUSTOM8, self::FIELD_CUSTOM9, self::FIELD_CUSTOM10,
			self::FIELD_WORKSTATE, self::FIELD_FITWSTATUS,
			self::FIELD_FITWEXCEMPTIONS, self::FIELD_SITWSTATE, self::FIELD_SITWSTATUS, self::FIELD_SITWEXCEMPTIONS,
			self::FIELD_SUISTATE, self::FIELD_DD1ROUTING, self::FIELD_DD1ACCOUNT, self::FIELD_DD1AMOUNT,
			self::FIELD_DD1AMOUNTCODE, self::FIELD_DD1CHECKING, self::FIELD_DD2ROUTING, self::FIELD_DD2ACCOUNT,
			self::FIELD_DD2AMOUNT, self::FIELD_DD2AMOUNTCODE, self::FIELD_DD2CHECKING);
		return $allFields;
	}

	/**
	 * Check if given value is within allowed field length for the given field
	 *
	 * @param string $fieldName The field to check
	 * @param string $value The field value to check
	 * @return boolean true if field length within allowed limits, false otherwise
	 */
	public function checkFieldLength($fieldName, $value) {

		if (isset($this->maxFieldLengths[$fieldName])) {
			$maxLength = $this->maxFieldLengths[$fieldName];
			if (($maxLength > -1) && (strlen($value) > $maxLength)) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Get array with maximum allowed field lengths for all supported fields
	 * Max length is set to -1 where not applicable
	 *
	 * @return array Array with maximum allowed field lengths.
	 */
	public static function getMaxFieldLengths() {
		$maxLengths = array(
			self::FIELD_EMPID => 50,
		    self::FIELD_LASTNAME => 100,
			self::FIELD_FIRSTNAME =>  100,
			self::FIELD_MIDDLENAME => 100,
			self::FIELD_STREET1 => 100,
			self::FIELD_STREET2 => 100,
			self::FIELD_CITY => 100,
			self::FIELD_STATE => 100,
			self::FIELD_ZIP => 20,
			self::FIELD_GENDER => -1,
			self::FIELD_BIRTHDATE => -1,
			self::FIELD_SSN => 100,
			self::FIELD_JOINEDDATE => -1,
			self::FIELD_WORKSTATION => -1,
			self::FIELD_CUSTOM1 => 250,
			self::FIELD_CUSTOM2 => 250,
			self::FIELD_CUSTOM3 => 250,
			self::FIELD_CUSTOM4 => 250,
			self::FIELD_CUSTOM5 => 250,
			self::FIELD_CUSTOM6 => 250,
			self::FIELD_CUSTOM7 => 250,
			self::FIELD_CUSTOM8 => 250,
			self::FIELD_CUSTOM9 => 250,
			self::FIELD_CUSTOM10 => 250,
			self::FIELD_WORKSTATE => 13,
			self::FIELD_FITWSTATUS => 13,
			self::FIELD_FITWEXCEMPTIONS => -1,
			self::FIELD_SITWSTATE => 13,
			self::FIELD_SITWSTATUS => 13,
			self::FIELD_SITWEXCEMPTIONS => -1,
			self::FIELD_SUISTATE => 13,
			self::FIELD_DD1ROUTING => -1,
			self::FIELD_DD1ACCOUNT => 100,
			self::FIELD_DD1AMOUNT => -1,
			self::FIELD_DD1AMOUNTCODE => 20,
			self::FIELD_DD1CHECKING => 20,
			self::FIELD_DD2ROUTING => -1,
			self::FIELD_DD2ACCOUNT => 100,
			self::FIELD_DD2AMOUNT => -1,
			self::FIELD_DD2AMOUNTCODE => 20,
			self::FIELD_DD2CHECKING => 20,
			self::FIELD_HOME_PHONE => 50,
			self::FIELD_MOBILE_PHONE => 50,
			self::FIELD_WORK_PHONE => 50,
			self::FIELD_WORK_EMAIL => 50,
			self::FIELD_OTHER_EMAIL => 50,
			self::FIELD_DRIVING_LIC => 100);
		return $maxLengths;
	}

	/**
	 * Return array of fields that must be included in import file
	 * @return array Array of compulsary fields
	 */
	public static function getCompulsaryFields() {

		$compulsaryFields = array(self::FIELD_LASTNAME, self::FIELD_FIRSTNAME);
		return $compulsaryFields;
	}

	/**
	 * Get list of Custom Import objects in the database
	 * @return Array Array of CustomImport objects
	 */
	public static function getCustomImportList() {
		return self::_getList();
	}

	/**
	 * Get list of defined Custom Imports in format suitable for view.php
	 * @return Array 2D array representing custom import objects defined in database.
	 */
	public static function getCustomImportListForView($pageNO,$schStr,$mode,$sortField = 0, $sortOrder = 'ASC') {

		$imports = CustomImport::getCustomImportList();

		$arrDispArr = null;
		for($i=0; count($imports) > $i; $i++) {
			$arrDispArr[$i][0] = $imports[$i]->getId();
			$arrDispArr[$i][1] = $imports[$i]->getName();
		}

		return $arrDispArr;
	}

	/**
	 * Get the available fields (fields not yet assigned to this CustomImport)
	 *
	 * @return array Array of fields not yet assigned to this CustomImport object
	 */
	public function getAvailableFields() {
		$allFields = CustomImport::getAllFields();
		$available = array_diff($allFields, $this->assignedFields);
		return $available;
	}

	/**
	 * Delete custom imports with the given ids
	 *
	 * @param array $ids Array of import id's
	 * @return int the number of CustomImport's actually deleted
	 */
	public static function deleteImports($ids) {

		$count = 0;

		if (!is_array($ids)) {
			throw new CustomImportException("Invalid parameter to deleteImports(): ids should be an array", CustomImportException::INVALID_PARAMETERS);
		}

		foreach ($ids as $id) {
			if (!CommonFunctions::isValidId($id)) {
				throw new CustomImportException("Invalid parameter to deleteImports(): id = $id", CustomImportException::INVALID_PARAMETERS);
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
	 * Save this CustomImport Object.
	 * If an id is available the existing values are updated, if not a new
	 * id is assigned and a new CustomImport is saved
	 *
	 */
	public function save() {

		// Validate fieleds
		if (empty($this->name)) {
			throw new CustomImportException("Empty name", CustomImportException::EMPTY_IMPORT_NAME);
		}

		if ($this->_isNameInUse()) {
			throw new CustomImportException("Duplicate name", CustomImportException::DUPLICATE_IMPORT_NAME);
		}

		if (empty($this->assignedFields) || !is_array($this->assignedFields)) {
			throw new CustomImportException("No valid Assigned fields", CustomImportException::NO_ASSIGNED_FIELDS);
		}

		$compulsaryFields = self::getCompulsaryFields();
		$allFields = self::getAllFields();
		foreach ($this->assignedFields as $field) {
			if (!in_array($field, $allFields)) {
				throw new CustomImportException("Invalid field name", CustomImportException::INVALID_FIELD_NAME);
			}
			$key = array_search($field, $compulsaryFields);
			if ($key !== FALSE) {
				unset($compulsaryFields[$key]);
			}
		}

		if (count($compulsaryFields) > 0) {
			throw new CustomImportException("Missing compulsary fields: " . implode(',', $compulsaryFields), CustomImportException::COMPULSARY_FIELDS_NOT_ASSIGNED);
		}

		if (empty($this->id)) {
			$this->_insert();
		} else {
			$this->_update();
		}
	}

	/**
	 * Add new CustomImport object to database
	 */
	private function _insert() {

		$fields[0] = self::DB_FIELDS_ID;
		$fields[1] = self::DB_FIELDS_NAME;
		$fields[2] = self::DB_FIELDS_FIELDS;
		$fields[3] = self::DB_FIELDS_HAS_HEADING;

		$this->id = UniqueIDGenerator::getInstance()->getNextID(self::TABLE_NAME, self::DB_FIELDS_ID);
		$values[0] = $this->id;
		$values[1] = "'{$this->name}'";
		$values[2] = "'" . implode(",", $this->assignedFields) . "'";
		$values[3] = $this->containsHeader ? self::HAS_HEADING : self::NO_HEADING;

		$sqlBuilder = new SQLQBuilder();
		$sqlBuilder->table_name = self::TABLE_NAME;
		$sqlBuilder->flg_insert = 'true';
		$sqlBuilder->arr_insert = $values;
		$sqlBuilder->arr_insertfield = $fields;

		$sql = $sqlBuilder->addNewRecordFeature2();

		$conn = new DMLFunctions();

		$result = $conn->executeQuery($sql);
		if (!$result || (mysql_affected_rows() != 1)) {
			throw new CustomImportException("Insert failed. $sql", CustomImportException::DB_EXCEPTION);
		}
	}

	/**
	 * Update existing CustomImport data
	 */
	private function _update() {

		$fields[0] = self::DB_FIELDS_ID;
		$fields[1] = self::DB_FIELDS_NAME;
		$fields[2] = self::DB_FIELDS_FIELDS;
		$fields[3] = self::DB_FIELDS_HAS_HEADING;

		$values[0] = $this->id;
		$values[1] = "'{$this->name}'";
		$values[2] = "'" . implode(",", $this->assignedFields) . "'";
		$values[3] = $this->containsHeader ? self::HAS_HEADING : self::NO_HEADING;

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
			throw new CustomImportException("Update failed. SQL=$sql", CustomImportException::DB_EXCEPTION);
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
	 * Get a list of custom import objects with the given conditions.
	 *
	 * @param array   $selectCondition Array of select conditions to use.
	 * @return array  Array of CustomImport objects. Returns an empty (length zero) array if none found.
	 */
	private static function _getList($selectCondition = null) {

		$fields[0] = self::DB_FIELDS_ID;
		$fields[1] = self::DB_FIELDS_NAME;
		$fields[2] = self::DB_FIELDS_FIELDS;
		$fields[3] = self::DB_FIELDS_HAS_HEADING;

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
	 * Creates a CustomImport object from a resultset row
	 *
	 * @param array $row Resultset row from the database.
	 * @return CustomImport Custom Import object.
	 */
	private static function _createFromRow($row) {

		$tmp = new CustomImport();
		$tmp->setId($row[self::DB_FIELDS_ID]);
		$tmp->setName($row[self::DB_FIELDS_NAME]);

		$assignedFields = $row[self::DB_FIELDS_FIELDS];
		if (!empty($assignedFields)) {
			$tmp->setAssignedFields(explode(",", $assignedFields));
		} else {
			$tmp->setAssignedFields(array());
		}

		$hasHeader = ($row[self::DB_FIELDS_HAS_HEADING] == self::HAS_HEADING) ? true : false;
		$tmp->setContainsHeader($hasHeader);

		return $tmp;
	}

}

class CustomImportException extends Exception {

	const INVALID_FIELD_NAME = 0;
	const NO_ASSIGNED_FIELDS = 1;
	const DUPLICATE_IMPORT_NAME = 2;
	const EMPTY_IMPORT_NAME = 3;
	const DB_EXCEPTION = 4;
	const INVALID_PARAMETERS = 5;
	const ID_NOT_FOUND = 6;
	const COMPULSARY_FIELDS_NOT_ASSIGNED = 7;
}

?>
