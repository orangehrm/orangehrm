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

require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';
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
	
	
	public function addLeaveType() {
		
		
		$arrRecordsList[0] = $this->getLeaveTypeId($this->_getNewLeaveTypeId());
		$arrRecordsList[1] = $this->getLeaveTypeId($this->_getNewLeaveTypeNameId());
		$arrRecordsList[2] = "'".$this->getLeaveTypeName() ."'";
		$arrRecordsList[3] = $this->avalableStatuFlag;
		
		$sqlBuilder = new SQLQBuilder();
					
		$arrTable = "`hs_hr_leavetype`";
		
		//print_r($arrRecordsList);	
		
		$query = $sqlBuilder->simpleInsert($arrTable, $arrRecordsList);
		
		//echo  $query;
		
		$dbConnection = new DMLFunctions();	

		$result = $dbConnection -> executeQuery($query);
	}
	
	private function _getNewLeaveTypeId() {		
		
		$sql_builder = new SQLQBuilder();
		$tableName = "'hs_hr_leave'";		
		$arrFieldList[0] = 'Leave_Type_ID';
				
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
	
		$sqlQString = $sql_builder->selectOneRecordOnly();
			
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
				
		$common_func = new CommonFunctions();
		
		if (isset($message2)) {
			
			$i=0;
		
		while ($line = mysql_fetch_array($message2, MYSQL_ASSOC)) {		
			foreach ($line as $col_value) {
			$this->singleField = $col_value;
			}		
		}
			
		return $common_func->explodeString($this->singleField,"LTY"); 
				
		}
	}
	
	private function _getNewLeaveNameId() {		
		
		$sql_builder = new SQLQBuilder();
		
		$selectTable = "`hs_hr_leavetype`";		
		$selectFields[0] = '`Leave_Type_NameID`';
		$selectOrder = "DESC";
		$selectLimit = 1;
		$sortingField = '`Leave_Type_NameID`';
		
		$query = $sql_builder->simpleSelect($selectTable, $selectFields, null, $sortingField, $selectOrder, $selectLimit);
		//echo $query;
		$dbConnection = new DMLFunctions();	

		$result = $dbConnection -> executeQuery($query);
		
		$row = mysql_fetch_row($result);
		
		$this->setLeaveId($row[0]+1);
	}

}
?>