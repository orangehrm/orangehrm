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
require_once ROOT_PATH . '/lib/logs/LogFileWriter.php';
require_once ROOT_PATH . '/lib/common/UniqueIDGenerator.php';

class EthnicRace {

	var $tableName = 'HS_HR_ETHNIC_RACE';

	var $ethnicrace;
	var $ethnicraceDesc;
	var $arrayDispList;
	var $singleField;


	function EthnicRace() {
	}

	function setethnicraceId($ethnicrace) {
		$this->ethnicrace = $ethnicrace;
	}

	function setethnicraceDescription($ethnicraceDesc) {
		$this->ethnicraceDesc = $ethnicraceDesc;
	}

	function getethnicrace() {
		return $this->ethnicrace;
	}

	function getethnicraceDescription() {
		return $this->ethnicraceDesc;
	}

	function getListofEthnicRace($pageNO,$schStr,$mode, $sortField = 0, $sortOrder = 'ASC') {

		$tableName = 'HS_HR_ETHNIC_RACE';
		$arrFieldList[0] = 'ETHNIC_RACE_CODE';
		$arrFieldList[1] = 'ETHNIC_RACE_DESC';

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

	function countEthnicRace($schStr,$mode) {

		$tableName = 'HS_HR_ETHNIC_RACE';
		$arrFieldList[0] = 'ETHNIC_RACE_CODE';
		$arrFieldList[1] = 'ETHNIC_RACE_DESC';

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

	function delEthnicRace($arrList) {

		$tableName = 'HS_HR_ETHNIC_RACE';
		$arrFieldList[0] = 'ETHNIC_RACE_CODE';

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

	function addEthnicRace() {

		if ($this->_isDuplicateName()) {
			throw new EthnicaRaceException("Duplicate name", 1);
		}

		$tableName = 'hs_hr_ethnic_race';

		$this->ethnicrace = UniqueIDGenerator::getInstance()->getNextID($tableName, 'ethnic_race_code', 'ETH');
		$arrFieldList[0] = "'". $this->getethnicrace() . "'";
		$arrFieldList[1] = "'". $this->getethnicraceDescription() . "'";

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_insert = 'true';
		$sql_builder->arr_insert = $arrFieldList;

		$sqlQString = $sql_builder->addNewRecordFeature1();

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		 return $message2;
	}

	function updateEthnicRace() {

		if ($this->_isDuplicateName(true)) {
			throw new EthnicaRaceException("Duplicate name", 1);
		}

		$this->getethnicrace();
		$arrRecordsList[0] = "'". $this->getethnicrace() . "'";
		$arrRecordsList[1] = "'". $this->getethnicraceDescription() . "'";
		$arrFieldList[0] = 'ETHNIC_RACE_CODE';
		$arrFieldList[1] = 'ETHNIC_RACE_DESC';

		$tableName = 'HS_HR_ETHNIC_RACE';

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

	function filterEthnicRace($getID) {

		$this->getID = $getID;
		$tableName = 'HS_HR_ETHNIC_RACE';
		$arrFieldList[0] = 'ETHNIC_RACE_CODE';
		$arrFieldList[1] = 'ETHNIC_RACE_DESC';

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

	function getEthnicRaceCodes () {

		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_ETHNIC_RACE';
		$arrFieldList[0] = 'ETHNIC_RACE_CODE';
		$arrFieldList[1] = 'ETHNIC_RACE_DESC';

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
	     }
	}

	function filterGetEthnicRaceInfo($getID) {

		$this->getID = $getID;
		$tableName = 'HS_HR_ETHNIC_RACE';
		$arrFieldList[0] = 'ETHNIC_RACE_CODE';
		$arrFieldList[1] = 'ETHNIC_RACE_DESC';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->selectOneRecordFiltered($this->getID);

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$i=0;

		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {

	    	$arrayDispList[$i][0] = $line[0]; // Province Code
	    	$arrayDispList[$i][1] = $line[1]; // Provicne Name

	    	$i++;
	     }

	     if (isset($arrayDispList)) {
			return $arrayDispList;
		} else {
			$arrayDispList = '';
			return $arrayDispList;
		}
	}

	function filterNotEqualSubEthnicRaceInfo($getID) {

		$this->getID = $getID;

		$tableName = 'HS_HR_ETHNIC_RACE';
		$arrFieldList[0] = 'ETHNIC_RACE_CODE';
		$arrFieldList[1] = 'ETHNIC_RACE_DESC';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->filterNotEqualRecordSet($this->getID);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$i=0;

		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {

	    	$arrayDispList[$i][0] = $line[0]; // Province Code
	    	$arrayDispList[$i][1] = $line[1]; // Provicne Name

	    	$i++;

	     }

	    if (isset($arrayDispList)) {
			return $arrayDispList;
		} else {
			$arrayDispList = '';
			return $arrayDispList;
		}
	}

	private function _isDuplicateName($update=false) {
		$races = $this->getListofEthnicRace(0, $this->getethnicraceDescription(), 1);

		if (is_array($races)) {
			if ($update) {
				if ($races[0][0] == $this->getethnicrace()) {
					return false;
				}
			}
			return true;
		}

		return false;
	}
}

class EthnicaRaceException extends Exception {
}
?>
