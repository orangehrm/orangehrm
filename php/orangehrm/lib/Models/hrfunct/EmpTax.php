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

class EmpTax {

	var $tableName = 'HS_HR_TAX_EMP';
	
	var $empId;
	var $empTaxId;
	var $empFedStateFlag;
	var $empTaxFillStat;
	var $empTaxAllowance;
	var $empTaxExtra;
	var $empTaxState;
	var $arrayDispList;
	var $singleField;
	
	function EmpTax() {
		
	}
	
	function setEmpId($empId) {
	
	$this->empId=$empId;
	}

	function setTaxId($empTaxId) {
		
		$this->empTaxId=$empTaxId;
	}
	
	function setEmpFedStateFlag($empFedStateFlag) {
		$this->empFedStateFlag=$empFedStateFlag;
	}
	
	function setEmpTaxFillStat($empTaxFillStat) {
		$this->empTaxFillStat=$empTaxFillStat;
	}
	
	function setEmpTaxAllowance($empTaxAllowance) {
		$this->empTaxAllowance=$empTaxAllowance;
	}
	
	function setEmpTaxExtra($empTaxExtra) {
		$this->empTaxExtra=$empTaxExtra;
	}
	
	function setEmpTaxState($empTaxState) {
		$this->empTaxState=$empTaxState;
	}
	
	function getEmpId() {
	
	return $this->empId;
	}
	
	function getTaxId() {
		
		return $this->empTaxId;
	}
	
	function getEmpFedStateFlag() {
		return $this->empFedStateFlag;
	}
	
	function getEmpTaxFillStat() {
		return $this->empTaxFillStat;
	}
	
	function getEmpTaxAllowance() {
		return $this->empTaxAllowance;
	}
	
	function getEmpTaxExtra() {
		return $this->empTaxExtra;
	}
	
	function getEmpTaxState() {
		return $this->empTaxState;
	}
	
////
	function getListofEmpTax($str,$mode) {
		
		$tableName = 'HS_HR_TAX_EMP';

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

	function delEmpTax($arrList) {

		$tableName = 'HS_HR_TAX_EMP';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'TAX_CODE';
		$arrFieldList[2] = 'FEDERAL_STATE_FLG';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_delete = 'true';
		$sql_builder->arr_delete = $arrFieldList;

		$sqlQString = $sql_builder->deleteRecord($arrList);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

	}

	function addEmpTax() {
		
		$this->getEmpId();
		$arrFieldList[0] = "'". $this->getEmpId() . "'";
		$arrFieldList[1] = "'". $this->getTaxId() . "'";
		$arrFieldList[2] = "'". $this->getEmpFedStateFlag() . "'";
		$arrFieldList[3] = "'". $this->getEmpTaxFillStat() . "'";
		$arrFieldList[4] = "'". $this->getEmpTaxAllowance() . "'";
		$arrFieldList[5] = "'". $this->getEmpTaxExtra() . "'";
		$arrFieldList[6] = "'". $this->getEmpTaxState() . "'";

		$tableName = 'HS_HR_TAX_EMP';
	
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
	
	function updateEmpTax() {
		
		$arrRecordsList[0] = "'". $this->getEmpId() . "'";
		$arrRecordsList[1] = "'". $this->getTaxId() . "'";
		$arrRecordsList[2] = "'". $this->getEmpFedStateFlag() . "'";
		$arrRecordsList[3] = "'". $this->getEmpTaxFillStat() . "'";
		$arrRecordsList[4] = "'". $this->getEmpTaxAllowance() . "'";
		$arrRecordsList[5] = "'". $this->getEmpTaxExtra() . "'";
		$arrRecordsList[6] = "'". $this->getEmpTaxState() . "'";

		$tableName = 'HS_HR_TAX_EMP';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'TAX_CODE';
		$arrFieldList[2] = 'FEDERAL_STATE_FLG';
		$arrFieldList[3] = 'TAX_FILLING_STATUS';
		$arrFieldList[4] = 'TAX_ALLOWANCES';
		$arrFieldList[5] = 'TAX_EXTRA';
		$arrFieldList[6] = 'TAXED_STATE';

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
	
	
	function filterEmpTax($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_TAX_EMP';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'TAX_CODE';
		$arrFieldList[2] = 'FEDERAL_STATE_FLG';
		$arrFieldList[3] = 'TAX_FILLING_STATUS';
		$arrFieldList[4] = 'TAX_ALLOWANCES';
		$arrFieldList[5] = 'TAX_EXTRA';
		$arrFieldList[6] = 'TAXED_STATE';

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

	
	function getAssEmpTax($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_TAX_EMP';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'TAX_CODE';
		$arrFieldList[2] = 'FEDERAL_STATE_FLG';
		$arrFieldList[3] = 'TAX_FILLING_STATUS';
		$arrFieldList[4] = 'TAX_ALLOWANCES';
		$arrFieldList[5] = 'TAX_EXTRA';
		$arrFieldList[6] = 'TAXED_STATE';

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

	

	/*function getProvinceCodes($getID) {

		$this->getID = $getID;
		$tableName = 'HS_HR_PROVINCE';
		$arrFieldList[0] = 'COU_CODE';
		$arrFieldList[1] = 'PROVINCE_CODE';
		$arrFieldList[2] = 'PROVINCE_NAME';

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
	*/
	
}

?>
