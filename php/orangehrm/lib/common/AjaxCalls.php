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

	const COMPARE_LEFT = 1;
	const COMPARE_RIGHT = 2;
	const COMPARE_MID = 3;

	const NON_XML_DEFAULT_MODE_DELIMITER = ',';
	const NON_XML_MULTI_LEVEL_MODE_DELIMITER = ':';
	const NON_XML_MULTI_LEVEL_MODE_LEFT_ENCASEMENT = '[';
	const NON_XML_MULTI_LEVEL_MODE_RIGHT_ENCASEMENT = ']';

	const NON_XML_DEFAULT_MODE = 1;
	const NON_XML_MULTI_LEVEL_MODE  = 2;

	private static $levels = 1;

	public static function sendResponse($values, $responseXML = true, $nonXMLMode = self::NON_XML_DEFAULT_MODE) {

		if ($responseXML) {
			$response = self::_fetchXMLResponse($values);
		} else {
			$response = self::_fetchNonXMLResponse($values, $nonXMLMode);
		}

		echo $response;
	}

	public static function fetchOptions($table, $valueField, $labelField, $descField, $filterKey, $joinTable = null, $joinCondition = null, $compareMethod = self::COMPARE_LEFT, $caseSensitive = false) {
		$selecteFields[] = $valueField;
		$selecteFields[] = $labelField;
		$selecteFields[] = $descField;
		
		$selectTables[] = $table;
		$selectTables[] = $joinTable; 
		
		$joinConditions[1] = $joinCondition;
		
		if (!$caseSensitive) {
				$labelField = "LOWER($labelField)";
				$filterKey = strtolower($filterKey);
		}

		switch ($compareMethod) {
			case self::COMPARE_LEFT :
				$selectConditions[] = "$labelField LIKE '$filterKey%'";
				break;
			
			case self::COMPARE_RIGHT :
				$selectConditions[] = "$labelField LIKE '%$filterKey'";
				break;
				
			case self::COMPARE_MID :
				$selectConditions[] = "$labelField LIKE '%$filterKey%'";
				break;
		}
		
		$orderCondition = $labelField;
		
		$sqlBuilder = new SQLQBuilder();
		$query = $sqlBuilder->selectFromMultipleTable($selecteFields, $selectTables, $joinConditions, $selectConditions, null, $orderCondition);

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

	private static function _fetchXMLResponse($values) {

	}

	private static function _fetchNonXMLResponse($values, $mode) {

		switch ($mode) {
			case self::NON_XML_DEFAULT_MODE :
				$response = implode(self::NON_XML_DEFAULT_MODE_DELIMITER, $values);
				break;

			case self::NON_XML_MULTI_LEVEL_MODE :

				$response = self::_getMultiLevelResponseString($values);
				break;
		}

		return $response;

	}

	private static function _getMultiLevelResponseString($arrayElements) {

		static $level = 1;

		$str = '';
		$delimiter = self::getMultiLevelDelimiter($level);

		foreach ($arrayElements as $element) {
			
			if (is_array($element)) {
				$level++;
				$str .= self::_getMultiLevelResponseString($element);
				$level--;
			} else {
				$str .= $element;
			}

			$str .= $delimiter; 

		}

		$str = substr($str, 0, strlen($str) - strlen($delimiter));

		return $str;

	}

	public static function getMultiLevelDelimiter($level) {

		$str = self::NON_XML_MULTI_LEVEL_MODE_LEFT_ENCASEMENT;
		$str .= str_repeat(self::NON_XML_MULTI_LEVEL_MODE_DELIMITER, $level);
		$str .= self::NON_XML_MULTI_LEVEL_MODE_RIGHT_ENCASEMENT;

		return $str;

	}

	public static function getDelimiterLevelsArray($level) {

		$arrLevels = array();

		for($i = 0; $i < $level; $i++) {
			$arrLevels[$i] = self::getMultiLevelDelimiter($i + 1);
		}

		return $arrLevels;

	}
}
?>
