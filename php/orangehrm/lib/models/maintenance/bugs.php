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

require_once ROOT_PATH . '/lib/exception/ExceptionHandler.php';

class Bugs {
	var $tableName = 'HS_HR_BUGS';

	var $id;
	var $bugNumber;
	var $dateEntered;
	var $dateModified;
	var $modifiedUserId;
	var $assignedDeveloperId;
	var $deleted;
	var $name;
	var $status;
	var $priority;
	var $description;
	var $createdBy;
	var $resolution;
	var $foundInRelease;
	var $type;
	var $fixedInRelease;
	var $workLog;
	var $source;
	var $module;
	var $email;
	var $sql_builder;
	var $dbConnection;

	var $arrayDispList;

	var $statusArray= array("new","assigned","closed","pending","rejected","reopen");
	var $typeArray = array("defect","feature");
	var $priorityArray = array("low","medium","high","urgent");
	var $resolutionArray = array("accepted","duplicate","fixed","out of date","invalid","later");
	var $sourceArray = array("internal","web","forum");
	var $csrfToken ;

	function Bugs(){
		$this->sql_builder = new SQLQBuilder();
		$this->dbConnection = new DMLFunctions();
	}

	function setBugId($id){
		$this->id = $id;
	}

	function setBugNumber($bugNumber){
		$this->bugNumber = $bugNumber;
	}

	function setDateEntered($dateEntered){
		$this->dateEntered = $dateEntered;
	}

	function setDateModified($dateModified){
		$this->dateModified = $dateModified;
	}

	function setModifiedUserId($modifiedUserId){
		$this->modifiedUserId = $modifiedUserId;
	}

	function setAssignedDeveloperId($assignedDeveloperId){
		$this->assignedDeveloperId = $assignedDeveloperId;
	}

	function setDeleted($deleted){
		$this->deleted = $deleted;
	}

	function setName($name){
		$this->name = $name;
	}

	function setStatus($status){
		$this->status = $status;
	}

	function setPriority($priority){
		$this->priority = $priority;
	}


	function setDescription($description){
		$this->description = $description;
	}

	function setCreatedBy($createdBy){
		$this->createdBy = $createdBy;
	}

	function setResolution($resolution){
		$this->resolution = $resolution;
	}

	function setFoundInrelease($foundInRelease){
		$this->foundInRelease = $foundInRelease;
	}

	function setType($type){
		$this->type = $type;
	}

	function setFixedInRelease ($fixedInRelease){
		$this->fixedInRelease = $fixedInRelease;
	}

	function setWorkLog($workLog){
		$this->workLog = $workLog;
	}

	function setSource($source){
		$this->source = $source;
	}

	function setModule($module){
		$this->module = $module;
	}

	function setEmail($email) {
		$this->email = $email;
	}
	
	function setCsrfToken($csrfToken){
		$this->csrfToken = $csrfToken;
	}

	function getDate(){
		$date = getdate();
		$textDate = $date['year']. "-".$date['mon']."-".$date['mday'] ;

		return $textDate;
	}


	//////
	function getBugId(){
		return $this->id;
	}

	function getBugNumber(){
		return $this->bugNumber;
	}

	function getDateEntered(){
		return $this->dateEntered;
	}

	function getDateModified(){
		return $this->dateModified;
	}

	function getModifiedUserId(){
		return $this->modifiedUserId;
	}

	function getAssignedDeveloperId(){
		return $this->assignedDeveloperId;
	}

	function getDeleted(){
		return $this->deleted;
	}

	function getName(){
		return $this->name;
	}

	function getStatus(){
		return $this->status;
	}

	function getPriority(){
		return $this->priority;
	}

	function getDescription(){
		return $this->description;
	}

	function getCreatedBy(){
		return $this->createdBy;
	}

	function getResolution(){
		return $this->resolution;
	}

	function getFoundInrelease(){
		return $this->foundInRelease;
	}

	function getType(){
		return $this->type;
	}

	function getFixedInRelease (){
		return $this->fixedInRelease;
	}

	function getWorkLog(){
		return $this->workLog;
	}

	function getSource(){
		return $this->source;
	}

	function getEmail() {
		return $this->email;
	}

	function getModule(){
		return $this->module;
	}

	function getCsrfToken(){
		return $this->csrfToken ;
	}

	function getListOfBugs($pageNo,$schStr,$mode){

		$arrFieldList[0] = 'ID';
		$arrFieldList[1] = 'name';


		$this->sql_builder->table_name = $this->tableName;
		$this->sql_builder->flg_select = 'true';
		$this->sql_builder->arr_select = $arrFieldList;

		$sqlQString =$this->sql_builder->passResultSetMessage($pageNo,$schStr,$mode);

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

	function countBugs($schStr,$mode) {

		$arrFieldList[0] = 'ID';
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

	function addBugs(){
		$arrFieldList[0] = "'". $this->getBugId() . "'";
		$arrFieldList[1] = "'". $this->getBugNumber() . "'";
		$arrFieldList[2] = "'". $this->getCreatedBy() . "'";
		$arrFieldList[3] = "'". $this->getDateEntered() . "'";
		$arrFieldList[4] = 'null';//"'". $this->getAssignedDeveloperId() . "'";
		$arrFieldList[5] = "'". $this->getDescription() ."'";
		$arrFieldList[6] = "'". $this->getFoundInrelease() ."'";
		$arrFieldList[7] = "'". $this->getModule() ."'";
		$arrFieldList[8] = "'". $this->getName() ."'";
		$arrFieldList[9] = "'". $this->getPriority() ."'";
		$arrFieldList[10] = "'". $this->getSource() ."'";
		$arrFieldList[11] = "'". $this->getStatus() ."'";
		$arrFieldList[12] = "'". $this->getType() ."'";
		$arrFieldList[13] = "'". $this->getWorkLog() ."'";

		$screenParam = array('uniqcode' => 'BUG');
		$tokenGenerator = CSRFTokenGenerator::getInstance();
		$tokenGenerator->setKeyGenerationInput($screenParam);
		$token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
		
		$bugSent = false;
		
		if( $token == $this->getCsrfToken()){
			                  	
			$sysConst = new sysConf();
	
			$to = 'koshika@beyondm.net';
		 	$body = "Reported Date:".date("Y-m-d")."\n"."Name:".$this->getName()."\nModule:" .$this->getModule(). "\n Priority:".$this->getPriority()."\n". "Description:".$this->getDescription(). "\n";
			$subject = "Bug Reported";
			$headers = 'From: '. ($this->getEmail() != '') ? $this->getEmail() : 'noname@none.net' . "\r\n" .'Reply-To: ' . ($this->getEmail() != '') ? $this->getEmail() : 'noname@none.net' . "\r\n" ;
	
			$emailSent = $this->sendMail($to,$subject,$body,$headers);
	
			$description = "Module: " .$this->getModule(). "\n Description:".$this->getDescription(). "\n Email: ". $this->getEmail();
	
			$host = 'sourceforge.net';
			$method = 'POST';
			$path = '/tracker/?func=add&group_id=156477&atid=799942';
			$data = "group_id=156477&atid=799942&func=postadd&category_id=" .$this->getSource(). "&artifact_group_id=" . $this->getFoundInrelease(). "&summary=" .$this->getName(). "&details=" .$description ."&priority=" .$this->getPriority();
	
		    $fp = fsockopen($host, 80);
	
		    fputs($fp, "POST $path HTTP/1.1\r\n");
		    fputs($fp, "Host: $host\r\n");
		    fputs($fp,"Content-type: application/x-www-form-urlencoded\r\n");
		    fputs($fp, "Content-length: " . strlen($data) . "\r\n");
		    fputs($fp, "User-Agent: ".$_SERVER['HTTP_USER_AGENT']."\r\n");
		    fputs($fp, "Connection: close\r\n\r\n");
		    fputs($fp, $data);
	
		    
		    $ostr = '';
		    while (!feof($fp)) {
		        $ostr .= fgets($fp,128);
	
		        if(strstr($ostr, 'Item Successfully Created') !== false) {
		        	$bugSent = true;
		        	break;
		        }
		    }
	
		    fclose($fp);
		}
		return $bugSent;
	}

	function updateBugs(){
		$arrFieldList[0] = "'". $this->getBugId() . "'";
		$arrFieldList[1] = "'". $this->getBugNumber() . "'";
		$arrFieldList[2] = "'". $this->getDateModified() . "'";
		$arrFieldList[3] = 'null'; //"'". $this->getAssignedDeveloperId() . "'";
		$arrFieldList[4] = "'". $this->getDeleted() ."'";
		$arrFieldList[5] = "'". $this->getDescription() ."'";
		$arrFieldList[6] = "'". $this->getFixedInRelease() ."'";
		$arrFieldList[7] = "'". $this->getModifiedUserId() ."'";
		$arrFieldList[8] = "'". $this->getModule() ."'";
		$arrFieldList[9] = "'". $this->getName() ."'";
		$arrFieldList[10] = "'". $this->getPriority() ."'";
		$arrFieldList[11] = "'". $this->getResolution() ."'";
		$arrFieldList[12] = "'". $this->getSource() ."'";
		$arrFieldList[13] = "'". $this->getStatus() ."'";
		$arrFieldList[14] = "'". $this->getType() ."'";
		$arrFieldList[15] = "'". $this->getWorkLog() ."'";

		$arrRecordsList[0] = 'id';
		$arrRecordsList[1] = 'number';
		$arrRecordsList[2] = 'date_modified';
		$arrRecordsList[3] = 'assigned_developer_id';
		$arrRecordsList[4] = 'deleted';
		$arrRecordsList[5] = 'description';
		$arrRecordsList[6] = 'fixed_in_release';
		$arrRecordsList[7] = 'modified_user_id';
		$arrRecordsList[8] = 'module';
		$arrRecordsList[9] = 'name';
		$arrRecordsList[10] = 'priority';
		$arrRecordsList[11] = 'resolution';
		$arrRecordsList[12] = 'source';
		$arrRecordsList[13] = 'status';
		$arrRecordsList[14] = 'type';
		$arrRecordsList[15] = 'work_log';

		$this->sql_builder->table_name = $this->tableName;
		$this->sql_builder->flg_update = 'true';
		$this->sql_builder->arr_update = $arrRecordsList;
		$this->sql_builder->arr_updateRecList = $arrFieldList;

		$sqlQString = $this->sql_builder->addUpdateRecord1();

		$message2 = $this->dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$sysConst = new sysConf();

		$modlist = $this->getModulesCodes();
		for($c=0;count($modlist)>$c;$c++)
			if($this->getModule()==$modlist[$c][0])
				break;

		$to = $modlist[$c][2];
		$body = "Modified Date:".date("Y-m-d")."\n"."Name:".$this->getName()."\nModule: " .$modlist[$c][1]. "\nStatus:".$this->getStatus()."\n"."Priority:".$this->getPriority()."\n". "Resolution:" .$this->getResolution(). "\n". "Description:".$this->getDescription(). "\n"."WorkLog:".$this->getWorkLog();
		$subject = "Report Bug";
		$headers = 'From: ' . $sysConst->userEmail . "\r\n" .'Reply-To: ' . $sysConst->userEmail . "\r\n" ;

   		$emailSent = $this->sendMail($to,$subject,$body,$headers);

		 return array($message2,$emailSent);
	}

	function getNextNumber(){
		$arrFieldList[0] = 'number';

		$this->sql_builder->table_name = $this->tableName;
		$this->sql_builder->flg_select = 'true';
		$this->sql_builder->arr_select = $arrFieldList;

		$sqlQString = $this->sql_builder->selectOneRecordOnly();

		$message2 = $this->dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		if (isset($message2)) {

			$i=0;

		while ($line = mysql_fetch_array($message2, MYSQL_ASSOC)) {
			foreach ($line as $col_value) {
			$this->singleField = $col_value;
			}
		}
		}

		return (int)($this->singleField)+1;

	}

	function filterBugs($getID) {

		$this->ID = $getID;
		$arrFieldList[0] = 'id';
		$arrFieldList[1] = 'number';
		$arrFieldList[2] = 'date_modified';
		$arrFieldList[3] = 'assigned_developer_id';
		$arrFieldList[4] = 'deleted';
		$arrFieldList[5] = 'description';
		$arrFieldList[6] = 'fixed_in_release';
		$arrFieldList[7] = 'modified_user_id';
		$arrFieldList[8] = 'module';
		$arrFieldList[9] = 'name';
		$arrFieldList[10] = 'priority';
		$arrFieldList[11] = 'resolution';
		$arrFieldList[12] = 'source';
		$arrFieldList[13] = 'status';
		$arrFieldList[14] = 'type';
		$arrFieldList[15] = 'work_log';
		$arrFieldList[16] = 'date_entered';
		$arrFieldList[17] = 'created_by';
		$arrFieldList[18] = 'found_in_release';

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
	    	$arrayDispList[$i][6] = $line[6];
	    	$arrayDispList[$i][7] = $line[7];
	    	$arrayDispList[$i][8] = $line[8];
	    	$arrayDispList[$i][9] = $line[9];
	    	$arrayDispList[$i][10] = $line[10];
	    	$arrayDispList[$i][11] = $line[11];
	    	$arrayDispList[$i][12] = $line[12];
	    	$arrayDispList[$i][13] = $line[13];
	    	$arrayDispList[$i][14] = $line[14];
	    	$arrayDispList[$i][15] = $line[15];
	    	$arrayDispList[$i][16] = $line[16];
	    	$arrayDispList[$i][17] = $line[17];
	    	$arrayDispList[$i][18] = $line[18];
	    	$i++;

	     }

	     if (isset($arrayDispList)) {

			return $arrayDispList;

		} else {

			$arrayDispList = '';
			return $arrayDispList;

		}

	}

	function getAlias($Alias){


		switch ($Alias){
			case "version":
			$returnArray = $this->getValue('hs_hr_versions','ID','name');
			break;

			case "user":
			$returnArray = $this->getValue('hs_hr_users','ID','user_name');
			break;

			case "module":
			$returnArray = $this->getValue('hs_hr_module','MOD_ID','name');
			break;

			case "developer":
			$returnArray = $this->getValue('hs_hr_developer','ID','last_name');
			break;


			default:
			$returnArray='';

		}
		return $returnArray;

	}

	function getValue($tablename, $col1,$col2){

		$tabName= $tablename;
		$arrFieldList[0] = $col1;
		$arrFieldList[1] = $col2;

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

	function getArrayValues($Alias){
		switch ($Alias){
			case "status":
			$returnArray = $this->statusArray;
			break;

			case "type":
			$returnArray = $this->typeArray;
			break;

			case "priority":
			$returnArray = $this->priorityArray;
			break;

			case "source":
			$returnArray = $this->sourceArray;
			break;

			case "resolution":
			$returnArray = $this->resolutionArray;
			break;


			default:
			$returnArray='';
		}
		return $returnArray;

	}

	function sendMail($to, $subject, $body, $headers){

		$sysConf = new Conf();

		ini_set('SMTP',$sysConf->smtphost);

		//@mail($to, $subject, $body, $headers);

	return true;
	}

	function getModulesCodes() {

		$arrFieldList[0] = 'mod_id';
		$arrFieldList[1] = 'name';
		$arrFieldList[2] = 'owner_email';

		$this->sql_builder->table_name = 'hs_hr_module';
		$this->sql_builder->flg_select = 'true';
		$this->sql_builder->arr_select = $arrFieldList;

		$sqlQString =$this->sql_builder->passResultSetMessage();

		$message2 = $this->dbConnection -> executeQuery($sqlQString);

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

}
?>