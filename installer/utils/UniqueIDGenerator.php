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
/* Check if running through upgrader and skip including if so */
$confPHP = ROOT_PATH . '/lib/confs/Conf.php';
if (file_exists($confPHP)) {
	@require_once ROOT_PATH . '/installer/utils/DMLFunctions.php';
}

/**
 * Class to generate unique incrementing ID's.
 * Implemented as a singleton.
 */
class UniqueIDGenerator {
	const TABLE_NAME = "hs_hr_unique_id";

	const INCREMENT_ID_SQL = "UPDATE hs_hr_unique_id SET last_id = LAST_INSERT_ID(last_id + 1) WHERE table_name = '%s' AND field_name = '%s'";
	const GET_ID_SQL = "SELECT LAST_INSERT_ID()";

	const SELECT_SQL = "SELECT last_id FROM hs_hr_unique_id WHERE table_name = '%s' AND field_name = '%s'";
	const INSERT_SQL = "INSERT INTO hs_hr_unique_id(table_name, field_name, last_id) VALUES('%s', '%s', %d)";
	const UPDATE_SQL = "UPDATE hs_hr_unique_id SET last_id = %s WHERE table_name = '%s' AND field_name = '%s'";
	const FIND_INVALID_ID_SQL = "SELECT COUNT(*) FROM %s WHERE %s NOT LIKE '%s%%'";
	const FIND_EXISTING_MAX_ID_SQL = "SELECT MAX(%s) FROM %s";
	const RESET_SQL = "UPDATE hs_hr_unique_id SET last_id = 0";

	/** This singleton instance */
	private static $instance;

	/**
	 * Private constructor
	 */
	private function __construct() {
		
	}

	/**
	 * Get the singleton instance of this class
	 */
	public static function getInstance() {

		if (!is_object(self::$instance)) {
			self::$instance = new UniqueIDGenerator();
		}
		return self::$instance;
	}

	/**
	 * Get the next unique ID for the given table and field
	 * If $prefix is specified, uses that when generating the ID
	 * eg: $prefix = "NAT" gives ID's like "NAT013".
	 *
	 * @param string $tableName    The table Name
	 * @param string $fieldName    The field Name
	 * @param string $prefix       Optional prefix used in the ID
	 * @param int    $minNumLength Minimum width of the numeric part if the ID.
	 *                             If the number is less than this width, it is
	 *                             left padded with zeros. (only used when a $prefix is given)
	 *
	 * @return mixed The next ID.
	 */
	public function getNextID($tableName, $fieldName, $prefix = null, $minNumWidth = 3) {

		$updateSql = sprintf(self::INCREMENT_ID_SQL, strtolower($tableName), strtolower($fieldName));

		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($updateSql);
		if (!$result || (mysql_affected_rows() == 0)) {
			throw new IDGeneratorException("Error getting next ID for $tableName, $fieldName");
		}

		$result = $dbConnection->executeQuery(self::GET_ID_SQL);
		if (!$result) {
			throw new IDGeneratorException("Error in query for last inserted ID for $tableName, $fieldName");
		}

		$row = mysql_fetch_array($result, MYSQL_NUM);
		if (empty($row)) {
			throw new IDGeneratorException("Error getting last inserted ID for $tableName, $fieldName");
		}

		/* LAST_INSERT_ID always returns a value */
		$nextId = $row[0];

		if (!empty($prefix)) {
			$nextId = $prefix . str_pad($nextId, $minNumWidth, "0", STR_PAD_LEFT);
		}

		return $nextId;
	}

	/**
	 * Get the last highest unique ID for the given table and field
	 * If $prefix is specified, uses that when generating the ID
	 * eg: $prefix = "NAT" gives ID's like "NAT013".
	 *
	 * @param string $tableName    The table Name
	 * @param string $fieldName    The field Name
	 * @param string $prefix       Optional prefix used in the ID
	 * @param int    $minNumLength Minimum width of the numeric part if the ID.
	 *                             If the number is less than this width, it is
	 *                             left padded with zeros. (only used when a $prefix is given)
	 *
	 * @return mixed The last ID.
	 */
	public function getLastID($tableName, $fieldName, $prefix = null, $minNumWidth = 3) {

		/* Get existing lastId value */
		$sql = sprintf(self::SELECT_SQL, $tableName, $fieldName);
		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($sql);

		if ($result && (mysql_num_rows($result) == 1)) {

			$row = mysql_fetch_array($result, MYSQL_NUM);
			$lastId = $row[0];
			if (!empty($prefix)) {
				$lastId = $prefix . str_pad($lastId, $minNumWidth, "0", STR_PAD_LEFT);
			}
			return $lastId;
		} else {
			throw new IDGeneratorException("Error querying last ID. SQL = $sql");
		}
	}

	/**
	 * Resets unique ID's to lowest possible values.
	 * Calling this method will lose all previous ID values and reset
	 * them to the max of existing ID's.
	 */
	public function resetIDs() {

		$sql = self::RESET_SQL;
		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($sql);
		if (!$result) {
			throw new IDGeneratorException("Error resetting id's to zero. = $sql");
		}
		$this->initTable();
	}

	/**
	 * Initializes the unique ID table by checking for tables with
	 * incorrect ID values and resetting them. Could be run after a
	 * database upgrade to reset the ID values if needed.
	 *
	 * If $link is given, uses that mysql link identifier, otherwise uses
	 * the DMLFunctions class to access the database.
	 *
	 * @param resource $link mysql link identifier
	 */
	public function initTable($link = null) {

		$idFields = array(
		    //new IDField("hs_hr_users", "id", "USR"),
		    new IDField("hs_hr_module", "mod_id", "MOD"),
		    new IDField("hs_hr_employee", "emp_number"),
		    new IDField("hs_hr_custom_export", "export_id"),
		    new IDField("hs_hr_custom_import", "import_id"),
			/* Not used yet. Uncomment when we start using these
			  new IDField("hs_hr_employee_timesheet_period", "timesheet_period_id"),
			  new IDField("hs_hr_timesheet_submission_period", "timesheet_period_id"),
			 */
		);

		foreach ($idFields as $idField) {

			$insert = false;
			$lastId = 0;

			$tableName = $idField->getTableName();
			$fieldName = $idField->getFieldName();
			$prefix = $idField->getPrefix();

			/* Get existing lastId value */
			$sql = sprintf(self::SELECT_SQL, $tableName, $fieldName);

			$result = (empty($link)) ? mysql_query($sql) : mysql_query($sql, $link);

			if (!$result) {
				$errMsg = (empty($link)) ? mysql_error() : mysql_error($link);
				throw new IDGeneratorException("Error querying last ID. SQL = $sql. Msg = $errMsg");
			}

			$numRows = mysql_num_rows($result);
			if ($numRows === 1) {
				$insert = false;
				$row = mysql_fetch_array($result, MYSQL_NUM);
				$lastId = $row[0];
			} else if ($numRows === 0) {
				$insert = true;
			} else {
				$errMsg = (empty($link)) ? mysql_error() : mysql_error($link);
				throw new IDGeneratorException("Error in hs_hr_unique_id table. Msg = $errMsg");
			}

			/* If the field has a prefix, look for existing invalid id's */
			if (!empty($prefix)) {
				$sql = sprintf(self::FIND_INVALID_ID_SQL, $tableName, $fieldName, $prefix);
				$result = (empty($link)) ? mysql_query($sql) : mysql_query($sql, $link);
				if (!$result) {
					$errMsg = (empty($link)) ? mysql_error() : mysql_error($link);
					throw new IDGeneratorException("Error looking for invalid ID's. SQL = $sql. Msg = $errMsg");
				}

				$row = mysql_fetch_array($result, MYSQL_NUM);
				if (empty($row)) {
					throw new IDGeneratorException("Error fetching num_rows. SQL = $sql");
				}
				if ($row[0] > 0) {
					throw new IDGeneratorException("Invalid ID's in table=$tableName, Field=$fieldName. SQL=$sql");
				}
			}

			/* Get existing maximum ID from the table */
			$sql = sprintf(self::FIND_EXISTING_MAX_ID_SQL, $fieldName, $tableName);
			$result = (empty($link)) ? mysql_query($sql) : mysql_query($sql, $link);
			if (!$result) {
				$errMsg = (empty($link)) ? mysql_error() : mysql_error($link);
				throw new IDGeneratorException("Error looking for existing MAX ID. SQL = $sql. Msg = $errMsg");
			}

			$row = mysql_fetch_array($result, MYSQL_NUM);
			if (empty($row)) {
				throw new IDGeneratorException("Error looking for existing MAX ID. SQL = $sql");
			}

			$existingMax = $row[0];

			if (!empty($existingMax)) {

				if (!empty($prefix)) {

					/* Remove prefix and covert to int */
					$existingMax = str_replace($prefix, "", $existingMax);
					$existingMax = intVal($existingMax);
				}

				if ($existingMax > $lastId) {
					$lastId = $existingMax;
				}
			}

			if ($insert) {
				$sql = sprintf(self::INSERT_SQL, $tableName, $fieldName, $lastId);
			} else {
				$sql = sprintf(self::UPDATE_SQL, $lastId, $tableName, $fieldName);
			}

			$result = (empty($link)) ? mysql_query($sql) : mysql_query($sql, $link);
			if (!$result) {
				$errMsg = (empty($link)) ? mysql_error() : mysql_error($link);
				throw new IDGeneratorException("Error updating hs_hr_unique_id table. SQL = $sql. Msg = $errMsg");
			}
		}
	}

}

/**
 * Class representing one unique ID field
 */
class IDField {

	protected $tableName;
	protected $fieldName;
	protected $prefix;

	public function getTableName() {
		return $this->tableName;
	}

	public function getFieldName() {
		return $this->fieldName;
	}

	public function getPrefix() {
		return $this->prefix;
	}

	/**
	 * Constructor
	 *
	 * @param string $table Name of the table
	 * @param string $field ID Field
	 * @param string $prefix Optional prefix used in the ID.
	 */
	public function __construct($table, $field, $prefix = null) {

		$this->tableName = $table;
		$this->fieldName = $field;
		$this->prefix = $prefix;
	}

}

class IDGeneratorException extends Exception {
	
}

?>
