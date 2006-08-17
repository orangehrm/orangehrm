<?php

function createDB() {
	
	connectDB();							
	mysql_query("CREATE DATABASE " . $_SESSION['dbInfo']['dbName']);
	
	if(!mysql_select_db($_SESSION['dbInfo']['dbName'])) {
		$_SESSION['error'] = 'Unable to create Database!';
		return;
	}								
								
								
}

function connectDB() {

	if(!@mysql_connect($_SESSION['dbInfo']['dbHostName'].':'.$_SESSION['dbInfo']['dbHostPort'], 		$_SESSION['dbInfo']['dbUserName'], $_SESSION['dbInfo']['dbPassword'])) {
		$_SESSION['error'] =  'Database Connection Error!';		
		return;
	}
	
}

function fillData() {

	connectDB();
	
	if(!mysql_select_db($_SESSION['dbInfo']['dbName'])) {
		$_SESSION['error'] = 'Unable to create Database!';
		return;
	}
	
	$queryFile = ROOT_PATH . "/dbscript/dbscript.sql";
	$fp    = fopen($queryFile, 'r');
	$query = fread($fp, filesize($queryFile));
	fclose($fp);
								
	$dbScriptStatements = explode(";", $query);
								
	for($c=0;(count($dbScriptStatements)-1)>$c;$c++)
		if(!mysql_query($dbScriptStatements[$c])) {  
			$_SESSION['error'] = mysql_error();
			return;
		}
									
	if(isset($error))
		return;
}	

function createUser() {

	if(isset($_SESSION['dbInfo']['dbOHRMUserName'])) {
	
		$dbName = $_SESSION['dbInfo']['dbName'];
		$dbOHRMUser = $_SESSION['dbInfo']['dbOHRMUserName'];
		$dbOHRMPassword = $_SESSION['dbInfo']['dbOHRMPassword'];
	
      	$query = <<< USRSQL
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, DROP, INDEX
ON `$dbName`.*
TO "$dbOHRMUser"@"localhost"
IDENTIFIED BY '$dbOHRMPassword';
USRSQL;

      	if(!mysql_query($query)) {
         	$_SESSION['error'] = mysql_error();
         	return;
      	}

      	$query = <<< USRSQL
set password for "$dbOHRMUser"@"localhost"
 = old_password('$dbOHRMPassword');
USRSQL;

      	if(!mysql_query($query)) {
        	$_SESSION['error'] = mysql_error();
         	return;
      	}
      
	  	$dbName = $_SESSION['dbInfo']['dbName'];
	  	$dbOHRMUser = $_SESSION['dbInfo']['dbOHRMUserName'];
		$dbOHRMPassword = $_SESSION['dbInfo']['dbOHRMPassword'];
	
      	$query = <<< USRSQL
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, DROP, INDEX
ON `$dbName`.*
TO "$dbOHRMUser"@"%"
IDENTIFIED BY '$dbOHRMPassword';
USRSQL;

      	if(!mysql_query($query)) {
         	$_SESSION['error'] = mysql_error();
         	return;
      	}

      	$query = <<< USRSQL
set password for "$dbOHRMUser"@"%"
 = old_password('$dbOHRMPassword');
USRSQL;

      	if(!mysql_query($query)) {
         	$_SESSION['error'] = mysql_error();
         	return;
      	}
	}

	if(!mysql_select_db($_SESSION['dbInfo']['dbName'])) {
		$_SESSION['error'] = 'Unable to access OrangeHRM Database!';
		return;
	}
								
	$query = "INSERT INTO `hs_hr_users` VALUES ('USR001','" .$_SESSION['defUser']['AdminUserName']. "','".md5($_SESSION['defUser']['AdminPassword'])."','Admin','',null,'','Yes','1','','0000-00-00 00:00:00','0000-00-00 00:00:00',null,null,'','','','','','','','','','Enabled','','','','','','',0,'','USG001')";

	if(!mysql_query($query)) {
		$_SESSION['error'] = 'Unable to Create OrangeHRM Admin User Account';
		return;
	}

}

function writeConfFile() {

	$dbHost = $_SESSION['dbInfo']['dbHostName'];
	$dbHostPort = $_SESSION['dbInfo']['dbHostPort'];
	$dbName = $_SESSION['dbInfo']['dbName'];
							  
	if(isset($_SESSION['dbInfo']['dbOHRMUserName'])) {
		$dbOHRMUser = $_SESSION['dbInfo']['dbOHRMUserName'];
		$dbOHRMPassword = $_SESSION['dbInfo']['dbOHRMPassword'];
	} else {	
		$dbOHRMUser = $_SESSION['dbInfo']['dbUserName'];
		$dbOHRMPassword = $_SESSION['dbInfo']['dbPassword'];
	}

    $confContent = <<< CONFCONT
<?
class Conf {

	var \$smtphost;
	var \$dbhost;
	var \$dbport;
	var \$dbname;
	var \$dbuser;
	var \$dbpass;

	function Conf() {
		
	\$this->dbhost	= '$dbHost';
	\$this->dbport 	= '$dbHostPort';
	\$this->dbname	= '$dbName';
	\$this->dbuser	= '$dbOHRMUser';
	\$this->dbpass	= '$dbOHRMPassword';
	\$this->smtphost = 'mail.beyondm.net';
	}
}
?>
CONFCONT;
						      
	$filename = ROOT_PATH . '/lib/confs/Conf.php';
	$handle = fopen($filename, 'w');
	fwrite($handle, $confContent);
	 
    fclose($handle);

}						

   if (isset($_SESSION['INSTALLING'])) {
	switch ($_SESSION['INSTALLING']) {		
		case 0	:	createDB();	
					$_SESSION['INSTALLING'] = 1;																										
					break;
								
		case 1	:	fillData();								
					$_SESSION['INSTALLING'] = 2;																				
					break;
									
		case 2	:	createUser();
					$_SESSION['INSTALLING'] = 3;													
					break;
								
		case 3 :	writeConfFile();
					$_SESSION['INSTALLING'] = 4;																
					break;					
		
	}
  }
?>