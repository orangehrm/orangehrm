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
require_once ROOT_PATH . '/lib/common/UniqueIDGenerator.php';

class SalaryGrades {

	var $tableName = 'HS_PR_SALARY_GRADE';

	var $salgrdId;
	var $salgrdDesc;
	var $arrayDispList;
	var $singleField;


	function SalaryGrades() {

	}

	function setSalGrdId($salgrdId) {

		$this->salgrdId = $salgrdId;

	}

	function setSalGrdDesc($salgrdDesc) {

		$this->salgrdDesc = $salgrdDesc;
	}


	function getSalGrdId() {

		return $this->salgrdId;

	}

	function getSalGrdDesc() {

		return $this->salgrdDesc;

	}

	function getListofCashBenefits($pageNO,$schStr,$mode, $sortField = 0, $sortOrder = 'ASC') {

		$tableName = 'HS_PR_SALARY_GRADE';
		$arrFieldList[0] = 'SAL_GRD_CODE';
		$arrFieldList[1] = 'SAL_GRD_NAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_CASH_BEN_SALGRADE';
		$sql_builder->field = 'SAL_GRD_CODE';
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->selectMultipleTab($pageNO,$schStr,$mode, $sortField, $sortOrder);

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

	function countCashBenefits($schStr,$mode) {

		$tableName = 'HS_PR_SALARY_GRADE';
		$arrFieldList[0] = 'SAL_GRD_CODE';
		$arrFieldList[1] = 'SAL_GRD_NAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name='HS_HR_CASH_BEN_SALGRADE';
		$sql_builder->flg_select = 'true';
		$sql_builder->field='SAL_GRD_CODE';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->countMultipleTab($schStr,$mode);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$line = mysql_fetch_array($message2, MYSQL_NUM);

	    	return $line[0];
	}

	function getUnAssCashBenefits($pageNO,$schStr,$mode) {

		$tableName = 'HS_PR_SALARY_GRADE';
		$arrFieldList[0] = 'SAL_GRD_CODE';
		$arrFieldList[1] = 'SAL_GRD_NAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_CASH_BEN_SALGRADE';
		$sql_builder->field = 'SAL_GRD_CODE';
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

	function countUnAssCashBenefits($schStr,$mode) {

		$tableName = 'HS_PR_SALARY_GRADE';
		$arrFieldList[0] = 'SAL_GRD_CODE';
		$arrFieldList[1] = 'SAL_GRD_NAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name='HS_HR_CASH_BEN_SALGRADE';
		$sql_builder->flg_select = 'true';
		$sql_builder->field='SAL_GRD_CODE';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->countResultFilter($schStr,$mode);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$line = mysql_fetch_array($message2, MYSQL_NUM);

	    	return $line[0];
	}

	function getSalGrades() {

		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_PR_SALARY_GRADE';
		$arrFieldList[0] = 'SAL_GRD_CODE';
		$arrFieldList[1] = 'SAL_GRD_NAME';

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultSetMessage();

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

	function getListofSalaryGrades($pageNO,$schStr,$mode, $sortField = 0, $sortOrder = 'ASC') {

		$tableName = 'HS_PR_SALARY_GRADE';
		$arrFieldList[0] = 'SAL_GRD_CODE';
		$arrFieldList[1] = 'SAL_GRD_NAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultSetMessage($pageNO,$schStr,$mode, $sortField, $sortOrder);

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

	function countSalaryGrades($schStr,$mode) {

		$tableName = 'HS_PR_SALARY_GRADE';
		$arrFieldList[0] = 'SAL_GRD_CODE';
		$arrFieldList[1] = 'SAL_GRD_NAME';

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

	function delSalaryGrades($arrList) {

		$tableName = 'HS_PR_SALARY_GRADE';
		$arrFieldList[0] = 'SAL_GRD_CODE';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_delete = 'true';
		$sql_builder->arr_delete = $arrFieldList;

		$sqlQString = $sql_builder->deleteRecord($arrList);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		return $message2;
	}


	function addSalaryGrades() {

		$tableName = 'HS_PR_SALARY_GRADE';

		$this->salgrdId = UniqueIDGenerator::getInstance()->getNextID($tableName, 'SAL_GRD_CODE', 'SAL');
		$arrFieldList[0] = "'". $this->getSalGrdId() . "'";
		$arrFieldList[1] = "'". $this->getSalGrdDesc() . "'";

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_insert = 'true';
		$sql_builder->arr_insert = $arrFieldList;


		$sqlQString = $sql_builder->addNewRecordFeature1();

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		return $message2;
	}

	function updateSalaryGrades() {

		$this->getSalGrdId();
		$arrRecordsList[0] = "'". $this->getSalGrdId() . "'";
		$arrRecordsList[1] = "'". $this->getSalGrdDesc() . "'";
		$arrFieldList[0] = 'SAL_GRD_CODE';
		$arrFieldList[1] = 'SAL_GRD_NAME';

		$tableName = 'HS_PR_SALARY_GRADE';

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


	function filterSalaryGrades($getID) {

		$this->getID = $getID;
		$tableName = 'HS_PR_SALARY_GRADE';
		$arrFieldList[0] = 'SAL_GRD_CODE';
		$arrFieldList[1] = 'SAL_GRD_NAME';

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

	function getListofNonCashBenefits($pageNO,$schStr,$mode) {

		$tableName = 'HS_PR_SALARY_GRADE';
		$arrFieldList[0] = 'SAL_GRD_CODE';
		$arrFieldList[1] = 'SAL_GRD_NAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_NONCASH_BEN_SALGRADE';
		$sql_builder->field = 'SAL_GRD_CODE';
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->selectMultipleTab($pageNO,$schStr,$mode);

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

	function countNonCashBenefits($schStr,$mode) {

		$tableName = 'HS_PR_SALARY_GRADE';
		$arrFieldList[0] = 'SAL_GRD_CODE';
		$arrFieldList[1] = 'SAL_GRD_NAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name='HS_HR_NONCASH_BEN_SALGRADE';
		$sql_builder->flg_select = 'true';
		$sql_builder->field='SAL_GRD_CODE';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->countMultipleTab($schStr,$mode);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$line = mysql_fetch_array($message2, MYSQL_NUM);

	    	return $line[0];
	}

	function getUnAssNonCashBenefits($pageNO,$schStr,$mode) {

		$tableName = 'HS_PR_SALARY_GRADE';
		$arrFieldList[0] = 'SAL_GRD_CODE';
		$arrFieldList[1] = 'SAL_GRD_NAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_NONCASH_BEN_SALGRADE';
		$sql_builder->field = 'SAL_GRD_CODE';
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

	function countUnAssNonCashBenefits($schStr,$mode) {

		$tableName = 'HS_PR_SALARY_GRADE';
		$arrFieldList[0] = 'SAL_GRD_CODE';
		$arrFieldList[1] = 'SAL_GRD_NAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name='HS_HR_NONCASH_BEN_SALGRADE';
		$sql_builder->flg_select = 'true';
		$sql_builder->field='SAL_GRD_CODE';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->countResultFilter($schStr,$mode);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$line = mysql_fetch_array($message2, MYSQL_NUM);

	    	return $line[0];
	}

	function getSalGrdCodes() {

		$tableName = 'HS_PR_SALARY_GRADE';
		$arrFieldList[0] = 'SAL_GRD_CODE';
		$arrFieldList[1] = 'SAL_GRD_NAME';

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
