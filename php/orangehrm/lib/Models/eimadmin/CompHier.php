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

class CompHierachy {

	var $tableName = 'HS_HR_COMPANY_HIERARCHY';
	
	var $hiId;
	var $hiDesc;
    var $hiRelat;
    var $empId;
    var $defLev;
    var $telep;
    var $fax;
    var $email;
    var $url;
    var $logo;
    var $loc;
	var $arrayDispList;
	var $singleField;
	
	
	function CompHierachy() {
		
	}
	
	function setHiId($hiId) {
	
		$this->hiId = $hiId;
	
	}
	
	function setHiDesc($hiDesc) {

		$this->hiDesc = $hiDesc;
    }

    function setHiRelat($hiRelat) {
    
        $this->hiRelat = $hiRelat;
    }

    function setEmpId($empId) {

        $this->empId=$empId;
    }

    function setDefLev($defLev) {

        $this->defLev=$defLev;
    }
    
	function setTelep($telep) {
	
		$this->telep = $telep;
	}
		
	function setFax($fax) {

		$this->fax = $fax;
	}

	function setEmail($email) {

		$this->email = $email;
	}

	function setUrl($url) {

		$this->url = $url;
	}

	function setLoc($loc) {

		$this->loc = $loc;
	}
	function setLogo($logo) {

		$this->logo = $logo;
	}

	function getHiId() {

		return $this->hiId;

	}

	function getHiDesc() {

		return $this->hiDesc;
    }

    function getHiRelat() {

        return $this->hiRelat;
    }

    function getEmpId() {

        return $this->empId;
    }

    function getDefLev() {

        return $this->defLev;
    }

	function getTelep() {

		return $this->telep;
	}

	function getFax() {

		return $this->fax;
	}

	function getEmail() {

		return $this->email;
	}

	function getUrl() {

		return $this->url;
	}

	function getLoc() {

		return $this->loc;
	}
	function getLogo() {

		return $this->logo;
	}


	function getListofCompHierachy($pageNO,$schStr,$mode) {
		
		$tableName = 'HS_HR_COMPANY_HIERARCHY';
		$arrFieldList[0] = 'HIE_CODE';
		$arrFieldList[1] = 'HIE_NAME';

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

	function countCompHierachy($schStr,$mode) {
		
		$tableName = 'HS_HR_COMPANY_HIERARCHY';
		$arrFieldList[0] = 'HIE_CODE';
		$arrFieldList[1] = 'HIE_NAME';

		
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

	function delCompHierachy($arrList) {

		$tableName = 'HS_HR_COMPANY_HIERARCHY';
		$arrFieldList[0] = 'HIE_CODE';
		$arrFieldList[1] = 'HIE_RELATIONSHIP';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_delete = 'true';
		$sql_builder->arr_delete = $arrFieldList;

		$sqlQString = $sql_builder->deleteRecord($arrList);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
	}
	
	function addCompHierachy() {
		
		$arrFieldList[0] = "'". $this->getHiId() . "'";
		$arrFieldList[1] = "'". $this->getHiDesc() . "'";
		$arrFieldList[2] = ($this->getHiRelat()=='0') ? 'null' :"'". $this->getHiRelat() . "'";
		$arrFieldList[3] = ($this->getEmpId()=='0') ? 'null' :"'". $this->getEmpId() . "'";
		$arrFieldList[4] = "'". $this->getDefLev() . "'";
		$arrFieldList[5] = "'". $this->getTelep() . "'";
		$arrFieldList[6] = "'". $this->getFax() . "'";
		$arrFieldList[7] = "'". $this->getEmail() . "'";
		$arrFieldList[8] = "'". $this->getUrl() . "'";
		$arrFieldList[9] = "'". $this->getLogo() . "'";
		$arrFieldList[10] = ($this->getLoc()=='') ? 'null' :"'". $this->getLoc() . "'";


		$tableName = 'HS_HR_COMPANY_HIERARCHY';
	
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
	
	function updateCompHierachy() {

		$this->getHiId();
		$arrRecordsList[0] = "'". $this->getHiId() . "'";
		$arrRecordsList[1] = "'". $this->getHiDesc() . "'";
		$arrRecordsList[2] = ($this->getHiRelat()=='0') ? 'null' :"'". $this->getHiRelat() . "'";
		$arrRecordsList[3] = ($this->getEmpId()=='0') ? 'null' :"'". $this->getEmpId() . "'";
		$arrRecordsList[4] = "'". $this->getDefLev() . "'";
		$arrRecordsList[5] = "'". $this->getTelep() . "'";
		$arrRecordsList[6] = "'". $this->getFax() . "'";
		$arrRecordsList[7] = "'". $this->getEmail() . "'";
		$arrRecordsList[8] = "'". $this->getUrl() . "'";
		$arrRecordsList[9] = "'". $this->getLogo() . "'";
		$arrRecordsList[10] = ($this->getLoc()=='') ? 'null' :"'". $this->getLoc() . "'";

		$tableName = 'HS_HR_COMPANY_HIERARCHY';
		$arrFieldList[0] = 'HIE_CODE';
		$arrFieldList[1] = 'HIE_NAME';
        $arrFieldList[2] = 'HIE_RELATIONSHIP';
        $arrFieldList[3] = 'EMP_NUMBER';
        $arrFieldList[4] = 'DEF_LEVEL';
        $arrFieldList[5] = 'HIE_TELEPHONE';
        $arrFieldList[6] = 'HIE_FAX';
        $arrFieldList[7] = 'HIE_EMAIL';
        $arrFieldList[8] = 'HIE_URL';
        $arrFieldList[9] = 'HIE_LO';
        $arrFieldList[10] = 'LOC_CODE';

	
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
	
	
	function filterCompHierachy($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_COMPANY_HIERARCHY';
		$arrFieldList[0] = 'HIE_CODE';
		$arrFieldList[1] = 'HIE_NAME';
        $arrFieldList[2] = 'HIE_RELATIONSHIP';
        $arrFieldList[3] = 'EMP_NUMBER';
        $arrFieldList[4] = 'DEF_LEVEL';
        $arrFieldList[5] = 'HIE_TELEPHONE';
        $arrFieldList[6] = 'HIE_FAX';
        $arrFieldList[7] = 'HIE_EMAIL';
        $arrFieldList[8] = 'HIE_URL';
        $arrFieldList[9] = 'HIE_LO';
        $arrFieldList[10] = 'LOC_CODE';


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
	    	$arrayDispList[$i][6] = $line[6];
	    	$arrayDispList[$i][7] = $line[7];
	    	$arrayDispList[$i][8] = $line[8];
	    	$arrayDispList[$i][9] = $line[9];
	    	$arrayDispList[$i][10] = $line[10];
	    	$i++;
	    	
	     }
	     
	     if (isset($arrayDispList)) {
	     
			return $arrayDispList;
			
		} else {
		
			$arrayDispList = '';
			return $arrayDispList;
			
		}
				
	}
	
	function getHierachyCodes () {

		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_COMPANY_HIERARCHY';
		$arrFieldList[0] = 'HIE_CODE';
		$arrFieldList[1] = 'HIE_NAME';

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

	function getAssCompHier($lev,$relat) {
		
		$sql_builder = new SQLQBuilder();
			
		$sqlQString = $sql_builder->getAssCompStruct($lev,$relat);
		
			//$exception_handler = new ExceptionHandler();
	  	 	//$exception_handler->logW($sqlQString);
	  	 	
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

	function getHierRelation($hiCode) {

		$c=0;
		while($hiCode != null) {
		$tableName = 'HS_HR_COMPANY_HIERARCHY';
		$arrFieldList[0] = 'HIE_CODE';
        $arrFieldList[1] = 'HIE_RELATIONSHIP';


		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->selectOneRecordFiltered($hiCode);
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$i=0;
		
		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {
		 	
	    	$arrayDispList[$i][0] = $line[0];
	    	$arrayDispList[$i][1] = $line[1];
	    	$i++;
	    	
	     }
	     
	     $hierRelation[$c++] = $hiCode;
	     if(isset($arrayDispList))
	     	$hiCode = $arrayDispList[0][1];
		 else
		 	$hiCode = null;
		 	
		} 
		
		$hierRelation = array_reverse($hierRelation);
		return $hierRelation;
	}
	
	function getLastRecord() {
		
		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_COMPANY_HIERARCHY';
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
}

?>
