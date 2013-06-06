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

function notifyUser($errlevel, $errstr, $errfile='', $errline='', $errcontext=''){

	if (error_reporting() == 0) {
		return;
	}

	$errMsg = "\n".$errstr.' in '.$errfile.' on line '.$errline."\n";
	switch ($errlevel) {
		case E_USER_WARNING : $type = "Warning";
							  $errType = "warning";
							  break;
		case E_USER_NOTICE 	: $type = "Notice";
							  $errType = "notice";
							  break;
		case E_USER_ERROR 	: $type = "Error";
							  $errType = "error";
							  break;
		case E_WARNING 		: $type = "Warning";
							  $sysErr = true;
							  $errType = "warning";
							  break;
		case E_NOTICE 		: $type = "Notice";
							  $sysErr = true;
							  $errType = "notice";
							  break;
		case E_ERROR 		: $type = "Error";
							  $sysErr = true;
							  $errType = "error";
							  break;
		case E_ALL			: $type = "General Error";
							  $sysErr = true;
							  $errType = "error";
	}

	if (isset($type)) {
	if (!isset($_SESSION)) {
		session_start();
	}

	ob_get_clean();

	$message = "<?xml version='1.0' encoding='iso-8859-1'?>\n";
	$message .= "<?xml-stylesheet href='".$_SESSION['WPATH']."/error.xsl' type='text/xsl'?>\n";
	$message .= "<report>\n";
	$message .= "	<heading>$type</heading>\n";
	$message .= "	<type>$errType</type>\n";

	$errstr = strip_tags($errstr);

	$message .= "	<message><![CDATA[$errstr]]></message>\n";

	$confObj = new Conf();

	if (isset($confObj->logPath) && !empty($confObj->logPath)) {
		$logPath = $confObj->logPath;
	} else {
		$logPath = ROOT_PATH.'/lib/logs/';
	}


	$message .= "	<root>".ROOT_PATH."</root>\n";
	$message .= "	<Wroot>".$_SESSION['WPATH']."</Wroot>\n";
	$message .= "   <stylesheet></stylesheet>\n";
	$message .= "	<logPath><![CDATA[".$logPath."]]></logPath>\n";

	$errfileEsc = str_replace("\\", "/", $errfile);

	if (isset($sysErr)) {

		$message .= "	<cause>\n";
		$message .= "		<message><![CDATA[Encountered the problem in ".$errfile."]]></message>\n";
		$message .= "	</cause>\n";
		$message .= "	<cause>\n";
		$message .= "		<message><![CDATA[Line ".$errline."]]></message>\n";
		$message .= "	</cause>\n";

		error_log(date('r').' : '.strip_tags($errMsg), 3, $logPath.'logDB.txt');

		$errMsgEsc = str_replace("'", "\'",strip_tags($type." :".'\n'.$errstr.'\n'."in ".$errfileEsc.'\n'."on line ".$errline));

	} else {

		$message .= "	<cause>\n";
		$message .= "		<message><![CDATA[".mysql_error()."]]></message>\n";
		$message .= "	</cause>\n";

		$message .= "	<cause>\n";
		$message .= "		<message><![CDATA[MySQL Error # :".mysql_errno()."]]></message>\n";
		$message .= "	</cause>\n";

		$errMsgEsc = str_replace("'", "\'",strip_tags($type." :".'\n'.$errstr.'\n'."Tech Info".'\n'."------------".'\n'.mysql_error()));
		error_log(date('r').' : '.strip_tags($errMsgEsc), 3, $logPath.'logDB.txt');
	}

	$confObj = new Conf();

	$message .= "	<environment>\n";
	$message .= "		<version type='ohrm' description='OrangeHRM' ><![CDATA[". $confObj->version."]]></version>\n";
	$message .= "		<version type='php' description='PHP' ><![CDATA[".constant('PHP_VERSION')."]]></version>\n";
	$message .= "		<version type='mysql' description='MySQL Client' ><![CDATA[".mysql_get_client_info()."]]></version>\n";
	$message .= "		<info type='memory_limit' description='Memory limit' ><![CDATA[".ini_get('memory_limit')."]]></info>\n";
	$message .= "		<info type='session.gc_maxlifetime' description='Maximum session lifetime' ><![CDATA[".ini_get('session.gc_maxlifetime')."]]></info>\n";
	$message .= "	</environment>\n";

	$message .= "	<cmd n='js'><![CDATA[alert('$errMsgEsc');]]></cmd>\n";
	$message .= "</report>\n";

	header("Content-type: application/xml; charset=UTF-8");

	echo $message;

	exit;
	}
}

set_error_handler('notifyUser');

require_once ROOT_PATH . '/installer/utils/LogFileWriter.php';

class ExceptionHandler {


	var $db_ex_mysql_not_connected;
	var $db_ex_no_database_found;


	function ExceptionHandler() {

	}

	function setDBExMysqlNoConn() {

		$db_ex_mysql_not_connected = 'Cannot Connect to MySQL Database\n';

	}

	function setNoDBFound() {

		$db_ex_no_database_found = 'No Database Found\n';

	}


	function getNoDBFound() {

		return $this->db_ex_no_database_found;

	}

	function getDBExMysqlNoConn() {

		return $this->db_ex_no_database_found;

	}


	function minorException() {
	}



	function dbexNoConnection() {

		$today = date("F j, Y, g:i a");
		$noConnection = rand (10000,100000) . ' ' . $today . ' ' . "NO database Connection found\n";
		$log_writer = new LogFileWriter();
		$log_writer->writeLogDB($noConnection);
		trigger_error("NO database Connection found", E_USER_ERROR);
	}

	function dbexNoDatabase() {

		$today = date("F j, Y, g:i a");
		$noDBFound = rand (10000,100000) . ' ' . $today . ' ' . "NO database found\n";
		$log_writer = new LogFileWriter();
		$log_writer->writeLogDB($noDBFound);
		trigger_error("NO database found", E_USER_ERROR);
	}

	function dbexNoQueryFound() {

		$today = date("F j, Y, g:i a");
		$noQuery = rand (10000,100000) . ' ' . $today . ' ' . "MySQL Query Error\n";
		$log_writer = new LogFileWriter();
		$log_writer->writeLogDB($noQuery);
		trigger_error("MySQL Query Error: No query found", E_USER_ERROR);

	}

	function dbexInvalidSQL($sql) {

		$today = date("F j, Y, g:i a");
		$invalidSQL = rand (10000,100000) . ' ' . $today . ' ' . "MySQL Query Error\n";
		$log_writer = new LogFileWriter();
		$log_writer->writeLogDB($invalidSQL);
		trigger_error("MySQL Query Error : $sql", E_USER_ERROR);

	}

	function logW($string) {

		$today = date("F j, Y, g:i a");
		$invalidSQL = rand (10000,100000) . ' ' . $today . ' ' . $string . "\n";
		$log_writer = new LogFileWriter();
		$log_writer->writeLogDB($invalidSQL);

	}

}



?>
