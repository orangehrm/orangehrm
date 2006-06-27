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
require_once ROOT_PATH . '/lib/models/eimadmin/NonCashBen.php';

class EmpNonCashBen {

	var $tableName = 'HS_HR_EMP_NONCASH_BENEFIT';
	
	var $empId;
	var $empBenCode;
	var $empBenIssDat;
	var $empBenQty;
	var $empBenComment;
	var $empBenItmReturnableFlag;
	var $empBenItmRetDat;
	var $empBenItmRetFlag;
	var $empBenFltType;

	var $arrayDispList;
	var $singleField;
	
	function EmpNonCashBen() {
		$this->noncashben = new NonCashBen();
	}
	
	function setEmpId($empId) {
	
	$this->empId=$empId;
	}
	
	function setEmpBenCode($empBenCode) {
	
	$this->empBenCode=$empBenCode;
	}
	
	function setEmpBenIssDat($empBenIssDat) {
	
	$this->empBenIssDat=$empBenIssDat;
	}
	
	function setEmpBenQty($empBenQty) {
	
	$this->empBenQty=$empBenQty;
	}
	
	function setEmpBenComment($empBenComment) {
	
	$this->empBenComment=$empBenComment;
	}
	
	function setEmpBenItmReturnableFlag($empBenItmReturnableFlag) {
	
	$this->empBenItmReturnableFlag=$empBenItmReturnableFlag;
	}
	
	function setEmpBenItmRetDat($empBenItmRetDat) {
	
	$this->empBenItmRetDat=$empBenItmRetDat;
	}
	
	function setEmpBenItmRetFlag($empBenItmRetFlag) {
	
	$this->empBenItmRetFlag=$empBenItmRetFlag;
	}
	
	function setEmpBenFltType($empBenFltType) {
	
	$this->empBenFltType=$empBenFltType;
	}
	
	function getEmpId() {
	
	return $this->empId;
	}
	
	function getEmpBenCode() {
	
	return $this->empBenCode;
	}
	
	function getEmpBenIssDat() {
	
	return $this->empBenIssDat;
	}
	
	function getEmpBenQty() {
	
	return $this->empBenQty;
	}
	
	function getEmpBenComment() {
	
	return $this->empBenComment;
	}
	
	function getEmpBenItmReturnableFlag() {
	
	return $this->empBenItmReturnableFlag;
	}
	
	function getEmpBenItmRetDat() {
	
	return $this->empBenItmRetDat;
	}
	
	function getEmpBenItmRetFlag() {
	
	return $this->empBenItmRetFlag;
	}
	
	function getEmpBenFltType() {
	
	return $this->empBenFltType;
	}

	
	
	function getListofEmpNonCashBen($page,$str,$mode) {
		
		$tableName = 'HS_HR_EMP_NONCASH_BENEFIT';

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

	function countEmpNonCashBen($str,$mode) {
		
		$tableName = 'HS_HR_EMP_NONCASH_BENEFIT';

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

	function delEmpNonCashBen($arrList) {

		$tableName = 'HS_HR_EMP_NONCASH_BENEFIT';
		$arrFieldList1[0] = 'EMP_NUMBER';
		$arrFieldList1[1] = 'NBEN_CODE';

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

    		$tableName = 'HS_HR_EMP_NONCASH_BEN_REMOVE';


    		$sql_builder2->table_name = $tableName;
    		$sql_builder2->flg_insert = 'true';
    		$sql_builder2->arr_insert = $arrFieldList2;


    		$sqlQString = $sql_builder2->addNewRecordFeature1();

    		$message2 = $dbConnection2 -> executeQuery($sqlQString); //Calling the addData()
        }
	}

	function addEmpNonCashBen() {
		$benlist = $this->noncashben->getBenCodes();
		
		$arrFieldList[0] = "'". $this->getEmpId() . "'";
		$arrFieldList[1] = "'". $this->getEmpBenCode() . "'";
		$arrFieldList[2] = "'". date('Y-m-d') . "'";
		$arrFieldList[3] = "'1'";
		$arrFieldList[4] = "null";
       	for($j=0;count($benlist)>$j;$j++)
       	    if($benlist[$j][0]==$this->getEmpBenCode())
				$arrFieldList[5] = "'". $benlist[$j][2] . "'";
		$arrFieldList[6] = "null";
		$arrFieldList[7] = "null";
		$arrFieldList[8] = "'3'";

		$tableName = 'HS_HR_EMP_NONCASH_BENEFIT';
	
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

	function addUnAssEmpNonCashBen() {
		$benlist = $this->noncashben->getBenCodes();
		
		$arrFieldList[0] = "'". $this->getEmpId() . "'";
		$arrFieldList[1] = "'". $this->getEmpBenCode() . "'";
		$arrFieldList[2] = "'". date('Y-m-d') . "'";
		$arrFieldList[3] = "'1'";
		$arrFieldList[4] = "null";
       	for($j=0;count($benlist)>$j;$j++)
       	    if($benlist[$j][0]==$this->getEmpBenCode())
				$arrFieldList[5] = "'". $benlist[$j][2] . "'";
		$arrFieldList[6] = "null";
		$arrFieldList[7] = "null";
		$arrFieldList[8] = "'3'";

		$tableName = 'HS_HR_EMP_NONCASH_BENEFIT';
	
		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_insert = 'true';
		$sql_builder->arr_insert = $arrFieldList;
			
	
		$sqlQString = $sql_builder->addNewRecordFeature1();
	
		$dbConnection = new DMLFunctions();
		$message = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		if($message) {
			$tableName = 'HS_HR_EMP_NONCASH_BEN_REMOVE';
			$arrFieldList1[0] = 'EMP_NUMBER';
			$arrFieldList1[1] = 'NBEN_CODE';
	
			$sql_builder1 = new SQLQBuilder();
	
			$sql_builder1->table_name = $tableName;
			$sql_builder1->flg_delete = 'true';
			$sql_builder1->arr_delete = $arrFieldList1;
			$arr[0][0]=$this->getEmpId();
			$arr[1][0]=$this->getEmpBenCode();
		
			$sqlDelString = $sql_builder1->deleteRecord($arr);
		
			$dbConnection1 = new DMLFunctions();
			$message2 = $dbConnection1 -> executeQuery($sqlDelString); //Calling the addData() function
		}
	}
	
	function updateEmpNonCashBen() {
		
		$arrRecordsList[0] = "'". $this->getEmpId() . "'";
		$arrRecordsList[1] = "'". $this->getEmpBenCode() . "'";
		$arrRecordsList[2] = "'". $this->getEmpBenIssDat() . "'";
		$arrRecordsList[3] = "'". $this->getEmpBenQty() . "'";
		$arrRecordsList[4] = "'". $this->getEmpBenComment() . "'";
		$arrRecordsList[5] = "'". $this->getEmpBenItmReturnableFlag() . "'";
		$arrRecordsList[6] = "'". $this->getEmpBenItmRetDat() . "'";
		$arrRecordsList[7] = "'". $this->getEmpBenItmRetFlag() . "'";

		$tableName = 'HS_HR_EMP_NONCASH_BENEFIT';
        $arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'NBEN_CODE';
		$arrFieldList[2] = 'ENBEN_ISSUE_DATE';
		$arrFieldList[3] = 'ENBEN_QUANTITY';
		$arrFieldList[4] = 'ENBEN_COMMENTS';
		$arrFieldList[5] = 'ENBEN_ITEM_RETURNABLE_FLG';
		$arrFieldList[6] = 'ENBEN_ITEM_RETURNED_DATE';
		$arrFieldList[7] = 'ENBEN_ITEM_RETURNED_FLG';

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

	function filterEmpNonCashBen($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_EMP_NONCASH_BENEFIT';
        $arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'NBEN_CODE';
		$arrFieldList[2] = 'ENBEN_ISSUE_DATE';
		$arrFieldList[3] = 'ENBEN_QUANTITY';
		$arrFieldList[4] = 'ENBEN_COMMENTS';
		$arrFieldList[5] = 'ENBEN_ITEM_RETURNABLE_FLG';
		$arrFieldList[6] = 'ENBEN_ITEM_RETURNED_DATE';
		$arrFieldList[7] = 'ENBEN_ITEM_RETURNED_FLG';
		$arrFieldList[8] = 'ENBEN_FILTER_TYPE';

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

function getAssEmpNonCashBen($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_EMP_NONCASH_BENEFIT';
        $arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'NBEN_CODE';
		$arrFieldList[2] = 'ENBEN_ISSUE_DATE';
		$arrFieldList[3] = 'ENBEN_QUANTITY';
		$arrFieldList[4] = 'ENBEN_COMMENTS';
		$arrFieldList[5] = 'ENBEN_ITEM_RETURNABLE_FLG';
		$arrFieldList[6] = 'ENBEN_ITEM_RETURNED_DATE';
		$arrFieldList[7] = 'ENBEN_ITEM_RETURNED_FLG';
		$arrFieldList[8] = 'ENBEN_FILTER_TYPE';

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

	function getUnAssEmpNonCashBen($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_EMP_NONCASH_BEN_REMOVE';
        $arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'NBEN_CODE';

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
