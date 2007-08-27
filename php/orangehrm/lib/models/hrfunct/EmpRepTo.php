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
require_once ROOT_PATH . '/lib/models/hrfunct/EmpInfo.php';

class EmpRepTo {

	var $tableName = 'HS_HR_EMP_REPORTTO';

	var $empId;
	var $empSupId;
	var $empSubId;
	var $empRepMod;

	var $arrayDispList;
	var $singleField;

	var $employeeIdLength;

	function EmpRepTo() {
		$sysConfObj = new sysConf();

		$this->employeeIdLength = $sysConfObj->getEmployeeIdLength();
	}

	function setEmpId($empId) {

	$this->empId=$empId;
	}

	function setEmpSupId($empSupId) {

	$this->empSupId=$empSupId;
	}

	function setEmpSubId($empSubId) {

	$this->empSubId=$empSubId;
	}

	function setEmpRepMod($empRepMod) {

	$this->empRepMod=$empRepMod;
	}

	function getEmpId() {

	return $this->empId;
	}

	function getEmpSupId() {

	return $this->empSupId;
	}

	function getEmpSubId() {

	return $this->empSubId;
	}

	function getEmpRepMod() {

	return $this->empRepMod;
	}


	function getListofEmpRepTo($page,$str,$mode) {

		$tableName = 'HS_HR_EMP_REPORTTO';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->field = 'LPAD(`EMP_NUMBER`, '.$this->employeeIdLength.', 0)';

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


	function countEmpRepTo($str,$mode) {

		$tableName = 'HS_HR_EMP_REPORTTO';

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


	function delEmpRepTo($arrList) {

		$tableName = 'HS_HR_EMP_REPORTTO';
		$arrFieldList[0] = 'EREP_SUP_EMP_NUMBER';
		$arrFieldList[1] = 'EREP_SUB_EMP_NUMBER';
		$arrFieldList[2] = 'EREP_REPORTING_MODE';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_delete = 'true';
		$sql_builder->arr_delete = $arrFieldList;

		$sqlQString = $sql_builder->deleteRecord($arrList);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

	}

	function addEmpRepTo() {

		$arrRecordsList[0] = "'". $this->getEmpSupId() . "'";
		$arrRecordsList[1] = "'". $this->getEmpSubId() . "'";
		$arrRecordsList[2] = "'". $this->getEmpRepMod() . "'";

		$tableName = 'HS_HR_EMP_REPORTTO';

		$arrFieldList[0] = 'EREP_SUP_EMP_NUMBER';
		$arrFieldList[1] = 'EREP_SUB_EMP_NUMBER';
		$arrFieldList[2] = 'EREP_REPORTING_MODE';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_insert = 'true';
		$sql_builder->arr_insertfield = $arrFieldList;
		$sql_builder->arr_insert = $arrRecordsList;

		$sqlQString = $sql_builder->addNewRecordFeature2(true, true);

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		return $message2;
	}

	function updateEmpRepTo($supEmpID,$subEmpID,$oldRepMethod,$newRepMethod) {

		$sqlQString = "UPDATE hs_hr_emp_reportto SET EREP_SUP_EMP_NUMBER='".$supEmpID."', EREP_SUB_EMP_NUMBER='".$subEmpID."', EREP_REPORTING_MODE='".$newRepMethod."' WHERE EREP_SUP_EMP_NUMBER='".$supEmpID."' AND EREP_SUB_EMP_NUMBER='".$subEmpID."' AND EREP_REPORTING_MODE='".$oldRepMethod."'";

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		return $message2;


	}


	function filterEmpRepTo($getID) {

		$this->getID = $getID;
		$tableName = 'HS_HR_EMP_REPORTTO a, HS_HR_EMPLOYEE b';
		$arrFieldList[0] = 'LPAD(a.`EREP_SUP_EMP_NUMBER`, '.$this->employeeIdLength.', 0)';
		$arrFieldList[1] = 'LPAD(a.`EREP_SUB_EMP_NUMBER`, '.$this->employeeIdLength.', 0)';
		$arrFieldList[2] = 'a.EREP_REPORTING_MODE';
		$arrFieldList[3] = 'b.EMPLOYEE_ID';

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

	function getEmpSup($getID) {

		$this->getID = $getID;
		$tableName = 'HS_HR_EMP_REPORTTO a';
		$arrFieldList[0] = 'LPAD(a.`EREP_SUB_EMP_NUMBER`, '.$this->employeeIdLength.', 0)';
		$arrFieldList[1] = 'LPAD(a.`EREP_SUP_EMP_NUMBER`, '.$this->employeeIdLength.', 0)';
		$arrFieldList[2] = 'a.EREP_REPORTING_MODE';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->selectOneRecordFiltered($this->getID);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$i=0;

		$empInfoObj = new EmpInfo();

		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {

			for($c=0;count($arrFieldList)>$c;$c++)
			   $arrayDispList[$i][$c] = $line[$c];

			$supervisorId = $empInfoObj->fetchEmployeeId($line[0]);
		 	if ($supervisorId) {
		 		$arrayDispList[$i][count($arrFieldList)] = $supervisorId;
		 	} else {
		 		$arrayDispList[$i][count($arrFieldList)] = $line[0];
		 	}

		 	$subordinateId = $empInfoObj->fetchEmployeeId($line[1]);
		 	if ($subordinateId) {
		 		$arrayDispList[$i][(count($arrFieldList)+1)] = $subordinateId;
		 	} else {
		 		$arrayDispList[$i][(count($arrFieldList)+1)] = $line[1];
		 	}

	    	$i++;

	     }

	     if (isset($arrayDispList)) {

			return $arrayDispList;

		} else {

			$arrayDispList = '';
			return $arrayDispList;

		}

	}

	function getEmpSub($getID) {

		$this->getID = $getID;
		$tableName = 'HS_HR_EMP_REPORTTO a';
		$arrFieldList[0] = 'LPAD(a.`EREP_SUP_EMP_NUMBER`, '.$this->employeeIdLength.', 0)';
		$arrFieldList[1] = 'LPAD(a.`EREP_SUB_EMP_NUMBER`, '.$this->employeeIdLength.', 0)';
		$arrFieldList[2] = 'a.EREP_REPORTING_MODE';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->selectOneRecordFiltered($this->getID);

		//echo $sqlQString."\n";
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$i=0;

		$empInfoObj = new EmpInfo();

		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {

			for($c=0;count($arrFieldList)>$c;$c++)
			   $arrayDispList[$i][$c] = $line[$c];

			$supervisorId = $empInfoObj->fetchEmployeeId($line[0]);
		 	if ($supervisorId) {
		 		$arrayDispList[$i][count($arrFieldList)] = $supervisorId;
		 	} else {
		 		$arrayDispList[$i][count($arrFieldList)] = $line[0];
		 	}

		 	$subordinateId = $empInfoObj->fetchEmployeeId($line[1]);
		 	if ($subordinateId) {
		 		$arrayDispList[$i][(count($arrFieldList)+1)] = $subordinateId;
		 	} else {
		 		$arrayDispList[$i][(count($arrFieldList)+1)] = $line[1];
		 	}

	    	$i++;

	     }

	     if (isset($arrayDispList)) {

			return $arrayDispList;

		} else {

			$arrayDispList = '';
			return $arrayDispList;

		}

	}

	function getEmpSubDetails($getID) {

		$sqlBuilder = new SQLQBuilder();

		$arrFields[0] = 'LPAD(b.`emp_number`, '.$this->employeeIdLength.', 0)';
		$arrFields[1] = "CONCAT(b.`emp_firstname`, ' ', b.`emp_lastname`)";
		$arrFields[2] = "b.`employee_id`";

		$arrTables[0] = '`hs_hr_emp_reportto` a';
		$arrTables[1] = '`hs_hr_employee` b';

		$joinConditions[1] = "a.`erep_sub_emp_number` = b.`emp_number`";

		$selectConditions[1] = "a.`erep_sup_emp_number` = '".$getID."'";

		$query = $sqlBuilder->selectFromMultipleTable($arrFields, $arrTables, $joinConditions, $selectConditions);

		//echo $query."\n";

		$dbConnection = new DMLFunctions();

		$result = $dbConnection -> executeQuery($query);

		$i=0;

		while ($line = mysql_fetch_array($result, MYSQL_NUM)) {

			for($c=0;count($arrFields)>$c;$c++)
			   $arrayDispList[$i][$c] = $line[$c];

	    	$i++;
	    }

	    return $arrayDispList;
	}

	function getEmpSupDetails($getID) {

		$sqlBuilder = new SQLQBuilder();

		$arrFields[0] = 'LPAD(b.`emp_number`, '.$this->employeeIdLength.', 0)';
		$arrFields[1] = "CONCAT(b.`emp_firstname`, ' ', b.`emp_lastname`)";
		$arrFields[2] = "b.`employee_id`";

		$arrTables[0] = '`hs_hr_emp_reportto` a';
		$arrTables[1] = '`hs_hr_employee` b';

		$joinConditions[1] = "a.`erep_sup_emp_number` = b.`emp_number`";

		$selectConditions[1] = "a.`erep_sub_emp_number` = '".$getID."'";

		$query = $sqlBuilder->selectFromMultipleTable($arrFields, $arrTables, $joinConditions, $selectConditions, null, 'a.`erep_reporting_mode`', null);

		//echo $query."\n";

		$dbConnection = new DMLFunctions();

		$result = $dbConnection -> executeQuery($query);

		$i=0;

		$arrayDispList = null;

		while ($line = mysql_fetch_array($result, MYSQL_NUM)) {

			for($c=0;count($arrFields)>$c;$c++)
			   $arrayDispList[$i][$c] = $line[$c];

	    	$i++;
	    }

	    return $arrayDispList;
	}

    /**
     * Get list of subordinates for supervisors with a name that matches the search term.
     * The supervisors first, middle and lastnames are searched and the subordinates of
     * matching supervisors are returned.
     *
     * @param searchTerm The search term to match with the supervisor names
     * @return Array of matching employee ID's
     */
    public function getSubordinatesOfSupervisorWithName($searchTerm) {

		$sqlBuilder = new SQLQBuilder();

		$arrFields[0] = 'DISTINCT r.`erep_sub_emp_number`';

		$arrTables[0] = '`hs_hr_emp_reportto` r';
		$arrTables[1] = '`hs_hr_employee` e';

		$joinConditions[1] = "r.`erep_sup_emp_number` = e.`emp_number`";

        $filteredSearchTerm = mysql_real_escape_string($searchTerm);
		$selectConditions[1] = "e.`emp_firstname`   LIKE '" . $filteredSearchTerm . "%' OR "
                             . "e.`emp_lastname`    LIKE '" . $filteredSearchTerm . "%' OR "
                             . "e.`emp_middle_name` LIKE '" . $filteredSearchTerm . "%'";

		$query = $sqlBuilder->selectFromMultipleTable($arrFields, $arrTables, $joinConditions, $selectConditions, null, null, null);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection -> executeQuery($query);

        $subordinateIds = null;
        $rowNum = 0;

		while ($line = mysql_fetch_array($result, MYSQL_NUM)) {

            $subordinateIds[$rowNum] = $line[0];
            $rowNum++;
	    }

	    return $subordinateIds;
    }
}

?>
