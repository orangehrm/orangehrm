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

class Licenses {

	var $tableName = 'HS_HR_LICENSES';
	var $LicensesId;
	var $LicensesDesc;
	var $arrayDispList;
	var $singleField;


	function Licenses() {

	}

	function setLicensesId($LicensesId) {

		$this->LicensesId = $LicensesId;

	}

	function setLicensesDesc($LicensesDesc) {

		$this->LicensesDesc = $LicensesDesc;

	}


	function getLicensesId() {

		return $this->LicensesId;

	}

	function getLicensesDesc() {

		return $this->LicensesDesc;

	}


	function getListofLicenses($pageNO,$schStr,$mode,$sortField = 0, $sortOrder = 'ASC') {

		$tableName = 'HS_HR_LICENSES';
		$arrFieldList[0] = 'LICENSES_CODE';
		$arrFieldList[1] = 'LICENSES_DESC';

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

	function countLicenses($schStr,$mode) {

		$tableName = 'HS_HR_LICENSES';
		$arrFieldList[0] = 'LICENSES_CODE';
		$arrFieldList[1] = 'LICENSES_DESC';

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

	function delLicenses($arrList) {

		$tableName = 'HS_HR_LICENSES';
		$arrFieldList[0] = 'LICENSES_CODE';

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


	function addLicenses() {

		if ($this->_isDuplicateName($this->getLicensesDesc())) {
			throw new LicensesException("Duplicate name", 1);
		}
		
		$tableName = 'HS_HR_LICENSES';

		$this->LicensesId = UniqueIDGenerator::getInstance()->getNextID($tableName, 'LICENSES_CODE', 'LIC');
		$arrFieldList[0] = "'". $this->getLicensesId() . "'";
		$arrFieldList[1] = "'". $this->getLicensesDesc() . "'";

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_insert = 'true';
		$sql_builder->arr_insert = $arrFieldList;

		$sqlQString = $sql_builder->addNewRecordFeature1();

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		 return $message2;

	}

	function updateLicenses() {

		if ($this->_isDuplicateName($this->getLicensesDesc())) {
			throw new LicensesException("Duplicate name", 1);
		}
		
		$this->getLicensesId();
		$arrRecordsList[0] = "'". $this->getLicensesId() . "'";
		$arrRecordsList[1] = "'". $this->getLicensesDesc() . "'";


		$arrFieldList[0] = 'LICENSES_CODE';
		$arrFieldList[1] = 'LICENSES_DESC';

		$tableName = 'HS_HR_LICENSES';

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

	function getUnAssLicensesCodes($id) {

		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_LICENSES';
		$arrFieldList[0] = 'LICENSES_CODE';
		$arrFieldList[1] = 'LICENSES_DESC';

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;
		$sql_builder->field = 'LICENSES_CODE';
		$sql_builder->table2_name = 'HS_HR_EMP_LICENSES';

		$arr[0][0]='EMP_NUMBER';
		$arr[0][1]=$id;
		$sqlQString = $sql_builder->selectFilter($arr,1);

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

			return false;

		}
	}

	function filterLicenses($getID) {

		$this->getID = $getID;
		$tableName = 'HS_HR_LICENSES';
		$arrFieldList[0] = 'LICENSES_CODE';
		$arrFieldList[1] = 'LICENSES_DESC';


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

	function getLicensesCodes () {

		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_LICENSES';
		$arrFieldList[0] = 'LICENSES_CODE';
		$arrFieldList[1] = 'LICENSES_DESC';

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
	
	private function _isDuplicateName($licenses) {
		
		$selectTable = $this->tableName;
		$selectFields[] = '`licenses_desc`';
	    $selectConditions[] = "`licenses_desc` = '$licenses'";	    
	    
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
}
class LicensesException extends Exception {
}
?>
