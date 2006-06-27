<?
class CompStruct {
	
	var $compStructCode;
	var $compStructName;
	var $compStructDescription;
	var $compStructComments;
	var $compStructLevel;
	
	function CompStruct() {
		
	}
	
	function setCompStructCode($compStructCode) {
		$this->compStructCode = $compStructCode;	
	}
	
	function setCompStructName($compStructName) {
		$this->compStructName = $compStructName;
	}
	
	function setCompStructDescription($compStructDescription) {
		$this->compStructDescription = $compStructDescription;
	}
	
	function setCompStructComments($compStructComments) {
		$this->compStructComments = $compStructComments;
	}
	
	function setCompStructLevel($compStructLevel) {
		$this->compStructLevel = $compStructLevel;
	}
	
	function getCompStructCode() {
		return $this->compStructCode;	
	}
	
	function getCompStructName() {
		return $this->compStructName;
	}
	
	function getCompStructDescription() {
		return $this->compStructDescription;
	}
	
	function getCompStructComments() {
		return $this->compStructComments;
	}
	
	function getCompStructLevel() {
		return $this->compStructLevel;
	}

	function getListofCompStruct($pageNO,$schStr,$mode) {
		
		$tableName = 'HS_HR_COMPANY_STRUCT';
		$arrFieldList[0] = 'HIE_CODE';
		$arrFieldList[1] = 'HIE_NAME';
		$arrFieldList[2] = 'HIE_DESC';
		$arrFieldList[3] = 'HIE_COMMENTS';
		$arrFieldList[4] = 'DEF_LEVEL';
		
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

	function delCompStruct($arrList) {

		$tableName = 'HS_HR_COMPANY_STRUCT';
		$arrFieldList[0] = 'HIE_CODE';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_delete = 'true';
		$sql_builder->arr_delete = $arrFieldList;

		$sqlQString = $sql_builder->deleteRecord($arrList);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
	}
	
	function addCompStruct() {
		
		$arrFieldList[0] = "'". $this->getCompStructCode() . "'";
		$arrFieldList[1] = "'". $this->getCompStructName() . "'";
		$arrFieldList[2] = "'". $this->getCompStructDescription() . "'";
		$arrFieldList[3] = "'". $this->getCompStructComments() . "'";
		$arrFieldList[4] = "'". $this->getCompStructLevel() . "'";

		$tableName = 'HS_HR_COMPANY_STRUCT';
	
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
	
	function updateCompStruct() {

		$arrRecordsList[0] = "'". $this->getCompStructCode() . "'";
		$arrRecordsList[1] = "'". $this->getCompStructName() . "'";
		$arrRecordsList[2] = "'". $this->getCompStructDescription() . "'";
		$arrRecordsList[3] = "'". $this->getCompStructComments() . "'";
		$arrRecordsList[4] = "'". $this->getCompStructLevel() . "'";

		$tableName = 'HS_HR_COMPANY_STRUCT';
		$arrFieldList[0] = 'HIE_CODE';
		$arrFieldList[1] = 'HIE_NAME';
        $arrFieldList[2] = 'HIE_DESC';
        $arrFieldList[3] = 'HIE_COMMENTS';
        $arrFieldList[4] = 'DEF_LEVEL';
	
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
	
	
	function filterCompStruct($getID) {
		
		$tableName = 'HS_HR_COMPANY_STRUCT';
		$arrFieldList[0] = 'HIE_CODE';
		$arrFieldList[1] = 'HIE_NAME';
        $arrFieldList[2] = 'HIE_DESC';
        $arrFieldList[3] = 'HIE_COMMENTS';
        $arrFieldList[4] = 'DEF_LEVEL';


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
	
	
	function getLastRecord() {
		
		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_COMPANY_STRUCT';
		$arrFieldList[0] = 'HIE_CODE';

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
			
		return $common_func->explodeString($this->singleField,"HIE");
				
		}
	}

	function getUnAssCompStruct($hiRelat,$level) {
		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_COMPANY_STRUCT';
		$arrFieldList[0] = 'HIE_CODE';
		$arrFieldList[1] = 'HIE_NAME';

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;
		$sql_builder->field = 'HIE_CODE';
		$sql_builder->table2_name= 'HS_HR_COMPANY_HIERARCHY';
		$arr[0][0]= 'HIE_RELATIONSHIP';
		$arr[0][1]= $hiRelat;
		$arr[1][0] = 'DEF_LEVEL';
		$arr[1][1] = $level;
		$arr2[0][0] = 'DEF_LEVEL';
		$arr2[0][1] = $level;

		$sqlQString = $sql_builder->selectFilter($arr,$arr2);

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
}
?>