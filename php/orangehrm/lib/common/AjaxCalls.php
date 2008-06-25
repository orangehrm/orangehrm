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

require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';
require_once ROOT_PATH . '/lib/dao/SQLQBuilder.php';

class AjaxCalls {

	public static function fetchOptions($table, $valueField, $labelField, $descField, $filterKey, $joinTable = null, $joinCondition = null) {
		/*$query = "SELECT $valueField, $labelField, $descField FROM $table ";

		if ($joinTable) {
			$query .= "LEFT OUTER JOIN $joinTable ON $joinCondition";
		}

		$query .= "WHERE $labelField LIKE '$filterKey%'";*/
		
		$selecteFields[] = $valueField;
		$selecteFields[] = $labelField;
		$selecteFields[] = $descField;
		
		$selectTables[] = $table;
		$selectTables[] = $joinTable; 
		
		$joinConditions[1] = $joinCondition;
		
		$selectConditions[] = "$labelField LIKE '$filterKey%'";
		
		$sqlBuilder = new SQLQBuilder();
		$query = $sqlBuilder->selectFromMultipleTable($selecteFields, $selectTables, $joinConditions, $selectConditions);

		$query = self::_formatQuery($query);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($query);
		
		if (mysql_error()) { echo mysql_error() + "\n" + $query; die;}
		
		$result = $dbConnection->executeQuery($query);

		while($row = mysql_fetch_array($result, MYSQL_NUM)) {
			$value = trim($row[0]);
			$label = trim($row[1]);
			$description = ($row[2] == '') ? '&nbsp;' : trim($row[2]);
			echo "$value,$label,$description\n";
		}
	}
	
	private static function _formatQuery($query) {
		$query = preg_replace("/\\\'/", "'", $query);
		
		return $query;
	}

}
?>