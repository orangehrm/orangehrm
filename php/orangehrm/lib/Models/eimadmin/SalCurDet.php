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

class SalCurDet {

	
	
	var $salGrdId;
	var $currId;
	var $minSal;
	var $maxSal;
	var $stepSal;

	var $arrayDispList;
	var $singleField;
	
	function SalCurDet() {	
		
	}
	
	function setSalGrdId($salGrdId) {
	
	$this->salGrdId=$salGrdId;
	}
	
	function setCurrId($currId) {
	
	$this->currId=$currId;
	}
	
	function setMinSal($minSal) {
	
	$this->minSal=$minSal;
	}
	
	function setMaxSal($maxSal) {
	
	$this->maxSal=$maxSal;
	}
	
	function setStepSal($stepSal) {
	
	$this->stepSal=$stepSal;
	}
	
	function getSalGrdId() {
	
	return $this->salGrdId;
	}
	
	function getCurrId() {
	
	return $this->currId;
	}
	
	function getMinSal() {
	
	return $this->minSal;
	}
	
	function getMaxSal() {
	
	return $this->maxSal;
	}

	function getStepSal() {
	
	return $this->stepSal;
	}

	function delSalCurDet($arrList) {

		$tableName = 'HS_PR_SALARY_CURRENCY_DETAIL';
		$arrFieldList1[0] = 'SAL_GRD_CODE';
		$arrFieldList1[1] = 'CURRENCY_ID';

		$sql_builder1 = new SQLQBuilder();

		$sql_builder1->table_name = $tableName;
		$sql_builder1->flg_delete = 'true';
		$sql_builder1->arr_delete = $arrFieldList1;

		$sqlDelString = $sql_builder1->deleteRecord($arrList);

		//echo $sqlQString;
		$dbConnection1 = new DMLFunctions();
		$message2 = $dbConnection1 -> executeQuery($sqlDelString); //Calling the addData() function
	}

	function addSalCurDet() {

		$arrFieldList[0] = "'". $this->getSalGrdId() . "'";
		$arrFieldList[1] = "'". $this->getCurrId() . "'";
		$arrFieldList[2] = "'". $this->getMinSal() . "'";
		$arrFieldList[3] = "'". $this->getStepSal() . "'";
		$arrFieldList[4] = "'". $this->getMaxSal() . "'";

		$tableName = 'HS_PR_SALARY_CURRENCY_DETAIL';
	
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
	
	function updateSalCurDet() {
		
		$arrRecordsList[0] = "'". $this->getSalGrdId() . "'";
		$arrRecordsList[1] = "'". $this->getCurrId() . "'";
		$arrRecordsList[2] = "'". $this->getMinSal() . "'";
		$arrRecordsList[3] = "'". $this->getMaxSal() . "'";
		$arrRecordsList[4] = "'". $this->getStepSal() . "'";

		$tableName = 'HS_PR_SALARY_CURRENCY_DETAIL';
        $arrFieldList[0] = 'SAL_GRD_CODE';
		$arrFieldList[1] = 'CURRENCY_ID';
		$arrFieldList[2] = 'SALCURR_DTL_MINSALARY';
		$arrFieldList[3] = 'SALCURR_DTL_MAXSALARY';
		$arrFieldList[4] = 'SALCURR_DTL_STEPSALARY';

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

	function filterSalCurDet($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_PR_SALARY_CURRENCY_DETAIL';
        $arrFieldList[0] = 'SAL_GRD_CODE';
		$arrFieldList[1] = 'CURRENCY_ID';
		$arrFieldList[2] = 'SALCURR_DTL_MINSALARY';
		$arrFieldList[3] = 'SALCURR_DTL_MAXSALARY';
		$arrFieldList[4] = 'SALCURR_DTL_STEPSALARY';

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

function getUnAssSalCurDet($salgrd) {
		
		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_CURRENCY_TYPE';			
		$arrFieldList[0] = 'CURRENCY_ID';
		$arrFieldList[1] = 'CURRENCY_NAME';

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;
		$sql_builder->field = 'CURRENCY_ID';
		$sql_builder->table2_name= 'HS_PR_SALARY_CURRENCY_DETAIL';
		$arr[0][0]= 'SAL_GRD_CODE';
		$arr[0][1]= $salgrd;

		$sqlQString = $sql_builder->selectFilter($arr);

		$dbConnection = new DMLFunctions();
       		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$common_func = new CommonFunctions();

		$i=0;

		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {

	    	$arrayDispList[$i][0] = $line[0];
	    	$arrayDispList[$i][1] = $line[1];

	    	$i++;
	     }

	     if (isset($arrayDispList)) {

	       	return $arrayDispList;

	     } else {
	     	//Handle Exceptions
	     	//Create Logs
	     }
	}

function getAssSalCurDet($salgrd) {
		
		$sql_builder = new SQLQBuilder();
		
		$sqlQString = $sql_builder->getCurrencyAssigned($salgrd);
		
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
