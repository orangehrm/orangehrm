<?php
session_start();

function backupData() {
	require_once 'Backup.php';

	$backupObj = new Backup();

	$db = $_SESSION['dbInfo']['dbName'];
	$conn = connectDB();

	$backupObj->setDatabase($db);
	$backupObj->setConnection($conn);

	return $backupObj->dumpDatabase();

}

function connectDB() {

	if(!@$conn = mysql_connect($_SESSION['dbInfo']['dbHostName'].':'.$_SESSION['dbInfo']['dbHostPort'], 		$_SESSION['dbInfo']['dbUserName'], $_SESSION['dbInfo']['dbPassword'])) {
		$_SESSION['error'] =  'Database Connection Error!';
		return $conn;
	}

}

if (isset($_SESSION['dbInfo'])) {
	$file = backupData();

	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private", false);
	header("Content-Type: application/sql");
	header('Content-Disposition: attachment; filename="OrangeHRM-backup.sql";');
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: " .strlen($file));

	echo $file;
} else {
	echo 'No file';
}
?>