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

class EmpBank {

	var $tableName = 'HS_HR_EMP_BANK';
	
	var $empId;
	var $empBranchCode;
    var $empAccNo;
    var $empAccType;
    var $empAmount;
    var $empBankOrder;

    var $arrayDispList;
	var $singleField;
	
	function EmpBank() {
		
	}
	
	function setEmpId($empId) {
	
	$this->empId=$empId;
	}

    function setEmpBranchCode($empBranchCode) {

    $this->empBranchCode=$empBranchCode;
    }

	function setEmpAccNo($empAccNo) {
	
	$this->empAccNo=$empAccNo;
	}
	
	function setEmpAccType($empAccType) {
	
	$this->empAccType=$empAccType;
	}
	
	function setEmpAmount($empAmount) {
	
	$this->empAmount=$empAmount;
	}
	
	function setEmpBankOrder($empBankOrder) {
	
	$this->empBankOrder=$empBankOrder;
	}
	
	function getEmpId() {
	
	return $this->empId;
	}
	
    function getEmpBranchCode() {

    return $this->empBranchCode;
    }

	function getEmpAccNo() {
	
	return $this->empAccNo;
	}
	
	function getEmpAccType() {
	
	return $this->empAccType;
	}
	
	function getEmpAmount() {
	
	return $this->empAmount;
	}
	
	function getEmpBankOrder() {
	
	return $this->empBankOrder;
	}
////
	function getListofEmpBank($str,$mode) {
		
		$tableName = 'HS_HR_EMP_BANK';

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

	function delEmpBank($arrList) {

		$tableName = 'HS_HR_EMP_BANK';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'BBRANCH_CODE';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_delete = 'true';
		$sql_builder->arr_delete = $arrFieldList;

		$sqlQString = $sql_builder->deleteRecord($arrList);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

	}

	function addEmpBank() {
		
		$this->getEmpId();
		$arrFieldList[0] = "'". $this->getEmpBranchCode() . "'";
		$arrFieldList[1] = "'". $this->getEmpId() . "'";
		$arrFieldList[2] = "'". $this->getEmpAccNo() . "'";
		$arrFieldList[3] = "'". $this->getEmpAccType() . "'";
		$arrFieldList[4] = "'". $this->getEmpAmount() . "'";
		$arrFieldList[5] = "'". $this->getEmpBankOrder() . "'";

		$tableName = 'HS_HR_EMP_BANK';
	
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
	
	function updateEmpBank() {
		
		$arrRecordsList[0] = "'". $this->getEmpBranchCode() . "'";
		$arrRecordsList[1] = "'". $this->getEmpId() . "'";
		$arrRecordsList[2] = "'". $this->getEmpAccNo() . "'";
		$arrRecordsList[3] = "'". $this->getEmpAccType() . "'";
		$arrRecordsList[4] = "'". $this->getEmpAmount() . "'";
		$arrRecordsList[5] = "'". $this->getEmpBankOrder() . "'";

		$tableName = 'HS_HR_EMP_BANK';
		$arrFieldList[0] = 'BBRANCH_CODE';
		$arrFieldList[1] = 'EMP_NUMBER';
		$arrFieldList[2] = 'EBANK_ACC_NO';
		$arrFieldList[3] = 'EBANK_ACC_TYPE_FLG';
		$arrFieldList[4] = 'EBANK_AMOUNT';
		$arrFieldList[5] = 'EBANK_ORDER';

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
	
	
	function filterEmpBank($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_EMP_BANK';
		$arrFieldList[0] = 'BBRANCH_CODE';
		$arrFieldList[1] = 'EMP_NUMBER';
		$arrFieldList[2] = 'EBANK_ACC_NO';
		$arrFieldList[3] = 'EBANK_ACC_TYPE_FLG';
		$arrFieldList[4] = 'EBANK_AMOUNT';
		$arrFieldList[5] = 'EBANK_ORDER';

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

	


	function getAssEmpBank($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_EMP_BANK';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'BBRANCH_CODE';
		$arrFieldList[2] = 'EBANK_ACC_NO';
		$arrFieldList[3] = 'EBANK_ACC_TYPE_FLG';
		$arrFieldList[4] = 'EBANK_AMOUNT';
		$arrFieldList[5] = 'EBANK_ORDER';

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

function getAllBranchCodes() {
		
		$sqlQString = "SELECT a.BBRANCH_CODE,a.BBRANCH_NAME,a.BANK_CODE, b.BANK_NAME FROM hs_hr_branch a, hs_hr_bank b WHERE a.BANK_CODE = b.BANK_CODE";
		
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

	

}

?>
