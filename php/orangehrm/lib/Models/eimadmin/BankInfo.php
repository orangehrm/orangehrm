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

class BankInfo {

	var $tableName = 'HS_HR_BANK';
	var $bankId;
	var $bankDesc;
	var $bankAddress;
	var $bankAutoCLRCode;
	var $arrayDispList;
	var $singleField;
	
	
	function BankInfo() {
		
	}
	
	function setBankInfoId($bankId) {
	
		$this->bankId = $bankId;
	
	}
	
	function setBankInfoDesc($bankDesc) {
	
		$this->bankDesc = $bankDesc;

	}

	function setBankInfoAddress($bankAddress) {
	
		$this->bankAddress = $bankAddress;
	
	}
	
	function setBankInfoCLRCode ($bankAutoCLRCode) {
	
		$this->bankAutoCLRCode = $bankAutoCLRCode;

	}	
	
	
	function getBankInfoId() {
	
		return $this->bankId;
	
	}
	
	function getBankInfoDesc() {
	
		return $this->bankDesc;

	}

	function getBankInfoAddress() {
	
		return $this->bankAddress;
	
	}
	
	function getBankInfoCLRCode() {
	
		return $this->bankAutoCLRCode;

	}	
			
	
	function getListofBankInfo($pageNO,$schStr,$mode) {
		
		$tableName = 'HS_HR_BANK';			
		$arrFieldList[0] = 'BANK_CODE';
		$arrFieldList[1] = 'BANK_NAME';
		
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

	function countBankInfo($schStr,$mode) {
		
		$tableName = 'HS_HR_BANK';			
		$arrFieldList[0] = 'BANK_CODE';
		$arrFieldList[1] = 'BANK_NAME';
		
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

	function delBankInfo($arrList) {

		$tableName = 'HS_HR_BANK';
		$arrFieldList[0] = 'BANK_CODE';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_delete = 'true';
		$sql_builder->arr_delete = $arrFieldList;

		$sqlQString = $sql_builder->deleteRecord($arrList);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

	}

	
	function addBankInfo() {
		
		$this->getBankInfoId();
		$arrFieldList[0] = "'". $this->getBankInfoId() . "'";
		$arrFieldList[1] = "'". $this->getBankInfoDesc() . "'";
		$arrFieldList[2] = "'". $this->getBankInfoAddress() . "'";
		$arrFieldList[3] = "'". $this->getBankInfoCLRCode() . "'";
	
		$tableName = 'HS_HR_BANK';			
	
		//print_r($arrFieldList);
		//exit;
	
		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_insert = 'true';
		$sql_builder->arr_insert = $arrFieldList;		
		
		$sqlQString = $sql_builder->addNewRecordFeature1();
		//echo $sqlQString;
		//exit;
		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		 return $message2;
					
	}
	
	function updateBankInfo() {
		
		$this->getBankInfoId();
		$arrRecordsList[0] = "'". $this->getBankInfoId() . "'";
		$arrRecordsList[1] = "'". $this->getBankInfoDesc() . "'";
		$arrRecordsList[2] = "'". $this->getBankInfoAddress() . "'";
		$arrRecordsList[3] = "'". $this->getBankInfoCLRCode() . "'";
		
		$arrFieldList[0] = 'BANK_CODE';
		$arrFieldList[1] = 'BANK_NAME';
		$arrFieldList[2] = 'BANK_ADDRESS';
		$arrFieldList[3] = 'BANK_AUTO_CLR_HOUSE_CODE';
		
		$tableName = 'HS_HR_BANK';			
	
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
	
	
	function filterBankInfo($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_BANK';			
		$arrFieldList[0] = 'BANK_CODE';
		$arrFieldList[1] = 'BANK_NAME';
		$arrFieldList[2] = 'BANK_ADDRESS';
		$arrFieldList[3] = 'BANK_AUTO_CLR_HOUSE_CODE';
		
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
	
	
	function getLastRecord() {
		
		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_BANK';		
		$arrFieldList[0] = 'BANK_CODE';
				
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
			
		return $common_func->explodeString($this->singleField,"BNK"); 
				
		}
		
	}	
	
	
}

?>
