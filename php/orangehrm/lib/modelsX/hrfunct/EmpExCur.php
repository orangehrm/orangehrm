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

require_once ROOT_PATH . '/lib/confs/Conf.php';
require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';
require_once ROOT_PATH . '/lib/dao/SQLQBuilder.php';
require_once ROOT_PATH . '/lib/common/CommonFunctions.php';

class EmpExCur {

	var $tableName = 'HS_HR_EMP_EXTRA_ACTIVITY';
	
	var $empId;
	var $empECActSeqNo;
	var $empECCatCode;
	var $empECTypeCode;
	var $empECAchmnt;
	var $arrayDispList;
	var $singleField;
	
	function EmpExCur() {
		
	}
	
	function setEmpId($empId) {
	
	$this->empId=$empId;
	}
	
	function setEmpECActSeqNo($empECActSeqNo) {
	
	$this->empECActSeqNo=$empECActSeqNo;
	}
	
	function setEmpECCatCode($empECCatCode) {
	
	$this->empECCatCode=$empECCatCode;
	}
	
	function setEmpECTypeCode($empECTypeCode) {
	
	$this->empECTypeCode=$empECTypeCode;
	}
	
	function setEmpECAchmnt($empECAchmnt) {
	
	$this->empECAchmnt=$empECAchmnt;
	}
	
	function getEmpId() {
	
	return $this->empId;
	}
	
	function getEmpECActSeqNo() {
	
	return $this->empECActSeqNo;
	}
	
	function getEmpECCatCode() {
	
	return $this->empECCatCode;
	}
	
	function getEmpECTypeCode() {
	
	return $this->empECTypeCode;
	}
	
	function getEmpECAchmnt() {
	
	return $this->empECAchmnt;
	}
////
	

	

	function getListofEmpExCur($page,$str,$mode) {
		
		$tableName = 'HS_HR_EMP_EXTRA_ACTIVITY';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->field = 'EMP_NUMBER';
			
		$sqlQString = $sql_builder->selectEmployee($page,$str,$mode);
		
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

	function countEmpExCur($str,$mode) {
		
		$tableName = 'HS_HR_EMP_EXTRA_ACTIVITY';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->field = 'EMP_NUMBER';
			
		$sqlQString = $sql_builder->countEmployee($str,$mode);
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$line = mysql_fetch_array($message2, MYSQL_NUM);
		 	
	    	return $line[0];
	}

	function getAssExCur($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_EMP_EXTRA_ACTIVITY';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EEXTACT_SEQNO';
		$arrFieldList[2] = 'EACAT_CODE';
		$arrFieldList[3] = 'EATYPE_CODE';
		$arrFieldList[4] = 'EEXTACT_ACHIEVEMENT';

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
		 	
			for($c=0;count($arrFieldList)>$c;$c++)
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

	function delEmpExCur($arrList) {

		$tableName = 'HS_HR_EMP_EXTRA_ACTIVITY';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EEXTACT_SEQNO';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_delete = 'true';
		$sql_builder->arr_delete = $arrFieldList;

		$sqlQString = $sql_builder->deleteRecord($arrList);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

	}

	function addEmpExCur() {
		
		$this->getEmpId();
		$arrFieldList[0] = "'". $this->getEmpId() . "'";
		$arrFieldList[1] = "'". $this->getEmpECActSeqNo() . "'";
		$arrFieldList[2] = "'". $this->getEmpECCatCode() . "'";
		$arrFieldList[3] = "'". $this->getEmpECTypeCode() . "'";
		$arrFieldList[4] = "'". $this->getEmpECAchmnt() . "'";

		$tableName = 'HS_HR_EMP_EXTRA_ACTIVITY';
	
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
	
	function updateEmpExCur() {
		
		$arrRecordsList[0] = "'". $this->getEmpId() . "'";
		$arrRecordsList[1] = "'". $this->getEmpECActSeqNo() . "'";
		$arrRecordsList[2] = "'". $this->getEmpECCatCode() . "'";
		$arrRecordsList[3] = "'". $this->getEmpECTypeCode() . "'";
		$arrRecordsList[4] = "'". $this->getEmpECAchmnt() . "'";

		$tableName = 'HS_HR_EMP_EXTRA_ACTIVITY';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EEXTACT_SEQNO';
		$arrFieldList[2] = 'EACAT_CODE';
		$arrFieldList[3] = 'EATYPE_CODE';
		$arrFieldList[4] = 'EEXTACT_ACHIEVEMENT';

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
	
	
	function filterEmpExCur($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_EMP_EXTRA_ACTIVITY';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EEXTACT_SEQNO';
		$arrFieldList[2] = 'EACAT_CODE';
		$arrFieldList[3] = 'EATYPE_CODE';
		$arrFieldList[4] = 'EEXTACT_ACHIEVEMENT';

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
		 	
			for($c=0;count($arrFieldList)>$c;$c++)
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

	function getLastRecord($str) {
		
		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_EMP_EXTRA_ACTIVITY';
		$arrFieldList[0] = 'EEXTACT_SEQNO';
		$arrFieldList[1] = 'EMP_NUMBER';

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
		
		$arrSel[0]=$str;
		$sqlQString = $sql_builder->selectOneRecordOnly(1,$arrSel);
	
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
			
		$lastrec=((int)$this->singleField)+1;
		return $lastrec;
				
		}
		
	}	
	

	
	
}

?>
