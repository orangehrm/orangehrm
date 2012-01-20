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
 */

require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';
require_once ROOT_PATH . '/lib/dao/SQLQBuilder.php';
require_once ROOT_PATH . '/lib/common/UniqueIDGenerator.php';

class HspPayPeriod {
	const PAY_PERIOD_DB_TABLE = 'hs_hr_pay_period';
	const PAY_PERIOD_DB_FIELD_ID = 'id';
	const PAY_PERIOD_DB_FIELD_START_DATE = 'start_date';
	const PAY_PERIOD_DB_FIELD_END_DATE = 'end_date';
	const PAY_PERIOD_DB_FIELD_CLOSE_DATE = 'close_date';
	const PAY_PERIOD_DB_FIELD_CHECK_DATE = 'check_date';
	const PAY_PERIOD_DB_FIELD_TIMESHEET_APROVAL_DUE_DATE = 'timesheet_aproval_due_date';

	private $id;
	private $startDate;
	private $endDate;
	private $closeDate;
	private $checkDate;
	private $timesheetAprovalDueDate;

	public function setId($id) {
		$this->id=$id;
	}

	public function getId() {
		return $this->id;
	}

	public function setStartDate($startDate) {
		$this->startDate=$startDate;
	}

	public function getStartDate() {
		return $this->startDate;
	}

	public function setEndDate($endDate) {
		$this->endDate=$endDate;
	}

	public function getEndDate() {
		return $this->endDate;
	}

	public function setCloseDate($closeDate) {
		$this->closeDate=$closeDate;
	}

	public function getCloseDate() {
		return $this->closeDate;
	}

	public function setCheckDate($checkDate) {
		$this->checkDate=$checkDate;
	}

	public function getCheckDate() {
		return $this->checkDate;
	}

	public function setTimesheetAprovalDueDate($timesheetAprovalDueDate) {
		$this->timesheetAprovalDueDate=$timesheetAprovalDueDate;
	}

	public function getTimesheetAprovalDueDate() {
		return $this->timesheetAprovalDueDate;
	}

	public function __construct() {
		// nothing to do
	}

	public function add() {
		$this->id = UniqueIDGenerator::getInstance()->getNextID(self::PAY_PERIOD_DB_TABLE, self::PAY_PERIOD_DB_FIELD_ID);

		$arrTable = '`'.self::PAY_PERIOD_DB_TABLE.'`';

		$insertFields[0] = '`'.self::PAY_PERIOD_DB_FIELD_ID.'`';
		$insertFields[1] = '`'.self::PAY_PERIOD_DB_FIELD_START_DATE.'`';
		$insertFields[2] = '`'.self::PAY_PERIOD_DB_FIELD_END_DATE.'`';
		$insertFields[3] = '`'.self::PAY_PERIOD_DB_FIELD_CLOSE_DATE.'`';
		$insertFields[4] = '`'.self::PAY_PERIOD_DB_FIELD_CHECK_DATE.'`';
		$insertFields[5] = '`'.self::PAY_PERIOD_DB_FIELD_TIMESHEET_APROVAL_DUE_DATE.'`';

		$arrRecordsList[0] = $this->id;
		$arrRecordsList[1] = "'". $this->startDate."'";
		$arrRecordsList[2] = "'". $this->endDate."'";
		$arrRecordsList[3] = "'". $this->closeDate."'";
		$arrRecordsList[4] = "'".$this->checkDate."'";
		$arrRecordsList[5] = "'".$this->timesheetAprovalDueDate."'";

		$sqlBuilder = new SQLQBuilder();
		$query = $sqlBuilder->simpleInsert($arrTable, $arrRecordsList, $insertFields);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection -> executeQuery($query);

		/*if ($result) {
			return mysql_affected_rows();
		} else {
			throw new HspPayPeriodException("Error in SQL Query", HspPayPeriodException::ERROR_IN_DB_QUERY);
		}*/

		return $result;
	}

	public function update() {

		if (!CommonFunctions::isValidId($this->id)) {
			throw new HspPayPeriodException("Invalid id", HspPayPeriodException::INVALID_ID);
		}

		$updateTable = '`'.self::PAY_PERIOD_DB_TABLE.'`';

		$changeFields[0] = '`'.self::PAY_PERIOD_DB_FIELD_START_DATE.'`';
		$changeFields[1] = '`'.self::PAY_PERIOD_DB_FIELD_END_DATE.'`';
		$changeFields[2] = '`'.self::PAY_PERIOD_DB_FIELD_CLOSE_DATE.'`';
		$changeFields[3] = '`'.self::PAY_PERIOD_DB_FIELD_CHECK_DATE.'`';
		$changeFields[4] = '`'.self::PAY_PERIOD_DB_FIELD_TIMESHEET_APROVAL_DUE_DATE.'`';

		$changeValues[0] = "'".$this->startDate."'";
		$changeValues[1] = "'".$this->endDate."'";
		$changeValues[2] = "'".$this->closeDate."'";
		$changeValues[3] = "'".$this->checkDate."'";
		$changeValues[4] = "'".$this->timesheetAprovalDueDate."'";

		$updateConditions[0] = "`".self::PAY_PERIOD_DB_FIELD_ID."` = '".$this->id."'";

		$sqlBuilder = new SQLQBuilder();
		$query = $sqlBuilder->simpleUpdate($updateTable, $changeFields, $changeValues, $updateConditions);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection -> executeQuery($query);

		return $result;
	}

	public function delete() {

		if (!CommonFunctions::isValidId($this->id)) {
			throw new HspPayPeriodException("Invalid id", HspPayPeriodException::INVALID_ID);
		}

		$arrList = array($this->id);
		$count = self::_delete($arrList);

		if ($count !== 1) {
			throw new HspPayPeriodException("Error in Delete", HspPayPeriodException::INVALID_ROW_COUNT);
		}

	}

	private static function _delete($ids) {
		$tableName = self::PAY_PERIOD_DB_TABLE;
		$arrFieldList[0] = self::PAY_PERIOD_DB_FIELD_ID;

		$sqlBuilder = new SQLQBuilder();

		$sqlBuilder->table_name = $tableName;
		$sqlBuilder->flg_delete = 'true';
		$sqlBuilder->arr_delete = $arrFieldList;

		$arrList[] = $ids;
		$sqlQString = $sqlBuilder->deleteRecord($arrList);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($sqlQString);

		if ($result) {
			return mysql_affected_rows();
		} else {
			throw new HspPayPeriodException("Error in SQL Query", HspPayPeriodException::ERROR_IN_DB_QUERY);
		}
	}

	public static function getYears() {
		$sqlBuilder = new SQLQBuilder();

		$selectTable = "`".self::PAY_PERIOD_DB_TABLE."`";
		$selectFields[] = "`".self::PAY_PERIOD_DB_FIELD_CHECK_DATE."`";
		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($query);

		if ($dbConnection->dbObject->numberOfRows($result) > 0) {
			while ($row = $dbConnection->dbObject->getArray($result)) {
				$years[] = (int)substr($row[0], 0, 4);
			}
		}

		$years[] = date('Y');
		$years[] = date('Y')+1;
		$years = array_unique($years);
		sort($years);

		return $years;
	}

	public static function listPayPeriods($year) {
		$sqlBuilder = new SQLQBuilder();

		$selectTable = "`".self::PAY_PERIOD_DB_TABLE."`";

		$selectFields[] = "`".self::PAY_PERIOD_DB_FIELD_ID."`";
		$selectFields[] = "`".self::PAY_PERIOD_DB_FIELD_START_DATE."`";
		$selectFields[] = "`".self::PAY_PERIOD_DB_FIELD_END_DATE."`";
		$selectFields[] = "`".self::PAY_PERIOD_DB_FIELD_CLOSE_DATE."`";
		$selectFields[] = "`".self::PAY_PERIOD_DB_FIELD_CHECK_DATE."`";
		$selectFields[] = "`".self::PAY_PERIOD_DB_FIELD_TIMESHEET_APROVAL_DUE_DATE."`";

		$selectConditions[] = "`".self::PAY_PERIOD_DB_FIELD_CHECK_DATE."` >= '{$year}-01-01'";
		$selectConditions[] = "`".self::PAY_PERIOD_DB_FIELD_CHECK_DATE."` <= '{$year}-12-31'";

		$selectOrder = "ASC";
		$selectOrderBy = "`".self::PAY_PERIOD_DB_FIELD_CHECK_DATE."`";

		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions, $selectOrderBy, $selectOrder);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection -> executeQuery($query);

		return self::_buildObjArr($result);
	}

	public static function getPayPeriod($id) {
		if (!CommonFunctions::isValidId($id)) {
			throw new HspException("Invalid id", HspException::INVALID_ID);
		}

		$sqlBuilder = new SQLQBuilder();

		$selectTable = "`".self::PAY_PERIOD_DB_TABLE."`";

		$selectFields[] = "`".self::PAY_PERIOD_DB_FIELD_ID."`";
		$selectFields[] = "`".self::PAY_PERIOD_DB_FIELD_START_DATE."`";
		$selectFields[] = "`".self::PAY_PERIOD_DB_FIELD_END_DATE."`";
		$selectFields[] = "`".self::PAY_PERIOD_DB_FIELD_CLOSE_DATE."`";
		$selectFields[] = "`".self::PAY_PERIOD_DB_FIELD_CHECK_DATE."`";
		$selectFields[] = "`".self::PAY_PERIOD_DB_FIELD_TIMESHEET_APROVAL_DUE_DATE."`";

		$selectConditions[] = "`".self::PAY_PERIOD_DB_FIELD_ID."` = {$id}";

		$selectOrder = "ASC";
		$selectOrderBy = "`".self::PAY_PERIOD_DB_FIELD_CHECK_DATE."`";

		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions, $selectOrderBy, $selectOrder);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection -> executeQuery($query);

		$numResults = mysql_num_rows($result);
		if ($numResults == 1) {
			$objs = self::_buildObjArr($result);
			return $objs[0];
		} else if ($numResults == 0) {
			throw new HspPayPeriodException("Invalid number of results returned.", HspPayPeriodException::HSP_NOT_FOUND);
		} else {
			throw new HspPayPeriodException("Invalid number of results returned.", HspPayPeriodException::INVALID_ROW_COUNT);
		}
	}

	public static function countPayPeriods($year, $left=false) {
		$sqlBuilder = new SQLQBuilder();

		$selectTable = "`".self::PAY_PERIOD_DB_TABLE."`";

		$selectFields[] = "COUNT(*)";

		if ($left) {
			if(strcmp($year, date('Y', time())) == 0) {
				$selectConditions[] = "`".self::PAY_PERIOD_DB_FIELD_CHECK_DATE."` > '".date('Y-m-d',time())."'";
			}else{
				$selectConditions[] = "`".self::PAY_PERIOD_DB_FIELD_CHECK_DATE."` >= '{$year}-01-01'";
			}
		} else {
			$selectConditions[] = "`".self::PAY_PERIOD_DB_FIELD_CHECK_DATE."` >= '{$year}-01-01'";
		}

		$selectConditions[] = "`".self::PAY_PERIOD_DB_FIELD_CHECK_DATE."` <= '{$year}-12-31'";

		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection -> executeQuery($query);

		$row = mysql_fetch_row($result);

		return $row[0];
	}
	public static function countClosedPayPeriods($year, $left=false) {
		$sqlBuilder = new SQLQBuilder();

		$selectTable = "`".self::PAY_PERIOD_DB_TABLE."`";

		$selectFields[] = "COUNT(*)";

		if ($left) {
			if(strcmp($year, date('Y', time())) == 0) {
				$selectConditions[] = "`".self::PAY_PERIOD_DB_FIELD_CLOSE_DATE."` > '".date('Y-m-d',time())."'";
			}else{
				$selectConditions[] = "`".self::PAY_PERIOD_DB_FIELD_CLOSE_DATE."` >= '{$year}-01-01'";
			}
		} else {
			$selectConditions[] = "`".self::PAY_PERIOD_DB_FIELD_CLOSE_DATE."` >= '{$year}-01-01'";
		}

		$selectConditions[] = "`".self::PAY_PERIOD_DB_FIELD_CLOSE_DATE."` <= '{$year}-12-31'";

		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection -> executeQuery($query);

		$row = mysql_fetch_row($result);

		return $row[0];
	}

	public static function countCheckDates($date1, $date2, $currentYear=true) {

		$yearStart = date('Y')."-01-01";
		$startDate = $date1;
		$endDate = $date2;

		if ($startDate < $yearStart && $currentYear) {
			$startDate = $yearStart;
		}

		$selectTable = "`".self::PAY_PERIOD_DB_TABLE."`";
		$selectFields[0] = "COUNT(*)";
		$selectConditions[0] = "`".self::PAY_PERIOD_DB_FIELD_CHECK_DATE."` > '".$startDate."'";
		$selectConditions[1] = "`".self::PAY_PERIOD_DB_FIELD_CHECK_DATE."` <= '".$endDate."'";

		$sqlBuilder = new SQLQBuilder();

		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		$resultArray = $dbConnection->dbObject->getArray($result);

		return $resultArray[0];

	}

	private static function _buildObjArr($result) {

		$objArr = array();

		while ($row = mysql_fetch_assoc($result)) {
			$tmpArr = new HspPayPeriod();
			$tmpArr->setId($row[self::PAY_PERIOD_DB_FIELD_ID]);
			$tmpArr->setStartDate($row[self::PAY_PERIOD_DB_FIELD_START_DATE]);
			$tmpArr->setEndDate($row[self::PAY_PERIOD_DB_FIELD_END_DATE]);
			$tmpArr->setCloseDate($row[self::PAY_PERIOD_DB_FIELD_CLOSE_DATE]);
			$tmpArr->setCheckDate($row[self::PAY_PERIOD_DB_FIELD_CHECK_DATE]);
			$tmpArr->setTimesheetAprovalDueDate($row[self::PAY_PERIOD_DB_FIELD_TIMESHEET_APROVAL_DUE_DATE]);

			$objArr[] = $tmpArr;
		}

		return $objArr;
	}
}

class HspPayPeriodException extends Exception {
	const PAY_PERIOD_EXCEPTION_NO_IDS = 1;
	const ERROR_IN_DB_QUERY = 2;
	const INVALID_ID = 3;
	const INVALID_ROW_COUNT = 4;
	const HSP_NOT_FOUND = 5;
}
?>
