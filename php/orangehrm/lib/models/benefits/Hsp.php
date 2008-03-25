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

require_once ROOT_PATH.'/lib/dao/DMLFunctions.php';
require_once ROOT_PATH.'/lib/dao/SQLQBuilder.php';
require_once ROOT_PATH.'/lib/common/Config.php';

class Hsp {

    const DB_TABLE_HSP_SUMMARY = "hs_hr_hsp_summary";
    const DB_FIELD_SUMMARY_ID = "summary_id";
    const DB_FIELD_EMPLOYEE_ID = "employee_id";
    const DB_FIELD_HSP_PLAN_ID = "hsp_plan_id";
    const DB_FIELD_HSP_PLAN_YEAR = "hsp_plan_year";
    const DB_FIELD_HSP_PLAN_STATUS = "hsp_plan_status";
    const DB_FIELD_ANNUAL_LIMIT = "annual_limit";
    const DB_FIELD_EMPLOYER_AMOUNT = "employer_amount";
    const DB_FIELD_EMPLOYEE_AMOUNT = "employee_amount";
    const DB_FIELD_TOTAL_ACCRUED = "total_accrued";
    const DB_FIELD_TOTAL_USED = "total_used";

    private $summaryId;
    private	$employeeId;
	private	$hspPlanId;
	private $hspPlanName;
	private $employeeName;
	private	$hspPlanYear;
	private $hspPlanStatus;
	private $annualLimit;
	private $employerAmount;
	private $employeeAmount;
	private $totalAccrued;
	private $totalUsed;

	public function setSummaryId($summaryId) {
	    $this->summaryId = $summaryId;
	}

	public function getSummaryId() {
	    return $this->summaryId;
	}

	public function setEmployeeId($employeeId) {
	    $this->employeeId = $employeeId;
	}

	public function getEmployeeId() {
	    return $this->employeeId;
	}

	public function setHspPlanId($hspPlanId) {
	    $this->hspPlanId = $hspPlanId;
	}

	public function getHspPlanId() {
	    return $this->hspPlanId;
	}

	public function setHspPlanName($hspPlanName) {
	    $this->hspPlanName = $hspPlanName;
	}

	public function getHspPlanName() {
	    return $this->hspPlanName;
	}

	public function setEmployeeName($employeeName) {
	    $this->employeeName = $employeeName;
	}

	public function getEmployeeName() {
	    return $this->employeeName;
	}

	public function setHspPlanYear($hspPlanYear) {
	    $this->hspPlanYear = $hspPlanYear;
	}

	public function getHspPlanYear() {
	    return $this->hspPlanYear;
	}

	public function setHspPlanStatus($hspPlanStatus) {
	    $this->hspPlanStatus = $hspPlanStatus;
	}

	public function getHspPlanStatus() {
	    return $this->hspPlanStatus;
	}

	public function setAnnualLimit($annualLimit) {
	    $this->annualLimit = $annualLimit;
	}

	public function getAnnualLimit() {
	    return $this->annualLimit;
	}

	public function setEmployerAmount($employerAmount) {
	    $this->employerAmount = $employerAmount;
	}

	public function getEmployerAmount() {
	    return $this->employerAmount;
	}

	public function setEmployeeAmount($employeeAmount) {
	    $this->employeeAmount = $employeeAmount;
	}

	public function getEmployeeAmount() {
	    return $this->employeeAmount;
	}

	public function setTotalAccrued($totalAccrued) {
	    $this->totalAccrued = $totalAccrued;
	}

	public function getTotalAccrued() {
	    return $this->totalAccrued;
	}

	public function setTotalUsed($totalUsed) {
	    $this->totalUsed = $totalUsed;
	}

	public function getTotalUsed() {
	    return $this->totalUsed;
	}

	/**
	 * This function updates 'total_accrued' field in 'hs_hr_hsp_summary'.
	 * It first calls 'hsp_accrued_last_updated' field from 'hs_hr_config' table.
	 * If updated date is older than current day, it checks for new 'check_date's from
	 * 'hs_hr_pay_period' table. If new check dates of current year are available, it checks current
	 * HSP scheme and updates 'total_accrued' only for that scheme.
	 */

	public static function updateAccrued($year) {

		if (Config::getHspAccruedLastUpdated() < date('Y-m-d')) {

			$checkDates = HspPayPeriod::countCheckDates(Config::getHspAccruedLastUpdated(), date('Y-m-d'));

			if ($checkDates > 0) {

				$selectTable = "`".self::DB_TABLE_HSP_SUMMARY."`";
				$selectFields[0] = "`".self::DB_FIELD_SUMMARY_ID."`";
				$selectFields[1] = "`".self::DB_FIELD_HSP_PLAN_STATUS."`";
				$selectFields[2] = "`".self::DB_FIELD_EMPLOYER_AMOUNT."`";
				$selectFields[3] = "`".self::DB_FIELD_EMPLOYEE_AMOUNT."`";
				$selectFields[4] = "`".self::DB_FIELD_TOTAL_ACCRUED."`";
				$selectConditions[0] = self::_twoHspPlansCondition(Config::getHspCurrentPlan());
				$selectConditions[1] = "`".self::DB_FIELD_HSP_PLAN_YEAR."`= '".$year."'";

				$sqlBuilder = new SQLQBuilder();

				$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions);

				$dbConnection = new DMLFunctions();

				$result = $dbConnection->executeQuery($query);

				$rowCount = $dbConnection->dbObject->numberOfRows($result);

				for ($i=0; $i<$rowCount; $i++) {

					$row = $dbConnection->dbObject->getArray($result);
					if ($row[1] == 1) {
					    $updatedArray[$i][0] = $row[0];
					    $updatedArray[$i][1] = $row[4]+($checkDates*($row[2]+$row[3]));
					} else {
					    $updatedArray[$i][0] = $row[0];
					    $updatedArray[$i][1] = $row[4];
					}

				}

				for ($i=0; $i<count($updatedArray); $i++) {

				    $updateTable = "`".self::DB_TABLE_HSP_SUMMARY."`";
				    $updateFields[0] = "`".self::DB_FIELD_TOTAL_ACCRUED."`";
				    $updateValues[0] = "'".$updatedArray[$i][1]."'";
				    $updateConditions[0] = "`".self::DB_FIELD_SUMMARY_ID."` = '".$updatedArray[$i][0]."'";

				    $query = $sqlBuilder->simpleUpdate($updateTable, $updateFields, $updateValues, $updateConditions);

				    $dbConnection->executeQuery($query);

				}

			}

			Config::setHspAccruedLastUpdated(date('Y-m-d'));

		}

	}

	/**
	 * For a given HSP ID, this function updates 'hsp_plan_status'
	 * based on current HSP status
	 */

	public static function updateStatus($hspSummaryId, $hspStatus) {

	    $dbConnection = new DMLFunctions();

	    $newHspStatus = ($hspStatus == '1') ? 0 : 1;

	    $sqlBuilder = new SQLQBuilder();

	    $updateTable = "`".self::DB_TABLE_HSP_SUMMARY."`";

	    $updateFields[0] = "`".self::DB_FIELD_HSP_PLAN_STATUS."`";
	    $updateValues[0] = "'".$newHspStatus."'";

	    $updateConditions[0] = "`".self::DB_FIELD_SUMMARY_ID."` = '".$hspSummaryId."'";

	    $query = $sqlBuilder->simpleUpdate($updateTable, $updateFields, $updateValues, $updateConditions);

	    return $dbConnection->executeQuery($query);

	}

	/**
	 * For a give year, this function updates 'total_used' for all employees
	 * based on current HSP Scheme and 'hsp_used_last_updated'
	 */

	public static function updateUsed($year) {

	    if (Config::getHspUsedLastUpdated() < date('Y-m-d')) {

			$selectTable = "`".self::DB_TABLE_HSP_SUMMARY."`";

			$selectFields[0] = "`".self::DB_FIELD_SUMMARY_ID."`";
			$selectFields[1] = "`".self::DB_FIELD_EMPLOYEE_ID."`";
			$selectFields[2] = "`".self::DB_FIELD_HSP_PLAN_ID."`";
			$selectFields[3] = "`".self::DB_FIELD_TOTAL_USED."`";

			$selectConditions[0] = "`".self::DB_FIELD_HSP_PLAN_YEAR."` = '".$year."'";
			$selectConditions[1] = self::_twoHspPlansCondition(Config::getHspCurrentPlan());

			$sqlBuilder = new SQLQBuilder();
			$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions);

			$dbConnection = new DMLFunctions();
			$result = $dbConnection->executeQuery($query);

			$rowCount = $dbConnection->dbObject->numberOfRows($result);
			$hspUsedLastUpdated = Config::getHspUsedLastUpdated();

			for ($i=0; $i<$rowCount; $i++) {

				$row = $dbConnection->dbObject->getArray($result);
			    $updatedArray[$i][0] = $row[0];
			    $updatedArray[$i][1] = $row[3]+HspPaymentRequest::calculateNewHspUsed($row[1], $row[2], $hspUsedLastUpdated);

			}

			for ($i=0; $i<count($updatedArray); $i++) {

			    $updateTable = "`".self::DB_TABLE_HSP_SUMMARY."`";
			    $updateFields[0] = "`".self::DB_FIELD_TOTAL_USED."`";
			    $updateValues[0] = "'".$updatedArray[$i][1]."'";
			    $updateConditions[0] = "`".self::DB_FIELD_SUMMARY_ID."` = '".$updatedArray[$i][0]."'";

			    $query = $sqlBuilder->simpleUpdate($updateTable, $updateFields, $updateValues, $updateConditions);

			    $dbConnection->executeQuery($query);

			}

			Config::setHspUsedLastUpdated(date('Y-m-d'));

	    }

	}

	public static function updateUsedPerPayment($year, $hspPlanId, $employeeId, $amount) {

	    $updateTable = "`".self::DB_TABLE_HSP_SUMMARY."`";
	    $updateFields[0] = "`".self::DB_FIELD_TOTAL_USED."`";
	    $updateValues[0] = "`".self::DB_FIELD_TOTAL_USED."` + ".$amount;
	    $updateConditions[0] = "`".self::DB_FIELD_HSP_PLAN_YEAR."` = '".$year."'";
	    $updateConditions[1] = "`".self::DB_FIELD_HSP_PLAN_ID."` = '".$hspPlanId."'";
	    $updateConditions[2] = "`".self::DB_FIELD_EMPLOYEE_ID."` = '".$employeeId."'";

	    $sqlBuilder = new SQLQBuilder();
	    $query = $sqlBuilder->simpleUpdate($updateTable, $updateFields, $updateValues, $updateConditions, false);

	    $dbConnection = new DMLFunctions();
	    $dbConnection->executeQuery($query);

	}

	/**
	 * Used when `hsp_plan_id` can be 4, 5 or 6
	 */

	protected static function _twoHspPlansCondition($hspPlanId) {

		if ($hspPlanId == 4) {
    		$condition = "`".self::DB_FIELD_HSP_PLAN_ID."` In ('1', '2')";
    	} elseif ($hspPlanId == 5) {
    		$condition = "`".self::DB_FIELD_HSP_PLAN_ID."` In ('2', '3')";
    	} elseif ($hspPlanId == 6) {
    		$condition = "`".self::DB_FIELD_HSP_PLAN_ID."` In ('1', '3')";
    	} else {
    		$condition = "`".self::DB_FIELD_HSP_PLAN_ID."` = '".$hspPlanId."'";
    	}

    	return $condition;

	}


}

?>
