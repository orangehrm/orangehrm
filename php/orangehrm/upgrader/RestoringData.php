<?php
if(!isset($_SESSION))
	session_start();
if (isset($_SESSION['error'])) {
	unset($_SESSION['error']);
}	

//require_once(ROOT_PATH.'/upgrader/applicationSetup.php');
require ROOT_PATH.'/upgrader/Restore.php';

function createDB() {
	
	connectDB();							
	mysql_query("CREATE DATABASE " . $_SESSION['dbInfo']['dbName']);
	
	if(!@mysql_select_db($_SESSION['dbInfo']['dbName'])) {
		$_SESSION['error'] = 'Unable to create Database!';
		return;
	}								
								
								
}

function connectDB() {

	if(!$connect = @mysql_connect($_SESSION['dbInfo']['dbHostName'].':'.$_SESSION['dbInfo']['dbHostPort'], 		$_SESSION['dbInfo']['dbUserName'], $_SESSION['dbInfo']['dbPassword'])) {
		$_SESSION['error'] =  'Database Connection Error!';		
		return;
	}
	return $connect;
}


function fillData($phase=1, $source='/dbscript/dbscript-') {
	$source .= $phase.'.sql';
	connectDB();
	
	error_log (date("r")." Fill Data Phase $phase - Connected to the DB Server\n",3, "log.txt");
	
	if(!mysql_select_db($_SESSION['dbInfo']['dbName'])) {
		$_SESSION['error'] = 'Unable to create Database!';
		error_log (date("r")." Fill Data Phase $phase - Error - Unable to create Database\n",3, "log.txt");
		return;
	}
	
	error_log (date("r")." Fill Data Phase $phase - Selected the DB\n",3, "log.txt");
	error_log (date("r")." Fill Data Phase $phase - Reading DB Script\n",3, "log.txt");
	
	$queryFile = ROOT_PATH . $source;
	$fp    = fopen($queryFile, 'r');
	
	error_log (date("r")." Fill Data Phase $phase - Opened DB Script\n",3, "log.txt");
	
	$query = fread($fp, filesize($queryFile));
	fclose($fp);
	
	error_log (date("r")." Fill Data Phase $phase - Read DB script\n",3, "log.txt");
								
	$dbScriptStatements = explode(";", $query);
	
	error_log (date("r")." Fill Data Phase $phase - There are ".count($dbScriptStatements)." Statements in the DB script\n",3, "log.txt");
								
	for($c=0;(count($dbScriptStatements)-1)>$c;$c++)
		if(!@mysql_query($dbScriptStatements[$c])) {  
			$_SESSION['error'] = mysql_error();
			$error = mysql_error();
			error_log (date("r")." Fill Data Phase $phase - Error Statement # $c \n",3, "log.txt");
			error_log (date("r")." ".$dbScriptStatements[$c]."\n",3, "log.txt");
			return;
		}
									
	if(isset($error))
		return;
}	


function writeLog() {
	$Content = "Client Info\n\n";
	
	$Content .= "User Agent : ".$_SERVER['HTTP_USER_AGENT']."\n";
	$Content .= "Remote Address : ".$_SERVER['REMOTE_ADDR']."\n\n";
	
	$Content .= "Server Info\n\n";
	$Content .= "Host : ".$_SERVER['HTTP_HOST']."\n";	
	$Content .= "PHP Version : ".constant('PHP_VERSION')."\n";
	$Content .= "Server : ".$_SERVER['SERVER_SOFTWARE']."\n";
	$Content .= "Admin : ".$_SERVER['SERVER_ADMIN']."\n\n";
	
	$Content .= "Document Root : ".$_SERVER['DOCUMENT_ROOT']."\n";
	$Content .= "ROOT_PATH : ".ROOT_PATH."\n\n";
	
	$Content .= "OrangeHRM Upgrading Log\n\n";
	
	$filename = 'log.txt';
	$handle = fopen($filename, 'a');
	fwrite($handle, $Content);
	fclose($handle);
}

if (isset($_SESSION['RESTORING'])) {
	connectDB();
	switch ($_SESSION['RESTORING']) {		
		case 0	:	$db = mysql_select_db($_SESSION['dbInfo']['dbName']);
					writeLog();
					error_log (date("r")." DB ".$_SESSION['dbInfo']['dbName']." selected".$db." - Starting\n",3, "log.txt");
					if (!$db) {
						error_log (date("r")." DB Creation - Starting\n",3, "log.txt");
						createDB();
						error_log (date("r")." DB Creation - Done\n",3, "log.txt");
						if (!isset($error) || !isset($_SESSION['error'])) {	
							$_SESSION['RESTORING'] = 1;
							error_log (date("r")." DB Creation - No Errors\n",3, "log.txt");																										
						} else {
							error_log (date("r")." DB Creation - Errors\n",3, "log.txt");
							error_log (date("r")." ".(isset($error)? $error: $_SESSION['error'])."\n",3, "log.txt");
						}
					}else {
						
						$dump = new Backup();
						$connec = connectDB();
						$dump->setConnection($connec);
						$dump->setDatabase($_SESSION['dbInfo']['dbName']);
						$_SESSION['DATABASE_BACKUP']=$dump->dumpDatabase(true);
						
						error_log (date("r")." Going to drop existing database- \n",3, "log.txt");
										  
						@mysql_query('DROP DATABASE `'.$_SESSION['dbInfo']['dbName']."`");
										  
						error_log (date("r")."database ".$_SESSION['dbInfo']['dbName']." is droped".mysql_errno()."- \n",3, "log.txt");
										 
						$_SESSION['RESTORING'] = 0;
				    } 
				   error_log (date("r")." Next step".$_SESSION['RESTORING']." - Starting\n",3, "log.txt");
					break;
		case 1 	:	error_log (date("r")." Fill Data Phase 1 - Starting\n",3, "log.txt");		
					fillData();
					error_log (date("r")." Fill Data Phase 1 - Done\n",3, "log.txt");		
					if (!isset($error) || !isset($_SESSION['error'])) {								
						$_SESSION['RESTORING'] = 2;
						error_log (date("r")." Fill Data Phase 1 - No Errors\n",3, "log.txt");		
					} else {
						error_log (date("r")." Fill Data Phase 1 - Errors\n",3, "log.txt");		
						error_log (date("r")." ".(isset($error)? $error: $_SESSION['error'])."\n",3, "log.txt");
					}																				
					break;			
	    
		case 2	:	error_log (date("r")." Getting the file content  - \n",3, "log.txt");
					error_log (date("r")." File content ok  - \n",3, "log.txt");
					$restorex = new Restore();
					//$connection = mysql_connect($_SESSION['dbInfo']['dbHostName'], $_SESSION['dbInfo']['dbUserName'], $_SESSION['dbInfo']['dbPassword']);	 			//						
					//mysql_close();
					$connec = connectDB();
					$restorex->setConnection($connec);
					$restorex->setDatabase($_SESSION['dbInfo']['dbName']);
					$restorex->setFileSource($_SESSION['FILEDUMP']);
					error_log (date("r")." Fill Data  - Starting\n",3, "log.txt");
					$restorex->fillDatabase();
					error_log (date("r")." Fill Data  finish- \n",3, "log.txt");
					error_log (date("r")." connection".$connec ." finish- \n",3, "log.txt");
					$_SESSION['RESTORING'] = 3;
					break;
	}
}
									
	    	
?>