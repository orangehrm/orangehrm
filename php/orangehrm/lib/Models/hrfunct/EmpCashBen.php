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

class EmpCashBen {

	var $tableName = 'HS_HR_EMP_CASH_BENEFIT';
	
	var $empId;
	var $empCashBenCode;
	var $empBenAmount;
	var $empBenDatAss;
	var $empBenFltType;

	var $arrayDispList;
	var $singleField;
	
	function EmpCashBen() {
		
	}
	
	function setEmpId($empId) {
	
	$this->empId=$empId;
	}
	
	function setEmpCashBenCode($empCashBenCode) {
	
	$this->empCashBenCode=$empCashBenCode;
	}
	
	function setEmpBenAmount($empBenAmount) {
	
	$this->empBenAmount=$empBenAmount;
	}
	
	function setEmpBenDatAss($empBenDatAss) {
	
	$this->empBenDatAss=$empBenDatAss;
	}
	
	function setEmpBenFltType($empBenFltType) {
	
	$this->empBenFltType=$empBenFltType;
	}
	
	function getEmpId() {
	
	return $this->empId;
	}
	
	function getEmpCashBenCode() {
	
	return $this->empCashBenCode;
	}
	
	function getEmpBenAmount() {
	
	return $this->empBenAmount;
	}
	
	function getEmpBenDatAss() {
	
	return $this->empBenDatAss;
	}
	
	function getEmpBenFltType() {
	
	return $this->empBenFltType;
	}

	////
	function getUnAssEmployee($pageNO,$schStr,$mode) {
		
		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_FULLNAME';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_EMP_CASH_BENEFIT';
		$sql_builder->field = 'EMP_NUMBER';
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->passResultFilter($pageNO,$schStr,$mode);
		
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

	function countUnAssEmployee($schStr,$mode) {
		
		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_FULLNAME';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_EMP_CASH_BENEFIT';
		$sql_builder->flg_select = 'true';
		$sql_builder->field = 'EMP_NUMBER';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->countResultFilter($schStr,$mode);
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$line = mysql_fetch_array($message2, MYSQL_NUM);
		 	
	    	return $line[0];
	}
	
	
	function getListofEmpCashBen($page,$str,$mode) {
		
		$tableName = 'HS_HR_EMP_CASH_BENEFIT';

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

	function countEmpCashBen($str,$mode) {
		
		$tableName = 'HS_HR_EMP_CASH_BENEFIT';

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

	function delEmpCashBen($arrList) {

		$tableName = 'HS_HR_EMP_CASH_BENEFIT';
		$arrFieldList1[0] = 'EMP_NUMBER';
		$arrFieldList1[1] = 'BEN_CODE';

		$sql_builder1 = new SQLQBuilder();

		$sql_builder1->table_name = $tableName;
		$sql_builder1->flg_delete = 'true';
		$sql_builder1->arr_delete = $arrFieldList1;

		$sqlDelString = $sql_builder1->deleteRecord($arrList);

		//echo $sqlQString;
		$dbConnection1 = new DMLFunctions();
		$message2 = $dbConnection1 -> executeQuery($sqlDelString); //Calling the addData() function

		$sql_builder2 = new SQLQBuilder();
		$dbConnection2 = new DMLFunctions();
        for($c=0;count($arrList[1])>$c;$c++)
            if($arrList[$c][0]!=NULL) {

    		$arrFieldList2[0] = "'". $arrList[0][$c] . "'";
    		$arrFieldList2[1] = "'". $arrList[1][$c] . "'";

    		$tableName = 'HS_HR_EMP_CASH_BEN_REMOVE';


    		$sql_builder2->table_name = $tableName;
    		$sql_builder2->flg_insert = 'true';
    		$sql_builder2->arr_insert = $arrFieldList2;


    		$sqlQString = $sql_builder2->addNewRecordFeature1();

    		$message2 = $dbConnection2 -> executeQuery($sqlQString); //Calling the addData()
        }
	}

	function addEmpCashBen() {
		
		$this->getEmpId();
		$arrFieldList[0] = "'". $this->getEmpId() . "'";
		$arrFieldList[1] = "'". $this->getEmpCashBenCode() . "'";
		$arrFieldList[2] = "'". $this->getEmpBenAmount() . "'";
		$arrFieldList[3] = "'". $this->getEmpBenDatAss() . "'";
		$arrFieldList[4] = "'". $this->getEmpBenFltType() . "'";

		$tableName = 'HS_HR_EMP_CASH_BENEFIT';
	
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
	
	function addUnAssEmpCashBen() {
		
		$this->getEmpId();
		$arrFieldList[0] = "'". $this->getEmpId() . "'";
		$arrFieldList[1] = "'". $this->getEmpCashBenCode() . "'";
		$arrFieldList[2] = "'". $this->getEmpBenAmount() . "'";
		$arrFieldList[3] = "'". $this->getEmpBenDatAss() . "'";
		$arrFieldList[4] = "'". $this->getEmpBenFltType() . "'";

		$tableName = 'HS_HR_EMP_CASH_BENEFIT';
	
		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_insert = 'true';
		$sql_builder->arr_insert = $arrFieldList;
		
		$sqlQString = $sql_builder->addNewRecordFeature1();
	
		$dbConnection = new DMLFunctions();
		$message = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		if($message) {
			$tableName = 'HS_HR_EMP_CASH_BEN_REMOVE';
			$arrFieldList1[0] = 'EMP_NUMBER';
			$arrFieldList1[1] = 'BEN_CODE';
	
			$sql_builder1 = new SQLQBuilder();
	
			$sql_builder1->table_name = $tableName;
			$sql_builder1->flg_delete = 'true';
			$sql_builder1->arr_delete = $arrFieldList1;
			$arr[0][0]=$this->getEmpId();
			$arr[1][0]=$this->getEmpCashBenCode();
		
			$sqlDelString = $sql_builder1->deleteRecord($arr);
		
			$dbConnection1 = new DMLFunctions();
			$message2 = $dbConnection1 -> executeQuery($sqlDelString); //Calling the addData() function
		}
	}

	function updateEmpCashBen() {
		
		$arrRecordsList[0] = "'". $this->getEmpId() . "'";
		$arrRecordsList[1] = "'". $this->getEmpCashBenCode() . "'";
		$arrRecordsList[2] = "'". $this->getEmpBenAmount() . "'";
		$arrRecordsList[3] = "'". $this->getEmpBenDatAss() . "'";

		$tableName = 'HS_HR_EMP_CASH_BENEFIT';
        $arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'BEN_CODE';
		$arrFieldList[2] = 'EBEN_AMOUNT';
		$arrFieldList[3] = 'EBEN_DATE_ASSIGNED';

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

	function filterEmpCashBen($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_EMP_CASH_BENEFIT';
        $arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'BEN_CODE';
		$arrFieldList[2] = 'EBEN_AMOUNT';
		$arrFieldList[3] = 'EBEN_DATE_ASSIGNED';
		$arrFieldList[4] = 'EBEN_FILTER_TYPE';

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

	function getBenCodes() {
		
		$tableName = 'HS_HR_CASH_BENEFIT';
		$arrFieldList[0] = 'BEN_CODE';
		$arrFieldList[1] = 'BEN_NAME';
		$arrFieldList[2] = 'BEN_AMOUNT';
		
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
	    	$arrayDispList[$i][2] = $line[2];
	    	$i++;
	    	
	     }
	     
	     if (isset($arrayDispList)) {
	     
			return $arrayDispList;
			
		} else {
		
			$arrayDispList = '';
			return $arrayDispList;
			
		}
	}

function getAssEmpCashBen($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_EMP_CASH_BENEFIT';
        $arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'BEN_CODE';
		$arrFieldList[2] = 'EBEN_AMOUNT';

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

	function getOthEmpCashBen($id) {

		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_CASH_BENEFIT';
		$arrFieldList[0] = 'BEN_CODE';
		$arrFieldList[1] = 'BEN_NAME';
		$arrFieldList[2] = 'BEN_AMOUNT';

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;
		$sql_builder->field='BEN_CODE';
		$sql_builder->table2_name= 'HS_HR_EMP_CASH_BENEFIT';
		$arr[0][0]='EMP_NUMBER';
		$arr[0][1]=$id;

		$sqlQString = $sql_builder->selectFilter($arr);

		$dbConnection = new DMLFunctions();
       		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$common_func = new CommonFunctions();

		$i=0;

		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {

	    	$arrayDispList[$i][0] = $line[0];
	    	$arrayDispList[$i][1] = $line[1];
	    	$arrayDispList[$i][2] = $line[2];

	    	$i++;
	     }

	     if (isset($arrayDispList)) {

	       	return $arrayDispList;

	     } else {
	     	//Handle Exceptions
	     	//Create Logs
	     }
	}

function getUnAssEmpCashBen($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_EMP_CASH_BEN_REMOVE';
        $arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'BEN_CODE';

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
	
}
?>
