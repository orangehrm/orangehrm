<?php

/*
 *
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

require_once ROOT_PATH . '/lib/common/CommonFunctions.php';
require_once ROOT_PATH . '/lib/dao/SQLQBuilder.php';
require_once ROOT_PATH . '/lib/dao/SQLQBuilder.php';
require_once ROOT_PATH . '/lib/common/UniqueIDGenerator.php';

class LeaveType {

	/*
	 *	Leave Status Constants
	 *
	 **/
	public  $availableStatusFlag = 1;
	public  $unAvailableStatusFlag = 0;

	/*
	 *	Class Attributes
	 *
	 **/
	private $leaveTypeId;
	private $leaveTypeName;
	private $leaveTypeAvailable;

	/*
	 *
	 *	Class Constructor
	 *
	 **/

	public function __construct() {
		// nothing to do
	}

	/*
	 *	Setter method followed by getter method for each
	 *	attribute
	 *
	 **/

	public function getLeaveTypeId () {
		return $this->leaveTypeId;
	}

	public function setLeaveTypeId ($leaveTypeId) {
		$this->leaveTypeId = $leaveTypeId;
	}
	/**
	 * @param void
	 */
	public function getLeaveTypeName () {
		return $this->leaveTypeName;
	}

	public function setLeaveTypeName($leaveTypeName) {
		$this->leaveTypeName = $leaveTypeName;
	}

	public function getLeaveTypeAvailable () {
		return $this->leaveTypeAvailable;
	}

	public function setLeaveTypeAvailable($flag) {
		$this->leaveTypeAvailable = $flag;
	}

	/**
	 * Add the Leave Type
	 *
	 */
	public function addLeaveType() {

		$this->leaveTypeId = UniqueIDGenerator::getInstance()->getNextID('hs_hr_leavetype', 'leave_type_id', 'LTY');

		$arrRecordsList[0] = "'".$this->getLeaveTypeId()."'";
		$arrRecordsList[1] = "'".$this->getLeaveTypeName() ."'";
		$arrRecordsList[2] = $this->availableStatusFlag;


		$sqlBuilder = new SQLQBuilder();

		$arrTable = "`hs_hr_leavetype`";



		$query = $sqlBuilder->simpleInsert($arrTable, $arrRecordsList);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection -> executeQuery($query);
		return $result;

	}

	public function retriveLeaveType($leaveType)
	{
		$sql_builder = new SQLQBuilder();

		$selectTable = "`hs_hr_leavetype` ";

		$selectFields[0] = '`leave_type_id`';
		$selectFields[1] = '`leave_type_name`';
		$selectFields[2] = '`available_flag`';

		$updateConditions[0] = "`leave_type_id` = '".$leaveType."'";

		$query = $sql_builder->simpleSelect($selectTable, $selectFields, $updateConditions, null, null, null);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		$leaveTypeArr = $this->_buildObjArr($result);

		return $leaveTypeArr;
	}

	/**
	 * Get leave type with given name
	 * @param string $leaveTypeName Leave type name
	 * @return LeaveType object
	 */
	public function getLeaveTypeWithName($leaveTypeName, $includeDeleted = false) {
		$sql_builder = new SQLQBuilder();

		$selectTable = "`hs_hr_leavetype` ";

		$selectFields[0] = '`leave_type_id`';
		$selectFields[1] = '`leave_type_name`';
		$selectFields[2] = '`available_flag`';

                $selectConditions[0] = "`leave_type_name` = '".$leaveTypeName."'";

                $selectLimit     = '0,1';
                $selectOrder     = 'DESC';
                $selectOrderBy   = '`leave_type_id`';

		$selectConditions[0] = "`leave_type_name` = '".$leaveTypeName."'";

		if (!$includeDeleted) {
			$selectConditions[1] = "`available_flag` = '".$this->availableStatusFlag."'";
		}

                $query = $sql_builder->simpleSelect($selectTable, $selectFields, $selectConditions, $selectOrderBy,  $selectOrder, $selectLimit);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		$leaveTypeArr = $this->_buildObjArr($result);

		return $leaveTypeArr;
	}

	public function editLeaveType () {

		$sql_builder = new SQLQBuilder();

		$selectTable = "`hs_hr_leavetype` ";

		$changeFields[0] = "`leave_type_name`";

		$changeValues[0] = "'".$this->getLeaveTypeName()."'";

		$updateConditions[0] = "`leave_type_id` = '".$this->getLeaveTypeId()."'";

		$query = $sql_builder->simpleUpdate($selectTable, $changeFields, $changeValues, $updateConditions);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		if ($result === true) {
			return mysql_affected_rows();
		}
		return false;

	}

	public function deleteLeaveType() {

		$sql_builder = new SQLQBuilder();

		$selectTable = "`hs_hr_leavetype` ";

		$changeFields[0] = "`available_flag`";

		$changeValues[0] = "'".$this->unAvailableStatusFlag."'";


		$updateConditions[0] = "`leave_type_id` = '".$this->getLeaveTypeId()."'";

		$query = $sql_builder->simpleUpdate($selectTable, $changeFields, $changeValues, $updateConditions);

		//echo $query."\n";

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		if (isset($result) && (mysql_affected_rows() > 0)) {
			return true;
		};

		return false;

	}

	public function undeleteLeaveType() {

		$sql_builder = new SQLQBuilder();

		$selectTable = "`hs_hr_leavetype` ";

		$changeFields[0] = "`available_flag`";

		$changeValues[0] = "'".$this->availableStatusFlag."'";


		$updateConditions[0] = "`leave_type_id` = '".$this->getLeaveTypeId()."'";

		$query = $sql_builder->simpleUpdate($selectTable, $changeFields, $changeValues, $updateConditions);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		if (isset($result) && (mysql_affected_rows() > 0)) {
			return true;
		};

		return false;

	}

	public function fetchLeaveTypes($all=false) {

		$sql_builder = new SQLQBuilder();

		$selectTable = "`hs_hr_leavetype` ";

		$selectFields[0] = '`leave_type_id`';
		$selectFields[1] = '`leave_type_name`';

		if (!$all) {
			$selectConditions[0] = "`available_flag` = '".$this->availableStatusFlag."'";
		} else {
			$selectConditions = null;
			$selectFields[2] = '`available_flag`';
		}

    	$selectOrder = "ASC";

    	$selectOrderBy = $selectFields[1];

		$query = $sql_builder->simpleSelect($selectTable, $selectFields, $selectConditions, $selectOrderBy, $selectOrder, null);
		//echo $query."\n";
		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		$leaveTypeArr = $this->_buildObjArr($result);

		return $leaveTypeArr;
	}

	protected function _buildObjArr($result) {

		$objArr = null;

		while ($row = mysql_fetch_row($result)) {

			$tmpLeaveArr = new LeaveType();

			$tmpLeaveArr->setLeaveTypeId($row[0]);
			$tmpLeaveArr->setLeaveTypeName($row[1]);

			if (isset($row[2])) {
				$tmpLeaveArr->setLeaveTypeAvailable($row[2]);
			}

			$objArr[] = $tmpLeaveArr;
		}

		return $objArr;
	}
}
?>