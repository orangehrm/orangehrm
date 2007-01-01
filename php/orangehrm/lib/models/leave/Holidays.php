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

require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';
require_once ROOT_PATH . '/lib/dao/SQLQBuilder.php';

/**
 * Holidays Class
 * 
 * Manages holidays. Required in deciding which days should be leaves.
 * 
 * @author S.H.Mohanjith <mohanjith@orangehrm.com>, <moha@mohanjith.net>
 *  
 */
class Holidays {
	
	const recurring = 1;
	
	/*
	 * Class atributes
	 *
	 **/
	private $holidayId;
	private $description;
	private $date;
	private $recurring;
	private $length;
	
	/*
	 * Class atribute setters and getters
	 *
	 **/	
	public function setHolidayId($holidayId) {
		$this->holidayId = $holidayId;
	}	
	
	public function getHolidayId() {
		return $this->holidayId;
	}
	
	public function setDescription($description) {
		$this->description = $description;
	}	
	public function getDescription() {
		return $this->description;
	}
	
	public function setDate($date) {
		$this->date = $date;
	}	
	
	public function getDate() {
		return $this->date;
	}	
	
	public function setRecurring($recurring) {
		$this->recurring = $recurring;
	}	
	
	public function getRecurring() {
		return $this->recurring;
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
	 * Checks whether the date is a holiday. (It doesn't check whether it's a holiday)
	 *
	 * @param String $date 'Y-d-m'
	 * @return mixed
	 */
	public function isHoliday($date) {
		$this->setDate($date);
		
		if ($res = $this->_isHoliday()) {
			return $res;
		}
		
		if ($res = $this->_isHoliday(true)) {
			return $res;
		}
		
		return null;
	}
	
	private function _isHoliday($recurring=false) {
		
		$date = $this->getDate();		
		
		$sqlBuilder = new SQLQBuilder();
				
		$selectTable = "`hs_hr_holidays`";
		$selectFields[0] = '`length`';
		
		if ($recurring) {
			list($year, $month, $day) = explode('-', $date);
			$selectConditions[0] = "`recurring` = ".Holidays::recurring;
			$selectConditions[1] = "`date` LIKE '%-$month-$day'";
			$selectConditions[2] = "`date` <= '$date'";
		} else {
			$selectConditions[0] = "`date` = '$date'";
		}
		
		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions);
				
		$dbConnection = new DMLFunctions();	

		$result = $dbConnection -> executeQuery($query);
		
		if ($result && ($row = mysql_fetch_row($result))) {
			return $row[0];
		}
		
		return null;
	}
		
	/**
	 * Add Holiday - one at a time
	 * 
	 * The object needs to be filled, except for the id.
	 * 
	 * @access public
	 *
	 */
	public function add() {
		$this->_getNewHolidayId();
				
		$arrRecordsList[0] = $this->getHolidayId();
		$arrRecordsList[1] = "'". $this->getDescription()."'";
		$arrRecordsList[2] = "'". $this->getDate()."'";
		$arrRecordsList[3] = $this->getRecurring();		
		$arrRecordsList[4] = $this->getLength();			
					
		$arrTable = "`hs_hr_holidays`";
		
		$sqlBuilder = new SQLQBuilder();
				
		$query = $sqlBuilder->simpleInsert($arrTable, $arrRecordsList);
		
		//echo  $query;
		
		$dbConnection = new DMLFunctions();	

		$result = $dbConnection -> executeQuery($query);
	}
	
	/**
	 * Edits holiday
	 * 
	 * The object needs to be filled.
	 * 
	 * @access public
	 *
	 */
	public function edit() {		
		
		$arrFieldList[0] = '`description`';
		$arrFieldList[1] = '`date`';
		$arrFieldList[2] = '`recurring`';
		$arrFieldList[3] = '`length`';		
		
		$arrRecordsList[0] = "'". $this->getDescription()."'";
		$arrRecordsList[1] = "'". $this->getDate()."'";
		$arrRecordsList[2] = $this->getRecurring();		
		$arrRecordsList[3] = $this->getLength();

		$updateConditions[0] = '`holiday_id` = '.$this->getHolidayId();
					
		$arrTable = "`hs_hr_holidays`";
		
		$sqlBuilder = new SQLQBuilder();
				
		$query = $sqlBuilder->simpleUpdate($arrTable, $arrFieldList, $arrRecordsList, $updateConditions);
		
		//echo  $query;
		
		$dbConnection = new DMLFunctions();	

		$result = $dbConnection -> executeQuery($query);
	}
	
	/**
	 * Deletes holiday
	 * 
	 * The object needs to be filled.
	 * 
	 * @access public
	 *
	 */
	public function delete() {
		$sql_builder = new SQLQBuilder();
		
		$arrFieldList[0] = 'HOLIDAY_ID';
		$arrValueList[0] = array($this->getHolidayId());
		
		$sql_builder->table_name = "HS_HR_HOLIDAYS";
		$sql_builder->arr_delete = $arrFieldList;
		
		$sql_builder->flg_delete = 'true';		
		
		$query = $sql_builder->deleteRecord($arrValueList);
		
		//echo $query;
		
		$dbConnection = new DMLFunctions();	
		
		$result = $dbConnection -> executeQuery($query);		
	}
	
	private function _getNewHolidayId() {
		$sql_builder = new SQLQBuilder();
		
		$selectTable = "`hs_hr_holidays`";		
		$selectFields[0] = '`holiday_id`';
		$selectOrder = "DESC";
		$selectLimit = 1;
		$sortingField = '`holiday_id`';
		
		$query = $sql_builder->simpleSelect($selectTable, $selectFields, null, $sortingField, $selectOrder, $selectLimit);

		$dbConnection = new DMLFunctions();	

		$result = $dbConnection -> executeQuery($query);
		
		$row = mysql_fetch_row($result);
		
		$this->setHolidayId($row[0]+1);
	}
	
}
?>