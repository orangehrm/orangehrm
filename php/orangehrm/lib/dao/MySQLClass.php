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

require_once ROOT_PATH . '/lib/exception/ExceptionHandler.php';

class MySQLClass {

	private $myHost; // server name
	private $myHostPort;
	private $userName; //db user
	private $userPassword; // db user password
	private $db_name; // database name
	private $conn; // database connection
	private $orangeUser; //username of the current user
	private $result;

	/**
	 * Class Constructor for MySQLClass
	 * @param Conf $conf Object containing the database connection details
	 * @param String $orangeUser Username of the user who's currently logged in
	 */
	public function __construct($conf, $orangeUser = 'unkown') {
		$this->myHost = $conf->dbhost; //reference for the Host
		$this->myHostPort = $conf->dbport;
		$this->userName = $conf->dbuser; //reference for the Username
		$this->userPassword = $conf->dbpass; //reference for the Password
		$this->db_name = $conf->dbname; //reference for the DatabaseName
		$this->orangeUser = $orangeUser; //reference to the username of the current user 

		$this->dbConnect();
	}

	/**
	 * This method will make the database connection using mysql_connect() function
	 * @return boolean true if connected to the database successfully
	 */
	public function dbConnect() {
		/*
		 * TODO: The capturing of database errors should be done without returning true or false; Proper exceptions
		 * should be thrown instead
		 */
		if (!@ $this->conn = mysql_connect($this->myHost . ':' . $this->myHostPort, $this->userName, $this->userPassword)) {
			$exception_handler = new ExceptionHandler();
			$exception_handler->dbexNoConnection();
			exit;
		} else {
			if ($this->conn) {
				mysql_query("SET NAMES 'utf8'");
				if (mysql_select_db($this->db_name)) {
					mysql_query("SET @orangehrm_user = '{$this->orangeUser}';");
					return true;
				} else {
					$exception_handler = new ExceptionHandler();
					$exception_handler->dbexNoDatabase();
					exit;
				}
			} else {
				return false;
			}
		}

		if ($this->conn) {
			mysql_select_db($this->db_name);
			mysql_query("SET @orangehrm_user = '{$this->orangeUser}';");
			return true;
		} else {
			$exception_handler = new ExceptionHandler();
			$exception_handler->dbexNoDatabase();
			return false;
		}
	}

	/**
	 * This method will close the connection with the database
	 * @param ResultResource $result If this parameter is passed, the method will
	 * try to free the result resouce
	 */
	public function dbDisconnect($result = NULL) {
		if ($this->conn != null) {
			if ($result) {
				@ mysql_free_result($result);
			}
			if ($this->conn) {
				return @ mysql_close($this->conn);
			}
		}
	}

	/**
	 * This method will execute an SQL statement using mysql_query() function
	 * @param String $sql SQL statement to be executed
	 * @return ResultResource If the statement executed success, boolean false in an error
	 */
	public function sqlQuery($sql) {
		if ((isset ($this->conn)) && ($sql != '')) {
			$this->result = mysql_query($sql);
			if ($this->result) {
				return $this->result;
			}

			/* 
			 * Return false if duplicate key is entered
			 * TODO: Throw an exception here, and chanage code to catch it on model level
			 */
			if (mysql_errno() == 1062) {
				return false;
			}

			$exception_handler = new ExceptionHandler();
			$exception_handler->dbexInvalidSQL($sql);
			return false;
		} else {
			$exception_handler = new ExceptionHandler();
			$exception_handler->dbexNoQueryFound($sql);
			return false;
		}
	}

	/**
	 * This method will return a row from a result resource
	 * @param ResultResouce $result
	 * @return Array[] Row of field values
	 */
	public function getArray($result, $resultType = MYSQL_BOTH) {
		return mysql_fetch_array($result, $resultType);
	}

	/**
	 * This method will return the number of rows that have been affected by the executed 
	 * SQL statement
	 * @return int Number of rows affected
	 */
	public function numberOfAffectedRows() {
		return mysql_affected_rows();
	}

	/**
	 * This method will return the number of rows in a given result resource
	 * @param ResultResouce $result
	 * @return int Number of rows in the result resource
	 */
	public function numberOfRows($result) {
		return mysql_num_rows($result);
	}
}
