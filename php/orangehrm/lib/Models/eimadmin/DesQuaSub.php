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
require_once OpenSourceEIM . '/lib/Exceptionhandling/ExceptionHandler.php';

class DesQualSubject {

	var $tableName = 'HS_HR_JD_SUBJECT';
	
	var $desId;
	var $subId;
	var $qualId;
	var $ratGrd;
	var $arrayDispList;
	var $singleField;
	
	
	function DesDescription() {
		
	}
	
	function setDesId($desId) {
	
		$this->desId = $desId;
	}
	
	function setSubId($subId) {
	
		$this->subId = $subId;
	}
		
	function setQualId($qualId) {

		$this->qualId = $qualId;
	}

	function setRatGrd($ratGrd) {

		$this->ratGrd = $ratGrd;
	}

	function getDesId() {

		return $this->desId;

	}

	function getSubId() {

		return $this->subId;
	}

	function getQualId() {

		return $this->qualId;
	}

	function getRatGrd() {

		return $this->ratGrd;
	}

function getAssQuaSub($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_JD_SUBJECT';
		$arrFieldList[0] = 'DSG_CODE';
		$arrFieldList[1] = 'SBJ_CODE';
		$arrFieldList[2] = 'QUALIFI_CODE';
		$arrFieldList[3] = 'RATING_GRADE_CODE';

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
		 	
	    	$arrayDispList[$i][0] = $line[0];
	    	$arrayDispList[$i][1] = $line[1];
	    	$arrayDispList[$i][2] = $line[2];
	    	$arrayDispList[$i][3] = $line[3];
	    	$i++;
	    	
	     }
	     
	     if (isset($arrayDispList)) {
	     
			return $arrayDispList;
			
		} else {
		
			$arrayDispList = '';
			return $arrayDispList;
			
		}
				
	}
	
	function getListofQuaSub($schStr,$mode) {
		
		$tableName = 'HS_HR_JD_SUBJECT';
		$arrFieldList[0] = 'DSG_CODE';
		$arrFieldList[1] = 'SBJ_CODE';
		$arrFieldList[2] = 'QUALIFI_CODE';
		$arrFieldList[3] = 'RATING_GRADE_CODE';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->passResultSetMessage($schStr,$mode);
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$i=0;
		
		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {
		 	
	    	$arrayDispList[$i][0] = $line[0];
	    	$arrayDispList[$i][1] = $line[1];
	    	$arrayDispList[$i][2] = $line[2];
	    	$arrayDispList[$i][3] = $line[3];
	    	$i++;
	    	
	     }
	     
	     if (isset($arrayDispList)) {
	     
			return $arrayDispList;
			
		} else {
		
			$arrayDispList = '';
			return $arrayDispList;
			
		}
	}
	
//////
	function delQuaSub($arrList) {

		$tableName = 'HS_HR_JD_SUBJECT';
		$arrFieldList[0] = 'DSG_CODE';
		$arrFieldList[1] = 'SBJ_CODE';
		$arrFieldList[2] = 'QUALIFI_CODE';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_delete = 'true';
		$sql_builder->arr_delete = $arrFieldList;

		$sqlQString = $sql_builder->deleteRecord($arrList);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

	}

///
	function addQuaSub() {
		
		//$this->getLocationId();
		$arrFieldList[0] = "'". $this->getDesId() . "'";
		$arrFieldList[1] = "'". $this->getSubId() . "'";
		$arrFieldList[2] = "'". $this->getQualId() . "'";
		$arrFieldList[3] = "' '";
		$arrFieldList[4] = "'". $this->getRatGrd() . "'";

		//$arrFieldList[0] = 'LOC_CODE';
		//$arrFieldList[1] = 'LOC_NAME';
		
		$tableName = 'HS_HR_JD_SUBJECT';

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
	
	function updateQuaSub() {
		
		$arrRecordsList[0] = "'". $this->getDesId() . "'";
		$arrRecordsList[1] = "'". $this->getSubId() . "'";
		$arrRecordsList[2] = "'". $this->getQualId() . "'";
		$arrRecordsList[3] = "'". $this->getRatGrd() . "'";
		$tableName = 'HS_HR_JD_SUBJECT';
		$arrFieldList[0] = 'DSG_CODE';
		$arrFieldList[1] = 'SBJ_CODE';
		$arrFieldList[2] = 'QUALIFI_CODE';
		$arrFieldList[3] = 'RATING_GRADE_CODE';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_update = 'true';
		$sql_builder->arr_update = $arrFieldList;	
		$sql_builder->arr_updateRecList = $arrRecordsList;	
	
		$sqlQString = $sql_builder->addUpdateRecord1(2);
	
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		return $message2;
		 
				
	}
	
	
	function filterQuaSub($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_JD_SUBJECT';
		$arrFieldList[0] = 'DSG_CODE';
		$arrFieldList[1] = 'SBJ_CODE';
		$arrFieldList[2] = 'QUALIFI_CODE';
		$arrFieldList[3] = 'RATING_GRADE_CODE';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->selectOneRecordFiltered($this->getID,2);
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$i=0;
		
		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {
		 	
	    	$arrayDispList[$i][0] = $line[0];
	    	$arrayDispList[$i][1] = $line[1];
	    	$arrayDispList[$i][2] = $line[2];
	    	$arrayDispList[$i][3] = $line[3];
	    	$i++;
	    	
	     }
	     
	     if (isset($arrayDispList)) {
	     
			return $arrayDispList;
			
		} else {
		
			$arrayDispList = '';
			return $arrayDispList;
			
		}
				
	}

	function getSubjects($dsg,$qua) {

		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_SUBJECT';			
		$arrFieldList[0] = 'SBJ_CODE';
		$arrFieldList[1] = 'SBJ_NAME';

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;
		$sql_builder->field='SBJ_CODE';
		$sql_builder->table2_name= 'HS_HR_JD_SUBJECT';
		$arr[0][0]='DSG_CODE';
		$arr[0][1]=$dsg;
		$arr[1][0]='QUALIFI_CODE';
		$arr[1][1]=$qua;
		
		$arr2[0][0]='QUALIFI_CODE';
		$arr2[0][1]=$qua;

		$sqlQString = $sql_builder->selectFilter($arr,$arr2);

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

	function getRatGrds($qua) {

        $sqlQString="SELECT a.RATING_GRADE_CODE, a.RATING_GRADE FROM HS_HR_RATING_METHOD_GRADE a, HS_HR_QUALIFICATION b WHERE a.RATING_CODE = b.RATING_CODE AND b.QUALIFI_CODE = '" .$qua . "'";
		$sqlQString=strtolower($sqlQString);

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
