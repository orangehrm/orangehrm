<?
/*
// OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
// all the essential functionalities required for any enterprise. 
// Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com

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

require_once OpenSourceEIM . '/lib/Confs/Conf.php';
require_once OpenSourceEIM . '/lib/Models/DMLFunctions.php';
require_once OpenSourceEIM . '/lib/Models/SQLQBuilder.php';
require_once OpenSourceEIM . '/lib/CommonMethods/CommonFunctions.php';

class Users {
	var $tableName = 'hs_hr_users';
	
	var $userID;
	var $userName;
	var $userPassword;
	var $userFirstName;
	var $userLastName;
	var $userReportsToID;
	var $userIsAdmin;
	var $userDesc;
	var $userDateEntered;
	var $userDateModified;
	var $userModifiedBy;
	var $userCreatedBy;
	var $userDepartment;
	var $userPhoneHome;
	var $userPhoneMobile;
	var $userPhoneWork;
	var $userEmail1;
	var $userEmail2;
	var $userStatus;
	var $userAddress;
	var $userDeleted;
	var $userGroupID;
	
	var $arrayDispList;
	
	
	function Users() {
		$this->sql_builder = new SQLQBuilder();
		$this->dbConnection = new DMLFunctions();		
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
	
	function setUserReportsToID($userReportsToID) {
		$this->userReportsToID=$userReportsToID;
	}
	
	function setUserIsAdmin($userIsAdmin) {
		$this->userIsAdmin=$userIsAdmin;
	}
	
	function setUserDesc($userDesc) {
		$this->userDesc=$userDesc;
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
	
	function setUserDepartment($userDepartment) {
		$this->userDepartment=$userDepartment;
	}
	
	function setUserPhoneHome($userPhoneHome) {
		$this->userPhoneHome=$userPhoneHome;
	}
	
	function setUserPhoneMobile($userPhoneMobile) {
		$this->userPhoneMobile=$userPhoneMobile;
	}

	function setUserPhoneWork($userPhoneWork) {
		$this->userPhoneWork=$userPhoneWork;
	}

	function setUserEmail1($userEmail1) {
		$this->userEmail1=$userEmail1;
	}
	
	function setUserEmail2($userEmail2) {
		$this->userEmail2=$userEmail2;
	}
	
	function setUserStatus($userStatus){
		$this->userStatus=$userStatus;
	}
	
	function setUserAddress($userAddress) {
		$this->userAddress=$userAddress;
	}
	
	function setUserDeleted($userDeleted) {
		$this->userDeleted=$userDeleted;
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
	
	function getUserLastName() {
		return $this->userLastName;
	}
	
	function getUserReportsToID() {
		return $this->userReportsToID;
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
	
		
	function getListOfUsers($pageNO,$schStr,$mode){
		
		$arrFieldList[0] = 'id';
		$arrFieldList[1] = 'user_name';
	
		
		$this->sql_builder->table_name = $this->tableName;
		$this->sql_builder->flg_select = 'true';
		$this->sql_builder->arr_select = $arrFieldList;
		
		$sqlQString =$this->sql_builder->passResultSetMessage($pageNO,$schStr,$mode);
		
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
	
	function countUsers($schStr,$mode) {
		
		$arrFieldList[0] = 'id';
		$arrFieldList[1] = 'user_name';
	
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

	function addUsers(){
		 $arrFieldList[0] = "'" . $this->getUserID() . "'";
		 $arrFieldList[1] = "'" . $this->getUserName() . "'";
		 $arrFieldList[2] = "'" . $this->getUserPassword() . "'"; 
		 $arrFieldList[3] = "'" . $this->getUserFirstName() . "'"; 
		 $arrFieldList[4] = "'" . $this->getUserLastName() . "'"; 
		 $arrFieldList[5] = "'" . $this->getUserReportsToID() . "'"; 
		 $arrFieldList[6] = "'" . $this->getUserIsAdmin() . "'"; 
		 $arrFieldList[7] = "'" . $this->getUserDesc() . "'"; 
		 $arrFieldList[8] = "'" . $this->getUserDateEntered() . "'"; 
		 $arrFieldList[9] = "'" . $this->getUserCreatedBy() . "'"; 
		 $arrFieldList[10] = "'" . $this->getUserDepartment() . "'"; 
		 $arrFieldList[11] = "'" . $this->getUserPhoneHome() . "'"; 
		 $arrFieldList[12] = "'" . $this->getUserPhoneMobile() . "'"; 
		 $arrFieldList[13] = "'" . $this->getUserPhoneWork() . "'"; 
		 $arrFieldList[14] = "'" . $this->getUserEmail1() . "'"; 
		 $arrFieldList[15] = "'" . $this->getUserEmail2() . "'"; 
		 $arrFieldList[16] = "'" . $this->getUserStatus() . "'";
		 $arrFieldList[17] = "'" . $this->getUserAddress() . "'"; 
		 $arrFieldList[18] = "'" . $this->getUserDeleted() . "'"; 
		 $arrFieldList[19] = "'" . $this->getUserGroupID() . "'"; 
/////						
	    $arrRecordsList[0] = 'id';
		$arrRecordsList[1] = 'user_name';
		$arrRecordsList[2] = 'user_password';
		$arrRecordsList[3] = 'first_name';
		$arrRecordsList[4] = 'last_name';
		$arrRecordsList[5] = 'reports_to_id';
		$arrRecordsList[6] = 'is_admin';
		$arrRecordsList[7] = 'description';
		$arrRecordsList[8] = 'date_entered';
		$arrRecordsList[9] = 'created_by';
		$arrRecordsList[10] = 'department';
		$arrRecordsList[11] = 'phone_home';
		$arrRecordsList[12] = 'phone_mobile';
		$arrRecordsList[13] = 'phone_work';
		$arrRecordsList[14] = 'email1';
		$arrRecordsList[15] = 'email2';
		$arrRecordsList[16] = 'status';
		$arrRecordsList[17] = 'address_street';
		$arrRecordsList[18] = 'deleted';
		$arrRecordsList[19] = 'userg_id';
								
		$this->sql_builder->table_name = $this->tableName;
		$this->sql_builder->flg_insert = 'true';
		$this->sql_builder->arr_insertfield = $arrRecordsList;
		$this->sql_builder->arr_insert = $arrFieldList;		
	
		$sqlQString = $this->sql_builder->addNewRecordFeature2();
		
		$message2 = $this->dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		 return $message2;
		 echo $message2;
	}
	
	function updateUsers(){

		 $arrFieldList[0] = "'" . $this->getUserID() . "'";
		 $arrFieldList[1] = "'" . $this->getUserName() . "'";
		 $arrFieldList[2] = "'" . $this->getUserPassword() . "'"; 
		 $arrFieldList[3] = "'" . $this->getUserFirstName() . "'"; 
		 $arrFieldList[4] = "'" . $this->getUserLastName() . "'"; 
		 $arrFieldList[5] = "'" . $this->getUserReportsToID() . "'"; 
		 $arrFieldList[6] = "'" . $this->getUserIsAdmin() . "'"; 
		 $arrFieldList[7] = "'" . $this->getUserDesc() . "'"; 
		 $arrFieldList[8] = "'" . $this->getUserDateModified() . "'"; 
		 $arrFieldList[9] = "'" . $this->getUserModifiedBy() . "'"; 
		 $arrFieldList[10] = "'" . $this->getUserDepartment() . "'"; 
		 $arrFieldList[11] = "'" . $this->getUserPhoneHome() . "'"; 
		 $arrFieldList[12] = "'" . $this->getUserPhoneMobile() . "'"; 
		 $arrFieldList[13] = "'" . $this->getUserPhoneWork() . "'"; 
		 $arrFieldList[14] = "'" . $this->getUserEmail1() . "'"; 
		 $arrFieldList[15] = "'" . $this->getUserEmail2() . "'"; 
		 $arrFieldList[16] = "'" . $this->getUserStatus() . "'";
		 $arrFieldList[17] = "'" . $this->getUserAddress() . "'"; 
		 $arrFieldList[18] = "'" . $this->getUserDeleted() . "'"; 
		 $arrFieldList[19] = "'" . $this->getUserGroupID() . "'"; 
/////						
	    $arrRecordsList[0] = 'id';
		$arrRecordsList[1] = 'user_name';
		$arrRecordsList[2] = 'user_password';
		$arrRecordsList[3] = 'first_name';
		$arrRecordsList[4] = 'last_name';
		$arrRecordsList[5] = 'reports_to_id';
		$arrRecordsList[6] = 'is_admin';
		$arrRecordsList[7] = 'description';
		$arrRecordsList[8] = 'date_modified';
		$arrRecordsList[9] = 'modified_user_id';
		$arrRecordsList[10] = 'department';
		$arrRecordsList[11] = 'phone_home';
		$arrRecordsList[12] = 'phone_mobile';
		$arrRecordsList[13] = 'phone_work';
		$arrRecordsList[14] = 'email1';
		$arrRecordsList[15] = 'email2';
		$arrRecordsList[16] = 'status';
		$arrRecordsList[17] = 'address_street';
		$arrRecordsList[18] = 'deleted';
		$arrRecordsList[19] = 'userg_id';

		$this->sql_builder->table_name = $this->tableName;
		$this->sql_builder->flg_update = 'true';
		$this->sql_builder->arr_update = $arrRecordsList;	
		$this->sql_builder->arr_updateRecList = $arrFieldList;	
	
		$sqlQString = $this->sql_builder->addUpdateRecord1();
		
		$message2 = $this->dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		return $message2;
	}
	
	function getLastRecord(){
		$arrFieldList[0] = 'id';
		
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
			
		return $common_func->explodeString($this->singleField,"USR");
				
		}		
	}
	
	function filterUsers($getID) {
		
		$this->ID = $getID;
	    $arrFieldList[0] = 'id';
		$arrFieldList[1] = 'user_name';
		$arrFieldList[2] = 'user_password';
		$arrFieldList[3] = 'first_name';
		$arrFieldList[4] = 'last_name';
		$arrFieldList[5] = 'reports_to_id';
		$arrFieldList[6] = 'is_admin';
		$arrFieldList[7] = 'description';
		$arrFieldList[8] = 'date_entered';
		$arrFieldList[9] = 'date_modified';
		$arrFieldList[10] = 'modified_user_id';
		$arrFieldList[11] = 'created_by';
		$arrFieldList[12] = 'department';
		$arrFieldList[13] = 'phone_home';
		$arrFieldList[14] = 'phone_mobile';
		$arrFieldList[15] = 'phone_work';
		$arrFieldList[16] = 'email1';
		$arrFieldList[17] = 'email2';
		$arrFieldList[18] = 'status';
		$arrFieldList[19] = 'address_street';
		$arrFieldList[20] = 'deleted';
		$arrFieldList[21] = 'userg_id';

						
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
	    	$arrayDispList[$i][3] = $line[3];
	    	$arrayDispList[$i][4] = $line[4];
	    	$arrayDispList[$i][5] = $line[5];
	    	$arrayDispList[$i][6] = $line[6];
	    	$arrayDispList[$i][7] = $line[7];
	    	$arrayDispList[$i][8] = $line[8];
	    	$arrayDispList[$i][9] = $line[9];
	    	$arrayDispList[$i][10] = $line[10];
	    	$arrayDispList[$i][11] = $line[11];
	    	$arrayDispList[$i][12] = $line[12];
	    	$arrayDispList[$i][13] = $line[13];
	    	$arrayDispList[$i][14] = $line[14];
	    	$arrayDispList[$i][15] = $line[15];
	    	$arrayDispList[$i][16] = $line[16];
	    	$arrayDispList[$i][17] = $line[17];
	    	$arrayDispList[$i][18] = $line[18];
	    	$arrayDispList[$i][19] = $line[19];
	    	$arrayDispList[$i][20] = $line[20];
	    	$arrayDispList[$i][21] = $line[21];
	    	
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

		$sqlQString = $this->sql_builder->deleteRecord($arrList);

		$message2 = $this->dbConnection -> executeQuery($sqlQString); //Calling the addData() function

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

	function getUsers() {
		
		$arrFieldList[0] = 'id';
		$arrFieldList[1] = 'user_name';
	
		
		$this->sql_builder->table_name = $this->tableName;
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
	
}
?>