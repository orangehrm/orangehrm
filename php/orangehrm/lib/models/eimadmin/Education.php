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
require_once ROOT_PATH . '/lib/common/UniqueIDGenerator.php';

class Education {

	var $tableName = 'hs_hr_education';

	var $eduId;
	var $eduUni;
	var $eduDeg;
	var $arrayDispList;
	var $singleField;


	function Education() {

	}

	function setEduId($eduId) {

		$this->eduId = $eduId;

	}

	function setEduUni($eduUni) {

        $this->eduUni=$eduUni;
     }

	function setEduDeg($eduDeg) {

		$this->eduDeg = $eduDeg;
	}


    function getEduId() {

		return $this->eduId;

	}

	function getEduUni() {

        return $this->eduUni;
     }

	function getEduDeg() {

		return $this->eduDeg;
	}

	function getListofEducation($pageNO,$schStr,$mode, $sortField = 0, $sortOrder = 'ASC') {

		$tableName = 'HS_HR_EDUCATION';
		$arrFieldList[0] = 'EDU_CODE';
		$arrFieldList[1] = 'EDU_DEG';
		$arrFieldList[2] = 'EDU_UNI';


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

	function countEducation($schStr,$mode) {

		$tableName = 'HS_HR_EDUCATION';
		$arrFieldList[0] = 'EDU_CODE';
		$arrFieldList[1] = 'EDU_DEG';
		$arrFieldList[2] = 'EDU_UNI';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->countResultset($schStr,$mode);

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

			$line = mysql_fetch_array($message2, MYSQL_NUM);

	    	return $line[0];
	}

	function delEducation($arrList) {

		$tableName = 'HS_HR_EDUCATION';
		$arrFieldList[0] = 'EDU_CODE';

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


	function addEducation() {

		if ($this->_isDuplicateName()) {
			throw new EducationException("Duplicate name", 1);
		}
		
		$tableName = 'hs_hr_education';
		$this->eduId = UniqueIDGenerator::getInstance()->getNextID($tableName, 'edu_code', 'EDU');

		$arrFieldList[0] = "'". $this->getEduId() . "'";
		$arrFieldList[1] = "'". $this->getEduUni() . "'";
		$arrFieldList[2] = "'". $this->getEduDeg() . "'";

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

	function updateEducation() {

		if ($this->_isDuplicateName(true)) {
			throw new EducationException("Duplicate name", 1);
		}
		
		$this->getEduId();
		$arrRecordsList[0] = "'". $this->getEduId() . "'";
		$arrRecordsList[1] = "'". $this->getEduUni() . "'";
		$arrRecordsList[2] = "'". $this->getEduDeg() . "'";


		$arrFieldList[0] = 'EDU_CODE';
		$arrFieldList[1] = 'EDU_UNI';
		$arrFieldList[2] = 'EDU_DEG';


		$tableName = 'HS_HR_EDUCATION';

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


	function filterEducation($getID) {

		$this->getID = $getID;
		$tableName = 'HS_HR_EDUCATION';
		$arrFieldList[0] = 'EDU_CODE';
		$arrFieldList[1] = 'EDU_UNI';
		$arrFieldList[2] = 'EDU_DEG';

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

	function filterGetQualifiInfo($getID) {

		$this->getID = $getID;
		$tableName = 'HS_HR_EDUCATION';
		$arrFieldList[0] = 'EDU_CODE';
		$arrFieldList[1] = 'EDU_UNI';
		$arrFieldList[2] = 'EDU_DEG';

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

	function filterNotEqualEducation($getID) {

		$this->getID = $getID;

		$tableName = 'HS_HR_EDUCATION';
		$arrFieldList[0] = 'EDU_CODE';
		$arrFieldList[1] = 'EDU_UNI';
		$arrFieldList[2] = 'EDU_DEG';

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
	/*
		function getEducation() {

		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_EDUCATION';
		$arrFieldList[0] = 'EDU_CODE';
		$arrFieldList[1] = 'EDU_UNI';
		$arrFieldList[2] = 'EDU_GEG';


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

	    	$i++;

	     }

	     if (isset($arrayDispList)) {

	       	return $arrayDispList;

	     } else {

	     	//Handle Exceptions
	     	//Create Logs

	     }

	}   */

	function getAllEducation() {

		$tableName = 'HS_HR_EDUCATION';
		$arrFieldList[0] = 'EDU_CODE';
		$arrFieldList[1] = 'EDU_UNI';
		$arrFieldList[2] = 'EDU_DEG';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultSetMessage();

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

	function getEducation($getID) {

		$this->getID = $getID;
		$tableName = 'HS_HR_EDUCATION';
		$arrFieldList[0] = 'EDU_CODE';
		$arrFieldList[1] = 'EDU_UNI';
		$arrFieldList[2] = 'EDU_DEG';

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

	/*
	//////////////////////////////////////////////////////////////////////////////////
	function getQualCodes() {

		$tableName = 'HS_HR_QUALIFICATION';
		$arrFieldList[0] = 'QUALIFI_CODE';
		$arrFieldList[1] = 'QUALIFI_NAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultSetMessage();

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

////////////////////////////////////////////////////////////////////////////////
	function getUnAssEducation($eno,$typ) {
		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_EDUCATION';
		$arrFieldList[0] = 'QUALIFI_CODE';
		$arrFieldList[1] = 'QUALIFI_NAME';

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;
		$sql_builder->field='QUALIFI_CODE';
		$sql_builder->table2_name= 'HS_HR_EMP_QUALIFICATION';
		$arr1[0][0]='EMP_NUMBER';
		$arr1[0][1]=$eno;
		$arr2[0][0]='QUALIFI_TYPE_CODE';
		$arr2[0][1]=$typ;

		$sqlQString = $sql_builder->selectFilter($arr1,$arr2);

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
	}  */
	
	private function _isDuplicateName($update=false) {
		$education = $this->filterExistingEducations();

		if (is_array($education)) {
			if ($update) {
				if ($education[0][0] == $this->getEduId()){
					return false;
				}
			}
			return true;
		}

		return false;
	}

	public function filterExistingEducations() {

		$selectFields[] ='`edu_code`'; 
        $selectFields[] = '`edu_uni`';	    
	    $selectFields[] = '`edu_deg`';	
        $selectTable = $this->tableName;

        $selectConditions[] = "`edu_deg` = '".$this->getEduDeg()."'";
	    $selectConditions[] = "`edu_uni` = '".$this->getEduUni()."'";	   
         
        $sqlBuilder = new SQLQBuilder();
        $query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions);
         
        $dbConnection = new DMLFunctions();
        $result = $dbConnection->executeQuery($query);

        $cnt = 0;

        while ($row = mysql_fetch_array($result, MYSQL_NUM)){
            $existingEducations[$cnt++] = $row;
        }

        if (isset($existingEducations)) {
            return  $existingEducations;
        } else {
            $existingEducations = '';
            return  $existingEducations;
        }
	}
}
class EducationException extends Exception {
}
?>
