<?php
/**
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
 * @copyright 2006 OrangeHRM Inc., http://www.orangehrm.com
 */

require_once ROOT_PATH.'/lib/dao/DMLFunctions.php';
require_once ROOT_PATH.'/lib/dao/SQLQBuilder.php';
require_once ROOT_PATH.'/lib/models/leave/Leave.php';

/**
 * Weekend class
 *
 * Manages weekends off. Required in deciding which days should be leaves.
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

		//echo mysql_num_rows($result)."\n";

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
	 * Check whether the given date is a weekend.
	 * @param date $date
	 * @return bool true on success and false on failiure
	 */
	public static function isWeekend($date) {

		$dayNumber = date('N', strtotime($date));

		$selectTable = "`".self::WEEKENDS_TABLE."`";
		$selectFields[0] = "`".self::WEEKENDS_TABLE_LENGTH."`";
		$selectConditions[0] = "`".self::WEEKENDS_TABLE_DAY."` = $dayNumber";

		$sqlBuilder = new SQLQBuilder();
		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions);
		$dbConnection = new DMLFunctions();
		
		$result = $dbConnection -> executeQuery($query);
		$row = $dbConnection->dbObject->getArray($result);

		if ($row[0] == self::WEEKENDS_LENGTH_WEEKEND) {
		    return true;
		} else {
		    return false;
		}

	}
	
    public static function isHalfDayWeekend($date) {

        $dayNumber = date('N', strtotime($date));

        $selectTable = "`".self::WEEKENDS_TABLE."`";
        $selectFields[0] = "`".self::WEEKENDS_TABLE_LENGTH."`";
        $selectConditions[0] = "`".self::WEEKENDS_TABLE_DAY."` = $dayNumber";

        $sqlBuilder = new SQLQBuilder();
        $query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions);
        $dbConnection = new DMLFunctions();
        
        $result = $dbConnection -> executeQuery($query);
        $row = $dbConnection->dbObject->getArray($result);

        if ($row[0] == (self::WEEKENDS_LENGTH_HALF_DAY)) {
           return (self::WEEKENDS_LENGTH_HALF_DAY);
        } else {
            return null;
        }

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

    public static function updateWeekendsForLeaves(){

        $dbConnection = new DMLFunctions();

        $approved = Leave::LEAVE_STATUS_LEAVE_APPROVED;
        $taken = Leave::LEAVE_STATUS_LEAVE_TAKEN;
        $weekend = Leave::LEAVE_STATUS_LEAVE_WEEKEND;
        $lengthFullDay = Leave::LEAVE_LENGTH_FULL_DAY;

        $query = "UPDATE hs_hr_leave SET leave_status = $approved, leave_length_hours = $lengthFullDay " .
                " WHERE leave_status = $weekend AND leave_date > CURDATE()";
        $result = $dbConnection -> executeQuery($query);
        $query = "UPDATE hs_hr_leave SET leave_status = $taken, leave_length_hours = $lengthFullDay " .
                "WHERE leave_status = $weekend AND leave_date <= CURDATE()";
        $result = $dbConnection -> executeQuery($query);

        $query = "SELECT leave_id,leave_date FROM hs_hr_leave ";
        $result = $dbConnection -> executeQuery($query);
        while ($row = $dbConnection->dbObject->getArray($result)) {

            $length = self::getWeekendLength($row['leave_date']);
            if ($length) {
                if ($length == $lengthFullDay) {
                	$length = 0;
                }
                $query = "UPDATE hs_hr_leave SET leave_status = $weekend, leave_length_hours = $length " .
                        "WHERE leave_id = $row[leave_id]";
                $dbConnection -> executeQuery($query);
            }
        }
    }

    public static function getWeekendLength($date) {

        $dayNumber = date('N', strtotime($date));

        $selectTable = "`".self::WEEKENDS_TABLE."`";
        $selectFields[0] = "`".self::WEEKENDS_TABLE_LENGTH."`";
        $selectConditions[0] = "`".self::WEEKENDS_TABLE_DAY."` = $dayNumber";

        $sqlBuilder = new SQLQBuilder();
        $query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions);
        $dbConnection = new DMLFunctions();
        $result = $dbConnection -> executeQuery($query);
        $row = $dbConnection->dbObject->getArray($result);

        return $row[0];
    }

}
?>