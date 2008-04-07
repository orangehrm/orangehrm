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

require_once ROOT_PATH.'/lib/models/hrfunct/EmpInfo.php';
require_once ROOT_PATH.'/lib/models/benefits/DefineHsp.php';
require_once 'Hsp.php';

class HspSummary extends Hsp {

	const DB_TABLE_EMPLOYEE = "hs_hr_employee";
	const DB_FIELD_EMP_NUMBER = "emp_number";

	/**
	 * This function queries database for $year. If records exist then it would output those records.
	 * Otherwise it would output an empty record set using $empId and $hspPlanId.
	 * If records exist it first calls "updateAccrued" and "updateUsed" functions so that fetched data
	 * would be fresh.
	 */

    public static function fetchHspSummary($year, $page=1, $empId=null) {

		parent::updateAccrued($year); // Update 'total_accrued' for $year
		//parent::updateUsed($year); // Update 'total_used' for $year

		$selectTable = parent::DB_TABLE_HSP_SUMMARY;
		$selectFields[0] = "*";
		$selectConditions[0] = "`".parent::DB_FIELD_HSP_PLAN_YEAR."` = ".$year;
		$hspPlanId = Config::getHspCurrentPlan();
		$selectConditions[1] = parent::_twoHspPlansCondition($hspPlanId);
		if (isset($empId)) {
		    $selectConditions[2] = "`".parent::DB_FIELD_EMPLOYEE_ID."` = ".$empId;
		}
		$selectOrderBy = "`".parent::DB_FIELD_EMPLOYEE_ID."` , `".parent::DB_FIELD_HSP_PLAN_ID."`";
		$selectOrder = "ASC";
		$selectLimit = ($page*50-50).",".(50);

		$sqlBuilder = new SQLQBuilder();
		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions, $selectOrderBy, $selectOrder, $selectLimit);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($query);

		$hsbObjArr = self::_buildSummaryObjects($result);

		return $hsbObjArr;

   }

    /**
     * This function stores initial data for a given year
     */

	public static function saveInitialSummary($year, $hspPlanId) {

		$selectTable = "`".self::DB_TABLE_EMPLOYEE."`";
		$selectFields[0] = "`".self::DB_FIELD_EMP_NUMBER."`";

		$sqlBuilder = new SQLQBuilder();
		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($query);

		if ($dbConnection->dbObject->numberOfRows($result) < 1) {
		    throw new Exception("No employee has been defiened");
		}

		for ($i=0; $i<$dbConnection->dbObject->numberOfRows($result); $i++) {

			$nextID = UniqueIDGenerator::getInstance()->getNextID(parent::DB_TABLE_HSP_SUMMARY, parent::DB_FIELD_SUMMARY_ID);

			$row = $dbConnection->dbObject->getArray($result);

			$insertTable = "`".parent::DB_TABLE_HSP_SUMMARY."`";

			$insertFields[0] = "`".parent::DB_FIELD_SUMMARY_ID."`";
			$insertFields[1] = "`".parent::DB_FIELD_EMPLOYEE_ID."`";
			$insertFields[2] = "`".parent::DB_FIELD_HSP_PLAN_ID."`";
			$insertFields[3] = "`".parent::DB_FIELD_HSP_PLAN_YEAR."`";
			$insertFields[4] = "`".parent::DB_FIELD_HSP_PLAN_STATUS."`";
			$insertFields[5] = "`".parent::DB_FIELD_ANNUAL_LIMIT."`";
			$insertFields[6] = "`".parent::DB_FIELD_EMPLOYER_AMOUNT."`";
			$insertFields[7] = "`".parent::DB_FIELD_EMPLOYEE_AMOUNT."`";
			$insertFields[8] = "`".parent::DB_FIELD_TOTAL_ACCRUED."`";
			$insertFields[9] = "`".parent::DB_FIELD_TOTAL_USED."`";

			$insertValues[0] = $nextID;
			$insertValues[1] = $row[0];
			$insertValues[2] = $hspPlanId;
			$insertValues[3] = $year;
			$insertValues[4] = 1;
			$insertValues[5] = 0;
			$insertValues[6] = 0;
			$insertValues[7] = 0;
			$insertValues[8] = 0;
			$insertValues[9] = 0;

			$query = $sqlBuilder->simpleInsert($insertTable, $insertValues, $insertFields);

			$dbConnection->executeQuery($query);

		}

	}

	public static function saveInitialSummaryForOneEmployee($year, $hspPlanId, $empId) {

		switch ($hspPlanId) {

		    case 4:
		    	$hspPlanId = 1;
		    	$hspPlanId2 = 3;
		    	break;

		    case 5:
		    	$hspPlanId = 2;
		    	$hspPlanId2 = 3;
		    	break;

		    case 6:
		    	$hspPlanId = 1;
		    	$hspPlanId2 = 2;
		    	break;

		}

		$insertTable = "`".parent::DB_TABLE_HSP_SUMMARY."`";

		$insertFields[0] = "`".parent::DB_FIELD_SUMMARY_ID."`";
		$insertFields[1] = "`".parent::DB_FIELD_EMPLOYEE_ID."`";
		$insertFields[2] = "`".parent::DB_FIELD_HSP_PLAN_ID."`";
		$insertFields[3] = "`".parent::DB_FIELD_HSP_PLAN_YEAR."`";
		$insertFields[4] = "`".parent::DB_FIELD_HSP_PLAN_STATUS."`";
		$insertFields[5] = "`".parent::DB_FIELD_ANNUAL_LIMIT."`";
		$insertFields[6] = "`".parent::DB_FIELD_EMPLOYER_AMOUNT."`";
		$insertFields[7] = "`".parent::DB_FIELD_EMPLOYEE_AMOUNT."`";
		$insertFields[8] = "`".parent::DB_FIELD_TOTAL_ACCRUED."`";
		$insertFields[9] = "`".parent::DB_FIELD_TOTAL_USED."`";

		$insertValues[0] = UniqueIDGenerator::getInstance()->getNextID(parent::DB_TABLE_HSP_SUMMARY, parent::DB_FIELD_SUMMARY_ID);
		$insertValues[1] = $empId;
		$insertValues[2] = $hspPlanId;
		$insertValues[3] = $year;
		$insertValues[4] = 1;
		$insertValues[5] = 0;
		$insertValues[6] = 0;
		$insertValues[7] = 0;
		$insertValues[8] = 0;
		$insertValues[9] = 0;

		$sqlBuilder = new SQLQBuilder();
		$query = $sqlBuilder->simpleInsert($insertTable, $insertValues, $insertFields);

		$dbConnection = new DMLFunctions();
		$dbConnection->executeQuery($query);

		if (isset($hspPlanId2)) {

		    $insertValues[0] = UniqueIDGenerator::getInstance()->getNextID(parent::DB_TABLE_HSP_SUMMARY, parent::DB_FIELD_SUMMARY_ID);
		    $insertValues[2] = $hspPlanId2;

		    $query = $sqlBuilder->simpleInsert($insertTable, $insertValues, $insertFields);
		    $dbConnection->executeQuery($query);

		}

	}

	/**
	 * This function saves edited HSP data
	 */

	public static function saveHspSummary($summaryObjArr) {

		for ($i=0; $i<count($summaryObjArr); $i++) {

			$updateTable = "`".self::DB_TABLE_HSP_SUMMARY."`";

			$updateFields[0] = "`".parent::DB_FIELD_ANNUAL_LIMIT."`";
			$updateFields[1] = "`".parent::DB_FIELD_EMPLOYER_AMOUNT."`";
			$updateFields[2] = "`".parent::DB_FIELD_EMPLOYEE_AMOUNT."`";
			$updateFields[3] = "`".parent::DB_FIELD_TOTAL_ACCRUED."`";
			$updateFields[4] = "`".parent::DB_FIELD_TOTAL_USED."`";

			$updateValues[0] = $summaryObjArr[$i]->getAnnualLimit();
			$updateValues[1] = $summaryObjArr[$i]->getEmployerAmount();
			$updateValues[2] = $summaryObjArr[$i]->getEmployeeAmount();
			$updateValues[3] = $summaryObjArr[$i]->getTotalAccrued();
			$updateValues[4] = $summaryObjArr[$i]->getTotalUsed();

			$updateConditions[0] = "`".parent::DB_FIELD_SUMMARY_ID."` = '".$summaryObjArr[$i]->getSummaryId()."'";

			$sqlBuilder = new SQLQBuilder();
			$query = $sqlBuilder->simpleUpdate($updateTable, $updateFields, $updateValues, $updateConditions);

			$dbConnection = new DMLFunctions();
			if (!$dbConnection->executeQuery($query)) {
				$error = false;
			}

		}

		if (isset($error)) {
			return false;
		} else {
			return true;
		}

	}


    /**
     * Check whether the data exists for a give year
     */

    public static function recordsExist($year, $hspPlanId=null) {

    	$selectTable = "`".parent::DB_TABLE_HSP_SUMMARY."`";
    	$selectFields[0] = "`".parent::DB_FIELD_HSP_PLAN_YEAR."`";
    	$selectConditions[0] = "`".parent::DB_FIELD_HSP_PLAN_YEAR."` = '".$year."'";
    	if (isset($hspPlanId)) {
    		$selectConditions[1] = "`".parent::DB_FIELD_HSP_PLAN_ID."` = '".$hspPlanId."'";
    	}

    	$sqlBuilder = new SQLQBuilder();
    	$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions);

    	$dbConnection = new DMLFunctions();
    	$result = $dbConnection->executeQuery($query);

    	if ($dbConnection->dbObject->numberOfRows($result) > 0) {
    	    return true;
    	} else {
    	    return false;
    	}

    }

    /**
     * This functions returns the number of records that maches given conditions.
     */

	public static function recordsCount($year, $hspPlanId) {

    	$selectTable = "`".parent::DB_TABLE_HSP_SUMMARY."`";
    	$selectFields[0] = "COUNT(summary_id)";
    	$selectConditions[0] = "`".parent::DB_FIELD_HSP_PLAN_YEAR."` = '".$year."'";
		$selectConditions[1] = parent::_twoHspPlansCondition($hspPlanId);

    	$sqlBuilder = new SQLQBuilder();
    	$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions, null, null, null, true);

    	$dbConnection = new DMLFunctions();
    	$result = $dbConnection->executeQuery($query);
    	$row = $dbConnection->dbObject->getArray($result);

    	return $row[0];

	}

    /**
     * This function get a database resource as the input and creates a HSP objects array
     * containing the data of the resource.
     */

    private static function _buildSummaryObjects($result) {

        $dbConnection = new DMLFunctions();
        $hspObjArr = null;

        while ($row = $dbConnection->dbObject->getArray($result)) {

            $hspObj = new Hsp();

            $hspObj->setSummaryId($row[0]);
            $hspObj->setEmployeeId($row[1]);
            $hspObj->setHspPlanId($row[2]);
            $hspObj->setHspPlanName(DefineHsp::getHspPlanName($row[2]));
            $hspObj->setEmployeeName(EmpInfo::getFullName($row[1]));
            $hspObj->setHspPlanYear($row[3]);
            $hspObj->setHspPlanStatus($row[4]);
            $hspObj->setAnnualLimit($row[5]);
            $hspObj->setEmployerAmount($row[6]);
            $hspObj->setEmployeeAmount($row[7]);
            $hspObj->setTotalAccrued($row[8]);
            $hspObj->setTotalUsed($row[9]);

            $hspObjArr[] = $hspObj;

        }

        return $hspObjArr;

	}

	public function isHspValueChangedByAdmin($existing) {
		$isChanged = false;

                $msg = 'HR Admin Changing HSP Value (Emp ID - '. $existing->getEmployeeId() .', Summary ID -'.$existing->getSummaryId().')';
		

                if($this->getAnnualLimit() != $existing->getAnnualLimit()) {
                        $isChanged = true;
                        $msg = $msg . "\r\n[Annual Limit] " . $existing->getAnnualLimit() . " -> " . $this->getAnnualLimit();
                }

                if($this->getEmployerAmount() != $existing->getEmployerAmount()) {
                        $isChanged = true;
                        $msg = $msg . "\r\n[Employer Amount] " . $existing->getEmployerAmount() . " -> " . $this->getEmployerAmount();
                }

                if($this->getEmployeeAmount() != $existing->getEmployeeAmount()) {
                        $isChanged = true;
                        $msg = $msg . "\r\n[Employee Amount] " . $existing->getEmployeeAmount() . " -> " . $this->getEmployeeAmount();
                }

                if($this->getTotalAccrued() != $existing->getTotalAccrued()) {
                        $isChanged = true;
                        $msg = $msg . "\r\n[Total Accrued] " . $existing->getTotalAccrued() . " -> " . $this->getTotalAccrued();
                }

                if($this->getTotalUsed() != $existing->getTotalUsed()) {
                        $isChanged = true;
                        $msg = $msg . "\r\n[Total Used] " . $existing->getTotalUsed() . " -> " . $this->getTotalUsed();
                }

                if (!$isChanged) {
                        return $isChanged;
                }else {
                        return $msg;
                }

	}

}



?>
