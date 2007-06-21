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

class EmpPicture {

	var $tableName = 'HS_HR_EMP_PICTURE';

	var $empId;
	var $empPicture;
	var $empPicFilename;
	var $empPicSize;
	var $empPicType;
	var $arrayDispList;
	var $singleField;

	function EmpPicture() {

	}

	function setEmpId($empId) {

	$this->empId=$empId;
	}

	function setEmpPicture($empPicture) {

		$this->empPicture = $empPicture;
	}

	function setEmpFilename($empPicFilename) {

		$this->empPicFilename = $empPicFilename;
	}

	function setEmpPicType($empPicType) {

		$this->empPicType = $empPicType;
	}

	function setEmpPicSize($empPicSize) {

		$this->empPicSize = $empPicSize;
	}

	function getEmpId() {

	return $this->empId;
	}

	function getEmpPicture() {

		return $this->empPicture;
	}

	function getEmpFilename() {

		return $this->empPicFilename;
	}

	function getEmpPicType() {

		return $this->empPicType;
	}

	function getEmpPicSize() {

		return $this->empPicSize;
	}

////

	function delEmpPic($arrList) {

		$tableName = 'HS_HR_EMP_PICTURE';
		$arrFieldList[0] = 'EMP_NUMBER';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_delete = 'true';
		$sql_builder->arr_delete = $arrFieldList;

		$sqlQString = $sql_builder->deleteRecord($arrList);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

	}

	function addEmpPic() {

		$arrFieldList[0] = "'". $this->getEmpId() . "'";
		$arrFieldList[1] = "'". $this->getEmpPicture() . "'";
		$arrFieldList[2] = "'". $this->getEmpFilename() . "'";
		$arrFieldList[3] = "'". $this->getEmpPicType() . "'";
		$arrFieldList[4] = "'". $this->getEmpPicSize() . "'";

		$tableName = 'HS_HR_EMP_PICTURE';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_insert = 'true';
		$sql_builder->arr_insert = $arrFieldList;

		$sqlQString = $sql_builder->addNewRecordFeature1();

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		 return $message2;
	}

	function updateEmpPic() {

		$arrRecordsList[0] = "'". $this->getEmpId() . "'";
		$arrRecordsList[1] = "'". $this->getEmpPicture() . "'";
		$arrRecordsList[2] = "'". $this->getEmpFilename() . "'";
		$arrRecordsList[3] = "'". $this->getEmpPicType() . "'";
		$arrRecordsList[4] = "'". $this->getEmpPicSize() . "'";

		$tableName = 'HS_HR_EMP_PICTURE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EPIC_PICTURE';
		$arrFieldList[2] = 'EPIC_FILENAME';
		$arrFieldList[3] = 'EPIC_TYPE';
		$arrFieldList[4] = 'EPIC_FILE_SIZE';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_update = 'true';
		$sql_builder->arr_update = $arrFieldList;
		$sql_builder->arr_updateRecList = $arrRecordsList;

		$sqlQString = $sql_builder->addUpdateRecord1(0);

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		return $message2;
	}


	function filterEmpPic($getID) {

		$tableName = 'HS_HR_EMP_PICTURE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EPIC_PICTURE';
		$arrFieldList[2] = 'EPIC_FILENAME';
		$arrFieldList[3] = 'EPIC_TYPE';
		$arrFieldList[4] = 'EPIC_FILE_SIZE';

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
