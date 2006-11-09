<?php

/*
 *
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
 * all the essential functionalities required for any enterprise. 
 * Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com
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

class LeaveType {
	
	/*
	 *	Leave Status Constants
	 *
	 **/
	
	public  $avalableStatuFlag = 1;
	public  $unAvalableStatuFlag = 0;
	
	/*
	 *	Class Attributes
	 *
	 **/
	private $leaveTypeId;
	private $leaveTypeName;
	
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
	
	public function getLeaveTypeName () {
		return $this->leaveTypeName;
	}
	
	public function setLeaveTypeName($leaveTypeName) {
		$this->leaveTypeName = $leaveTypeName;
	}
	
	/**
	 * Enter description here...
	 *
	 */
	public function addLeaveType() {
		$this->_getNewLeaveTypeId();

		$arrRecordsList[0] = "'".$this->getLeaveTypeId()."'";
		$arrRecordsList[1] = "'".$this->getLeaveTypeName() ."'";
		$arrRecordsList[2] = $this->avalableStatuFlag;
		

		$sqlBuilder = new SQLQBuilder();
					
		$arrTable = "`hs_hr_leavetype`";
		

		
		$query = $sqlBuilder->simpleInsert($arrTable, $arrRecordsList);
		
		$dbConnection = new DMLFunctions();	

		$result = $dbConnection -> executeQuery($query);
		
	}
	
	private function _getNewLeaveTypeId() {		
		
		$sql_builder = new SQLQBuilder();
		$selectTable = "`hs_hr_leavetype`";		
		$selectFields[0] = '`leave_type_id`';
		$selectOrder = "DESC";
		$selectLimit = 1;
		$sortingField = '`leave_type_id`';
		
		$query = $sql_builder->simpleSelect($selectTable, $selectFields, null, $sortingField, $selectOrder, $selectLimit);

		$dbConnection = new DMLFunctions();	

		$result = $dbConnection -> executeQuery($query);		
		$common_func = new CommonFunctions();
		
		$row = mysql_fetch_row($result);

		$this->setLeaveTypeId($common_func->explodeString($row[0],"LTY"));  
				
		

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
	
	public function editLeaveType () {
		
		$sql_builder = new SQLQBuilder();
		
		$selectTable = "`hs_hr_leavetype` ";	
			
		$changeFields[0] = "`leave_type_name`";
		
		$changeValues[0] = "'".$this->getLeaveTypeName()."'";

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
	
	public function deleteLeaveType() {
		
		$sql_builder = new SQLQBuilder();
		
		$selectTable = "`hs_hr_leavetype` ";	
			
		$changeFields[0] = "`available_flag`";
		
		$changeValues[0] = "'".$this->unAvalableStatuFlag."'";

			
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
	
	public function fetchLeaveTypes() {
		
		$sql_builder = new SQLQBuilder();
		
		$selectTable = "`hs_hr_leavetype` ";	
		
		$selectFields[0] = '`leave_type_id`';
		$selectFields[1] = '`leave_type_name`';	
		
		$selectConditions[0] = "`available_flag` = '".$this->avalableStatuFlag."'";
		
    	$selectOrder = "ASC";

    	$selectOrderBy = $selectFields[0];
		
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
			
			$objArr[] = $tmpLeaveArr;
		}
		
		return $objArr;
	}
}
?>