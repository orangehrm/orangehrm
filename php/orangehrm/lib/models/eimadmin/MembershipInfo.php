<?php
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

require_once ROOT_PATH . '/lib/confs/Conf.php';
require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';
require_once ROOT_PATH . '/lib/dao/SQLQBuilder.php';
require_once ROOT_PATH . '/lib/common/CommonFunctions.php';

class MembershipInfo {

	var $tableName;
	var $membershipId;
	var $membershipDesc;
	var $membershipTypeId;
	
	var $arrayDispList;
	var $singleField;
	
	
	function MembershipInfo() {
		
	}
	
	function setMembershipInfoId($membershipId) {
	
		$this->membershipId = $membershipId;
	
	}
	
	function setMembershipInfoDesc($membershipDesc) {
	
		$this->membershipDesc = $membershipDesc;

	}
	
	function setMembershipTypeId($membershipTypeId) {
	
		$this->membershipTypeId = $membershipTypeId;

	}
	
		
	function getMembershipInfoId() {
	
		return $this->membershipId;
	
	}
	
	function getMembershipInfoDesc() {
	
		return $this->membershipDesc;
		
	}
	
	function getMembershipTypeId() {
	
		return $this->membershipTypeId;
		//echo $this->membershipTypeId;
		
	}
	
	
	function getListofMembershipInfo($pageNO,$schStr,$mode, $sortField = 0, $sortOrder = 'ASC') {
		
		$tableName = 'HS_HR_MEMBERSHIP';
		$tableName2 = 'HS_HR_MEMBERSHIP_TYPE';
				
		$arrFieldList[0] = 'MEMBSHIP_CODE';
		$arrFieldList[1] = 'MEMBSHIP_NAME';
		
		$arrFieldList2[0] = 'MEMBTYPE_NAME';
		
		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = $tableName2;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;	
		$sql_builder->arr_select2 = $arrFieldList2;	
		$sql_builder->field	= 'MEMBTYPE_CODE';
			
		$sqlQString = $sql_builder->passResultSetMessageMulti($pageNO,$schStr,$mode, $sortField, $sortOrder);
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
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
	
	function countMembershipInfo($schStr,$mode) {
		
		$tableName = 'HS_HR_MEMBERSHIP a LEFT JOIN HS_HR_MEMBERSHIP_TYPE b ON (a.membtype_code = b.membtype_code)';			
				
		$arrFieldList[0] = 'a.MEMBSHIP_CODE';
		$arrFieldList[1] = 'a.MEMBSHIP_NAME';
		
		$arrFieldList[2] = 'b.MEMBTYPE_NAME';
		
		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->countResultset($schStr,$mode);
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$line = mysql_fetch_array($message2, MYSQL_NUM);
		 	
	    	return $line[0];
	}

	function delMembershipInfo($arrList) {

		$tableName = 'HS_HR_MEMBERSHIP';
		$arrFieldList[0] = 'MEMBSHIP_CODE';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_delete = 'true';
		$sql_builder->arr_delete = $arrFieldList;

		$sqlQString = $sql_builder->deleteRecord($arrList);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

	}

	function addMembershipInfo() {
		
		$this->getMembershipInfoId();
		$arrFieldList[0] = "'". $this->getMembershipInfoId() . "'";
		$arrFieldList[1] = "'". $this->getMembershipTypeId() . "'";
		$arrFieldList[2] = "'". $this->getMembershipInfoDesc() . "'";

		$tableName = 'HS_HR_MEMBERSHIP';			
	
		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_insert = 'true';
		$sql_builder->arr_insert = $arrFieldList;		
		
		$sqlQString = $sql_builder->addNewRecordFeature1();
			
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		 return $message2;
					
	}
	
	function updateMembershipInfo() {
		
		$this->getMembershipInfoId();
		$arrRecordsList[0] = "'". $this->getMembershipInfoId() . "'";
		$arrRecordsList[1] = "'". $this->getMembershipInfoDesc() . "'";
		$arrRecordsList[2] = "'". $this->getMembershipTypeId() . "'";
		$arrFieldList[0] = 'MEMBSHIP_CODE';
		$arrFieldList[1] = 'MEMBSHIP_NAME';
		$arrFieldList[2] = 'MEMBTYPE_CODE';
		
		$tableName = 'HS_HR_MEMBERSHIP';			
	
		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_update = 'true';
		$sql_builder->arr_update = $arrFieldList;	
		$sql_builder->arr_updateRecList = $arrRecordsList;	
	
		$sqlQString = $sql_builder->addUpdateRecord1();
	
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		return $message2;
		 
				
	}
	
	
	function filterMembershipInfo($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_MEMBERSHIP';			
		$arrFieldList[0] = 'MEMBSHIP_CODE';
		$arrFieldList[1] = 'MEMBSHIP_NAME';
		$arrFieldList[2] = 'MEMBTYPE_CODE';
		
		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->selectOneRecordFiltered($this->getID);
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$i=0;
		
		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {
		 	
	    	$arrayDispList[$i][0] = $line[0]; // Province Code
	    	$arrayDispList[$i][1] = $line[1]; // Provicne Name
	    	$arrayDispList[$i][2] = $line[2]; // Country ID
	    	$i++;
	    	
	     }
	     
	     if (isset($arrayDispList)) {
	     
			return $arrayDispList;
			
		} else {
		
			$arrayDispList = '';
			return $arrayDispList;
			
		}
				
	}

	
	function getLastRecord() {
		
		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_MEMBERSHIP';		
		$arrFieldList[0] = 'MEMBSHIP_CODE';
				
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
			
		return $common_func->explodeString($this->singleField,"MME"); 
				
		}
		
	}

	function getAllMembershipCodes() {

		$tableName = 'HS_HR_MEMBERSHIP';
		$arrFieldList[0] = 'MEMBTYPE_CODE';
		$arrFieldList[1] = 'MEMBSHIP_CODE';
		$arrFieldList[2] = 'MEMBSHIP_NAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultSetMessage();

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

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

	function getMembershipCodes($getID) {

		$this->getID = $getID;
		$tableName = 'HS_HR_MEMBERSHIP';
		$arrFieldList[0] = 'MEMBTYPE_CODE';
		$arrFieldList[1] = 'MEMBSHIP_CODE';
		$arrFieldList[2] = 'MEMBSHIP_NAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->selectOneRecordFiltered($this->getID);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$i=0;

		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {
	    		for($c=0; count($arrFieldList) > $c ; $c++)
					$arrayDispList[$i][$c] = $line[$c];

	    		$i++;
	     }

	     if (isset($arrayDispList)) {

			return $arrayDispList;

		} else {

			$arrayDispList = '';
			return $arrayDispList;

		}
	}

	function getUnAssMembershipCodes($eno,$typ) {

		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_MEMBERSHIP';
		$arrFieldList[0] = 'MEMBSHIP_CODE';
		$arrFieldList[1] = 'MEMBSHIP_NAME';
		$arrFieldList[2] = 'MEMBTYPE_CODE';

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;
		$sql_builder->field='MEMBSHIP_CODE';
		$sql_builder->table2_name= 'HS_HR_EMP_MEMBER_DETAIL';
		$arr1[0][0]='EMP_NUMBER';
		$arr1[0][1]=$eno;
		$arr2[0][0]='MEMBTYPE_CODE';
		$arr2[0][1]=$typ;

		$sqlQString = $sql_builder->selectFilter($arr1,$arr2);

		$dbConnection = new DMLFunctions();
       		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$common_func = new CommonFunctions();

		$i=0;

		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {

	    	$arrayDispList[$i][0] = $line[0];
	    	$arrayDispList[$i][1] = $line[1];

	    	$i++;
	     }

	     if (isset($arrayDispList)) {

	       	return $arrayDispList;

	     } else {
	     	//Handle Exceptions
	     	//Create Logs
	     }
	}
	
	
}

?>
