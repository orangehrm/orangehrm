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

require_once OpenSourceEIM . '/lib/Confs/Conf.php';
require_once OpenSourceEIM . '/lib/Models/DMLFunctions.php';
require_once OpenSourceEIM . '/lib/Models/SQLQBuilder.php';
require_once OpenSourceEIM . '/lib/CommonMethods/CommonFunctions.php';

class DesDescription {

	var $tableName = 'HS_HR_JD_KPI';
	
	var $desId;
	var $jdkraId;
	var $jdKPI;
	var $arrayDispList;
	var $singleField;
	
	
	function DesDescription() {
		
	}
	
	function setDesId($desId) {
	
		$this->desId = $desId;
	
	}
	
	function setJDKraId($jdkraId) {
	
		$this->jdkraId = $jdkraId;
	}
		
	function setJDKPI($jdKPI) {

		$this->jdKPI = $jdKPI;
	}

	function getDesId() {

		return $this->desId;

	}

	function getJDKraId() {

		return $this->jdkraId;
	}

	function getJDKPI() {

		return $this->jdKPI;
	}


	function getListofDesignations($pageNO,$schStr,$mode) {
		
		$tableName = 'HS_HR_DESIGNATION';
		$arrFieldList[0] = 'DSG_CODE';
		$arrFieldList[1] = 'DSG_NAME';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_JD_KPI';
		$sql_builder->field = 'DSG_CODE';
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

	function countDesignations($schStr,$mode) {
		
		$tableName = 'HS_HR_DESIGNATION';
		$arrFieldList[0] = 'DSG_CODE';
		$arrFieldList[1] = 'DSG_NAME';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name='HS_HR_JD_KPI';
		$sql_builder->flg_select = 'true';
		$sql_builder->field='DSG_CODE';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->countMultipleTab($schStr,$mode);
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$line = mysql_fetch_array($message2, MYSQL_NUM);
		 	
	    	return $line[0];
	}
	
	function getUnAssDesignations($pageNO,$schStr,$mode) {
		
		$tableName = 'HS_HR_DESIGNATION';
		$arrFieldList[0] = 'DSG_CODE';
		$arrFieldList[1] = 'DSG_NAME';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_JD_KPI';
		$sql_builder->field = 'DSG_CODE';
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

	function countUnAssDesignations($schStr,$mode) {
		
		$tableName = 'HS_HR_DESIGNATION';
		$arrFieldList[0] = 'DSG_CODE';
		$arrFieldList[1] = 'DSG_NAME';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name='HS_HR_JD_KPI';
		$sql_builder->flg_select = 'true';
		$sql_builder->field='DSG_CODE';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->countResultFilter($schStr,$mode);
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$line = mysql_fetch_array($message2, MYSQL_NUM);
		 	
	    	return $line[0];
	}
	
	function delJDKPI($arrList) {

		$tableName = 'HS_HR_JD_KPI';
		$arrFieldList[0] = 'JDKRA_CODE';
		$arrFieldList[1] = 'DSG_CODE';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_delete = 'true';
		$sql_builder->arr_delete = $arrFieldList;

		$sqlQString = $sql_builder->deleteRecord($arrList);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

	}

///
	function addJDKPI() {
		
		//$this->getLocationId();
		$arrFieldList[0] = "'". $this->getJDKraId() . "'";
		$arrFieldList[1] = "'". $this->getDesId() . "'";
		$arrFieldList[2] = "'". $this->getJDKPI() . "'";

		//$arrFieldList[0] = 'LOC_CODE';
		//$arrFieldList[1] = 'LOC_NAME';
		
		$tableName = 'HS_HR_JD_KPI';

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
	
	function updateJDKPI() {
		
		$arrRecordsList[0] = "'". $this->getJDKraId() . "'";
		$arrRecordsList[1] = "'". $this->getDesId() . "'";
		$arrRecordsList[2] = "'". $this->getJDKPI() . "'";
		$tableName = 'HS_HR_JD_KPI';
		$arrFieldList[0] = 'JDKRA_CODE';
		$arrFieldList[1] = 'DSG_CODE';
		$arrFieldList[2] = 'JDKPI_INDICATORS';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_update = 'true';
		$sql_builder->arr_update = $arrFieldList;	
		$sql_builder->arr_updateRecList = $arrRecordsList;	
	
		$sqlQString = $sql_builder->addUpdateRecord1(1);
	
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		return $message2;
		 
				
	}
	
	
	function filterJDKPI($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_JD_KPI';
		$arrFieldList[0] = 'JDKRA_CODE';
		$arrFieldList[1] = 'DSG_CODE';
		$arrFieldList[2] = 'JDKPI_INDICATORS';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->selectOneRecordFiltered($this->getID,1);
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
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
		
			$arrayDispList = '';
			return $arrayDispList;
			
		}
				
	}

	function getDes() {

		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_DESIGNATION';
		$arrFieldList[0] = 'DSG_CODE';
		$arrFieldList[1] = 'DSG_NAME';

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

	function getGrouping($dsg) {

		$sql_builder = new SQLQBuilder();
		$sqlQString = $sql_builder -> getJDGrouping($dsg);
		
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
	    	$arrayDispList[$i][5] = $line[5];
	    	$i++;

	     }

	     if (isset($arrayDispList)) {

			return $arrayDispList;

		} else {

			$arrayDispList = '';
			return $arrayDispList;

		}
	}

	function getAssigned($dsg) {

		$sql_builder = new SQLQBuilder();
		$sqlQString = $sql_builder -> getJDAssigned($dsg);
		
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
	    	$arrayDispList[$i][5] = $line[5];
	    	$arrayDispList[$i][6] = $line[6];
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
