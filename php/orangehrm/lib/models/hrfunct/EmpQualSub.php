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

class EmpQualSubject {

	var $tableName = 'HS_HR_EMP_SUBJECT';
	
	var $empId;
	var $empQualId;
	var $empSubjectId;
	var $empSubMarks;
	var $empSubYear;
	var $empSubComment;
	var $empSubRat;
	
	var $arrayDispList;
	var $singleField;

	function EmpQualSubject() {
		
	}
	
	function setEmpId($empId) {
	
	$this->empId=$empId;
	}
	
	function setEmpQualId($empQualId) {
	
	$this->empQualId=$empQualId;
	}
	
	function setEmpSubId($empSubId) {
	
	$this->empSubId=$empSubId;
	}
	
	function setEmpSubMarks($empSubMarks) {
	
	$this->empSubMarks=$empSubMarks;
	}
	
	function setEmpSubYear($empSubYear) {
	
	$this->empSubYear=$empSubYear;
	}
	
	function setEmpSubComment($empSubComment) {
	
	$this->empSubComment=$empSubComment;
	}
	
	function setEmpSubRat($empSubRat) {
	
	$this->empSubRat=$empSubRat;
	}
	
	function getEmpId() {
	
	return $this->empId;
	}
	
	function getEmpQualId() {
	
	return $this->empQualId;
	}
	
	function getEmpSubId() {
	
	return $this->empSubId;
	}
	
	function getEmpSubMarks() {
	
	return $this->empSubMarks;
	}
	
	function getEmpSubYear() {
	
	return $this->empSubYear;
	}
	
	function getEmpSubComment() {
	
	return $this->empSubComment;
	}
	
	function getEmpSubRat() {
	
	return $this->empSubRat;
	}
////
	function getListofEmpQualSub() {
		
		$tableName = 'HS_HR_EMP_SUBJECT';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
			
		$sqlQString = $sql_builder->selectEmployee();
		
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

	function delEmpQualSub($arrList) {

		$tableName = 'HS_HR_EMP_SUBJECT';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'QUALIFI_CODE';
		$arrFieldList[2] = 'SBJ_CODE';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_delete = 'true';
		$sql_builder->arr_delete = $arrFieldList;

		$sqlQString = $sql_builder->deleteRecord($arrList);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

	}

	function addEmpQualSub() {
		
		$this->getEmpId();
		$arrFieldList[0] = "'". $this->getEmpId() . "'";
		$arrFieldList[1] = "'". $this->getEmpQualId() . "'";
		$arrFieldList[2] = "'". $this->getEmpSubId() . "'";
		$arrFieldList[3] = "'". $this->getEmpSubMarks() . "'";
		$arrFieldList[4] = "'". $this->getEmpSubYear() . "'";
		$arrFieldList[5] = "'". $this->getEmpSubComment() . "'";
		$arrFieldList[6] = "'". $this->getEmpSubRat() . "'";

		$tableName = 'HS_HR_EMP_SUBJECT';
	
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
	
	function updateEmpQualSub() {
		
		$arrRecordsList[0] = "'". $this->getEmpId() . "'";
		$arrRecordsList[1] = "'". $this->getEmpQualId() . "'";
		$arrRecordsList[2] = "'". $this->getEmpSubId() . "'";
		$arrRecordsList[3] = "'". $this->getEmpSubMarks() . "'";
		$arrRecordsList[4] = "'". $this->getEmpSubYear() . "'";
		$arrRecordsList[5] = "'". $this->getEmpSubComment() . "'";
		$arrRecordsList[6] = "'". $this->getEmpSubRat() . "'";

		$tableName = 'HS_HR_EMP_SUBJECT';
		
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'QUALIFI_CODE';
		$arrFieldList[2] = 'SBJ_CODE';
		$arrFieldList[3] = 'ESBJ_MARKS';
		$arrFieldList[4] = 'ESBJ_YEAR';
		$arrFieldList[5] = 'ESBJ_COMMENTS';
		$arrFieldList[6] = 'RATING_GRADE_CODE';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_update = 'true';
		$sql_builder->arr_update = $arrFieldList;	
		$sql_builder->arr_updateRecList = $arrRecordsList;	
	
		$sqlQString = $sql_builder->addUpdateRecord1(2);
	
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		return $message2;
		 
				
	}
	
	
	function filterEmpQualSub($getID) {
		
		$this->getID = $getID;

		$tableName = 'HS_HR_EMP_SUBJECT';

		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'QUALIFI_CODE';
		$arrFieldList[2] = 'SBJ_CODE';
		$arrFieldList[3] = 'ESBJ_MARKS';
		$arrFieldList[4] = 'ESBJ_YEAR';
		$arrFieldList[5] = 'ESBJ_COMMENTS';
		$arrFieldList[6] = 'RATING_GRADE_CODE';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->selectOneRecordFiltered($this->getID,2);
		
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

	
	

	function getRatGrds($qua) {

        $sqlQString="SELECT a.RATING_GRADE_CODE, a.RATING_GRADE FROM HS_HR_RATING_METHOD_GRADE a, HS_HR_QUALIFICATION b WHERE a.RATING_CODE = b.RATING_CODE AND b.QUALIFI_CODE = '" .$qua . "'";
		$sqlQString=strtolower($sqlQString);

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


	function getAssEmpQualSub($arr) {
		
		$tableName = 'HS_HR_EMP_SUBJECT';

		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'QUALIFI_CODE';
		$arrFieldList[2] = 'SBJ_CODE';
		$arrFieldList[3] = 'ESBJ_MARKS';
		$arrFieldList[4] = 'ESBJ_YEAR';
		$arrFieldList[5] = 'ESBJ_COMMENTS';
		$arrFieldList[6] = 'RATING_GRADE_CODE';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->selectOneRecordFiltered($arr,1);
		
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
