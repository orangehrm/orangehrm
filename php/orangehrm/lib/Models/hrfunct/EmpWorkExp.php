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

class EmpWorkExp {

	var $tableName = 'HS_HR_EMP_WORK_EXPERIENCE';
	
	var $empId;
	var $empExpSeqNo;
	var $empExpCompany;
	var $empExpDesOnLev;
	var $empExpFromDat;
	var $empExpToDat;
	var $empResLev;
	

	var $arrayDispList;
	var $singleField;
	
	function EmpWorkExp() {
		
	}
	
	function setEmpId($empId) {
	
	$this->empId=$empId;
	}
	
	function setEmpExpSeqNo($empExpSeqNo) {
	
	$this->empExpSeqNo=$empExpSeqNo;
	}
	
	function setEmpExpCompany($empExpCompany) {
	
	$this->empExpCompany=$empExpCompany;
	}
		
	function setEmpExpDesOnLev($empExpDesOnLev) {
	
	$this->empExpDesOnLev=$empExpDesOnLev;
	}
	
	function setEmpExpFromDat($empExpFromDat) {
	
	$this->empExpFromDat=$empExpFromDat;
	}
	
	function setEmpExpToDat($empExpToDat) {
	
	$this->empExpToDat=$empExpToDat;
	}
	
	function setEmpResLev($empResLev) {
	
	$this->empResLev=$empResLev;
	}
	
	function getEmpId() {
	
	return $this->empId;
	}
	
	function getEmpExpSeqNo() {
	
	return $this->empExpSeqNo;
	}
	
	function getEmpExpCompany() {
	
	return $this->empExpCompany;
	}
		
	function getEmpExpDesOnLev() {
	
	return $this->empExpDesOnLev;
	}
	
	function getEmpExpFromDat() {
	
	return $this->empExpFromDat;
	}
	
	function getEmpExpToDat() {
	
	return $this->empExpToDat;
	}
	
	function getEmpResLev() {
	
	return $this->empResLev;
	}
		

function getListofEmpWorkExp($page,$str,$mode) {
		
		$tableName = 'HS_HR_EMP_WORK_EXPERIENCE';
					  
		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->field = 'EMP_NUMBER';
			
		$sqlQString = $sql_builder->selectEmployee($page,$str,$mode);
			
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

	function countEmpWorkExp($str,$mode) {
		
		$tableName = 'HS_HR_EMP_WORK_EXPERIENCE';
					  
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

	function delEmpWorkExp($arrList) {

		$tableName = 'HS_HR_EMP_WORK_EXPERIENCE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EEXP_SEQNO';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_delete = 'true';
		$sql_builder->arr_delete = $arrFieldList;

		$sqlQString = $sql_builder->deleteRecord($arrList);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

	}

	function addEmpWorkExp() {
		
		$this->getEmpId();
		$arrFieldList[0]  = "'". $this->getEmpId() . "'";
		$arrFieldList[1]  = "'". $this->getEmpExpSeqNo() . "'";
		$arrFieldList[2]  = "'". $this->getEmpExpCompany() . "'";
		$arrFieldList[3]  = 'null';
		$arrFieldList[4]  = 'null';
		$arrFieldList[5]  = 'null';
		$arrFieldList[6]  = "'". $this->getEmpExpDesOnLev() . "'";
		$arrFieldList[7]  = 'null';
		$arrFieldList[8]  = "'". $this->getEmpExpFromDat() . "'";
		$arrFieldList[9]  = "'". $this->getEmpExpToDat() . "'";
		$arrFieldList[10] = 'null';
		$arrFieldList[11] = 'null';
		$arrFieldList[12] = "'". $this->getEmpResLev() . "'";
		$arrFieldList[13] = 'null';
		$arrFieldList[14] = 'null';
		$arrFieldList[15] = 'null';
		$arrFieldList[16] = 'null';
		$arrFieldList[17] = 'null';

		$tableName = 'HS_HR_EMP_WORK_EXPERIENCE';
	
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
	
	function updateEmpWorkExp() {
		
		$arrRecordsList[0]  = "'". $this->getEmpId() . "'";
		$arrRecordsList[1]  = "'". $this->getEmpExpSeqNo() . "'";
		$arrRecordsList[2]  = "'". $this->getEmpExpCompany() . "'";
		$arrRecordsList[3]  = 'null';
		$arrRecordsList[4]  = 'null';
		$arrRecordsList[5]  = 'null';
		$arrRecordsList[6]  = "'". $this->getEmpExpDesOnLev() . "'";
		$arrRecordsList[7]  = 'null';
		$arrRecordsList[8]  = "'". $this->getEmpExpFromDat() . "'";
		$arrRecordsList[9]  = "'". $this->getEmpExpToDat() . "'";
		$arrRecordsList[10] = 'null';
		$arrRecordsList[11] = 'null';
		$arrRecordsList[12] = "'". $this->getEmpResLev() . "'";
		$arrRecordsList[13] = 'null';
		$arrRecordsList[14] = 'null';
		$arrRecordsList[15] = 'null';
		$arrRecordsList[16] = 'null';
		$arrRecordsList[17] = 'null';

		$tableName = 'HS_HR_EMP_WORK_EXPERIENCE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EEXP_SEQNO';
		$arrFieldList[2] = 'EEXP_COMPANY';
		$arrFieldList[3] = 'EEXP_ADDRESS1';
		$arrFieldList[4] = 'EEXP_ADDRESS2';
		$arrFieldList[5] = 'EEXP_ADDRESS3';
		$arrFieldList[6] = 'EEXP_DESIG_ON_LEAVE';
		$arrFieldList[7] = 'EEXP_WORK_RELATED_FLG';
		$arrFieldList[8] = 'EEXP_FROM_DATE';
		$arrFieldList[9] = 'EEXP_TO_DATE';
		$arrFieldList[10] = 'EEXP_YEARS';
		$arrFieldList[11]= 'EEXP_MONTHS';
		$arrFieldList[12] = 'EEXP_REASON_FOR_LEAVE';
		$arrFieldList[13] = 'EEXP_CONTACT_PERSON';
		$arrFieldList[14] = 'EEXP_TELEPHONE';
		$arrFieldList[15] = 'EEXP_EMAIL';
		$arrFieldList[16] = 'EEXP_ACCOUNTABILITIES';
		$arrFieldList[17] = 'EEXP_ACHIEVEMENTS';

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
	
	function filterEmpWorkExp($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_EMP_WORK_EXPERIENCE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EEXP_SEQNO';
		$arrFieldList[2] = 'EEXP_COMPANY';
		$arrFieldList[3] = 'EEXP_ADDRESS1';
		$arrFieldList[4] = 'EEXP_ADDRESS2';
		$arrFieldList[5] = 'EEXP_ADDRESS3';
		$arrFieldList[6] = 'EEXP_DESIG_ON_LEAVE';
		$arrFieldList[7] = 'EEXP_WORK_RELATED_FLG';
		$arrFieldList[8] = 'EEXP_FROM_DATE';
		$arrFieldList[9] = 'EEXP_TO_DATE';
		$arrFieldList[10] = 'EEXP_YEARS';
		$arrFieldList[11]= 'EEXP_MONTHS';
		$arrFieldList[12] = 'EEXP_REASON_FOR_LEAVE';
		$arrFieldList[13] = 'EEXP_CONTACT_PERSON';
		$arrFieldList[14] = 'EEXP_TELEPHONE';
		$arrFieldList[15] = 'EEXP_EMAIL';
		$arrFieldList[16] = 'EEXP_ACCOUNTABILITIES';
		$arrFieldList[17] = 'EEXP_ACHIEVEMENTS';

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

	function getAssEmpWorkExp($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_EMP_WORK_EXPERIENCE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EEXP_SEQNO';
		$arrFieldList[2] = 'EEXP_COMPANY';
		$arrFieldList[3] = 'EEXP_FROM_DATE';
		$arrFieldList[4] = 'EEXP_TO_DATE';

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

	function getLastRecord($str) {
		
		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_EMP_WORK_EXPERIENCE';
		$arrFieldList[0] = 'EEXP_SEQNO';
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
