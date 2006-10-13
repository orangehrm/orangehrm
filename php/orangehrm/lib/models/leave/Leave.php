<?php

/*

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
 * @Date	:	October 12th, 2006
 *
 */

require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';
require_once ROOT_PATH . '/lib/dao/SQLQBuilder.php';

class Leave {

	/*
	 *	Retrieves Leave Details of all leave that have been applied for but
	 *	not yet taken.
	 *
	 *	Arguements
	 *	----------
	 *
	 *		$employeeID	-	String	-	Employee ID of the format EMP999
	 *
	 *	Returns
	 *	-------
	 *
	 *	A 2D array of the leaves
	 *
	 **/
	
	public function retriveLeaveEmployee($employeeID) {
		
		$sqlBuilder = new SQLQBuilder();
		
		$arrFields[0] = 'a.`Date_Applied`';
		$arrFields[1] = 'b.`Leave_Type_Name`';
		$arrFields[2] = 'a.`Status`';
		$arrFields[3] = 'a.`Leave_Length`';
		$arrFields[4] = 'a.`subordinate_comments`';
		
		$arrTables[0] = "`hs_hr_leave` a";
		$arrTables[1] = "`hs_hr_leavetype` b";		
		
		$joinConditions[1] = "a.`Leave_Type_ID` = b.`Leave_Type_ID`";
		
		$selectConditions[0] = "b.`Available_Flag` = 1";
		$selectConditions[1] = "a.`Employee_Id` = '$employeeID'";
		$selectConditions[2] = "a.`Status` != 0";
		$selectConditions[3] = "a.`Status` != 3";
		
		$query = $sqlBuilder->selectFromMultipleTable($arrFields, $arrTables, $joinConditions, $selectConditions);
				
		//echo $query."\n";
		
		$dbConnection = new DMLFunctions();	

		$result = $dbConnection -> executeQuery($query);
		
		$leaveArr = null;
		
		while ($row = mysql_fetch_row($result)) {
			$leaveArr[] = $row;
		}
		
		return $leaveArr; 
	}
	
	/*
	 *	Marks the status to be cancelled
	 *
	 *	Arguements
	 *	----------
	 *
	 *		$leaveID	-	Integer/String	-	Leave ID of integer
	 *
	 *	Returns
	 *	-------
	 *
	 *	A 2D array of the leaves
	 *
	 **/
	
	public function cancelLeave($leaveID) {
		return $this->_changeLeaveStatus($leaveID, "0");
	}
	
	private function _changeLeaveStatus($leaveID, $status="3") {
		
		$sqlBuilder = new SQLQBuilder();
		
		$table = "`hs_hr_leave`";
		
		$changeFields[0] = "`Status`";
		
		$changeValues[0] = $status;
		
		$updateConditions[0] = "`Leave_ID` = $leaveID";
		
		$query = $sqlBuilder->simpleUpdate($table, $changeFields, $changeValues, $updateConditions);
		
		//echo $query."\n";
		
		$dbConnection = new DMLFunctions();	

		$result = $dbConnection -> executeQuery($query);
		
		if (isset($result) && $result) {
			return true;
		};
		
		return false;	
	}
}

?>