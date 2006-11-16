<?php
if(!isset($_SESSION))
	session_start();
if (isset($_SESSION['error'])) {
	unset($_SESSION['error']);
}	
require ROOT_PATH.'/upgrader/Restore.php';
if(isset($_SESSION['DATABASE_BACKUP'])) {
$con = mysql_connect($_SESSION['dbInfo']['dbHostName'].':'.$_SESSION['dbInfo']['dbHostPort'], 		$_SESSION['dbInfo']['dbUserName'], $_SESSION['dbInfo']['dbPassword']);
$restore = new Restore();
$restore->setConnection($con);
$restore->setDatabase($_SESSION['dbInfo']['dbName']);
$restore->setfileSource($_SESSION['DATABASE_BACKUP']);
$restore->fillDatabase();
$_SESSION['DATABASE_BACKUP']="";
}
?>