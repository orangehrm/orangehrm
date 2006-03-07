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

require_once OpenSourceEIM . '/lib/logs/LogWriter.php';

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
		
	}	

	function dbexNoDatabase() {
		
		$today = date("F j, Y, g:i a");
		$noDBFound = rand (10000,100000) . ' ' . $today . ' ' . "NO database found\n";
		$log_writer = new LogWriter();
		$log_writer->writeLogDB($noDBFound);			
		
	}	
	
	function dbexNoQueryFound() {
		
		$today = date("F j, Y, g:i a");
		$noQuery = rand (10000,100000) . ' ' . $today . ' ' . "MySQL Query Error\n";
		$log_writer = new LogWriter();
		$log_writer->writeLogDB($noQuery);			
		
	}	
	
	function dbexInvalidSQL() {
		
		$today = date("F j, Y, g:i a");
		$invalidSQL = rand (10000,100000) . ' ' . $today . ' ' . "MySQL Query Error\n";
		$log_writer = new LogWriter();
		$log_writer->writeLogDB($invalidSQL);			
		
	}
	
	function logW($string) {
		
		$today = date("F j, Y, g:i a");
		$invalidSQL = rand (10000,100000) . ' ' . $today . ' ' . $string . "\n";
		$log_writer = new LogWriter();
		$log_writer->writeLogDB($invalidSQL);			
		
	}			
}



?>