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

class SubjectInfo {

	var $tableName;
	var $subjectId;
	var $subjectDesc;
	var $qualfiId;
	
	var $arrayDispList;
	var $singleField;
	
	
	function SubjectInfo() {
		
	}
	
	function setSubjectInfoId($subjectId) {
	
		$this->subjectId = $subjectId;
	
	}
	
	function setSubjectInfoDesc($subjectDesc) {
	
		$this->subjectDesc = $subjectDesc;

	}
	
	function setQualifiId($qualfiId) {
	
		$this->qualfiId = $qualfiId;

	}
	
		
	function getSubjectInfoId() {
	
		return $this->subjectId;
	
	}
	
	function getSubjectInfoDesc() {
	
		return $this->subjectDesc;
		
	}
	
	function getQualifiId() {
	
		return $this->qualfiId;
		//echo $this->qualfiId;
		
	}
	
	
	function getListofSubjectInfo($pageNO,$schStr,$mode) {
		
		$tableName = 'HS_HR_SUBJECT';			
		$arrFieldList[0] = 'SBJ_CODE';
		$arrFieldList[1] = 'SBJ_NAME';
		
		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->passResultSetMessage($pageNO,$schStr,$mode);
		
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

	function countSubjectInfo($schStr,$mode) {
		
		$tableName = 'HS_HR_SUBJECT';			
		$arrFieldList[0] = 'SBJ_CODE';
		$arrFieldList[1] = 'SBJ_NAME';
		
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
	
	function delSubjectInfo($arrList) {

    	$tableName = 'HS_HR_SUBJECT';
		$arrFieldList[0] = 'SBJ_CODE';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_delete = 'true';
		$sql_builder->arr_delete = $arrFieldList;

		$sqlQString = $sql_builder->deleteRecord($arrList);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

	}

	
	function addSubjectInfo() {
		
		$this->getSubjectInfoId();
		$arrFieldList[0] = "'". $this->getSubjectInfoId() . "'";
		$arrFieldList[1] = "'". $this->getSubjectInfoDesc() . "'";
		$arrFieldList[2] = "'". $this->getQualifiId() . "'";
	
		$tableName = 'HS_HR_SUBJECT';			
	
		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_insert = 'true';
		$sql_builder->arr_insert = $arrFieldList;		
		
		$sqlQString = $sql_builder->addNewRecordFeature1();
		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		 return $message2;
					
	}
	
	function updateSubjectInfo() {
		
		$this->getSubjectInfoId();
		$arrRecordsList[0] = "'". $this->getSubjectInfoId() . "'";
		$arrRecordsList[1] = "'". $this->getSubjectInfoDesc() . "'";
		$arrRecordsList[2] = "'". $this->getQualifiId() . "'";
		$arrFieldList[0] = 'SBJ_CODE';
		$arrFieldList[1] = 'SBJ_NAME';
		$arrFieldList[2] = 'QUALIFI_CODE';
		
		$tableName = 'HS_HR_SUBJECT';			
	
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
	
	
	function filterSubjectInfo($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_SUBJECT';			
		$arrFieldList[0] = 'SBJ_CODE';
		$arrFieldList[1] = 'SBJ_NAME';
		$arrFieldList[2] = 'QUALIFI_CODE';
		
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
	
function filterGetQualifiInfo($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_QUALIFICATION';			
		$arrFieldList[0] = 'QUALIFI_CODE';
		$arrFieldList[1] = 'QUALIFI_NAME';
	
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
	    	
	    	$i++;
	    	
	     }
	     
	     if (isset($arrayDispList)) {
	     
			return $arrayDispList;
			
		} else {
		
			$arrayDispList = '';
			return $arrayDispList;
			
		}
				
	}	
	
	

function filterNotEqualQualifiInfo($getID) {
	
		$this->getID = $getID;
		
		$tableName = 'HS_HR_QUALIFICATION';			
		$arrFieldList[0] = 'QUALIFI_CODE';
		$arrFieldList[1] = 'QUALIFI_NAME';
		
		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->filterNotEqualRecordSet($this->getID);
				
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$i=0;
		
		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {
		 	
	    	$arrayDispList[$i][0] = $line[0]; // Province Code
	    	$arrayDispList[$i][1] = $line[1]; // Provicne Name
	    	
	    	$i++;
	    	
	     }
	     
	     if (isset($arrayDispList)) {
	     
			return $arrayDispList;
			
		} else {
		
			$arrayDispList = '';
			return $arrayDispList;
			
		}
				
	}
	
/////////////////filterNotEqualRecordSet($filterID)
	function getQualifiCodes () {
	
		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_QUALIFICATION';		
		$arrFieldList[0] = 'QUALIFI_CODE';
		$arrFieldList[1] = 'QUALIFI_NAME';
				
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
	
		$sqlQString = $sql_builder->passResultSetMessage();
	
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$common_func = new CommonFunctions();
		
		$i=0;
		
		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {
		 	
	    	$arrayDispList[$i][0] = $line[0];
	    	$arrayDispList[$i][1] = $line[1];
	    	//$arrayDispList[$i][2] = $line[2];
	    	
	    	$i++;
	    	
	     }
	     
	     if (isset($arrayDispList)) {
	     
	       	return $arrayDispList;
	     
	     } else {
	     	
	     	//Handle Exceptions
	     	//Create Logs
	     	
	     }
	
	}
	
	
	
	function getLastRecord() {
		
		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_SUBJECT';		
		$arrFieldList[0] = 'SBJ_CODE';
				
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
			
		return $common_func->explodeString($this->singleField,"SBJ"); 
				
		}
		
	}	
	
	
}

?>
