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

class Branches {

	var $tableName = 'HS_HR_BRANCH';
	
	var $brchId;
    var $bnkId;
	var $brchDesc;
    var $brchAddr;
    var $brchSlipTrf;
    var $brchClrHos;
	var $arrayDispList;
	var $singleField;
	
	
	function Branches() {
		
	}
	
	function setBrchId($brchId) {
	
		$this->brchId = $brchId;
	
	}
	
	function setBrchBankId($bnkId) {

		$this->bnkId = $bnkId;
    }

    function setBrchAddr($brchAddr) {
    
        $this->brchAddr = $brchAddr;
    }

    function setBrchSlipTrf($brchSlipTrf) {

        $this->brchSlipTrf=$brchSlipTrf;
    }

    function setBrchClrHos($brchClrHos) {

        $this->brchClrHos=$brchClrHos;
    }
    
	function setBrchDesc($brchDesc) {
	
		$this->brchDesc = $brchDesc;
	}
		
	
	function getBrchId() {
	
		return $this->brchId;
	
	}
	
	function getBrchDesc() {
	
		return $this->brchDesc;
		
	}
////
	function getBrchBankId() {

		return $this->bnkId;
    }

    function getBrchAddr() {

        return $this->brchAddr;
    }

    function getBrchSlipTrf() {

        return $this->brchSlipTrf;
    }

    function getBrchClrHos() {

        return $this->brchClrHos;
    }

////
	function getListofBranches($pageNO,$schStr,$mode) {
		
		$tableName = 'HS_HR_BRANCH';
		$arrFieldList[0] = 'BBRANCH_CODE';
		$arrFieldList[1] = 'BBRANCH_NAME';
		
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

	function countBranches($schStr,$mode) {
		
		$tableName = 'HS_HR_BRANCH';
		$arrFieldList[0] = 'BBRANCH_CODE';
		$arrFieldList[1] = 'BBRANCH_NAME';
		
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

	function delBranches($arrList) {

		$tableName = 'HS_HR_BRANCH';
		$arrFieldList[0] = 'BBRANCH_CODE';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_delete = 'true';
		$sql_builder->arr_delete = $arrFieldList;

		$sqlQString = $sql_builder->deleteRecord($arrList);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

	}

	
	function addBranches() {
		
		$this->getBrchId();
		$arrFieldList[0] = "'". $this->getBrchId() . "'";
		$arrFieldList[1] = "'". $this->getBrchBankId() . "'";
		$arrFieldList[2] = "'". $this->getBrchDesc() . "'";
		$arrFieldList[3] = "'". $this->getBrchAddr() . "'";
		$arrFieldList[4] = "'". $this->getBrchSlipTrf() . "'";
		$arrFieldList[5] = "'". $this->getBrchClrHos() . "'";


/*
	var $brchId;
    var $bnkId;
	var $brchDesc;
    var $brchAddr;
    var $brchSlipTrf;
    var $brchClrHos;

*/		//$arrFieldList[0] = 'CURRENCY_ID';
		//$arrFieldList[1] = 'CURRENCY_NAME';
		
		$tableName = 'HS_HR_BRANCH';
	
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
	
	function updateBranches() {
		
		$this->getBrchId();
		$arrRecordsList[0] = "'". $this->getBrchId() . "'";
		$arrRecordsList[1] = "'". $this->getBrchBankId() . "'";
		$arrRecordsList[2] = "'". $this->getBrchDesc() . "'";
		$arrRecordsList[3] = "'". $this->getBrchAddr() . "'";
		$arrRecordsList[4] = "'". $this->getBrchSlipTrf() . "'";
		$arrRecordsList[5] = "'". $this->getBrchClrHos() . "'";
		$arrFieldList[0] = 'BBRANCH_CODE';
		$arrFieldList[1] = 'BANK_CODE';
		$arrFieldList[2] = 'BBRANCH_NAME';
		$arrFieldList[3] = 'BBRANCH_ADDRESS';
		$arrFieldList[4] = 'BBRANCH_SLIPTRANSFERS_FLG';
		$arrFieldList[5] = 'BBRANCH_AUTO_CLR_HOUSE_CODE';

		$tableName = 'HS_HR_BRANCH';
	
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
	
	
	function filterBranches($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_BRANCH';
		$arrFieldList[0] = 'BBRANCH_CODE';
		$arrFieldList[1] = 'BANK_CODE';
		$arrFieldList[2] = 'BBRANCH_NAME';
		$arrFieldList[3] = 'BBRANCH_ADDRESS';
		$arrFieldList[4] = 'BBRANCH_SLIPTRANSFERS_FLG';
		$arrFieldList[5] = 'BBRANCH_AUTO_CLR_HOUSE_CODE';

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
	
	function getBankCodes () {

		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_BANK';
		$arrFieldList[0] = 'BANK_CODE';
		$arrFieldList[1] = 'BANK_NAME';

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
		$tableName = 'HS_HR_BRANCH';
		$arrFieldList[0] = 'BBRANCH_CODE';
				
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
			
		return $common_func->explodeString($this->singleField,"BBK");
				
		}
		
	}	
	
	
}

?>
