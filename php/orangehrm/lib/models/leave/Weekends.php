<?php
/**
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
 * @copyright 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com
 */

require_once ROOT_PATH.'/lib/dao/DMLFunctions.php';
require_once ROOT_PATH.'/lib/dao/SQLQBuilder.php';

/**
 * Weekend class
 * 
 * Manages weekends off. Required in deciding which days should be leaves.
 * 
 * @author S.H.Mohanjith <mohanjith@orangehrm.com>, <moha@mohanjith.net>
 *  
 */
class Weekends {
	/*
	 * Class Constants
	 *
	 **/	
	const WEEKENDS_TABLE = 'hs_hr_weekends';
	const WEEKENDS_TABLE_DAY = 'day';
	const WEEKENDS_TABLE_LENGTH = 'length';
	
	const WEEKENDS_LENGTH_FULL_DAY = 0;
	const WEEKENDS_LENGTH_HALF_DAY = 4;
	const WEEKENDS_LENGTH_WEEKEND = 8;
	
	const WEEKENDS_MONDAY = 1;
	const WEEKENDS_TUESDAY = 2;
	const WEEKENDS_WEDNESDAY = 3;
	const WEEKENDS_THURSDAY = 4;
	const WEEKENDS_FRIDAY = 5;
	const WEEKENDS_SATURDAY = 6;
	const WEEKENDS_SUNDAY = 7;
	
	/*
	 * Class atributes
	 *
	 **/
	private $day;
	private $length;
	
	/*
	 * Class atribute setters and getters
	 *
	 **/		
	public function setDay($day) {
		$this->day = $day;
	}	
	
	public function getDay() {
		return $this->day;
	}
	
	public function setLength($length) {
		$this->length = $length;
	}	
	public function getLength() {
		return $this->length;
	}	
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct() {
		//nothing to do
	}
	
	/**
	 * Class distructor
	 *
	 */
	public function __destruct() {
		//nothing to do
	}	
	
	/**
	 * Fetch the whole week
	 *
	 * requires a filled object
	 * 
	 * @access pubic
	 * @return Holidays[] $objArr
	 */
	public function fetchWeek() {
		$selectTable = "`".self::WEEKENDS_TABLE."`";
		
		$arrFieldList[0] = "`".self::WEEKENDS_TABLE_DAY."`";
		$arrFieldList[1] = "`".self::WEEKENDS_TABLE_LENGTH."`";
		
		$sqlBuilder = new SQLQBuilder();
				
		$query = $sqlBuilder->simpleSelect($selectTable, $arrFieldList, null, $arrFieldList[0], 'ASC');
			
		$dbConnection = new DMLFunctions();	

		$result = $dbConnection -> executeQuery($query);
		
		return $this->_buildObjArr($result);
	}
	
	/**
	 * Updates the day
	 * 
	 * requires a filled object
	 *
	 * @access pubic
	 */
	public function editDay() {		
		
		$arrFieldList[0] = "`".self::WEEKENDS_TABLE_LENGTH."`";				
		
		$arrRecordsList[0] = $this->getLength();

		$updateConditions[0] = "`".self::WEEKENDS_TABLE_DAY .'` = '.$this->getDay();
					
		$updateTable = "`".self::WEEKENDS_TABLE."`";
		
		$sqlBuilder = new SQLQBuilder();
				
		$query = $sqlBuilder->simpleUpdate($updateTable, $arrFieldList, $arrRecordsList, $updateConditions);
		
		//echo  $query;
		
		$dbConnection = new DMLFunctions();	

		$result = $dbConnection -> executeQuery($query);
		
		if (mysql_affected_rows() == 0) {			
			return $this->_addDay();
		}
		
		return $result;
	}
	
	/**
	 * Adds a day incase the day is not there. 
	 * 
	 * Unlikely to happen, but added as an auto heal feature.
	 * Needs a filled object.
	 * 
	 * @access private
	 */
	private function _addDay() {		
		
		$arrRecordsList[0] = $this->getDay();
		$arrRecordsList[1] = $this->getLength();
							
		$insertTable = "`".self::WEEKENDS_TABLE."`";
		
		$sqlBuilder = new SQLQBuilder();
				
		$query = $sqlBuilder->simpleInsert($insertTable, $arrRecordsList)." ON DUPLICATE KEY UPDATE ".self::WEEKENDS_TABLE_LENGTH." = ".$this->getLength();
		
		//echo  $query;
		
		$dbConnection = new DMLFunctions();	

		$result = $dbConnection -> executeQuery($query);	
		
	}
	
	/**
	 * Builds an array of Weekend objects
	 *
	 * @access private
	 * @param resource $result
	 * @return Weekend[] $objArr
	 */
	private function _buildObjArr($result) {
		$objArr = null;	
		
		if ($result) {
			while ($row = mysql_fetch_assoc($result)) {
				$tmpObjWeekends = new Weekends();
				
				if (isset($row[self::WEEKENDS_TABLE_DAY])) {
					$tmpObjWeekends->setDay($row[self::WEEKENDS_TABLE_DAY]);
				}
				
				if (isset($row[self::WEEKENDS_TABLE_LENGTH])) {
					$tmpObjWeekends->setLength($row[self::WEEKENDS_TABLE_LENGTH]);
				}
				
				$objArr[] = $tmpObjWeekends;
			}
		}
		
		return $objArr;
	}
	
	
}
?>