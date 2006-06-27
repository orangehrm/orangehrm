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

class Designations {

	var $tableName = 'HS_HR_DESIGNATION';
	
	var $desId;
	var $desDesc;
	var $corpTitId;
	var $senMgmtFlag;
	var $revDate;
	var $desNxtUpg;
	var $arrayDispList;
	var $singleField;
	
	
	function Designations() {
		
	}
	
	function setDesId($desId) {
	
		$this->desId = $desId;
	
	}

	function setCorpTitId($corpTitId) {

		$this->corpTitId = $corpTitId;

	}

	function setSenMgmtFlag($senMgmtFlag) {

		$this->senMgmtFlag = $senMgmtFlag;

	}

	function setRevDate($revDate) {

		$this->revDate = $revDate;

	}

	function setDesNxtUpg($desNxtUpg) {

		$this->desNxtUpg = $desNxtUpg;

	}

	function setDesDesc($desDesc) {
	
		$this->desDesc = $desDesc;
	}
		
	
	function getDesId() {
	
		return $this->desId;
	
	}
	
	function getDesDesc() {
	
		return $this->desDesc;
		
	}

	function getCorpTitId() {

		return $this->corpTitId;

	}

	function getSenMgmtFlag() {

		return $this->senMgmtFlag;

	}

	function getRevDate() {

		return $this->revDate;

	}

	function getDesNxtUpg() {

		return $this->desNxtUpg;

	}


	function getListofDesignations($pageNO,$schStr,$mode) {
		
		$tableName = 'HS_HR_DESIGNATION';
		$arrFieldList[0] = 'DSG_CODE';
		$arrFieldList[1] = 'DSG_NAME';

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

	function countDesignations($schStr,$mode) {
		
		$tableName = 'HS_HR_DESIGNATION';
		$arrFieldList[0] = 'DSG_CODE';
		$arrFieldList[1] = 'DSG_NAME';

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
	
	
	function delDesignations($arrList) {

		$tableName = 'HS_HR_DESIGNATION';
		$arrFieldList[0] = 'DSG_CODE';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_delete = 'true';
		$sql_builder->arr_delete = $arrFieldList;

		$sqlQString = $sql_builder->deleteRecord($arrList);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

	}

	
	function addDesignations() {
		
		$this->getDesId();
		$arrFieldList[0] = "'". $this->getDesId() . "'";
		$arrFieldList[1] = "'". $this->getDesDesc() . "'";
		$arrFieldList[2] = "'". $this->getCorpTitId() . "'";
		$arrFieldList[3] = "'". $this->getSenMgmtFlag() . "'";
		$arrFieldList[4] = "'". $this->getRevDate() . "'";
		$arrFieldList[5] = ($this->getDesNxtUpg()=='0') ? 'null' :"'". $this->getDesNxtUpg() . "'";

		$tableName = 'HS_HR_DESIGNATION';
	
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
	
	function updateDesignations() {
		
		$this->getDesId();
		$arrRecordsList[0] = "'". $this->getDesId() . "'";
		$arrRecordsList[1] = "'". $this->getDesDesc() . "'";
		$arrRecordsList[2] = "'". $this->getCorpTitId() . "'";
		$arrRecordsList[3] = "'". $this->getSenMgmtFlag() . "'";
		$arrRecordsList[4] = "'". $this->getRevDate() . "'";
		$arrRecordsList[5] = ($this->getDesNxtUpg()=='0') ? 'null' :"'". $this->getDesNxtUpg() . "'";


		$arrFieldList[0] = 'DSG_CODE';
		$arrFieldList[1] = 'DSG_NAME';
        $arrFieldList[2] = 'CT_CODE';
        $arrFieldList[3] = 'DSG_SNRMGT_FLG';
        $arrFieldList[4] = 'DSG_REVIEW_DATE';
        $arrFieldList[5] = 'DSG_NEXT_UPGRADE';

		$tableName = 'HS_HR_DESIGNATION';
	
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
	
	
	function filterDesignations($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_DESIGNATION';
		$arrFieldList[0] = 'DSG_CODE';
		$arrFieldList[1] = 'DSG_NAME';
        $arrFieldList[2] = 'CT_CODE';
        $arrFieldList[3] = 'DSG_SNRMGT_FLG';
        $arrFieldList[4] = 'DSG_REVIEW_DATE';
        $arrFieldList[5] = 'DSG_NEXT_UPGRADE';

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
	
	
	function getLastRecord() {
		
		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_DESIGNATION';
		$arrFieldList[0] = 'DSG_CODE';
				
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
			
		return $common_func->explodeString($this->singleField,"DSG");
				
		}
		
	}	

	function getDes() {

		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_DESIGNATION';
		$arrFieldList[0] = 'DSG_CODE';
		$arrFieldList[1] = 'DSG_NAME';
        $arrFieldList[2] = 'CT_CODE';
        $arrFieldList[3] = 'DSG_SNRMGT_FLG';
        $arrFieldList[4] = 'DSG_REVIEW_DATE';
        $arrFieldList[5] = 'DSG_NEXT_UPGRADE';

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
	    	$arrayDispList[$i][2] = $line[2];
	    	$arrayDispList[$i][3] = $line[3];
	    	$arrayDispList[$i][4] = $line[4];
	    	$arrayDispList[$i][5] = $line[5];


	    	$i++;

	     }

	     if (isset($arrayDispList)) {

	       	return $arrayDispList;

	     } else {

	     	//Handle Exceptions
	     	//Create Logs

	     }

	}

	function getDesEmpInfo($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_DESIGNATION';
        $arrFieldList[0] = 'CT_CODE';
		$arrFieldList[1] = 'DSG_CODE';
		$arrFieldList[2] = 'DSG_NAME';
	
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
	    	$i++;
	    	
	     }
	     
	     if (isset($arrayDispList)) {
	     
			return $arrayDispList;
			
		} else {
		
			$arrayDispList = '';
			return $arrayDispList;
			
		}
				
	}
	
	function getListofDesignationsDes($pageNO,$schStr,$mode) {
		
		$tableName = 'HS_HR_DESIGNATION';
		$arrFieldList[0] = 'DSG_CODE';
		$arrFieldList[1] = 'DSG_NAME';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_JD_QUALIFICATION';
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
	
	function countDesignationsDes($schStr,$mode) {
		
		$tableName = 'HS_HR_DESIGNATION';
		$arrFieldList[0] = 'DSG_CODE';
		$arrFieldList[1] = 'DSG_NAME';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name='HS_HR_JD_QUALIFICATION';
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
	
	
	function getUnAssDesignationsDes($pageNO,$schStr,$mode) {
		
		$tableName = 'HS_HR_DESIGNATION';
		$arrFieldList[0] = 'DSG_CODE';
		$arrFieldList[1] = 'DSG_NAME';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_JD_QUALIFICATION';
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
	
	
	function countUnAssDesignationsDes($schStr,$mode) {
		
		$tableName = 'HS_HR_DESIGNATION';
		$arrFieldList[0] = 'DSG_CODE';
		$arrFieldList[1] = 'DSG_NAME';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name='HS_HR_JD_QUALIFICATION';
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
	
}

?>
