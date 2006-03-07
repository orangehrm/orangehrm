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

class CorpTitle {

	var $tableName = 'HS_HR_CORPORATE_TITLE';
	
	var $ctId;
	var $ctDesc;
    var $ctTopLev;
    var $ctHead;
    var $ctNxtUpg;
    var $ctHeadCnt;
    var $ctSalGrdId;
    var $ctRevDat;
    var $ctHirachId;
	var $arrayDispList;
	var $singleField;
	
	
	function CorpTitle() {
		
	}
	
	function setCtId($ctId) {
	
		$this->ctId = $ctId;
	
	}
	
	function setCtDesc($ctDesc) {

		$this->ctDesc = $ctDesc;
    }

    function setCtTopLev($ctTopLev) {
    
        $this->ctTopLev = $ctTopLev;
    }

    function setCtHead($ctHead) {

        $this->ctHead=$ctHead;
    }

    function setCtNxtUpg($ctNxtUpg) {

        $this->ctNxtUpg=$ctNxtUpg;
    }
    
	function setCtHeadCnt($ctHeadCnt) {
	
		$this->ctHeadCnt = $ctHeadCnt;
	}
		
	function setCtSalGrdId($ctSalGrdId) {

		$this->ctSalGrdId = $ctSalGrdId;
	}

	function setCtRevDat($ctRevDat) {

		$this->ctRevDat = $ctRevDat;
	}

	function setCtHirachId($ctHirachId) {

		$this->ctHirachId = $ctHirachId;
	}

	function getCtId() {

		return $this->ctId;

	}

	function getCtDesc() {

		return $this->ctDesc;
    }

    function getCtTopLev() {

        return $this->ctTopLev;
    }

    function getCtHead() {

        return $this->ctHead;
    }

    function getCtNxtUpg() {

        return $this->ctNxtUpg;
    }

	function getCtHeadCnt() {

		return $this->ctHeadCnt;
	}

	function getCtSalGrdId() {

		return $this->ctSalGrdId;
	}

	function getCtRevDat() {

		return $this->ctRevDat;
	}

	function getCtHirachId() {

		return $this->ctHirachId;
	}

	function getListofCorpTitles($pageNO,$schStr,$mode) {
		
		$tableName = 'HS_HR_CORPORATE_TITLE';
		$arrFieldList[0] = 'CT_CODE';
		$arrFieldList[1] = 'CT_NAME';
		
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

	function countCorpTitles($schStr,$mode) {
		
		$tableName = 'HS_HR_CORPORATE_TITLE';
		$arrFieldList[0] = 'CT_CODE';
		$arrFieldList[1] = 'CT_NAME';
		
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

	function delCorpTitles($arrList) {

		$tableName = 'HS_HR_CORPORATE_TITLE';
		$arrFieldList[0] = 'CT_CODE';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_delete = 'true';
		$sql_builder->arr_delete = $arrFieldList;

		$sqlQString = $sql_builder->deleteRecord($arrList);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

	}

	
	function addCorpTitles() {
		
		$this->getCtId();
		$arrFieldList[0] = "'". $this->getCtId() . "'";
		$arrFieldList[1] = "'". $this->getCtDesc() . "'";
		$arrFieldList[2] = "'". $this->getCtTopLev() . "'";
		$arrFieldList[3] = "'". $this->getCtHead() . "'";
		$arrFieldList[4] = ($this->getCtNxtUpg()=='0') ? 'null' :"'". $this->getCtNxtUpg() . "'";
		$arrFieldList[5] = "'". $this->getCtHeadCnt() . "'";
		$arrFieldList[6] = "'". $this->getCtSalGrdId() . "'";


		$tableName = 'HS_HR_CORPORATE_TITLE';
	
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
	
	function updateCorpTitles() {

		$this->getCtId();
		$arrRecordsList[0] = "'". $this->getCtId() . "'";
		$arrRecordsList[1] = "'". $this->getCtDesc() . "'";
		$arrRecordsList[2] = "'". $this->getCtTopLev() . "'";
		$arrRecordsList[3] = "'". $this->getCtHead() . "'";
		$arrRecordsList[4] = ($this->getCtNxtUpg()=='0') ? 'null' :"'". $this->getCtNxtUpg() . "'";
 		$arrRecordsList[5] = "'". $this->getCtHeadCnt() . "'";
		$arrRecordsList[6] = "'". $this->getCtSalGrdId() . "'";

		$arrFieldList[0] = 'CT_CODE';
		$arrFieldList[1] = 'CT_NAME';
		$arrFieldList[2] = 'CT_TOPLEV_FLG';
		$arrFieldList[3] = 'CT_HEADS_FLG';
		$arrFieldList[4] = 'CT_NEXT_UPGRADE';
		$arrFieldList[5] = 'CT_HEADS_COUNT';
		$arrFieldList[6] = 'SAL_GRD_CODE';


		$tableName = 'HS_HR_CORPORATE_TITLE';
	
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
	
	
	function filterCorpTitles($getID) {
		
		$this->getID = $getID;
		$arrFieldList[0] = 'CT_CODE';
		$arrFieldList[1] = 'CT_NAME';
		$arrFieldList[2] = 'CT_TOPLEV_FLG';
		$arrFieldList[3] = 'CT_HEADS_FLG';
		$arrFieldList[4] = 'CT_NEXT_UPGRADE';
		$arrFieldList[5] = 'CT_HEADS_COUNT';
		$arrFieldList[6] = 'SAL_GRD_CODE';


		$tableName = 'HS_HR_CORPORATE_TITLE';

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
	    	$i++;
	    	
	     }
	     
	     if (isset($arrayDispList)) {
	     
			return $arrayDispList;
			
		} else {
		
			$arrayDispList = '';
			return $arrayDispList;
			
		}
				
	}
	
	function getCorpTitles () {

		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_CORPORATE_TITLE';
		$arrFieldList[0] = 'CT_CODE';
		$arrFieldList[1] = 'CT_NAME';

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

	function getSalGrades () {

		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_PR_SALARY_GRADE';
		$arrFieldList[0] = 'SAL_GRD_CODE';
		$arrFieldList[1] = 'SAL_GRD_NAME';

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

	function getLastRecord() {
		
		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_CORPORATE_TITLE';
		$arrFieldList[0] = 'CT_CODE';
				
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
			
		return $common_func->explodeString($this->singleField,"CRT");
				
		}
		
	}	
	
	
}

?>
