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

require_once ROOT_PATH  . '/lib/confs/Conf.php';
require_once ROOT_PATH  . '/lib/dao/DMLFunctions.php';
require_once ROOT_PATH  . '/lib/dao/SQLQBuilder.php';
require_once ROOT_PATH  . '/lib/common/CommonFunctions.php';

class DistrictInfo {

	var $tableName;
	var $districtId;
	var $districtDesc;
	var $provinceId;

	var $arrayDispList;
	var $singleField;


	function DistrictInfo() {

	}

	function setDistrictInfoId($districtId) {

		$this->districtId = $districtId;

	}

	function setDistrictInfoDesc($districtDesc) {

		$this->districtDesc = $districtDesc;

	}

	function setProvinceId($provinceId) {

		$this->provinceId = $provinceId;

	}


	function getDistrictInfoId() {

		return $this->districtId;

	}

	function getDistrictInfoDesc() {

		return $this->districtDesc;

	}

	function getProvinceId() {

		return $this->provinceId;
		//echo $this->provinceId;

	}


	function getListofDistrictInfo($pageNO,$schStr,$mode) {

		$tableName = 'HS_HR_DISTRICT';
		$arrFieldList[0] = 'DISTRICT_CODE';
		$arrFieldList[1] = 'DISTRICT_NAME';
		$arrFieldList[2] = 'PROVINCE_CODE';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultSetMessage($pageNO,$schStr,$mode);

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

	function countDistrictInfo($schStr,$mode) {

		$tableName = 'HS_HR_DISTRICT';
		$arrFieldList[0] = 'DISTRICT_CODE';
		$arrFieldList[1] = 'DISTRICT_NAME';
		$arrFieldList[2] = 'PROVINCE_CODE';

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

	function filterDistrictInfo($getID) {

		$this->getID = $getID;
		$tableName = 'HS_HR_DISTRICT';
		$arrFieldList[0] = 'DISTRICT_CODE';
		$arrFieldList[1] = 'DISTRICT_NAME';
		$arrFieldList[2] = 'PROVINCE_CODE';

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

	    	$arrayDispList[$i][0] = $line[0]; // Province Code
	    	$arrayDispList[$i][1] = $line[1]; // Provicne Name
	    	$arrayDispList[$i][2] = $line[2]; // Country ID
	    	$i++;

	     }

	     if (isset($arrayDispList)) {

			return $arrayDispList;

		} else {

			$arrayDispList = '';
			return $arrayDispList;

		}

	}

	function getDistrictCodes($getID) {

		$sql_builder = new SQLQBuilder();

		$this->getID = $sql_builder->quoteCorrectString($getID, false);
		$tableName = 'HS_HR_DISTRICT';
		$arrFieldList[0] = 'PROVINCE_CODE';
		$arrFieldList[1] = 'DISTRICT_CODE';
		$arrFieldList[2] = 'DISTRICT_NAME';

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->selectOneRecordFiltered($this->getID);

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$i=0;

		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {
	    		for($c=0; count($arrFieldList) > $c ; $c++)
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
