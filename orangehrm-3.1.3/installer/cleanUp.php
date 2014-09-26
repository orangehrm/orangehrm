<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
 */


// Cleaning up
function connectDB() {

	if(!@mysql_connect($_SESSION['dbInfo']['dbHostName'].':'.$_SESSION['dbInfo']['dbHostPort'], 		$_SESSION['dbInfo']['dbUserName'], $_SESSION['dbInfo']['dbPassword'])) {
		$_SESSION['error'] =  'Database Connection Error!';
		return false;
	}
return true;
}

function cleanUp() {
	
	if ($_SESSION['cMethod'] == 'new' || $_SESSION['dbCreateMethod'] == 'new') {

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
