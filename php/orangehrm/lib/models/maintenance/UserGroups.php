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

class UserGroups {
	var $tableName = 'hs_hr_user_group';

	var $userGroupID;
	var $userGroupName;
	var $userGroupRepDef;
	var $arrayDispList;


	function UserGroups() {
		$this->sql_builder = new SQLQBuilder();
		$this->dbConnection = new DMLFunctions();
	}

	function setUserGroupID($userGroupID){
		$this->userGroupID = $userGroupID;
	}

	function setUserGroupName($userGroupName){
		$this->userGroupName = $userGroupName;
	}

	function setUserGroupRepDef($userGroupRepDef) {
		$this->userGroupRepDef = $userGroupRepDef;
	}

	function getUserGroupID() {
		return $this->userGroupID;
	}

	function getUserGroupName(){
		return $this->userGroupName;
	}

	function getUserGroupRepDef() {
		return $this->userGroupRepDef;
	}

	function getListOfUserGroups($pageNO,$schStr,$mode, $sortField, $sortOrder){

		$arrFieldList[0] = 'userg_id';
		$arrFieldList[1] = 'userg_name';

		$this->sql_builder->table_name = $this->tableName;
		$this->sql_builder->flg_select = 'true';
		$this->sql_builder->arr_select = $arrFieldList;

		$sqlQString =$this->sql_builder->passResultSetMessage($pageNO,$schStr,$mode, $sortField, $sortOrder);

		$message2 = $this->dbConnection -> executeQuery($sqlQString);

		$i=0;

		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {

	    	$arrayDispList[$i][0] = $line[0];
	    	$arrayDispList[$i][1] = $line[1];
	    	$i++;

	     }

	     if (isset($arrayDispList)) {

			return $arrayDispList;

		} else {

			$arrayDispList = '';
			return $arrayDispList;

		}
	}

	function countUserGroups($schStr,$mode) {

		$arrFieldList[0] = 'userg_id';
		$arrFieldList[1] = 'userg_name';

		$sql_builder = new SQLQBuilder();
		$sql_builder->table_name = $this->tableName;

		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->countResultset($schStr,$mode);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$line = mysql_fetch_array($message2, MYSQL_NUM);

	    	return $line[0];
	}

	function addUserGroups(){
		$arrFieldList[0] = "'". $this->getUserGroupID() . "'";
		$arrFieldList[1] = "'". $this->getUserGroupName() . "'";
		$arrFieldList[2] = "'". $this->getUserGroupRepDef() . "'";

		$arrRecordsList[0] = 'userg_id';
		$arrRecordsList[1] = 'userg_name';
		$arrRecordsList[2] = 'userg_repdef';

		$this->sql_builder->table_name = $this->tableName;
		$this->sql_builder->flg_insert = 'true';
		$this->sql_builder->arr_insertfield = $arrRecordsList;
		$this->sql_builder->arr_insert = $arrFieldList;

		$sqlQString = $this->sql_builder->addNewRecordFeature2();

		$message2 = $this->dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		 return $message2;
		 echo $message2;
	}

	function updateUserGroups(){
		$arrFieldList[0] = "'". $this->getUserGroupID() . "'";
		$arrFieldList[1] = "'". $this->getUserGroupName() . "'";
		$arrFieldList[2] = "'". $this->getUserGroupRepDef() . "'";

	    $arrRecordsList[0] = 'userg_id';
		$arrRecordsList[1] = 'userg_name';
		$arrRecordsList[2] = 'userg_repdef';

		$this->sql_builder->table_name = $this->tableName;
		$this->sql_builder->flg_update = 'true';
		$this->sql_builder->arr_update = $arrRecordsList;
		$this->sql_builder->arr_updateRecList = $arrFieldList;

		$sqlQString = $this->sql_builder->addUpdateRecord1();

		$message2 = $this->dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		return $message2;
	}

	function getLastRecord(){
		$arrFieldList[0] = 'userg_id';

		$this->sql_builder->table_name = $this->tableName;
		$this->sql_builder->flg_select = 'true';
		$this->sql_builder->arr_select = $arrFieldList;

		$sqlQString = $this->sql_builder->selectOneRecordOnly();

		$message2 = $this->dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$common_func = new CommonFunctions();

		if (isset($message2)) {

			$i=0;

		while ($line = mysql_fetch_array($message2, MYSQL_ASSOC)) {
			foreach ($line as $col_value) {
			$this->singleField = $col_value;
			}
		}

		return $common_func->explodeString($this->singleField,"USG");

		}
	}

	function filterUserGroups($getID) {

		$this->ID = $getID;
		$arrFieldList[0] = 'userg_id';
		$arrFieldList[1] = 'userg_name';
		$arrFieldList[2] = 'userg_repdef';

		$this->sql_builder->table_name = $this->tableName;
		$this->sql_builder->flg_select = 'true';
		$this->sql_builder->arr_select = $arrFieldList;

		$sqlQString = $this->sql_builder->selectOneRecordFiltered($this->ID);

		$message2 = $this->dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$i=0;

		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {

	    	$arrayDispList[$i][0] = $line[0];
	    	$arrayDispList[$i][1] = $line[1];
	    	$arrayDispList[$i][2] = $line[2];

	    	$i++;

	     }

	     if (isset($arrayDispList)) {

			return $arrayDispList;

		} else {

			return null;

		}

	}

	function delUserGroups($arrList) {

		$arrFieldList[0] = 'userg_id';

		$this->sql_builder->table_name = $this->tableName;
		$this->sql_builder->flg_delete = 'true';
		$this->sql_builder->arr_delete = $arrFieldList;

		$delFlag = false;
		for($c=0;count($arrList[0])>$c;$c++)
			if('USG001' == $arrList[0][$c])
				$delFlag = true;

		if($delFlag) {
			return false;
		}

		$sqlQString = $this->sql_builder->deleteRecord($arrList);

		$message2 = $this->dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		return $message2;
	}

}
?>