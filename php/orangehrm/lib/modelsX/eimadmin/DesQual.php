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

require_once ROOT_PATH  . '/lib/confs/Conf.php';
require_once ROOT_PATH  . '/lib/dao/DMLFunctions.php';
require_once ROOT_PATH  . '/lib/dao/SQLQBuilder.php';
require_once ROOT_PATH  . '/lib/common/CommonFunctions.php';

class DesQualification {

	var $tableName = 'HS_HR_JD_QUALIFICATION';
	
	var $desId;
	var $jdQualId;
	var $jdQualInst;
	var $jdQualStat;
	var $arrayDispList;
	var $singleField;
	
	
	function DesQualification() {
		
	}
	
	function setDesId($desId) {
	
		$this->desId = $desId;
	
	}
	
	function setJDQualId($jdQualId) {
	
		$this->jdQualId = $jdQualId;
	}
		
	function setJDQualInst($jdQualInst) {

		$this->jdQualInst = $jdQualInst;
	}

	function setJDQualStat($jdQualStat) {

		$this->jdQualStat = $jdQualStat;
	}

	function getDesId() {

		return $this->desId;

	}

	function getJDQualId() {

		return $this->jdQualId;
	}

	function getJDQualInst() {

		return $this->jdQualInst;
	}

	function getJDQualStat() {

		return $this->jdQualStat;
	}

//////
	function delJDQual($arrList) {

		$tableName = 'HS_HR_JD_QUALIFICATION';
		$arrFieldList[0] = 'DSG_CODE';
		$arrFieldList[1] = 'QUALIFI_CODE';

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
	function addJDQual() {
		
		//$this->getLocationId();
		$arrFieldList[0] = "'". $this->getDesId() . "'";
		$arrFieldList[1] = "'". $this->getJDQualId() . "'";
		$arrFieldList[2] = "'". $this->getJDQualInst() . "'";
		$arrFieldList[3] = "'". $this->getJDQualStat() . "'";

		//$arrFieldList[0] = 'LOC_CODE';
		//$arrFieldList[1] = 'LOC_NAME';
		
		$tableName = 'HS_HR_JD_QUALIFICATION';

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
	
	function updateJDQual() {
		

		$arrRecordsList[0] = "'". $this->getDesId() . "'";
		$arrRecordsList[1] = "'". $this->getJDQualId() . "'";
		$arrRecordsList[2] = "'". $this->getJDQualInst() . "'";
		$arrRecordsList[3] = "'". $this->getJDQualStat() . "'";
		$tableName = 'HS_HR_JD_QUALIFICATION';
		$arrFieldList[0] = 'DSG_CODE';
		$arrFieldList[1] = 'QUALIFI_CODE';
		$arrFieldList[2] = 'JDQUALIFI_INSTITUTE';
		$arrFieldList[3] = 'JDQUALIFI_STATUS';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_update = 'true';
		$sql_builder->arr_update = $arrFieldList;	
		$sql_builder->arr_updateRecList = $arrRecordsList;	
	
		$sqlQString = $sql_builder->addUpdateRecord1(1);
	
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		return $message2;
		 
				
	}
	
	
	function filterJDQual($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_JD_QUALIFICATION';
		$arrFieldList[0] = 'DSG_CODE';
		$arrFieldList[1] = 'QUALIFI_CODE';
		$arrFieldList[2] = 'JDQUALIFI_INSTITUTE';
		$arrFieldList[3] = 'JDQUALIFI_STATUS';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->selectOneRecordFiltered($this->getID,1);
		
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

	function getAssJDQual($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_JD_QUALIFICATION';
		$arrFieldList[0] = 'DSG_CODE';
		$arrFieldList[1] = 'QUALIFI_CODE';
		$arrFieldList[2] = 'JDQUALIFI_INSTITUTE';
		$arrFieldList[3] = 'JDQUALIFI_STATUS';

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
	
	function getQual($id) {

		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_QUALIFICATION';
		$arrFieldList[0] = 'QUALIFI_CODE';
		$arrFieldList[1] = 'QUALIFI_NAME';

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;
		$sql_builder->field='QUALIFI_CODE';
		$sql_builder->table2_name= 'HS_HR_JD_QUALIFICATION';
		$arr[0][0]='DSG_CODE';
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

	function getAllQualifications() {
		
		$tableName = 'HS_HR_QUALIFICATION';
		$arrFieldList[0] = 'QUALIFI_CODE';
		$arrFieldList[1] = 'QUALIFI_NAME';
		
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
