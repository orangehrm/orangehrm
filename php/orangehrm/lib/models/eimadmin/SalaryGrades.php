<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

require_once ROOT_PATH . '/lib/confs/Conf.php';
require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';
require_once ROOT_PATH . '/lib/dao/SQLQBuilder.php';
require_once ROOT_PATH . '/lib/common/CommonFunctions.php';
require_once ROOT_PATH . '/lib/common/UniqueIDGenerator.php';

class SalaryGrades {

	public $tableName = 'HS_PR_SALARY_GRADE';

	public $salgrdId;
	public $salgrdDesc;
	public $arrayDispList;
	public $singleField;


	public function SalaryGrades() {

	}

	public function setSalGrdId($salgrdId) {

		$this->salgrdId = $salgrdId;

	}

	public function setSalGrdDesc($salgrdDesc) {

		$this->salgrdDesc = $salgrdDesc;
	}


	public function getSalGrdId() {

		return $this->salgrdId;

	}

	public function getSalGrdDesc() {

		return $this->salgrdDesc;

	}

	public function getListofCashBenefits($pageNO,$schStr,$mode, $sortField = 0, $sortOrder = 'ASC') {

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

	public function countCashBenefits($schStr,$mode) {

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

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$line = mysql_fetch_array($message2, MYSQL_NUM);

	    	return $line[0];
	}

	public function getUnAssCashBenefits($pageNO,$schStr,$mode) {

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

	public function countUnAssCashBenefits($schStr,$mode) {

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

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$line = mysql_fetch_array($message2, MYSQL_NUM);

	    	return $line[0];
	}

	public function getSalGrades($fetchDetailedSalGradesOnly = false) {

		$sql_builder = new SQLQBuilder();

		$tableName = "`hs_pr_salary_grade`";
		$arrFieldList[0] = "`sal_grd_code`";
		$arrFieldList[1] = "`sal_grd_name`";

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		if ($fetchDetailedSalGradesOnly) {
			$subQueryTable = "`hs_pr_salary_currency_detail`";
			$subQueryFields[0] = "DISTINCT(`sal_grd_code`)";
			$subQuery = $sql_builder->simpleSelect($subQueryTable, $subQueryFields);
			$selectConditions[0] = "{$arrFieldList[0]} IN ($subQuery)";

			$sqlQString = $sql_builder->simpleSelect($tableName, $arrFieldList, $selectConditions);
		} else {
		$sqlQString = $sql_builder->passResultSetMessage();
		}

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

	     	//Handle Exceptions
	     	//Create Logs

	     }

	}

	public function getListofSalaryGrades($pageNO,$schStr,$mode, $sortField = 0, $sortOrder = 'ASC') {

		$tableName = 'HS_PR_SALARY_GRADE';
		$arrFieldList[0] = 'SAL_GRD_CODE';
		$arrFieldList[1] = 'SAL_GRD_NAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultSetMessage($pageNO,$schStr,$mode, $sortField, $sortOrder);

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

	public function countSalaryGrades($schStr,$mode) {

		$tableName = 'HS_PR_SALARY_GRADE';
		$arrFieldList[0] = 'SAL_GRD_CODE';
		$arrFieldList[1] = 'SAL_GRD_NAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->countResultset($schStr,$mode);

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$line = mysql_fetch_array($message2, MYSQL_NUM);

	    	return $line[0];
	}

	public function delSalaryGrades($arrList) {

		$tableName = 'HS_PR_SALARY_GRADE';
		$arrFieldList[0] = 'SAL_GRD_CODE';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_delete = 'true';
		$sql_builder->arr_delete = $arrFieldList;

		$sqlQString = $sql_builder->deleteRecord($arrList);

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		return $message2;
	}


	public function addSalaryGrades() {

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
		$message = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		if ($message) {
			return $message;
		} else {
			$errCode = mysql_errno();
			
			switch ($errCode) {
				case 1062 :
					$e = new SalaryGradesException('Pay Grades cannot have duplicate names', SalaryGradesException::DUPLICATE_SALARY_GRADE);
					break;
					
				default :
					$e = new SalaryGradesException('Unknown error in when adding Pay Grades', SalaryGradesException::UNKNOWN_EXCEPTION);
					break;
			}
			
			throw $e;
		}
	}

	public function updateSalaryGrades() {
		
		if ($this->isSalaryGradeNameExists($this->getSalGrdDesc())) {
		    throw new SalaryGradesException('Salary grade name already exists', SalaryGradesException::UNKNOWN_EXCEPTION); // Error code is set to comply with ViewController.php
		}

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
	
	public function isSalaryGradeNameExists($salGradeName) {	    
	    
	    $selectTable = '`hs_pr_salary_grade`';	    
	    $selectFields[] = '`sal_grd_code`';	    
	    $selectConditions[] = "`sal_grd_name` = '$salGradeName'";
	    
	    $sqlBuilder = new SQLQBuilder();
	    $query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions);
	    
	    $dbConnection = new DMLFunctions();
	    $result = $dbConnection->executeQuery($query);
	    
	    if ($dbConnection->dbObject->numberOfRows($result) > 0) {
	        return true;
	    } else {
	        return false;
	    }
	    
	}

	public function filterSalaryGrades($getID) {

		$this->getID = $getID;
		$tableName = 'HS_PR_SALARY_GRADE';
		$arrFieldList[0] = 'SAL_GRD_CODE';
		$arrFieldList[1] = 'SAL_GRD_NAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->selectOneRecordFiltered($this->getID);

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

	public function getListofNonCashBenefits($pageNO,$schStr,$mode) {

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

	public function countNonCashBenefits($schStr,$mode) {

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

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$line = mysql_fetch_array($message2, MYSQL_NUM);

	    	return $line[0];
	}

	public function getUnAssNonCashBenefits($pageNO,$schStr,$mode) {

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

	public function countUnAssNonCashBenefits($schStr,$mode) {

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

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$line = mysql_fetch_array($message2, MYSQL_NUM);

	    	return $line[0];
	}

	public function getSalGrdCodes() {

		$tableName = 'HS_PR_SALARY_GRADE';
		$arrFieldList[0] = 'SAL_GRD_CODE';
		$arrFieldList[1] = 'SAL_GRD_NAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultSetMessage();

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

class SalaryGradesException extends Exception {
	
	const UNKNOWN_EXCEPTION			= 1;
	const DUPLICATE_SALARY_GRADE	= 2;

}
?>
