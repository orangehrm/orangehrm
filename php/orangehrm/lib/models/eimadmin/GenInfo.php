<?php

require_once ROOT_PATH . '/lib/confs/Conf.php';
require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';
require_once ROOT_PATH . '/lib/dao/SQLQBuilder.php';
require_once ROOT_PATH . '/lib/common/CommonFunctions.php';

class GenInfo {
	
	var $genInfoKeys;
	var $genInfoValues;
	
	function GenInfo() {
		
	}
	
	function setGenInfoKeys($genInfoKeys) {
		$this->genInfoKeys = $genInfoKeys;
	}
	
	function setGenInfoValues($genInfoValues) {
		$this->genInfoValues = $genInfoValues;
	}
	
	function getGenInfoKeys() {
		return $this->genInfoKeys;
	}
	
	function getGenInfoValues() {
		return $this->genInfoValues;
	}
	
	function updateGenInfo() {
		
		$tableName = 'HS_HR_GENINFO';
		
		$arrRecordsList[0] = "'001'";
		$arrRecordsList[1] = "'". $this->getGenInfoKeys() . "'";
		$arrRecordsList[2] = "'". $this->getGenInfoValues() . "'";
		
		$arrFieldList[0] = 'CODE';
		$arrFieldList[1] = 'GENINFO_KEYS';
		$arrFieldList[2] = 'GENINFO_VALUES';
	
		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_update = 'true';
		$sql_builder->arr_update = $arrFieldList;	
		$sql_builder->arr_updateRecList = $arrRecordsList;	
	
		$sqlQString = $sql_builder->addUpdateRecord1();
		//$compStruct_newTitle = explode("'",$arrRecordsList[2]);
		$compStruct_newTitle = explode("|",$arrRecordsList[2]);
		$compStruct_newTitle = substr($compStruct_newTitle[0], 1);
		
		$sqlQString1 = sprintf("UPDATE `hs_hr_compstructtree` SET `title` = '%s' WHERE `lft`=1 LIMIT 1", mysql_real_escape_string($compStruct_newTitle));
					
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		//$message3 = $dbConnection -> executeQuery($sqlQString1);
		
		$compStructObj = new CompStruct();
		
		$compStructObj->setaddStr($compStruct_newTitle);
		$compStructObj->setid(1);
		$compStructObj->setlocation('');
		
		$compStructObj->updateCompStruct();
		
		return $message2;
	}
	
	function filterGenInfo() {
		
		$tableName = 'HS_HR_GENINFO';			
		$arrFieldList[0] = 'CODE';
		$arrFieldList[1] = 'GENINFO_KEYS';
		$arrFieldList[2] = 'GENINFO_VALUES';
		
		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->selectOneRecordFiltered('001');
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$i=0;
		
		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {
		 	
	    	$arrayDispList[$i][0] = $line[1];
	    	$arrayDispList[$i][1] = $line[2];
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
