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

class CustomFields {

	/**
	 * Table Name
	 */
	const TABLE_NAME = 'hs_hr_custom_fields';

	const DB_FIELDS_NUM = 'field_num';
	const DB_FIELDS_NAME = 'name';
	const DB_FIELDS_TYPE = 'type';
        const DB_FIELDS_SCREEN = 'screen';
	const DB_FIELDS_EXTRA_DATA = 'extra_data';

	const FIELD_TYPE_STRING = 0;
	const FIELD_TYPE_SELECT = 1;
	const MAX_FIELD_NUM = 10;

	/**
	 * Class Attributes
	 */
	private $fieldNumber;
	private $name;
	private $fieldType = self::FIELD_TYPE_STRING;
        private $screen;
	private $extraData;
        

	/**
	 *	Setter method followed by getter method for each
	 *	attribute
	 */
	public function setFieldNumber($fieldNumber) {
			$this->fieldNumber = $fieldNumber;
	}

	public function getFieldNumber() {
		return $this->fieldNumber;
	}

	public function setName($name){
		$this->name  = 	$name ;
	}

	public function getName(){
		return $this->name;
	}

	public function setFieldType($type) {
		$this->fieldType = $type;
	}

	public function getFieldType() {
		return $this->fieldType;
	}

	public function setExtraData($extraData) {
		return $this->extraData = $extraData;
	}

	public function getExtraData() {
		return $this->extraData;
	}
        
	public function setScreen($screen) {
		return $this->screen = $screen;
	}

	public function getScreen() {
		return $this->screen;
	}
	public function getOptions() {
		$options = array();

		if (($this->fieldType == self::FIELD_TYPE_SELECT) && !empty($this->extraData)) {
			$options = explode(',', $this->extraData);
		}

		for ($i=0; $i<count($options); $i++) {
			$options[$i] = trim($options[$i]);
		}

		return $options;
	}
	/**
	 * Add a new custom field to the database
	 */
	public function addCustomField() {

		if ($this->_isDuplicateName()) {
			throw new CustomFieldsException("Duplicate name", 1);
		}
		
		$fields[0] = self::DB_FIELDS_NUM;
		$fields[1] = self::DB_FIELDS_NAME;
		$fields[2] = self::DB_FIELDS_TYPE;
                $fields[3] = self::DB_FIELDS_SCREEN;                
		$fields[4] = self::DB_FIELDS_EXTRA_DATA;

		$values[0] = $this->fieldNumber;
		$values[1] = "'{$this->name}'";
		$values[2] = "'{$this->fieldType}'";
                $values[3] = "'{$this->screen}'";
		$values[4] = "'{$this->extraData}'";

		$sqlBuilder = new SQLQBuilder();
		$sqlBuilder->table_name = self::TABLE_NAME;
		$sqlBuilder->flg_insert = 'true';
		$sqlBuilder->arr_insert = $values;
		$sqlBuilder->arr_insertfield = $fields;

		$sql = $sqlBuilder->addNewRecordFeature2();

		$conn = new DMLFunctions();

		$result = $conn->executeQuery($sql);
		if (!$result || (mysql_affected_rows() != 1)) {
			throw new CustomFieldsException("Insert failed. ");
		}
	}

	/**
	 * Update the custom field
	 */
	public	function updateCustomField() {

		if ($this->_isDuplicateName(true)) {
			throw new CustomFieldsException("Duplicate name", 1);
		}
		
		$fields[0] = self::DB_FIELDS_NUM;
		$fields[1] = self::DB_FIELDS_NAME;
		$fields[2] = self::DB_FIELDS_TYPE;
                $fields[3] = self::DB_FIELDS_SCREEN;                
		$fields[4] = self::DB_FIELDS_EXTRA_DATA;


		$values[0] = $this->fieldNumber;
		$values[1] = "'{$this->name}'";
		$values[2] = "'{$this->fieldType}'";
                $values[3] = "'{$this->screen}'";
		$values[4] = "'{$this->extraData}'";

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
			throw new CustomFieldsException("Update failed. SQL=$sql");
		}
	}

	/**
	 * Get array of available custom field numbers
	 *
	 * @return array Array of available custom field numbers
	 */
	public static function getAvailableFieldNumbers() {
		$fields = self::getCustomFieldList();

		$available = array();
		for ($i=1; $i<=self::MAX_FIELD_NUM; $i++) {
			$available[] = $i;
		}

		foreach($fields as $field) {
			$num = $field->getFieldNumber();
			$index = array_search($num, $available);
			if ($index !== false) {
				unset($available[$index]);
			}
		}

		return $available;

	}

	/**
	 * Get a list of custom fields defined in the system
	 *
	 * @return array  Array of CustomField objects. Returns an empty (length zero) array if none found.
	 */
	public static function getCustomFieldList() {
		$actList = self::_getList();
		return $actList;
	}


	public function getCustomerFieldListForView($pageNO,$schStr,$mode,$sortField = 0, $sortOrder = 'ASC') {

		$customerArr = $this->getCustomFieldList();

		$arrDispArr = null;
		for($i=0; count($customerArr) > $i; $i++) {

			$arrDispArr[$i][0] = $customerArr[$i]->getFieldNumber();
			$arrDispArr[$i][1] = $customerArr[$i]->getName();
			$arrDispArr[$i][2] = $customerArr[$i]->getFieldType();

		}

		return $arrDispArr;
	}

	/**
	 * Get custom field with given number.
	 *
	 * @param int $fieldNum The field number of the custom field to return
	 *
	 * @return CustomField Custom field object with given num or null if not found
	 */
	public static function getCustomField($fieldNum) {

		if (!CommonFunctions::isValidId($fieldNum)) {
			throw new CustomFieldsException("Invalid parameters to getCustomField(): fieldNum = $fieldNum");
		}

		$selectCondition[] = self::DB_FIELDS_NUM . " = $fieldNum";
		$actList = self::_getList($selectCondition);
		$obj = count($actList) == 0 ? null : $actList[0];
		return $obj;
	}

	/**
	 * Deletes the given custom fields
	 *
	 * @param array $fieldNumbers The list of custom fields to delete
	 *
	 * @return int Number of custom fields deleted.
	 */
	public static function deleteFields($fieldNumbers) {

		$count = 0;

		if (!is_array($fieldNumbers)) {
			throw new CustomFieldsException("Invalid parameter to deleteFields(): activityIds should be an array");
		}

		foreach ($fieldNumbers as $num) {
			if (!CommonFunctions::isValidId($num)) {
				throw new CustomFieldsException("Invalid parameter to deleteFields(): field num = $num");
			}
		}

		if (!empty($fieldNumbers)) {

			$sql = sprintf("DELETE FROM %s WHERE `%s` IN (%s)", self::TABLE_NAME,
			                self::DB_FIELDS_NUM, implode(",", $fieldNumbers));

			$conn = new DMLFunctions();
			$result = $conn->executeQuery($sql);
			if ($result) {
				$count = mysql_affected_rows();
			}
		}
		return $count;
	}

	/**
	 * Get a list of custom fields with the given conditions.
	 *
	 * @param array   $selectCondition Array of select conditions to use.
	 * @return array  Array of CustomFields objects. Returns an empty (length zero) array if none found.
	 */
	private static function _getList($selectCondition = null) {

		$fields[0] = self::DB_FIELDS_NUM;
		$fields[1] = self::DB_FIELDS_NAME;
		$fields[2] = self::DB_FIELDS_TYPE;
                $fields[3] = self::DB_FIELDS_SCREEN;                
		$fields[4] = self::DB_FIELDS_EXTRA_DATA;

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
	 * Creates a CustomFields object from a resultset row
	 *
	 * @param array $row Resultset row from the database.
	 * @return CustomFields Custom Fields object.
	 */
	private static function _createFromRow($row) {

		$tmp = new CustomFields();
		$tmp->setFieldNumber($row[self::DB_FIELDS_NUM]);
		$tmp->setName($row[self::DB_FIELDS_NAME]);
		$tmp->setFieldType($row[self::DB_FIELDS_TYPE]);
                $tmp->setScreen($row[self::DB_FIELDS_SCREEN]);                
		$tmp->setExtraData($row[self::DB_FIELDS_EXTRA_DATA]);
		return $tmp;
	}
	
	private function _isDuplicateName($update=false) {
		$cutomFields = $this->filterExistingCustomFields();

		if (is_array($cutomFields)) {
			if ($cutomFields) {
				if ($cutomFields[0][0] == $this->getFieldNumber()){
					return false;
				}
			}
			return true;
		}

		return false;
	}
	
	public function filterExistingCustomFields() {

		$selectFields[] = self::DB_FIELDS_NUM; 
        $selectFields[] = self::DB_FIELDS_NAME;
	    $selectTable = self::TABLE_NAME;

        $selectConditions[] = "`".self::DB_FIELDS_NAME. "` = '".$this->getName()."'";	       
         
        $sqlBuilder = new SQLQBuilder();
        $query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions);
         
        $dbConnection = new DMLFunctions();
        $result = $dbConnection->executeQuery($query);

        $cnt = 0;

        while ($row = mysql_fetch_array($result, MYSQL_NUM)){
            $existingCustomFields[$cnt++] = $row;
        }

        if (isset($existingCustomFields)) {
            return $existingCustomFields;
        } else {
            $existingCustomFields = '';
            return $existingCustomFields;
        }
	}
	
}

class CustomFieldsException extends Exception {
}

?>
