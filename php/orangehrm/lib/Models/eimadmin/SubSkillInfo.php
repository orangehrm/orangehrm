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

class SubSkillInfo {

	var $tableName;
	var $subskillId;
	var $subskillName;
	var $subskillDesc;
	var $skillId;
	
	var $arrayDispList;
	var $singleField;
	
	
	function SubSkillInfo() {
		
	}
//////////	
	function setSubSkillInfoId($subskillId) {
	
		$this->subskillId = $subskillId;
	
	}
	
	function setSubSkillInfoDesc($subskillDesc) {
	
		$this->subskillDesc = $subskillDesc;

	}
	
	function setSubSkillInfoName($subskillName) {
	
		$this->subskillName = $subskillName;

	}
	
	function setSkillId ($skillId) {
	
		$this->skillId = $skillId;

	}

//////////		
	function getSubSkillInfoId() {
	
		return $this->subskillId;
	
	}
	
	function getSubSkillInfoName() {
	
		return $this->subskillName;
	
	}
	
	function getSkillId () {
	
		return $this->skillId;
	
	}

	function getSubSkillInfoDesc() {
	
		return $this->subskillDesc;
		
	}
	
///////	
	function getListofSubSkillInfo($pageNO,$schStr,$mode) {
		
		$tableName = 'HS_HR_SUBSKILL';			
		$arrFieldList[0] = 'SUBSKILL_CODE';
		$arrFieldList[1] = 'SUBSKILL_NAME';
		
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

	function countSubSkillInfo($schStr,$mode) {
		
		$tableName = 'HS_HR_SUBSKILL';			
		$arrFieldList[0] = 'SUBSKILL_CODE';
		$arrFieldList[1] = 'SUBSKILL_NAME';
		
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

	function delSubSkillInfo($arrList) {

		$tableName = 'HS_HR_SUBSKILL';
		$arrFieldList[0] = 'SUBSKILL_CODE';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_delete = 'true';
		$sql_builder->arr_delete = $arrFieldList;

		$sqlQString = $sql_builder->deleteRecord($arrList);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

	}

	
	function addSubSkillInfo() {
		
		$this->getSubSkillInfoId();
		$arrFieldList[0] = "'". $this->getSubSkillInfoId() . "'";
		$arrFieldList[1] = "'". $this->getSubSkillInfoName() . "'";
		$arrFieldList[2] = "'". $this->getSubSkillInfoDesc() . "'";
		$arrFieldList[3] = "'". $this->getSkillId() . "'";
	
		$tableName = 'HS_HR_SUBSKILL';			
	
		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_insert = 'true';
		$sql_builder->arr_insert = $arrFieldList;		
		
		$sqlQString = $sql_builder->addNewRecordFeature1();
			
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		 return $message2;
					
	}
	
	function updateSubSkillInfo() {
		
		$this->getSubSkillInfoId();
		$arrRecordsList[0] = "'". $this->getSubSkillInfoId() . "'";
		$arrRecordsList[1] = "'". $this->getSubSkillInfoName() . "'";
		$arrRecordsList[2] = "'". $this->getSubSkillInfoDesc() . "'";
		$arrRecordsList[3] = "'". $this->getSkillId() . "'";
		
		$arrFieldList[0] = 'SUBSKILL_CODE';
		$arrFieldList[1] = 'SUBSKILL_NAME';
		$arrFieldList[2] = 'SUBSKILL_DESCRIPTION';
		$arrFieldList[3] = 'SKILL_CODE';
		
		$tableName = 'HS_HR_SUBSKILL';			
	
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
	
	
	function filterSubSkillInfo($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_SUBSKILL';			
		$arrFieldList[0] = 'SUBSKILL_CODE';
		$arrFieldList[1] = 'SUBSKILL_NAME';
		$arrFieldList[2] = 'SUBSKILL_DESCRIPTION';
		$arrFieldList[3] = 'SKILL_CODE';
		
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
		 	
	    	$arrayDispList[$i][0] = $line[0]; // Sub Skill Code
	    	$arrayDispList[$i][1] = $line[1]; // Sub Skill Name
	    	$arrayDispList[$i][2] = $line[2]; // Sub Skill Desc
	    	$arrayDispList[$i][3] = $line[3]; // Skill ID
	    	$i++;
	    	
	     }
	     
	     if (isset($arrayDispList)) {
	     
			return $arrayDispList;
			
		} else {
		
			$arrayDispList = '';
			return $arrayDispList;
			
		}
				
	}
	
function filterGetSkillInfo($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_SKILL';			
		$arrFieldList[0] = 'SKILL_CODE';
		$arrFieldList[1] = 'SKILL_NAME';
	
		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->selectOneRecordFiltered($this->getID);
		
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
	
	

function filterNotEqualSubSkillInfo($getID) {
	
		$this->getID = $getID;
		
		$tableName = 'HS_HR_SKILL';			
		$arrFieldList[0] = 'SKILL_CODE';
		$arrFieldList[1] = 'SKILL_NAME';
		
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
	function getSkillCodes () {
	
		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_SKILL';		
		$arrFieldList[0] = 'SKILL_CODE';
		$arrFieldList[1] = 'SKILL_NAME';
				
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
		$tableName = 'HS_HR_SUBSKILL';		
		$arrFieldList[0] = 'SUBSKILL_CODE';
				
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
			
		return $common_func->explodeString($this->singleField,"SSK"); 
				
		}
		
	}	
	
	
}

?>
