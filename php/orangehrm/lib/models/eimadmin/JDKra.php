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

class JDKra {

	var $tableName = 'HS_HR_JD_KEY_RESULT_AREA'; //hs_hr_jd_key_result_area
	
	var $jdkraId;
	var $jdkraDesc;
	var $jdkBrf;
    var $jdTyp;
    var $jdSkTyp;
	var $arrayDispList;
	var $singleField;
	
	
	function JDKra() {
		
	}
	
	function setJDKraId($jdkraId) {
	
		$this->jdkraId = $jdkraId;
	
	}
	
	function setJDKraDesc($jdkraDesc) {
	
		$this->jdkraDesc = $jdkraDesc;
	}

	function setJDKraBrf($jdkBrf) {

        $this -> jdkBrf = $jdkBrf;
     }

	function setJDTyp($jdTyp) {

		$this->jdTyp = $jdTyp;
	}

    function setJDKraSkTyp($jdSkTyp) {

        $this->jdSkTyp = $jdSkTyp;
    }

	function getJDKraId() {
	
		return $this->jdkraId;
	
	}
	
	function getJDKraDesc() {

		return $this->jdkraDesc;

	}

	function getJDKraBrf() {

        return $this -> jdkBrf;
     }

	function getJDTyp() {

		return $this->jdTyp;

	}

    function getJDKraSkTyp() {

        return $this->jdSkTyp;
    }

	function getListofJDKra($pageNO,$schStr,$mode) {
		
		$tableName = 'HS_HR_JD_KEY_RESULT_AREA';
		$arrFieldList[0] = 'JDKRA_CODE';
		$arrFieldList[1] = 'JDKRA_NAME';

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

	function countJDKra($schStr,$mode) {
		
		$tableName = 'HS_HR_JD_KEY_RESULT_AREA';
		$arrFieldList[0] = 'JDKRA_CODE';
		$arrFieldList[1] = 'JDKRA_NAME';

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

	function delJDKra($arrList) {

		$tableName = 'HS_HR_JD_KEY_RESULT_AREA';
		$arrFieldList[0] = 'JDKRA_CODE';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_delete = 'true';
		$sql_builder->arr_delete = $arrFieldList;

		$sqlQString = $sql_builder->deleteRecord($arrList);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

	}

	
	function addJDKra() {
		
		$this->getJDKraId();
		$arrFieldList[0] = "'". $this->getJDKraId() . "'";
		$arrFieldList[1] = "'". $this->getJDKraDesc() . "'";
		$arrFieldList[2] = "'". $this->getJDKraBrf() ."'";
		$arrFieldList[3] = "'". $this->getJDTyp() . "'";
		$arrFieldList[4] = "'". $this->getJDKraSkTyp(). "'";

		
		//$arrFieldList[0] = 'CURRENCY_ID';
		//$arrFieldList[1] = 'CURRENCY_NAME';
		
		$tableName = 'HS_HR_JD_KEY_RESULT_AREA';
	
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
	
	function updateJDKra() {
		
		$this->getJDKraId();
		$arrRecordsList[0] = "'". $this->getJDKraId() . "'";
		$arrRecordsList[1] = "'". $this->getJDKraDesc() . "'";
		$arrRecordsList[2] = "'". $this->getJDKraBrf() ."'";
		$arrRecordsList[3] = "'". $this->getJDTyp() . "'";
		$arrRecordsList[4] = "'". $this->getJDKraSkTyp(). "'";

		$arrFieldList[0] = 'JDKRA_CODE';
		$arrFieldList[1] = 'JDKRA_NAME';
		$arrFieldList[2] = 'JDKRA_BRIEF_DESC';
		$arrFieldList[3] = 'JDTYPE_CODE';
		$arrFieldList[4] = 'SKILL_CODE';

		$tableName = 'HS_HR_JD_KEY_RESULT_AREA';
	
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

	function filterJDKra($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_JD_KEY_RESULT_AREA';
		$arrFieldList[0] = 'JDKRA_CODE';
		$arrFieldList[1] = 'JDKRA_NAME';
		$arrFieldList[2] = 'JDKRA_BRIEF_DESC';
		$arrFieldList[3] = 'JDTYPE_CODE';
		$arrFieldList[4] = 'SKILL_CODE';

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

	function getJDKras ($jdkType) {

		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_JD_KEY_RESULT_AREA';
		$arrFieldList[0] = 'JDKRA_CODE';
		$arrFieldList[1] = 'JDKRA_NAME';
        $arrFieldList[2] = 'JDKRA_BRIEF_DESC';

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->selectRecords($jdkType,'JDTYPE_CODE');

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$common_func = new CommonFunctions();

		$i=0;

		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {

	    	$arrayDispList[$i][0] = $line[0];
	    	$arrayDispList[$i][1] = $line[1];
	    	$arrayDispList[$i][2] = $line[2];


	    	$i++;

	     }

	     if (isset($arrayDispList)) {

	       	return $arrayDispList;

	     } else {

	     	//Handle Exceptions
	     	//Create Logs

	     }

	}
			
	function getLastRecord() {
		
		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_JD_KEY_RESULT_AREA';
		$arrFieldList[0] = 'JDKRA_CODE';
				
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
			
		return $common_func->explodeString($this->singleField,"JKR");
				
		}
		
	}	
	
	
}

?>
