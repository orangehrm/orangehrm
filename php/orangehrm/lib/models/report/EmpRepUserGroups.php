<?php
class EmpRepUserGroup {

	var $usgGrpCode;
	var $repCode;
	
	function EmpRepUserGroup() {
		
	}

	function setUserGroupID($usgGrpCode) {
		$this->usgGrpCode = $usgGrpCode;
	}
	
	function setRepCode($repCode) {
		$this->repCode = $repCode;
	}
	
	function getUserGroupID() {
		return $this->usgGrpCode;
	}
	
	function getRepCode() {
		return $this->repCode;
	}
	
	function delRepUserGroup($arrList) {

		$tableName = 'HS_HR_EMPREP_USERGROUP';
		$arrFieldList1[0] = 'USERG_ID';
		$arrFieldList1[1] = 'REP_CODE';

		$sql_builder1 = new SQLQBuilder();

		$sql_builder1->table_name = $tableName;
		$sql_builder1->flg_delete = 'true';
		$sql_builder1->arr_delete = $arrFieldList1;

		$sqlDelString = $sql_builder1->deleteRecord($arrList);

		//echo $sqlQString;
		$dbConnection1 = new DMLFunctions();
		$message2 = $dbConnection1 -> executeQuery($sqlDelString); //Calling the addData() function
        }
	

	function addRepUserGroup() {

		$arrFieldList[0] = "'". $this->getUserGroupID() . "'";
		$arrFieldList[1] = "'". $this->getRepCode() . "'";

		$tableName = 'HS_HR_EMPREP_USERGROUP';
	
		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_insert = 'true';
		$sql_builder->arr_insert = $arrFieldList;
			
	
		$sqlQString = $sql_builder->addNewRecordFeature1();
	
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		 return $message2;
		 echo $message2;
				
	}

function getAllUserGroups() {
		
		$arrFieldList[0] = 'userg_id';
		$arrFieldList[1] = 'userg_name';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = 'hs_hr_user_group';
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;
		
		$sqlQString =$sql_builder->passResultSetMessage();
		
		$dbConnection = new DMLFunctions();

		$message2 = $dbConnection -> executeQuery($sqlQString);
		
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
	
	function getAssignedUserGroup($getID) {
		
		$tableName = 'HS_HR_EMPREP_USERGROUP';
		$arrFieldList[0] = 'REP_CODE';
		$arrFieldList[1] = 'USERG_ID';
		
		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->selectOneRecordFiltered($getID);
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
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

	function getUnAssUserGroups($id) {

		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_USER_GROUP';
		$arrFieldList[0] = 'USERG_ID';
		$arrFieldList[1] = 'USERG_NAME';

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;
		$sql_builder->field='USERG_ID';
		$sql_builder->table2_name= 'HS_HR_EMPREP_USERGROUP';
		$arr[0][0]='REP_CODE';
		$arr[0][1]=$id;

		$sqlQString = $sql_builder->selectFilter($arr);

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

	function getReportInfo() {
		
		$tableName = 'HS_HR_EMPREPORT';			
		$arrFieldList[0] = 'REP_CODE';
		$arrFieldList[1] = 'REP_NAME';
		
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