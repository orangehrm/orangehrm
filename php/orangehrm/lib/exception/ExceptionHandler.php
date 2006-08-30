<?
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

function notifyUser($errlevel, $errstr, $errfile='', $errline='', $errcontext=''){

	switch ($errlevel) {
		case E_USER_WARNING : $type = "Warning";
					break;
		case E_USER_NOTICE : $type = "Notice";
					break;
		case E_USER_ERROR : $type = "Error";
					break;
	}
	if (isset($type)) {
	$message = "<?xml version='1.0' encoding='iso-8859-1'?>\n";
	$message .= "<?xml-stylesheet href='error.xsl' type='text/xsl'?>\n";
	$message .= "<report>\n";
	$message .= "	<heading>$type</heading>\n";
	$message .= "	<message>$errstr</message>\n";
	$message .= "	<root>".ROOT_PATH."</root>\n";
	$message .= "	<cause>\n";
	$message .= "		<message>".mysql_error()."</message>\n";
	$message .= "	</cause>\n";		
	$message .= "</report>\n";
	
	header("Content-type: application/xml");
	
	echo $message;
		
	exit;
	}
}	

set_error_handler('notifyUser');

require_once ROOT_PATH . '/lib/logs/LogWriter.php';

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
		$log_writer = new LogWriter();
		$log_writer->writeLogDB($noConnection);				
		trigger_error("NO database Connection found", E_USER_ERROR);
	}	

	function dbexNoDatabase() {
		
		$today = date("F j, Y, g:i a");
		$noDBFound = rand (10000,100000) . ' ' . $today . ' ' . "NO database found\n";
		$log_writer = new LogWriter();
		$log_writer->writeLogDB($noDBFound);
		trigger_error("NO database found", E_USER_ERROR);		
	}	
	
	function dbexNoQueryFound() {
		
		$today = date("F j, Y, g:i a");
		$noQuery = rand (10000,100000) . ' ' . $today . ' ' . "MySQL Query Error\n";
		$log_writer = new LogWriter();
		$log_writer->writeLogDB($noQuery);
		trigger_error("MySQL Query Error", E_USER_ERROR);			
		
	}	
	
	function dbexInvalidSQL() {
		
		$today = date("F j, Y, g:i a");
		$invalidSQL = rand (10000,100000) . ' ' . $today . ' ' . "MySQL Query Error\n";
		$log_writer = new LogWriter();
		$log_writer->writeLogDB($invalidSQL);		
		trigger_error("MySQL Query Error", E_USER_ERROR);		
		
	}
	
	function logW($string) {
		
		$today = date("F j, Y, g:i a");
		$invalidSQL = rand (10000,100000) . ' ' . $today . ' ' . $string . "\n";
		$log_writer = new LogWriter();
		$log_writer->writeLogDB($invalidSQL);			
		
	}	
			
}



?>