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
 */

require_once ROOT_PATH . '/lib/dao/MySQLClass.php';
require_once ROOT_PATH . '/lib/confs/Conf.php';
require_once ROOT_PATH . '/lib/common/authorize.php';

class DMLFunctions {

	public $dbObject; // Databse connection
	private $conf; // Connection configuration
	private $auth;
    private $maxAllowedPacketSize = -1;

	/**
	 * The constructor will take the configuration variables
	 * from the Conf Class (conf.php) and return the reference of 
	 * database connection object
	 */
	public function __construct() {
		$this->conf = new Conf();
		$this->dbObject = new MySQLClass($this->conf, authorize::getCurrentUserId());
	}

	/**
	 * This method will take in a SQL Query and execute it using
	 * the sqlQuery() method of the database connection object 
	 * @param String $sql SQL statement
	 * @return ResultResource If query execution is successful the 
	 * result will return otherwise boolean false will return
	 */
	public function executeQuery($sql) {
		$sql = $this->_formatQuery($sql);

		if ($this->dbObject->dbConnect()) {
			$result = $this->dbObject->sqlQuery($sql);
			return $result;
		}

		return false;
	}

	public function getMaxAllowedPacketSize() {

        if ($this->maxAllowedPacketSize == -1) {
            try {
                $result = $this->dbObject->sqlQuery("show variables like 'max_allowed_packet'");
                if ($result && mysql_num_rows($result) == 1) {
                    $dataRow = mysql_fetch_array($result);
                    if (isset($dataRow[1])) {
                        $this->maxAllowedPacketSize = $dataRow[1];        
                    }
                }
            } catch (Exception $e) {
                // ignore if cannot get max_allowed_packet.                
            }
        }

        return $this->maxAllowedPacketSize;
    }

	/**
	 * This method will correct and query when encryption is enabled
	 * to call the encryption methods correctly
	 * @param String $query SQL query to be formatted
	 * @return String Formatted (corrected) SQL query
	 */
	private function _formatQuery($query) {
		if (preg_match('/\'AES_[ED][NE]CRYPT\(/', $query)) {
			$query = preg_replace(array ("/^'AES_ENCRYPT\(/", "/\)'/"), array ('AES_ENCRYPT(', ')'), $query);
		}
		return $query;
	}
}

