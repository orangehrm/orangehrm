<?
class EmpReport {
	
	var $repID;
	var $repName;
	var $repCritriaDefStr;
	var $repFieldDefStr;
	
	function EmpReport() {
		
	}
	
	function setRepID($repID) {
		$this->repID = $repID;
	}
	
	function setRepName($repName) {
		$this->repName = $repName;
	}
	
	function setRepCriteriaDefString($repCritriaDefString) {
		$this->repCritriaDefStr = $repCritriaDefString;
	}

	function setRepFieldDefString($repFieldDefString) {
		$this->repFieldDefStr = $repFieldDefString;
	}

	function getRepID() {
		return $this->repID;
	}
	
	function getRepName() {
		return $this->repName;
	}
	
	function getRepCriteriaDefString() {
		return $this->repCritriaDefStr;
	}

	function getRepFieldDefString() {
		return $this->repFieldDefStr;
	}

	function getListofUserGroupReports($userGroup,$pageNO,$schStr,$mode) {
		
		$sql_builder = new SQLQBuilder();
		
		$sqlQString = $sql_builder->listReports($userGroup,$pageNO,$schStr,$mode);
		
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
	
	function countReports($schStr,$mode) {
		
		$tableName = 'HS_HR_EMPREPORT';
		$arrFieldList[0] = 'REP_CODE';
		$arrFieldList[1] = 'REP_NAME';
		
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
	
	function delReports($arrList) {

		$tableName = 'HS_HR_EMPREPORT';
		$arrFieldList[0] = 'REP_CODE';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_delete = 'true';
		$sql_builder->arr_delete = $arrFieldList;

		$sqlQString = $sql_builder->deleteRecord($arrList);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
	}
	
	function addReport() {
		
		$arrFieldList[0] = "'". $this->getRepID() . "'";
		$arrFieldList[1] = "'". $this->getRepName() . "'";
		$arrFieldList[2] = "'". $this->getRepCriteriaDefString() . "'";
		$arrFieldList[3] = "'". $this->getRepFieldDefString() . "'";
	
		$tableName = 'HS_HR_EMPREPORT';
	
		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_insert = 'true';
		$sql_builder->arr_insert = $arrFieldList;		
		
		$sqlQString = $sql_builder->addNewRecordFeature1();
		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		 return $message2;
					
	}
	
	function updateReport() {
		
		$arrRecordsList[0] = "'". $this->getRepID() . "'";
		$arrRecordsList[1] = "'". $this->getRepName() . "'";
		$arrRecordsList[2] = "'". $this->getRepCriteriaDefString() . "'";
		$arrRecordsList[3] = "'". $this->getRepFieldDefString() . "'";
		$arrFieldList[0] = 'REP_CODE';
		$arrFieldList[1] = 'REP_NAME';
		$arrFieldList[2] = 'REP_CRIDEF_STR';
		$arrFieldList[3] = 'REP_FLDDEF_STR';
		
		$tableName = 'HS_HR_EMPREPORT';			
	
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
	
	function filterReport($getID) {
		
		$tableName = 'HS_HR_EMPREPORT';
		$arrFieldList[0] = 'REP_CODE';
		$arrFieldList[1] = 'REP_NAME';
		$arrFieldList[2] = 'REP_CRIDEF_STR';
		$arrFieldList[3] = 'REP_FLDDEF_STR';
		
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
	    	$arrayDispList[$i][2] = $line[2];
	    	$arrayDispList[$i][3] = $line[3];
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
		$tableName = 'HS_HR_EMPREPORT';
		$arrFieldList[0] = 'REP_CODE';
				
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
		return $common_func->explodeString($this->singleField,"REP"); 
		}
	}	
	
}
?>