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

class EmpBasSalary {

	var $tableName = 'HS_HR_EMP_BASICSALARY';
	
	var $empId;
	var $empSalGrdCode;
	var $empCurrCode;
	var $empBasSal;

	var $arrayDispList;
	var $singleField;
	
	function EmpBasSalary() {
		
	}
	
	function setEmpId($empId) {
	
	$this->empId=$empId;
	}
	
	function setEmpSalGrdCode($empSalGrdCode) {
	
	$this->empSalGrdCode=$empSalGrdCode;
	}
	
	function setEmpCurrCode($empCurrCode) {
	
	$this->empCurrCode=$empCurrCode;
	}
	
	function setEmpBasSal($empBasSal) {
	
	$this->empBasSal=$empBasSal;
	}

	function getEmpId() {
	
	return $this->empId;
	}
	
	function getEmpSalGrdCode() {
	
	return $this->empSalGrdCode;
	}
	
	function getEmpCurrCode() {
	
	return $this->empCurrCode;
	}
	
	function getEmpBasSal() {
	
	return $this->empBasSal;
	}

	////
	
	
	function getListofEmpBasSal($page,$str,$mode) {
		
		$tableName = 'HS_HR_EMP_BASICSALARY';

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

	function countEmpBasSal($str,$mode) {
		
		$tableName = 'HS_HR_EMP_BASICSALARY';

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

	function delEmpBasSal($arrList) {

		$tableName = 'HS_HR_EMP_BASICSALARY';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'SAL_GRD_CODE';
		$arrFieldList[2] = 'CURRENCY_ID';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_delete = 'true';
		$sql_builder->arr_delete = $arrFieldList;

		$sqlQString = $sql_builder->deleteRecord($arrList);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

	}

	function addEmpBasSal() {
		
		$this->getEmpId();
		$arrFieldList[0] = "'". $this->getEmpId() . "'";
		$arrFieldList[1] = "'". $this->getEmpSalGrdCode() . "'";
		$arrFieldList[2] = "'". $this->getEmpCurrCode() . "'";
		$arrFieldList[3] = "'". $this->getEmpBasSal() . "'";

		$tableName = 'HS_HR_EMP_BASICSALARY';
	
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
	
	function updateEmpBasSal() {
		
		$arrRecordsList[0] = "'". $this->getEmpId() . "'";
		$arrRecordsList[1] = "'". $this->getEmpSalGrdCode() . "'";
		$arrRecordsList[2] = "'". $this->getEmpCurrCode() . "'";
		$arrRecordsList[3] = "'". $this->getEmpBasSal() . "'";

		$tableName = 'HS_HR_EMP_BASICSALARY';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'SAL_GRD_CODE';
		$arrFieldList[2] = 'CURRENCY_ID';
		$arrFieldList[3] = 'EBSAL_BASIC_SALARY';

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
	
	
	function filterEmpBasSal($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_EMP_BASICSALARY';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'SAL_GRD_CODE';
		$arrFieldList[2] = 'CURRENCY_ID';
		$arrFieldList[3] = 'EBSAL_BASIC_SALARY';

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

	function getAssEmpBasSal($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_EMP_BASICSALARY';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'SAL_GRD_CODE';
		$arrFieldList[2] = 'CURRENCY_ID';
		$arrFieldList[3] = 'EBSAL_BASIC_SALARY';

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
	
	function getCurrCodes($salGrd) {

		$sqlQString = "SELECT b.CURRENCY_NAME, a.* FROM HS_PR_SALARY_CURRENCY_DETAIL a, HS_HR_CURRENCY_TYPE b WHERE a.CURRENCY_ID = b.CURRENCY_ID AND a.SAL_GRD_CODE = '" . $salGrd . "'";
		$sqlQString = strtolower($sqlQString);
		
		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$i=0;

		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {

	    	$arrayDispList[$i][0] = $line[0];
	    	$arrayDispList[$i][1] = $line[1];
	    	$arrayDispList[$i][2] = $line[2];
	    	$arrayDispList[$i][3] = $line[3];
	    	$arrayDispList[$i][4] = $line[4];
	    	$arrayDispList[$i][5] = $line[5];
	    	$i++;

	     }

	     if (isset($arrayDispList)) {

			return $arrayDispList;

		} else {

			$arrayDispList = '';
			return $arrayDispList;

		}
	}

	function getUnAssCurrCodes($salGrd,$eno) {

		$sqlQString = "SELECT b.CURRENCY_NAME, a.* FROM HS_PR_SALARY_CURRENCY_DETAIL a, HS_HR_CURRENCY_TYPE b WHERE a.CURRENCY_ID NOT IN (SELECT CURRENCY_ID FROM HS_HR_EMP_BASICSALARY WHERE SAL_GRD_CODE = '" . $salGrd . "' AND EMP_NUMBER = '" .$eno. "') AND a.CURRENCY_ID = b.CURRENCY_ID AND a.SAL_GRD_CODE = '" . $salGrd . "'";
		$sqlQString = strtolower($sqlQString);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$i=0;

		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {

	    	$arrayDispList[$i][0] = $line[0];
	    	$arrayDispList[$i][1] = $line[1];
	    	$arrayDispList[$i][2] = $line[2];
	    	$arrayDispList[$i][3] = $line[3];
	    	$arrayDispList[$i][4] = $line[4];
	    	$arrayDispList[$i][5] = $line[5];
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
