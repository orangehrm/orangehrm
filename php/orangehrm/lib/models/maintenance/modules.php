<?php
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

class Modules {
	var $tableName = 'hs_hr_module';
	
	var $id;
	var $moduleName;
	var $owner;
	var $ownerEmail;
	var $version;
	var $description;
	
	
	var $arrayDispList;
	
	
	function Modules(){
		$this->sql_builder = new SQLQBuilder();
		$this->dbConnection = new DMLFunctions();		
	}
	
	function setModuleId($id){
		$this->id = $id;
	}
	
	function setModuleName($moduleName){
		$this->moduleName = $moduleName;
	}
	
	function setOwner($owner){
		$this->owner = $owner;
	}

	function setOwnerEmail($ownerEmail){
		$this->ownerEmail = $ownerEmail;
	}
	
	function setVersion($version){
		$this->version = $version;
	}
	
	function setDescription($description){
		$this->description = $description;
	}
	
		
	//////
	function getModuleId(){
		return $this->id;
	}
	
	function getModuleName(){
		return $this->moduleName;
	}
	
	function getOwner(){
		return $this->owner;
	}

	function getOwnerEmail(){
		return $this->ownerEmail;
	}
	
	function getVersion(){
		return $this->version;
	}
	
	function getDescription(){
		return $this->description;
	}
	
	///
		
	function getListOfModules($pageNO,$schStr,$mode){
		
		$arrFieldList[0] = 'mod_id';
		$arrFieldList[1] = 'name';
	
		
		$this->sql_builder->table_name = $this->tableName;
		$this->sql_builder->flg_select = 'true';
		$this->sql_builder->arr_select = $arrFieldList;
		
		$sqlQString =$this->sql_builder->passResultSetMessage($pageNO,$schStr,$mode);
		
		$message2 = $this->dbConnection -> executeQuery($sqlQString);
		
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
	
	function countModules($schStr,$mode) {
		
		$arrFieldList[0] = 'mod_id';
		$arrFieldList[1] = 'name';
	
		$sql_builder = new SQLQBuilder();
		$sql_builder->table_name = $this->tableName;
		
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->countResultset($schStr,$mode);
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$line = mysql_fetch_array($message2, MYSQL_NUM);
		 	
	    	return $line[0];
	}

	function addModules(){
		$arrFieldList[0] = "'". $this->getModuleId() . "'";
		$arrFieldList[1] = "'". $this->getModuleName() . "'";
		$arrFieldList[2] = "'". $this->getOwner() . "'";
		$arrFieldList[3] = "'". $this->getOwnerEmail() . "'";
		$arrFieldList[4] = "'". $this->getVersion() . "'";
		$arrFieldList[5] = "'". $this->getDescription() . "'";
				
		$arrRecordsList[0] = 'mod_id';
		$arrRecordsList[1] = 'name';
		$arrRecordsList[2] = 'owner';
		$arrRecordsList[3] = 'owner_email';
		$arrRecordsList[4] = 'version';
		$arrRecordsList[5] = 'description';
				
		$this->sql_builder->table_name = $this->tableName;
		$this->sql_builder->flg_insert = 'true';
		$this->sql_builder->arr_insertfield = $arrRecordsList;
		$this->sql_builder->arr_insert = $arrFieldList;		
	
		$sqlQString = $this->sql_builder->addNewRecordFeature2();
		
		$message2 = $this->dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		 return $message2;
		 echo $message2;
	}
	
	function updateModules(){
		$arrFieldList[0] = "'". $this->getModuleId() . "'";
		$arrFieldList[1] = "'". $this->getModuleName() . "'";
		$arrFieldList[2] = "'". $this->getOwner() . "'";
		$arrFieldList[3] = "'". $this->getOwnerEmail() . "'";
		$arrFieldList[4] = "'". $this->getVersion() ."'";
		$arrFieldList[5] = "'". $this->getDescription() ."'";
				
		$arrRecordsList[0] = 'mod_id';
		$arrRecordsList[1] = 'name';
		$arrRecordsList[2] = 'owner';
		$arrRecordsList[3] = 'owner_email';
		$arrRecordsList[4] = 'version';
		$arrRecordsList[5] = 'description';
		
		$this->sql_builder->table_name = $this->tableName;
		$this->sql_builder->flg_update = 'true';
		$this->sql_builder->arr_update = $arrRecordsList;	
		$this->sql_builder->arr_updateRecList = $arrFieldList;	
	
		$sqlQString = $this->sql_builder->addUpdateRecord1();
		
		$message2 = $this->dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		return $message2;
	}
	
	function getLastRecord(){
		$arrFieldList[0] = 'mod_id';
		
		$this->sql_builder->table_name = $this->tableName;
		$this->sql_builder->flg_select = 'true';
		$this->sql_builder->arr_select = $arrFieldList;		
	
		$sqlQString = $this->sql_builder->selectOneRecordOnly();
		
		$message2 = $this->dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$common_func = new CommonFunctions();
		
		if (isset($message2)) {
			
			$i=0;
		
		while ($line = mysql_fetch_array($message2, MYSQL_ASSOC)) {		
			foreach ($line as $col_value) {
			$this->singleField = $col_value;
			}		
		}
			
		return $common_func->explodeString($this->singleField,"MOD");
				
		}		
	}
	
	function filterModules($getID) {
		
		$this->ID = $getID;
		$arrFieldList[0] = 'mod_id';
		$arrFieldList[1] = 'name';
		$arrFieldList[2] = 'owner';
		$arrFieldList[3] = 'owner_email';
		$arrFieldList[4] = 'version';
		$arrFieldList[5] = 'description';
						
		$this->sql_builder->table_name = $this->tableName;
		$this->sql_builder->flg_select = 'true';
		$this->sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $this->sql_builder->selectOneRecordFiltered($this->ID);
		
		$message2 = $this->dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
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
	
	function getVersionList(){
		$tabName='hs_hr_versions';
		$arrFieldList[0] = 'id';
		$arrFieldList[1] = 'name';

		$this->sql_builder->table_name = $tabName;
		$this->sql_builder->flg_select = 'true';
		$this->sql_builder->arr_select = $arrFieldList;

		$sqlQString = $this->sql_builder->passResultSetMessage();

		$message2 = $this->dbConnection-> executeQuery($sqlQString); //Calling the addData() function

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
	     	//echo 'fhildufidlkfn';

	     	//Handle Exceptions
	     	//Create Logs

	     }

	}	

	function delModules($arrList) {

		$arrFieldList[0] = 'mod_id';

		$this->sql_builder->table_name = $this->tableName;
		$this->sql_builder->flg_delete = 'true';
		$this->sql_builder->arr_delete = $arrFieldList;

		$sqlQString = $this->sql_builder->deleteRecord($arrList);

		$message2 = $this->dbConnection -> executeQuery($sqlQString); //Calling the addData() function

	}

	
	
		
}
?>