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

class CurrencyTypes {

	var $tableName = 'HS_HR_CURRENCY_TYPE';
	
	var $currencyId;
	var $currencyDesc;
	var $arrayDispList;
	var $singleField;
	
	
	function CurrencyTypes() {
		
	}
	
	function setCurrencyId($currencyId) {
	
		$this->currencyId = $currencyId;
	
	}
	
	function setCurrencyDescription($currencyDesc) {
	
		$this->currencyDesc = $currencyDesc;
	}
		
	
	function getCurrencyId() {
	
		return $this->currencyId;
	
	}
	
	function getCurrencyDesc() {
	
		return $this->currencyDesc;
		
	}
	
	function getListofCurrencyTypes($pageNO,$schStr,$mode) {
		
		$tableName = 'HS_HR_CURRENCY_TYPE';			
		$arrFieldList[0] = 'CURRENCY_ID';
		$arrFieldList[1] = 'CURRENCY_NAME';
		
		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->passResultSetMessage($pageNO,$schStr,$mode,1);
		
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

	function countCurrencyTypes($schStr,$mode) {
		
		$tableName = 'HS_HR_CURRENCY_TYPE';			
		$arrFieldList[0] = 'CURRENCY_ID';
		$arrFieldList[1] = 'CURRENCY_NAME';
		
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

	function delCurrencyTypes($arrList) {

		$tableName = 'HS_HR_CURRENCY_TYPE';
		$arrFieldList[0] = 'CURRENCY_ID';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_delete = 'true';
		$sql_builder->arr_delete = $arrFieldList;

		$sqlQString = $sql_builder->deleteRecord($arrList);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

	}

	
	function addCurrencyTypes() {
		
		$this->getCurrencyId();
		$arrFieldList[0] = "'". $this->getCurrencyId() . "'";
		$arrFieldList[1] = "'". $this->getCurrencyDesc() . "'";
		
		
		//$arrFieldList[0] = 'CURRENCY_ID';
		//$arrFieldList[1] = 'CURRENCY_NAME';
		
		$tableName = 'HS_HR_CURRENCY_TYPE';			
	
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
	
	function updateCurrencyTypes() {
		
		$this->getCurrencyId();
		$arrRecordsList[0] = "'". $this->getCurrencyId() . "'";
		$arrRecordsList[1] = "'". $this->getCurrencyDesc() . "'";
		$arrFieldList[0] = 'CURRENCY_ID';
		$arrFieldList[1] = 'CURRENCY_NAME';
		
		$tableName = 'HS_HR_CURRENCY_TYPE';			
	
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
	
	
	function filterCurrencyTypes($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_CURRENCY_TYPE';			
		$arrFieldList[0] = 'CURRENCY_ID';
		$arrFieldList[1] = 'CURRENCY_NAME';
		
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
	    	$i++;
	    	
	     }
	     
	     if (isset($arrayDispList)) {
	     
			return $arrayDispList;
			
		} else {
		
			$arrayDispList = '';
			return $arrayDispList;
			
		}
				
	}
	
	
	function getLastRecord() {
		
		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_CURRENCY_TYPE';		
		$arrFieldList[0] = 'CURRENCY_ID';
				
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
			
		return $common_func->explodeString($this->singleField,"CUR"); 
				
		}
		
	}

	function getCurrencyCodes($id) {
		
		$tableName = 'HS_HR_CURRENCY_TYPE';			
		$arrFieldList[0] = 'CURRENCY_ID';
		$arrFieldList[1] = 'CURRENCY_NAME';
		
		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
		$sql_builder->field = 'CURRENCY_ID';		
		$sql_builder->table2_name = 'HS_PR_SALARY_CURRENCY_DETAIL';
			
		$arr[0][0]='SAL_GRD_CODE';
		$arr[0][1]=$id;
		$sqlQString = $sql_builder->passResultSetMessage(0,'',-1,1, 'ASC');
		
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
	
		function getAllCurrencyCodes() {
		
		$tableName = 'HS_HR_CURRENCY_TYPE';			
		$arrFieldList[0] = 'CURRENCY_ID';
		$arrFieldList[1] = 'CURRENCY_NAME';
		
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
