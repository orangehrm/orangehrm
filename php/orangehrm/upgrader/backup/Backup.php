<?php

/*
 *
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
 *
 *
 */

require_once("getConstraints.inc.php");

class Backup {

	/*
	 *	Class Attributes
	 *
	 **/
	 var $connection;
	 var $database;

	 var $constraints;



	 /*
	 *
	 *	Class Constructor
	 *
	 **/

	function __construct() {
		// nothing to do
	}

	/*
	 *	Setter method followed by getter method for each
	 *	attribute
	 *
	 **/

	function setConnection($connection){

		$this->connection = $connection;
	}

	function getConnection (){

		return $this->connection;
	}

	function setDatabase($database) {

		$this->database = $database;
	}

	function getDatabase() {

		return $this->database;
	}

	function getConstraints() {
		return $this->constraints;
	}

	function dumpDatabase($structure=false) {
		$struc="";
		if($structure) {
		 	$struc= $this->_dumpStructure();
		}
		$data = $this-> _dumpData();

		return $struc.$data;
	}

	function _dumpStructure() {
		$this->constraints = "";

		// Connect to database
		$db = @mysql_select_db($this->getDatabase());
        $structure="";
		if (!empty($db)) {

			// Get all table names from database
			$c = 0;
			$result = mysql_list_tables($this->getDatabase());
			for($x = 0; $x < mysql_num_rows($result); $x++) {
				$table = mysql_tablename($result, $x);
				if (!empty($table)) {
					$arr_tables[$c] = mysql_tablename($result, $x);
					$c++;
				}
			}

			// List tables
			$dump = '';
			if (isset($arr_tables) && is_array($arr_tables)) {
			for ($y = 0; $y < count($arr_tables); $y++) {

				// DB Table name
				$table = $arr_tables[$y];
				$this->constraints .= $this->_getConstraints($table);
				// Dump Structure
				$structure .= "DROP TABLE IF EXISTS `{$table}`; \r\n";
				$structure .= "CREATE TABLE `{$table}` (\r\n";
				$result = mysql_db_query($this->getDatabase(), "SHOW FIELDS FROM `{$table}`");
				while($row = mysql_fetch_object($result)) {

					$structure .= "  `{$row->Field}` {$row->Type}";
					$structure .= (!empty($row->Default)) ? " DEFAULT '{$row->Default}'" : false;
					$structure .= ($row->Null != "YES") ? " NOT NULL" : false;
					$structure .= (!empty($row->Extra)) ? " {$row->Extra}" : false;
					$structure .= ",\r\n";

				}

				$structure = ereg_replace(",\r\n$", "", $structure);

				// Save all Column Indexes in array
				unset($index);
				$result = mysql_db_query($this->getDatabase(), "SHOW KEYS FROM `{$table}`");
				while($row = mysql_fetch_object($result)) {

					if (($row->Key_name == 'PRIMARY') AND ($row->Index_type == 'BTREE')) {
						$index['PRIMARY'][$row->Key_name] = $row->Column_name;
					}

					if (($row->Key_name != 'PRIMARY') AND ($row->Non_unique == '0') AND ($row->Index_type == 'BTREE')) {
						$index['UNIQUE'][$row->Key_name] = $row->Column_name;
					}

					if (($row->Key_name != 'PRIMARY') AND ($row->Non_unique == '1') AND ($row->Index_type == 'BTREE')) {
						$index['INDEX'][$row->Key_name] = $row->Column_name;
					}

					if (($row->Key_name != 'PRIMARY') AND ($row->Non_unique == '1') AND ($row->Index_type == 'FULLTEXT')) {
						$index['FULLTEXT'][$row->Key_name] = $row->Column_name;
					}

				}

				// Return all Column Indexes of array
				if (isset($index) && is_array($index)) {
					foreach ($index as $xy => $columns) {

						$structure .= ",\r\n";

						$c = 0;
						foreach ($columns as $column_key => $column_name) {

							$c++;

							$structure .= ($xy == "PRIMARY") ? "  PRIMARY KEY  (`{$column_name}`)" : false;
							$structure .= ($xy == "UNIQUE") ? "  UNIQUE KEY `{$column_key}` (`{$column_name}`)" : false;
							$structure .= ($xy == "INDEX") ? "  KEY `{$column_key}` (`{$column_name}`)" : false;
							$structure .= ($xy == "FULLTEXT") ? "  FULLTEXT `{$column_key}` (`{$column_name}`)" : false;

							$structure .= ($c < (count($index[$xy]))) ? ",\r\n" : false;

						}

					}

				}

				$structure .= "\r\n);\r\n\r\n";
			}

			}

			return $structure;
		}

	}

	function _getConstraints($table) {

		/* Call getConstraints from included getConstraints.inc.php. That code
		 * is kept separate because it is based on 3rd party code.
		 */
		return getTableConstraints($table);
	}

	function _dumpData() {

		// Connect to database
		$db = @mysql_select_db($this->getDatabase());

		if (!empty($db)) {

			// Get all table names from database
			$c = 0;
			$result = mysql_list_tables($this->getDatabase());
			for($x = 0; $x < mysql_num_rows($result); $x++) {
				$table = mysql_tablename($result, $x);
				if (!empty($table)) {
					$arr_tables[$c] = mysql_tablename($result, $x);
					$query = "SHOW COLUMNS FROM {$arr_tables[$c]}";

					$res = mysql_query($query);

					$i=0;
					while ($row = mysql_fetch_object($res)) {
						$arr_fields[$c][$i] = $row;
						$i++;
					}
					$c++;
				}
			}

			// List tables
			$dump = '';
			if (isset($arr_tables) && is_array($arr_tables)) {
				for ($y = 0; $y < count($arr_tables); $y++){

					// DB Table name
					$table = $arr_tables[$y];

					// Dump data
					$data ="";
					$result     = mysql_query("SELECT * FROM `$table`");
					$num_rows   = mysql_num_rows($result);
					$num_fields = mysql_num_fields($result);

					$fieldSet = null;
					$valueSet = null;

					for ($i=0; $i <$num_rows; $i++) {

						$row = mysql_fetch_object($result);

						for ($x=0; $x<$num_fields; $x++) {
							$field_name = mysql_field_name($result, $x);
							$type = mysql_field_type($result, $x);

							$valueOfFiled = $row->$field_name;

							if ($type == 'blob') {
								if (bin2hex($valueOfFiled) == '') {
									$valueOfFiled = "''";
								} else {
									$valueOfFiled = '0x'.bin2hex($valueOfFiled);
								}
							} else if ($type == 'null') {
								$valueOfFiled = 'NULL';
							} else if ($type == 'string') {
								if (empty($valueOfFiled)) {
									if ($arr_fields[$y][$x]->Null == 'YES') {
										$valueOfFiled = 'NULL';
									} else {
										$valueOfFiled = "''";
									}
								} else {
									$valueOfFiled = "'".mysql_real_escape_string(stripslashes($valueOfFiled))."'";
								}
							} else if (($type == 'int') || ($type == 'real')) {
								if (empty($valueOfFiled)) {
									if ($arr_fields[$y][$x]->Null == 'YES') {
										$valueOfFiled = 'NULL';
									} else {
										$valueOfFiled = '0';
									}
								} else {
									$valueOfFiled = $valueOfFiled;
								}
							} else if (empty($valueOfFiled)) {
								if ($arr_fields[$y][$x]->Null == 'YES') {
									$valueOfFiled = 'NULL';
								} else {
									$valueOfFiled = "''";
								}
							} else {
								$valueOfFiled = "'".mysql_real_escape_string(stripslashes($valueOfFiled))."'";
							}

							if (($valueOfFiled != 'NULL') || (($type == 'int') && ($valueOfFiled === 0))) {
								$fieldSet[$i][] = "`{$field_name}`";
								$valueSet[$i][] = $valueOfFiled;
							}
						}

						$fieldStr = join($fieldSet[$i], ", ");
						$valueStr = join($valueSet[$i], ", ");

						$data.="INSERT INTO `$table` ({$fieldStr}) VALUES ({$valueStr});\r\n";
					}

					$data.= "\r\n";

					$dump .= $data;
				}
			}

			return $dump;

		}

	}
}
?>
