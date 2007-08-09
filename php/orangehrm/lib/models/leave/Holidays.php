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

/**
 * Holidays Class
 *
 * Manages holidays. Required in deciding which days should be leaves.
 *
 * @author S.H.Mohanjith <mohanjith@orangehrm.com>, <moha@mohanjith.net>
 *
 */
class Holidays {

	/*
	 * Class Constants
	 *
	 **/
	const HOLIDAYS_RECURRING = 1;
	const HOLIDAYS_NOT_RECURRING = 0;

	const HOLIDAYS_TABLE = 'hs_hr_holidays';
	const HOLIDAYS_TABLE_HOLIDAY_ID = 'holiday_id';
	const HOLIDAYS_TABLE_DESCRIPTION = 'description';
	const HOLIDAYS_TABLE_DATE = 'date';
	const HOLIDAYS_TABLE_RECURRING = 'recurring';
	const HOLIDAYS_TABLE_LENGTH = 'length';

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
	 * @access public
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

	/**
	 * Checks whether the date in the object is a holiday.
	 *
	 * If $recurring is set check whether the date is a recurring holiday
	 * else a specific holiday.
	 *
	 * @access private
	 * @param boolean $recurring
	 * @return mixed $length;
	 */
	private function _isHoliday($recurring=false) {

		$date = $this->getDate();

		$sqlBuilder = new SQLQBuilder();

		$selectTable = "`".self::HOLIDAYS_TABLE."`";
		$selectFields[0] = "`".self::HOLIDAYS_TABLE_LENGTH."`";

		if ($recurring) {
			list($year, $month, $day) = explode('-', $date);
			$selectConditions[0] = "`".self::HOLIDAYS_TABLE_RECURRING."` = ".self::HOLIDAYS_RECURRING;
			$selectConditions[1] = "`".self::HOLIDAYS_TABLE_DATE."` LIKE '%-$month-$day'";
			$selectConditions[2] = "`".self::HOLIDAYS_TABLE_DATE."` <= '$date'";
		} else {
			$selectConditions[0] = "`".self::HOLIDAYS_TABLE_DATE."` = '$date'";
		}

		$selectOrderBy = "`".self::HOLIDAYS_TABLE_LENGTH."`";

		$selectOrder = 'DESC';

		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions, $selectOrderBy, $selectOrder);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection -> executeQuery($query);

		$length = null;

		if ($result && ($row = mysql_fetch_row($result))) {
			$length = $row[0];
		}

		return $length;
	}

	/**
	 * List the holidays for a year
	 *
	 * @access public
	 * @param String $year
	 * @return Holidays[] $objArr
	 */
	public function listHolidays($year=null) {
		if (!isset($year)) {
			$year = date("Y");
		}
		$selectTable = "`".self::HOLIDAYS_TABLE."`";

		$arrFieldList[0] = "`".self::HOLIDAYS_TABLE_HOLIDAY_ID."`";
		$arrFieldList[1] = "`".self::HOLIDAYS_TABLE_DESCRIPTION."`";
		$arrFieldList[2] = "DATE_FORMAT(`".self::HOLIDAYS_TABLE_DATE."`, '$year-%m-%d') a";
		$arrFieldList[3] = "`".self::HOLIDAYS_TABLE_LENGTH."`";
		$arrFieldList[4] = "`".self::HOLIDAYS_TABLE_RECURRING."`";

		$arrSelectConditions[0] = "(`".self::HOLIDAYS_TABLE_DATE."` LIKE '$year-%' OR `".self::HOLIDAYS_TABLE_RECURRING."` = ".self::HOLIDAYS_RECURRING.")";

		$sqlBuilder = new SQLQBuilder();

		$query = $sqlBuilder->simpleSelect($selectTable, $arrFieldList, $arrSelectConditions, 'a', 'ASC');

		//echo $query;

		$dbConnection = new DMLFunctions();

		$result = $dbConnection -> executeQuery($query);

		return $this->_buildObjArr($result);
	}

	public function fetchHoliday($holidayId) {
		$selectTable = "`".self::HOLIDAYS_TABLE."`";

		$arrFieldList[0] = "`".self::HOLIDAYS_TABLE_HOLIDAY_ID."`";
		$arrFieldList[1] = "`".self::HOLIDAYS_TABLE_DESCRIPTION."`";
		$arrFieldList[2] = "`".self::HOLIDAYS_TABLE_DATE."`";
		$arrFieldList[3] = "`".self::HOLIDAYS_TABLE_LENGTH."`";
		$arrFieldList[4] = "`".self::HOLIDAYS_TABLE_RECURRING."`";

		$arrSelectConditions[0] = "`".self::HOLIDAYS_TABLE_HOLIDAY_ID."` = $holidayId";

		$sqlBuilder = new SQLQBuilder();

		$query = $sqlBuilder->simpleSelect($selectTable, $arrFieldList, $arrSelectConditions, null, null, 1);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection -> executeQuery($query);

		return $this->_buildObjArr($result);
	}

	/**
	 * Builds an array of Holidays.
	 *
	 * @access private
	 * @param resource $result
	 * @return Holidays $objArr
	 */
	private function _buildObjArr($result) {
		$objArr = null;

		if ($result) {
			while ($row = mysql_fetch_assoc($result)) {
				$tmpObjHolidays = new Holidays();

				if (isset($row[self::HOLIDAYS_TABLE_HOLIDAY_ID])) {
					$tmpObjHolidays->setHolidayId($row[self::HOLIDAYS_TABLE_HOLIDAY_ID]);
				}

				if (isset($row[self::HOLIDAYS_TABLE_DESCRIPTION ])) {
					$tmpObjHolidays->setDescription($row[self::HOLIDAYS_TABLE_DESCRIPTION]);
				}

				if (isset($row[self::HOLIDAYS_TABLE_DATE])) {
					$tmpObjHolidays->setDate($row[self::HOLIDAYS_TABLE_DATE]);
				} else if (isset($row['a'])) {
					$tmpObjHolidays->setDate($row['a']);
				}

				if (isset($row[self::HOLIDAYS_TABLE_RECURRING ])) {
					$tmpObjHolidays->setRecurring($row[self::HOLIDAYS_TABLE_RECURRING ]);
				}

				if (isset($row[self::HOLIDAYS_TABLE_LENGTH])) {
					$tmpObjHolidays->setLength($row[self::HOLIDAYS_TABLE_LENGTH]);
				}

				$objArr[] = $tmpObjHolidays;
			}
		}

		return $objArr;
	}

	/**
	 * Add Holiday - one at a time
	 *
	 * The object needs to be filled, except for the id.
	 *
	 * @access public
	 */
	public function add() {

		$this->holidayId = UniqueIDGenerator::getInstance()->getNextID(self::HOLIDAYS_TABLE, self::HOLIDAYS_TABLE_HOLIDAY_ID);

		$arrRecordsList[0] = $this->getHolidayId();
		$arrRecordsList[1] = "'". $this->getDescription()."'";
		$arrRecordsList[2] = "'". $this->getDate()."'";
		$arrRecordsList[3] = $this->getRecurring();
		$arrRecordsList[4] = $this->getLength();

		$arrTable = self::HOLIDAYS_TABLE;

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
	 */
	public function edit() {

		$arrFieldList[0] = "`".self::HOLIDAYS_TABLE_DESCRIPTION."`";
		$arrFieldList[1] = "`".self::HOLIDAYS_TABLE_DATE."`";
		$arrFieldList[2] = "`".self::HOLIDAYS_TABLE_RECURRING."`";
		$arrFieldList[3] = "`".self::HOLIDAYS_TABLE_LENGTH."`";

		$arrRecordsList[0] = "'". $this->getDescription()."'";
		$arrRecordsList[1] = "'". $this->getDate()."'";
		$arrRecordsList[2] = $this->getRecurring();
		$arrRecordsList[3] = $this->getLength();

		$updateConditions[0] = "`".self::HOLIDAYS_TABLE_HOLIDAY_ID.'` = '.$this->getHolidayId();

		$arrTable = "`".self::HOLIDAYS_TABLE."`";

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
	 */
	public function delete() {
		$sql_builder = new SQLQBuilder();

		$arrFieldList[0] = self::HOLIDAYS_TABLE_HOLIDAY_ID;
		$arrValueList[0] = array($this->getHolidayId());

		$sql_builder->table_name = self::HOLIDAYS_TABLE;
		$sql_builder->arr_delete = $arrFieldList;

		$sql_builder->flg_delete = 'true';

		$query = $sql_builder->deleteRecord($arrValueList);

		//echo $query;

		$dbConnection = new DMLFunctions();

		$result = $dbConnection -> executeQuery($query);
	}

}
?>