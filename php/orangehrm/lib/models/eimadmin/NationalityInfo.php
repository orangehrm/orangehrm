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

class NationalityInfo {

	var $tableName = 'HS_HR_NATIONALITY';
	var $nationalityId;
	var $nationalityDesc;
	var $arrayDispList;
	var $singleField;

	function NationalityInfo() {
	}

	function setNationalityInfoId($nationalityId) {
		$this->nationalityId = $nationalityId;
	}

	function setNationalityInfoDesc($nationalityDesc) {
		$this->nationalityDesc = $nationalityDesc;
	}

	function getNationalityInfoId() {
		return $this->nationalityId;
	}

	function getNationalityInfoDesc() {
		return $this->nationalityDesc;
	}

	function getListofNationalityInfo($pageNO,$schStr,$mode,$sortField = 0, $sortOrder = 'ASC') {

		$tableName = 'HS_HR_NATIONALITY';
		$arrFieldList[0] = 'NAT_CODE';
		$arrFieldList[1] = 'NAT_NAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultSetMessage($pageNO,$schStr,$mode,$sortField, $sortOrder);

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

	function countNationalityInfo($schStr,$mode) {

		$tableName = 'HS_HR_NATIONALITY';
		$arrFieldList[0] = 'NAT_CODE';
		$arrFieldList[1] = 'NAT_NAME';

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

	function delNationalityInfo($arrList) {

		$tableName = 'HS_HR_NATIONALITY';
		$arrFieldList[0] = 'NAT_CODE';

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


	function addNationalityInfo() {

		if ($this->_isDuplicateName()) {
			throw new NationalityInfoException("Duplicate name", 1);
		}

		$tableName = 'hs_hr_nationality';

		$this->nationalityId = UniqueIDGenerator::getInstance()->getNextID($tableName, 'nat_code', 'NAT');
		$arrFieldList[0] = "'". $this->getNationalityInfoId() . "'";
		$arrFieldList[1] = "'". $this->getNationalityInfoDesc() . "'";

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_insert = 'true';
		$sql_builder->arr_insert = $arrFieldList;

		$sqlQString = $sql_builder->addNewRecordFeature1();

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		return $message2;
	}

	function updateNationalityInfo() {

		if ($this->_isDuplicateName(true)) {
			throw new NationalityInfoException("Duplicate name", 1);
		}

		$this->getNationalityInfoId();
		$arrRecordsList[0] = "'". $this->getNationalityInfoId() . "'";
		$arrRecordsList[1] = "'". $this->getNationalityInfoDesc() . "'";
		$arrFieldList[0] = 'NAT_CODE';
		$arrFieldList[1] = 'NAT_NAME';

		$tableName = 'HS_HR_NATIONALITY';

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


	function filterNationalityInfo($getID) {

		$this->getID = $getID;
		$tableName = 'HS_HR_NATIONALITY';
		$arrFieldList[0] = 'NAT_CODE';
		$arrFieldList[1] = 'NAT_NAME';

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

	function getNationCodes() {

		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_NATIONALITY';
		$arrFieldList[0] = 'NAT_CODE';
		$arrFieldList[1] = 'NAT_NAME';

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultSetMessage(0, '', -1, 1);

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
	     }
	}

	private function _isDuplicateName($update=false) {
		$nationalities = $this->getListofNationalityInfo(0, $this->getNationalityInfoDesc(), 1);

		if (is_array($nationalities)) {
			if ($update) {
				if ($nationalities[0][0] == $this->getNationalityInfoId()) {
					return false;
				}
			}
			return true;
		}

		return false;
	}
}

class NationalityInfoException extends Exception {
}
?>
