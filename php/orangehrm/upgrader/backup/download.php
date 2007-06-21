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