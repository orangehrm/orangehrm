<?php
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

class EmploymentStatus {

	var $tableName = 'HS_HR_EMPSTAT';

	var $empStatId;
	var $empStatName;
	var $arrayDispList;
	var $singleField;

	function EmploymentStatus() {

	}

	function setEmpStatId($empStatId) {

		$this->empStatId = $empStatId;
	}

	function setEmpStatName($empStatName) {
		$this->empStatName = $empStatName;
	}

	function getEmpStatId() {

		return $this->empStatId;
	}

	function getEmpStatName() {
		return $this->empStatName;
	}

	function getListofEmpStat($pageNO,$schStr,$mode, $sortField = 0, $sortOrder = 'ASC') {

		$tableName = 'HS_HR_EMPSTAT';
		$arrFieldList[0] = 'ESTAT_CODE';
		$arrFieldList[1] = 'ESTAT_NAME';

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

	function countEmpStat($schStr,$mode) {

		$tableName = 'HS_HR_EMPSTAT';
		$arrFieldList[0] = 'ESTAT_CODE';
		$arrFieldList[1] = 'ESTAT_NAME';

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

	function delEmpStat($arrList) {

		$tableName = 'HS_HR_EMPSTAT';
		$arrFieldList[0] = 'ESTAT_CODE';

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

	function addEmpStat() {

		$arrFieldList[0] = "'". $this->getEmpStatId() . "'";
		$arrFieldList[1] = "'". $this->getEmpStatName() . "'";

		$tableName = 'HS_HR_EMPSTAT';

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

	function updateEmpStat() {

		$arrRecordsList[0] = "'". $this->getEmpStatId() . "'";
		$arrRecordsList[1] = "'". $this->getEmpStatName() . "'";

		$arrFieldList[0] = 'ESTAT_CODE';
		$arrFieldList[1] = 'ESTAT_NAME';

		$tableName = 'HS_HR_EMPSTAT';

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

	function getEmpStat() {

		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_EMPSTAT';
		$arrFieldList[0] = 'ESTAT_CODE';
		$arrFieldList[1] = 'ESTAT_NAME';

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultSetMessage();
//echo $sqlQString;
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

	function filterEmpStat($getID) {

		$arrFieldList[0] = 'ESTAT_CODE';
		$arrFieldList[1] = 'ESTAT_NAME';

		$tableName = 'HS_HR_EMPSTAT';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->selectOneRecordFiltered($getID);

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
		$tableName = 'HS_HR_EMPSTAT';
		$arrFieldList[0] = 'ESTAT_CODE';

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

		return $common_func->explodeString($this->singleField,"EST");
		}
	}
}

?>
