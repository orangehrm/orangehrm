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

/*
 * Class that is used by upgrader to apply foreign key constraints to the database.
 * Handles failures when applying constraints and attempts to recover by restoring
 * referential integrity to the database.
 *
 * NOTE: This class does not attempt to connect to mysql.
 */
class ConstraintHandler {

	const TABLE_NDX = 0;
	const FIELDS_NDX = 1;
	const CHILD_TABLE_NDX = 2;
	const CHILD_FIELDS_NDX = 3;
	const DEL_CLAUSE_NDX = 4;

	protected $logFile;

	/**
	 * Set the file to log to. If not set, no logging is done
	 */
	public function setLogFile($logFile) {
		$this->logFile = $logFile;
	}

	/**
	 * Get the log file.
	 */
	public function getLogFile() {
		return $this->logFile;
	}

	/**
	 * Apply the supplied array of constraints to the database.
	 *
	 * TODO: Pass an array of Constraint objects. But this would make
	 * dbscripts/constrainst.php a bit more difficult to read.
	 *
	 * @param array $constraints Array of constraints
	 * @param string $database The database to connect to.
	 *
	 * @return array Array of failed constraints. Empty array if none failed.
	 */
	public function applyConstraints($constraints, $database = null) {
		$maxTries = 10;
		$tries = 0;
		$cleaned = 0;

		do {
			$cleaned = $this->_cleanup($constraints);
			$tries++;
		} while (($tries < $maxTries) && ($cleaned > 0));

		// Handle case where unable to clean up
		if ($cleaned > 0) {
			$this->_log("Some constraint failures may not have been cleaned (after {$maxTries} tries)");
		}

		$failedConstraints = array();

		foreach ($constraints as $constraint) {

			$result = $this->_addConstraint($constraint);
			if (!$result) {
				$failedConstraints[] = $constraint;
			}
		}

		return $failedConstraints;
	}

	/**
	 * Get Alter table SQL to apply the given constraint.
	 * (Made public for ease of testing)
	 *
	 * @param string constraint apply constraint
	 * @return string Alter table statement to apply constraint to table
	 */
	public function getConstraintSQL($constraint) {

		$table = $constraint[ConstraintHandler::TABLE_NDX];
		$fields = implode(",", $constraint[ConstraintHandler::FIELDS_NDX]);
		$childTable = $constraint[ConstraintHandler::CHILD_TABLE_NDX];
		$childFields = implode(",", $constraint[ConstraintHandler::CHILD_FIELDS_NDX]);
		$onDelete = $constraint[ConstraintHandler::DEL_CLAUSE_NDX];

		$delClause = "";
		switch ($onDelete) {
			case "null":
				$delClause = "ON DELETE SET NULL";
				break;
			case "cascade":
				$delClause = "ON DELETE CASCADE";
				break;
			case "restrict":
				$delClause = "ON DELETE RESTRICT";
				break;
			default:
				$delClause = "";
				break;
		}

		$sql = sprintf("ALTER TABLE %s ADD CONSTRAINT FOREIGN KEY (%s) REFERENCES %s(%s) %s",
						$table, $fields, $childTable, $childFields, $delClause);
		return $sql;
	}

	/**
	 * Add the given constraint to the database
	 * @param array $constraint Constraint as an array
	 * @return boolean True if successfully added. False if failed to add
	 */
	private function _addConstraint($constraint) {

		$sql = $this->getConstraintSQL($constraint);
		$result = mysql_query($sql);
		if ($result) {
			return true;
		} else {
			$this->_log("Failed to apply constraint: $sql");
			return false;
		}
	}

	/**
	 * Checks if any data exists that violate the given constraints and
	 * attempt to clean them up.
	 *
	 * @param string $constraints The constraints to cleanup
	 * @return int Number of constraints which needed to be cleaned
	 */
	private function _cleanup($constraints) {

		$cleaned = 0;
		foreach ($constraints as $constraint) {

			$type = $constraint[self::DEL_CLAUSE_NDX];

			$fields = $constraint[ConstraintHandler::FIELDS_NDX];
			$childFields = $constraint[ConstraintHandler::CHILD_FIELDS_NDX];
			if (count($fields) != count($childFields)) {
				throw new ConstraintHandlerException("Invalid constraint: ". getConstraintSQL($constraint));
			}

			switch ($type) {
				case "null":
					$result = $this->_cleanSetNullConstraint($constraint);
					break;
				case "cascade":
					$result = $this->_cleanCascadeConstraint($constraint);
					break;
				case "restrict":
					$result = $this->_cleanRestrictConstraint($constraint);
					break;
				case null:
					$result = $this->_cleanEmptyConstraint($constraint);
					break;
				default:
					/* Unsupported */
					$this->_log("Unsupported constraint type: $type");
					$result = false;
					break;
			}

			if ($result) {
				$cleaned++;
			}
		}

		return $cleaned;
	}

	/**
	 * Attempts to clean up the given constraint
	 *
	 * TODO: Change naming. child actually refers to parent table
	 *
	 * @param array Constraint
	 * @return int number of rows affected (cleaned).
	 */
	private function _cleanSetNullConstraint($constraint) {

		$numAffected = 0;

		$table = $constraint[ConstraintHandler::TABLE_NDX];
		$fields = $constraint[ConstraintHandler::FIELDS_NDX];
		$childTable = $constraint[ConstraintHandler::CHILD_TABLE_NDX];
		$childFields = $constraint[ConstraintHandler::CHILD_FIELDS_NDX];

		for ($i=0; $i< count($fields); $i++) {
			$field = $fields[$i];
			$childField = $childFields[$i];

			$setArray[] = "`$field` = NULL";
			$checkNullArray[] = " (`{$table}`.`{$field}` IS NULL)";
			$whereArray[] = "`$table`.`$field` = `$childTable`.`$childField`";
		}
		$setClause = implode(",", $setArray);
		$checkNull = implode(" OR ", $checkNullArray);
		$whereClause = implode(" AND ", $whereArray);

		/* Self references need to be handled differently since we can't select and update
		 * from the same table in a multi table update.
		 */
		if ($table == $childTable) {

			/* This happens only for hs_hr_users - modified_user_id and created_by fields.
			 * So we are considering only the case where one field is in the constraint.
			 */
			if (count($fields) == 1) {

				$field = $fields[0];
				$childField = $childFields[0];
 				$sql = "SELECT DISTINCT A.`{$field}` FROM `{$table}` As A WHERE A.`{$field}` IS NOT NULL AND A.`{$field}` NOT IN " .
 						"(SELECT B.id FROM `{$table}` as B)";
				$result = mysql_query($sql);
				if ($result) {
					if (mysql_num_rows($result) > 0) {
						while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
							$ids[] = "'" . $row[0] . "'";
						}

						$idList = implode(",", $ids);
						$sql = "UPDATE `{$table}` SET `{$field}` = NULL WHERE `{$field}` IN ({$idList})";
						$result = mysql_query($sql);
						if (!$result) {
							throw new ConstraintHandlerException("Error when running query: $sql. MysqlError:" . mysql_error());
						}
						$numAffected = mysql_affected_rows();
					}
				} else {
					throw new ConstraintHandlerException("Error when running query: $sql. MysqlError:" . mysql_error());
				}
			}

		} else {

			$sql = sprintf("UPDATE `{$table}` SET {$setClause} WHERE NOT ({$checkNull}) AND NOT EXISTS(SELECT 1 FROM `{$childTable}` " .
					" WHERE {$whereClause} )");
			$result = mysql_query($sql);
			if (!$result) {
				throw new ConstraintHandlerException("Error when running query: $sql. MysqlError:" . mysql_error());
			}

			$numAffected = mysql_affected_rows();
		}

		if ($numAffected > 0) {
			$this->_log($sql . "NUM = " . $numAffected);
		}
		return $numAffected;
	}

	/**
	 * Attempts to clean up the given constraint
	 *
	 * @param array Constraint
	 * @return int number of rows affected (cleaned).
	 */
	private function _cleanCascadeConstraint($constraint) {
		$numAffected = 0;

		$table = $constraint[ConstraintHandler::TABLE_NDX];
		$fields = $constraint[ConstraintHandler::FIELDS_NDX];
		$childTable = $constraint[ConstraintHandler::CHILD_TABLE_NDX];
		$childFields = $constraint[ConstraintHandler::CHILD_FIELDS_NDX];

		for ($i=0; $i< count($fields); $i++) {
			$field = $fields[$i];
			$childField = $childFields[$i];
			$checkNullArray[] = " (`{$table}`.`{$field}` IS NULL)";
			$whereArray[] = "`$table`.`$field` = `$childTable`.`$childField`";
		}
		$checkNull = implode(" OR ", $checkNullArray);
		$whereClause = implode(" AND ", $whereArray);

		$countSql = "SELECT COUNT(*) FROM `{$childTable}`";
		$result = mysql_query($countSql);
		if (!$result) {
			throw new ConstraintHandlerException("Error when running query: $sql. MysqlError:" . mysql_error());
		}
		$row = mysql_fetch_array($result, MYSQL_NUM);
		$count = $row[0];

		/* If no parents all the children have to be deleted */
		if ($count == 0) {
			$sql = sprintf("DELETE FROM `{$table}`");
		} else {

			$sql = sprintf("DELETE FROM `{$table}` USING `{$table}`, `{$childTable}` WHERE NOT ({$checkNull}) AND NOT EXISTS(SELECT 1 FROM `{$childTable}` " .
					" WHERE {$whereClause} )");
		}
		$result = mysql_query($sql);

		if (!$result) {
			throw new ConstraintHandlerException("Error when running query: $sql. MysqlError:" . mysql_error());
		}

		$numAffected = mysql_affected_rows();
		if ($numAffected > 0) {
			$this->_log($sql . "NUM = " . $numAffected);
		}
		return $numAffected;
	}

	/**
	 * Attempts to clean up the given constraint
	 *
	 * @param array Constraint
	 * @return int number of rows affected (cleaned).
	 */
	private function _cleanRestrictConstraint($constraint) {

		/*
		 * The only restrict constraints are hs_hr_project and hs_hr_compstructtree
		 * Entries in hs_hr_project are not deleted, in hs_hr_compstructtree
		 * loc_code can be set to null (but shouldn't be necessary
		 * since we prevent deletion of locations if in use),
		 * so consider this the same as a null constraint.
		 */
		$numAffected = $this->_cleanSetNullConstraint($constraint);
		return $numAffected;
	}

	/**
	 * Attempts to clean up the given constraint
	 *
	 * @param array Constraint
	 * @return int number of rows affected (cleaned).
	 */
	private function _cleanEmptyConstraint($constraint) {
		$numAffected = 0;

		/* Not implemented yet. We don't have empty constraints */
		return $numAffected;
	}

	/**
	 * Verifies that the given constraints have been set in the database.
	 * @param array $constraints constraint array.
	 *
	 * @return array Array of constraints that were missing.
	 */
	public function getMissingConstraints($constraints) {

		$failedConstraints = array();

		foreach ($constraints as $constraint) {

			$found = false;

			$table = $constraint[ConstraintHandler::TABLE_NDX];
			$fields = implode("[\s,`]+", $constraint[ConstraintHandler::FIELDS_NDX]);
			$childTable = $constraint[ConstraintHandler::CHILD_TABLE_NDX];
			$childFields = implode("[\s,`]+", $constraint[ConstraintHandler::CHILD_FIELDS_NDX]);
			$onDelete = $constraint[ConstraintHandler::DEL_CLAUSE_NDX];

			$delClause = "ON\s+DELETE";
			switch ($onDelete) {
				case "null":
					$delClause = "ON\s+DELETE\s+SET\s+NULL";
					break;
				case "cascade":
					$delClause = "ON\s+DELETE\s+CASCADE";
					break;
				default:
					$delClause = "";
				break;
			}

			$sql = "SHOW CREATE TABLE $table";
			$result = mysql_query($sql);

			if ($result === false) {
				throw new ConstraintHandlerException("Error when running query: $sql. MysqlError:" . mysql_error());
			}

			$row = mysql_fetch_array($result, MYSQL_NUM);
			$createTable = $row[1];
			$lines = explode("\n", $createTable);

			foreach ($lines as $line) {
				$regexp = "/\w*CONSTRAINT\b.*\bFOREIGN\s+KEY[\s(`]+{$fields}[\s)`]+REFERENCES[\s`)]+{$childTable}[\s`(]+{$childFields}[\s`)]+{$delClause}.*/";
				$count = preg_match($regexp, $line);

				if ($count === 1) {
					$found = true;
					break;
				}
			}

			if (!$found) {
				$failedConstraints[] = $constraint;
			}
		}

		return $failedConstraints;
	}

	/**
	 * Log the given message to the log file
	 * @param string Log message
	 *
	 */
	private function _log($message) {
		if (!empty($this->logFile)) {
			error_log (date("r") . " " . $message ."\n", 3, $this->logFile);
		}
	}
}

class ConstraintHandlerException extends Exception {

}
?>
