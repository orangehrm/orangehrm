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
require_once ROOT_PATH . '/lib/logs/LogFileWriter.php';

class EmpPassPort {

	var $tableName = 'HS_HR_EMP_PASSPORT';
	
	var $empId;
	var $empPPSeqNo;
	var $empNationality;
	var $empI9Status;
	var $empI9ReviewDat;
	var $empPPIssDat;
	var $empPPExpDat;
	var $emppassportflag;
	var $emppassComm;
	var $empPPNo;
	
	function EmpPassPort() {
		
	}
	
	function setEmpId($empId) {
	
	$this->empId=$empId;
	}

	function setEmpPPSeqNo($empPPSeqNo) {
	
	$this->empPPSeqNo=$empPPSeqNo;
	}

	function setEmpPPNo($empPPNo) {
	
	$this->empPPNo=$empPPNo;
	}
	
	function setEmpNationality($empNationality) {
	
	$this->empNationality=$empNationality;
	}
	
	function setEmpI9Status($empI9Status) {
	
	$this->empI9Status=$empI9Status;
	}
	
	function setEmpI9ReviewDat($empI9ReviewDat) {
	
	$this->empI9ReviewDat=$empI9ReviewDat;
	}
	
	function setEmpPPIssDat($empPPIssDat) {
	
	$this->empPPIssDat=$empPPIssDat;
	}
	
	function setEmpPPExpDat($empPPExpDat) {
	
	$this->empPPExpDat=$empPPExpDat;
	}
	
	function setEmppassportflag($emppassportflag) {
	
	$this->emppassportflag=$emppassportflag;
	}
		
	function setEmpPPComment($emppassComm){
	
	$this->emppassComm=$emppassComm;
	}
	
	function getEmpId() {
	
	return $this->empId;
	}
	
	function getEmpPPSeqNo(){
		
	return $this->empPPSeqNo;
	}
	
	function getEmpPPNo() {
	
	return $this->empPPNo;
	}
	
	function getEmpNationality() {
	
	return $this->empNationality;
	}
	
	function getEmpI9Status() {
	
	return $this->empI9Status;
	}
	
	function getEmpI9ReviewDat() {
	
	return $this->empI9ReviewDat;
	}
	
	function getEmpPPIssDat() {
	
	return $this->empPPIssDat;
	}

	function getEmpPPExpDat() {
	
	return $this->empPPExpDat;
	}
	
	function getEmppassportflag() {
	
	return $this->emppassportflag;
	}
	
	
	function getEmpPPComment() {
	
	return $this->emppassComm;
	}
			
	
////
	function getListofEmpPP($str,$mode) {
		
		$tableName = 'HS_HR_EMP_PASSPORT';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->field = 'EMP_NUMBER';

		$sqlQString = $sql_builder->selectEmployee($str,$mode);
		
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

	function delEmpPP($arrList) {

		$tableName = 'HS_HR_EMP_PASSPORT';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EP_SEQNO';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_delete = 'true';
		$sql_builder->arr_delete = $arrFieldList;

		$sqlQString = $sql_builder->deleteRecord($arrList);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

	}

	function addEmpPP() {
		
		$this->getEmpId();
		
		$arrFieldList[0] = "'". $this->getEmpId() . "'";
		$arrFieldList[1] = "'". $this->getEmpPPSeqNo() . "'";
		$arrFieldList[2] = "'". $this->getEmpPPNo() . "'";
		$arrFieldList[3] = "'". $this->getEmpPPIssDat() . "'";
		$arrFieldList[4] = "'". $this->getEmpPPExpDat() . "'";
		$arrFieldList[5] = "'". $this->getEmpPPComment() . "'";
		$arrFieldList[6] = "'". $this->getEmppassportflag() . "'";
		$arrFieldList[7] = "'". $this->getEmpI9Status() . "'";
		$arrFieldList[8] = "'". $this->getEmpI9ReviewDat() . "'";
		$arrFieldList[9] = "'". $this->getEmpNationality() . "'";
			
		$tableName = 'HS_HR_EMP_PASSPORT';
	
		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_insert = 'true';
		$sql_builder->arr_insert = $arrFieldList;		
			
	
		$sqlQString = $sql_builder->addNewRecordFeature1();
	//$logw = new LogFileWriter();
	//$logw->writeLogDB($sqlQString);
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		 return $message2;
		 echo $message2;
		
	}
	
	function updateEmpPP() {
		
		$arrRecordsList[0] = "'". $this->getEmpId() . "'";
		$arrRecordsList[1] = "'". $this->getEmpPPSeqNo() . "'";
		$arrRecordsList[2] = "'". $this->getEmpPPNo() . "'";
		$arrRecordsList[3] = "'". $this->getEmpPPIssDat() . "'";
		$arrRecordsList[4] = "'". $this->getEmpPPExpDat() . "'";
		$arrRecordsList[5] = "'". $this->getEmpPPComment() . "'";
		$arrRecordsList[6] = "'". $this->getEmppassportflag() . "'";
		$arrRecordsList[7] = "'". $this->getEmpI9Status() . "'";
		$arrRecordsList[8] = "'". $this->getEmpI9ReviewDat() . "'";
		$arrRecordsList[9] = "'". $this->getEmpNationality() . "'";
		

		$tableName = 'HS_HR_EMP_PASSPORT';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EP_SEQNO';
		$arrFieldList[2] = 'EP_PASSPORT_NUM';
		$arrFieldList[3] = 'EP_PASSPORTISSUEDDATE';
		$arrFieldList[4] = 'EP_PASSPORTEXPIREDATE';
		$arrFieldList[5] = 'EP_COMMENTS';
		$arrFieldList[6] = 'EP_PASSPORT_TYPE_FLG';
		$arrFieldList[7] = 'EP_I9_STATUS';
		$arrFieldList[8] = 'EP_I9_REVIEW_DATE';
		$arrFieldList[9] = 'COU_CODE';
		

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
	
	
	function filterEmpPP($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_EMP_PASSPORT';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EP_SEQNO';
		$arrFieldList[2] = 'EP_PASSPORT_NUM';
		$arrFieldList[3] = 'EP_PASSPORTISSUEDDATE';
		$arrFieldList[4] = 'EP_PASSPORTEXPIREDATE';
		$arrFieldList[5] = 'EP_COMMENTS';
		$arrFieldList[6] = 'EP_PASSPORT_TYPE_FLG';
		$arrFieldList[7] = 'EP_I9_STATUS';
		$arrFieldList[8] = 'EP_I9_REVIEW_DATE';
		$arrFieldList[9] = 'COU_CODE';

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
		$tableName = 'HS_HR_EMP_PASSPORT';
		$arrFieldList[0] = 'EP_SEQNO';
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
	
	function getAssEmpPP($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_EMP_PASSPORT';
		
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EP_SEQNO';
		$arrFieldList[2] = 'EP_PASSPORT_NUM';
		$arrFieldList[3] = 'EP_PASSPORTISSUEDDATE';
		$arrFieldList[4] = 'EP_PASSPORTEXPIREDATE';
		$arrFieldList[5] = 'EP_COMMENTS';
		$arrFieldList[6] = 'EP_PASSPORT_TYPE_FLG';
		$arrFieldList[7] = 'EP_I9_STATUS';
		$arrFieldList[8] = 'EP_I9_REVIEW_DATE';
		$arrFieldList[9] = 'COU_CODE';
		
		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->selectOneRecordFiltered($this->getID);
		//$exception_handler = new ExceptionHandler();
	  	 	//$exception_handler->logW($sqlQString);
		//$sqlQString="SELECT EMP_NUMBER, EP_SEQNO, EP_PASSPORT_NUM, EP_PASSPORTISSUEDDATE, EP_PASSPORTEXPIREDATE, EP_COMMENTS, EP_PASSPORT_TYPE_FLG, EP_I9_STATUS, EP_I9_REVIEW_DATE, COU_CODE  FROM hs_hr_emp_passport WHERE EMP_NUMBER='EMP010'";
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
	
}

?>
