<?php

/*
 *
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
 * all the essential functionalities required for any enterprise. 
 * Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com
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
class Backup {
	
	/*
	 *	Class Attributes
	 *
	 **/
	 var $connection;
	 var $database;
	 
	 
	 
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
	
	
	function dumpDatabase($structure=false) {
		$struc="";
		if($structure) {			
		 	$struc= $this->_dumpStructure();	
		}
		$data = $this-> _dumpData();
		
		return $struc.$data;
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

				for ($i = 0; $i < $num_rows; $i++) {

					$row = mysql_fetch_object($result);
					$data .= "INSERT INTO `$table` (";

					// Field names
					for ($x = 0; $x < $num_fields; $x++) {

						$field_name = mysql_field_name($result, $x);
						
						$valueOfFiled = str_replace('\"', '"', mysql_escape_string($row->$field_name));
						$type = mysql_field_type($result, $x);
						
						if (!empty($valueOfFiled) || (($type == 'int') && ($valueOfFiled === 0))) {
							$data .= "`{$field_name}`, ";							
						}
					}
					
					$data = substr($data, 0, -2);

					$data .= ") VALUES (";

					// Values
					for ($x = 0; $x < $num_fields; $x++) {
						$field_name = mysql_field_name($result, $x);
						$type = mysql_field_type($result, $x);
						
						$valueOfFiled = str_replace('\"', '"', mysql_escape_string($row->$field_name));

						if (!empty($valueOfFiled)) {							
							$valueOfFiled = "'" .$valueOfFiled. "'";
							$data .= $valueOfFiled.", ";
						} else if (($type == 'int') && ($valueOfFiled === 0)) {
							$valueOfFiled = $valueOfFiled;
							$data .= $valueOfFiled.", ";
						}

					}
					
					$data = substr($data, 0, -2);

					$data.= ");\r\n";
				}

				$data.= "\r\n";

				$dump .= $data;
				
			}
			}

			return $dump;

		}

	}
	
	function _dumpStructure() {
		
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
}
?>