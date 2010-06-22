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
require_once ROOT_PATH . '/lib/common/UniqueIDGenerator.php';

class Users {
	var $tableName = 'hs_hr_users';

	var $userID;
	var $userName;
	var $userPassword;
	var $userFirstName;
	var $userLastName;
	var $userEmpID;
	var $userIsAdmin;
	var $userDateEntered;
	var $userDateModified;
	var $userModifiedBy;
	var $userCreatedBy;
	var $userStatus;
	var $userGroupID;
	var $arrayDispList;
	var $employeeIdLength;


	function Users() {
		$this->sql_builder = new SQLQBuilder();
		$this->dbConnection = new DMLFunctions();

		$tmpSysConf = new sysConf();

		$this->employeeIdLength	= $tmpSysConf->getEmployeeIdLength();
	}

	function setUserID($userID){
		$this->userID = $userID;
	}

	function setUserName($userName){
		$this->userName = $userName;
	}

	function setUserPassword($userPassword) {
		$this->userPassword=$userPassword;
	}

	function setUserFirstName($userFirstName) {
		$this->userFirstName=$userFirstName;
	}

	function setUserLastName($userLastName) {
		$this->userLastName=$userLastName;
	}

	function setUserEmpID($userEmpID) {
		$this->userEmpID=$userEmpID;
	}

	function setUserIsAdmin($userIsAdmin) {
		$this->userIsAdmin=$userIsAdmin;
	}

	function setUserDateEntered($userDateEntered) {
		$this->userDateEntered=$userDateEntered;
	}

	function setUserDateModified($userDateModified) {
		$this->userDateModified=$userDateModified;
	}

	function setUserModifiedBy($userModifiedBy) {
		$this->userModifiedBy=$userModifiedBy;
	}

	function setUserCreatedBy($userCreatedBy) {
		$this->userCreatedBy=$userCreatedBy;
	}

	function setUserStatus($userStatus){
		$this->userStatus=$userStatus;
	}

	function setUserAddress($userAddress) {
		$this->userAddress=$userAddress;
	}

	function setUserGroupID($userGroupID) {
		$this->userGroupID=$userGroupID;
	}
///
	function getUserID(){
		return $this->userID;
	}

	function getUserName(){
		return $this->userName;
	}

	function getUserPassword() {
		return $this->userPassword;
	}

	function getUserFirstName() {
		return $this->userFirstName;
	}

	function getUserEmpID() {
		return $this->userEmpID;
	}

	function getUserIsAdmin() {
		return $this->userIsAdmin;
	}

	function getUserDesc() {
		return $this->userDesc;
	}

	function getUserDateEntered() {
		return $this->userDateEntered;
	}

	function getUserDateModified() {
		return $this->userDateModified;
	}

	function getUserModifiedBy() {
		return $this->userModifiedBy;
	}

	function getUserCreatedBy() {
		return $this->userCreatedBy;
	}

	function getUserDepartment() {
		return $this->userDepartment;
	}

	function getUserPhoneHome() {
		return $this->userPhoneHome;
	}

	function getUserPhoneMobile() {
		return $this->userPhoneMobile;
	}

	function getUserPhoneWork() {
		return $this->userPhoneWork;
	}

	function getUserEmail1() {
		return $this->userEmail1;
	}

	function getUserEmail2() {
		return $this->userEmail2;
	}

	function getUserStatus(){
		return $this->userStatus;
	}

	function getUserAddress() {
		return $this->userAddress;
	}

	function getUserDeleted() {
		return $this->userDeleted;
	}

	function getUserGroupID() {
		return $this->userGroupID;
	}


	function getListOfUsers($pageNO,$schStr,$mode, $sortField, $sortOrder, $isAdmin){

		$arrFieldList[0] = 'id';
		$arrFieldList[1] = 'user_name';
		$arrFieldList[2] = 'is_admin';


		$this->sql_builder->table_name = $this->tableName;
		$this->sql_builder->flg_select = 'true';
		$this->sql_builder->arr_select = $arrFieldList;

		if ($isAdmin) {
			$isAdmin = 'Yes';
		} else {
			$isAdmin = 'No';
		}

		$schStr = array($schStr, $isAdmin);
		$mode = array($mode, 2);

		$sqlQString =$this->sql_builder->passResultSetMessage($pageNO,$schStr,$mode, $sortField, $sortOrder, true);

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

	function countUsers($schStr,$mode, $isAdmin) {

		$arrFieldList[0] = 'id';
		$arrFieldList[1] = 'user_name';
		$arrFieldList[2] = 'is_admin';

		$sql_builder = new SQLQBuilder();
		$sql_builder->table_name = $this->tableName;

		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		if ($isAdmin) {
			$isAdmin = 'Yes';
		} else {
			$isAdmin = 'No';
		}

		$schStr = array($schStr, $isAdmin);
		$mode = array($mode, 2);

		$sqlQString = $sql_builder->countResultset($schStr,$mode, true);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$line = mysql_fetch_array($message2, MYSQL_NUM);

	    	return $line[0];
	}

	function addUsers(){

		 $this->userID = UniqueIDGenerator::getInstance()->getNextID($this->tableName, 'id', 'USR');

		 $arrFieldList[0] = "'" . $this->getUserID() . "'";
		 $arrFieldList[1] = "'" . $this->getUserName() . "'";
		 $arrFieldList[2] = "'" . $this->getUserPassword() . "'";
		 $arrFieldList[3] = ($this->getUserEmpID() == '') ? 'null' :"'". $this->getUserEmpID() . "'";
		 $arrFieldList[4] = "'" . $this->getUserIsAdmin() . "'";
		 $arrFieldList[5] = "'" . $this->getUserDateEntered() . "'";
		 $arrFieldList[6] = "'" . $this->getUserCreatedBy() . "'";
		 $arrFieldList[7] = "'" . $this->getUserStatus() . "'";
		 $arrFieldList[8] = ($this->getUserGroupID()=='0') ? 'null' :"'". $this->getUserGroupID() . "'";

	    $arrRecordsList[0] = 'id';
		$arrRecordsList[1] = 'user_name';
		$arrRecordsList[2] = 'user_password';
		$arrRecordsList[3] = 'emp_number';
		$arrRecordsList[4] = 'is_admin';
		$arrRecordsList[5] = 'date_entered';
		$arrRecordsList[6] = 'created_by';
		$arrRecordsList[7] = 'status';
		$arrRecordsList[8] = 'userg_id';

		$this->sql_builder->table_name = $this->tableName;
		$this->sql_builder->flg_insert = 'true';
		$this->sql_builder->arr_insertfield = $arrRecordsList;
		$this->sql_builder->arr_insert = $arrFieldList;

		$sqlQString = $this->sql_builder->addNewRecordFeature2();

		$message2 = $this->dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		 return $message2;
	}

	function updateUsers() {

		if($this->getUserID() == $_SESSION['user'] && ($this->getUserStatus() != 'Enabled')) {
			return false;
		}

		 $arrFieldList[0] = "'" . $this->getUserID() . "'";
		 $arrFieldList[1] = "'" . $this->getUserName() . "'";
		 $arrFieldList[2] = ($this->getUserEmpID() == '') ? 'null' :"'". $this->getUserEmpID() . "'";
		 $arrFieldList[3] = "'" . $this->getUserIsAdmin() . "'";
		 $arrFieldList[4] = "'" . $this->getUserDateModified() . "'";
		 $arrFieldList[5] = "'" . $this->getUserModifiedBy() . "'";
		 $arrFieldList[6] = "'" . $this->getUserStatus() . "'";
		 $arrFieldList[7] = ($this->getUserGroupID()=='0') ? 'null' :"'". $this->getUserGroupID() . "'";
/////
	    $arrRecordsList[0] = 'id';
		$arrRecordsList[1] = 'user_name';
		$arrRecordsList[2] = 'emp_number';
		$arrRecordsList[3] = 'is_admin';
		$arrRecordsList[4] = 'date_modified';
		$arrRecordsList[5] = 'modified_user_id';
		$arrRecordsList[6] = 'status';
		$arrRecordsList[7] = 'userg_id';

		$password = $this->getUserPassword();

		if (isset($password) && $password) {
			$arrFieldList[8] = $password;
			$arrRecordsList[8] = 'user_password';
		}

		$this->sql_builder->table_name = $this->tableName;
		$this->sql_builder->flg_update = 'true';
		$this->sql_builder->arr_update = $arrRecordsList;
		$this->sql_builder->arr_updateRecList = $arrFieldList;

		$sqlQString = $this->sql_builder->addUpdateRecord1();

		$message2 = $this->dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		return $message2;
	}

	function updateChangeUsers() {
		//echo $this->getUserID(). $_SESSION['user'];
		 if($this->getUserID() !== $_SESSION['user']) {
			return false;
		}
		 //echo 'hi';
		 $arrFieldList[0] = "'" . $this->getUserID() . "'";
		 $arrFieldList[1] = "'" . $this->getUserName() . "'";
		if($this->getUserPassword() != '')
		 $arrFieldList[2] = "'" . md5($this->getUserPassword()) . "'";
/////

		$arrRecordsList[0] = 'id';
		$arrRecordsList[1] = 'user_name';
		if($this->getUserPassword() != '')
			$arrRecordsList[2] = 'user_password';

		$this->sql_builder->table_name = $this->tableName;
		$this->sql_builder->flg_update = 'true';
		$this->sql_builder->arr_update = $arrRecordsList;
		$this->sql_builder->arr_updateRecList = $arrFieldList;

		$this->sql_builder->flg_update = true;

		$sqlQString = $this->sql_builder->addUpdateRecord1();
		
		$message2 = $this->dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		return $message2;
	}

	public function updateUserEmail($userId, $email) {
		$sqlQBuilder = new SQLQBuilder();

		$arrFields[0] = '`email1`';

		$changeValues[0] = $email;

		$arrTable = "`hs_hr_users`";

		$updateConditions[1] = "`id` = '{$userId}'";

		$query = $sqlQBuilder->simpleUpdate($arrTable, $arrFields, $changeValues, $updateConditions);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		return true;
	}

	public function fetchUserEmail($userId) {
		$sqlQBuilder = new SQLQBuilder();

		$arrFields[0] = '`email1`';

		$arrTable = "`hs_hr_users`";

		$selectConditions[1] = "`id` = '{$userId}'";

		$query = $sqlQBuilder->simpleSelect($arrTable, $arrFields, $selectConditions, $arrFields[0], 'ASC');

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		$row = mysql_fetch_row($result);

		return $row[0];
	}

	function filterUsers($getID) {

		$this->ID = $getID;
	    $arrFieldList[0] = 'a.id';
		$arrFieldList[1] = 'a.user_name';
		$arrFieldList[2] = 'LPAD(a.`emp_number`, '.$this->employeeIdLength.', 0)';
		$arrFieldList[3] = 'a.is_admin';
		$arrFieldList[4] = 'a.date_entered';
		$arrFieldList[5] = 'a.date_modified';
		$arrFieldList[6] = 'a.modified_user_id';
		$arrFieldList[7] = 'a.created_by';
		$arrFieldList[8] = 'a.status';
		$arrFieldList[9] = 'a.userg_id';
		$arrFieldList[10] = 'b.EMP_FIRSTNAME';
		$arrFieldList[11] = 'b.EMPLOYEE_ID';
        $arrFieldList[12] = 'b.emp_lastname';
        $arrFieldList[13] = 'b.emp_work_email';

		$this->sql_builder->table_name = $this->tableName.' a LEFT JOIN HS_HR_EMPLOYEE b ON (a.EMP_NUMBER = b.EMP_NUMBER)';
		$this->sql_builder->flg_select = 'true';
		$this->sql_builder->arr_select = $arrFieldList;

		$sqlQString = $this->sql_builder->selectOneRecordFiltered($this->ID);

		$message2 = $this->dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$i=0;

		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {

	    	for ($j=0; $j < count($line); $j++) {
				$arrayDispList[$i][$j] = $line[$j];
			}
	    	/*
			$arrayDispList[$i][1] = $line[1];
	    	$arrayDispList[$i][3] = $line[3];
	    	$arrayDispList[$i][4] = $line[4];
	    	$arrayDispList[$i][5] = $line[5];
	    	$arrayDispList[$i][6] = $line[6];
	    	$arrayDispList[$i][7] = $line[7];
	    	$arrayDispList[$i][8] = $line[8];
	    	$arrayDispList[$i][9] = $line[9];
	    	$arrayDispList[$i][10] = $line[10];
			*/

	    	$i++;

	     }

	     if (isset($arrayDispList)) {

			return $arrayDispList;

		} else {

			$arrayDispList = '';
			return $arrayDispList;

		}

	}

	function filterChangeUsers($getID) {

	    $arrFieldList[0] = 'id';
		$arrFieldList[1] = 'user_name';
		$arrFieldList[2] = 'user_password';


		$this->sql_builder->table_name = $this->tableName;
		$this->sql_builder->flg_select = 'true';
		$this->sql_builder->arr_select = $arrFieldList;

        $this->ID = $this->sql_builder->quoteCorrect($getID);
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

			$arrayDispList = '';
			return $arrayDispList;

		}

	}

	function delUsers($arrList) {

		$arrFieldList[0] = 'id';

		$this->sql_builder->table_name = $this->tableName;
		$this->sql_builder->flg_delete = 'true';
		$this->sql_builder->arr_delete = $arrFieldList;

		$delFlag = false;
		for($c=0;count($arrList[0])>$c;$c++)
			if($_SESSION['user'] == $arrList[0][$c])
				$delFlag = true;

		if($delFlag) {
			return;
		}

		$sqlQString = $this->sql_builder->deleteRecord($arrList);

		$message2 = $this->dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		return $message2;
	}

	function getUserGroupCodes(){

		$arrFieldList[0] = 'userg_id';
		$arrFieldList[1] = 'userg_name';


		$this->sql_builder->table_name = 'hs_hr_user_group';
		$this->sql_builder->flg_select = 'true';
		$this->sql_builder->arr_select = $arrFieldList;

		$sqlQString =$this->sql_builder->passResultSetMessage();

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

	function getEmployeeCodes() {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'LPAD(`EMP_NUMBER`, '.$this->employeeIdLength.', 0)';
		$arrFieldList[1] = 'EMP_FIRSTNAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultSetMessage();

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		//echo mysql_error();
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
}
?>