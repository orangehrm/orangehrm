<?php

// Cleaning up
function connectDB() {

	if(!@mysql_connect($_SESSION['dbInfo']['dbHostName'].':'.$_SESSION['dbInfo']['dbHostPort'], 		$_SESSION['dbInfo']['dbUserName'], $_SESSION['dbInfo']['dbPassword'])) {
		$_SESSION['error'] =  'Database Connection Error!';		
		return false;
	}
return true;
}

function cleanUp() {

	if (!connectDB()) {	
		return false;
	}
	
	if (isset($_SESSION['dbInfo']['dbOHRMUserName'])) {
		$query = dropUser();
	}
	
	$query[0] = dropDB();
	
	$sucExec = $query;
	$overall = true;
	
	for ($i=0;  $i < count($query); $i++) {
		$sucExec[$i] = mysql_query($query[$i]);
		
		if (!$sucExec[$i]) {
			$overall = false;
		}
	}
	
	if (!$overall) {
		connectDB();
		for ($i=0;  $i < count($query); $i++) {
			if (!$sucExec[$i]) {
				$sucExec[$i] = mysql_query($query[$i]);
			}
		
			if (!$sucExec[$i]) {
				$overall = false;
			}
		}
	}
	
	$sucExec[] = delConf();	

return $sucExec;
}

function dropDB() {
	$query = "DROP DATABASE ". $_SESSION['dbInfo']['dbName'];
return $query;
}

function dropUser() {
	$tables = array('`user`', '`db`', '`tables_priv`', '`columns_priv`');
	
	foreach ($tables as $table) {
		$query[] = "DELETE FROM $table WHERE `User` = '".$_SESSION['dbInfo']['dbOHRMUserName']."' AND (`Host` = 'localhost' OR `Host` = '%')";
	}

return $query;
}

function delConf() {
	$filename = ROOT_PATH . '/lib/confs/Conf.php';
	
return @unlink($filename);
}


$_SESSION['cleanProgress'] = cleanUp();

if (isset($_SESSION['UNISTALL']) && $_SESSION['cleanProgress']) {
	unset($_SESSION['UNISTALL']);
	
}

?>