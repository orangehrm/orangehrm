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

require_once ROOT_PATH . '/lib/models/leave/Leave.php';
require_once ROOT_PATH . '/lib/models/leave/Holidays.php';
require_once ROOT_PATH . '/lib/models/leave/Weekends.php';
require_once ROOT_PATH . '/lib/common/UniqueIDGenerator.php';

class LeaveRequests extends Leave {

	const LEAVEREQUESTS_LEAVELENGTH_RANGE = 9;
	const MAX_COMMENT_LENGTH = 256;
	const LEAVEREQUESTS_MULTIPLESTATUSES = 6;

	private $leaveFromDate;
	private $leaveToDate;

	private $noDays;
	private $commentsDiffer;

	public function setLeaveFromDate($leaveFromDate) {
		$this->leaveFromDate = trim($leaveFromDate);
	}

	public function getLeaveFromDate() {
		return $this->leaveFromDate;
	}

	public function setLeaveToDate($leaveToDate) {
		$this->leaveToDate = trim($leaveToDate);
	}

	public function getLeaveToDate() {
		return $this->leaveToDate;
	}

	public function setNoDays($noDays) {
		$this->noDays = trim($noDays);
	}

	public function getNoDays() {
		return $this->noDays;
	}

	public function setCommentsDiffer($differ) {
		$this->commentsDiffer = $differ;
	}

	public function getCommentsDiffer() {
		return $this->commentsDiffer;
	}

	public function __construct() {
		$weekendObj = new Weekends();
		$this->weekends = $weekendObj->fetchWeek();
	}

	/**
	 *	Retrieves Leave Request Details of all leave that have been applied for but
	 *	not yet taken of the employee.
	 *
	 * @return LeaveRequests[][] $leaveArr A 2D array of the leaves
	 */
	public function retriveLeaveRequestsEmployee($employeeId) {

		$this->setEmployeeId($employeeId);

		$sqlBuilder = new SQLQBuilder();

		$arrFields[0] = 'b.`leave_type_name`';
		$arrFields[1] = 'a.`leave_request_id`';

		$arrTables[0] = "`hs_hr_leave_requests` a";
		$arrTables[1] = "`hs_hr_leavetype` b";

		$joinConditions[1] = "a.`leave_type_id` = b.`leave_type_id`";

		$selectConditions[1] = "a.`employee_id` = '".$employeeId."'";

		$query = $sqlBuilder->selectFromMultipleTable($arrFields, $arrTables, $joinConditions, $selectConditions, null, $arrFields[1], 'ASC');

		$dbConnection = new DMLFunctions();

		$result = $dbConnection -> executeQuery($query);

		$leaveArr = $this->_buildObjArr($result);

		return $leaveArr;
	}

	/**
	 * Retrieve leave requests for admin user
	 *
	 * @param $filterLeaveStatus array Array of leave statuses to include. If set, only
	 *                                 leaves with these statuses are returned.
	 * @param $fromDate Date Start date to search
	 * @param $toDate Date End date to search
	 */

	public function retriveLeaveRequestsAdmin($filterLeaveStatus = null, $fromDate, $toDate, $limit) {
		
		$sqlBuilder = new SQLQBuilder();

		$arrFields[0] = 'c.`leave_type_name`';
		$arrFields[1] = 'a.`leave_request_id`';
		$arrFields[2] = 'b.`emp_firstname`';
		$arrFields[3] = 'a.`employee_id`';
		$arrFields[4] = 'b.`emp_lastname`';

		$arrTables[0] = "`hs_hr_leave_requests` a";
		$arrTables[1] = "`hs_hr_employee` b";
		$arrTables[2] = "`hs_hr_leavetype` c";
		$arrTables[3] = "`hs_hr_leave` d";

		$leaveStates = implode(',', $filterLeaveStatus);

        $selectConditions[0] = "(b.`emp_status` IS  NULL OR b.`emp_status` != 'EST000')" ;
        $selectConditions[1] = "(d. `leave_status` IN ($leaveStates))";
        $selectConditions[2] = "(d.`leave_date` >= '$fromDate' AND d.`leave_date` <= '$toDate')"; 

		$joinConditions[1] = "a.`employee_id` = b.`emp_number`";
		$joinConditions[2] = "a.`leave_type_id` = c.`leave_type_id`";
		$joinConditions[3] = "a.`leave_request_id` = d.`leave_request_id`";

		$query = $sqlBuilder->selectFromMultipleTable($arrFields, $arrTables, $joinConditions, $selectConditions, null, null, null, $limit, null, true);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		$leaveArr = $this->_buildObjArr($result, true, $filterLeaveStatus, $fromDate, $toDate);

		return $leaveArr;
		
	}
	
	public function countLeaveRequestsAdmin($filterLeaveStatus = null, $fromDate, $toDate) {
		
		$sqlBuilder = new SQLQBuilder();

		$arrFields[0] = 'COUNT(*)';

		$arrTables[0] = "`hs_hr_leave_requests` a";
		$arrTables[1] = "`hs_hr_employee` b";
		$arrTables[2] = "`hs_hr_leave` d";

		$leaveStates = implode(',', $filterLeaveStatus);

        $selectConditions[0] = "(b.`emp_status` IS  NULL OR b.`emp_status` != 'EST000')" ;
        $selectConditions[1] = "(d. `leave_status` IN ($leaveStates))";
        $selectConditions[2] = "(d.`leave_date` >= '$fromDate' AND d.`leave_date` <= '$toDate')"; 

		$joinConditions[1] = "a.`employee_id` = b.`emp_number`";
		$joinConditions[2] = "a.`leave_request_id` = d.`leave_request_id`";

		$query = $sqlBuilder->selectFromMultipleTable($arrFields, $arrTables, $joinConditions, $selectConditions);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);
		
		$row = mysql_fetch_array($result);
		
		if (!empty($row)) {
			return $row[0]; 
		} else {
			return 0;
		}
		
	}

	/**
	 * Retrieves Leave Request Details of all leave that have been applied for but
	 * not yet taken by all supervisor's subordinates.
	 *
	 * @return LeaveRequests[][] $leaveArr A 2D array of the leaves
	 */
	public function retriveLeaveRequestsSupervisor($supervisorId,$leaveStatuses, $fromDate, $toDate, $limit) {

		$sqlBuilder = new SQLQBuilder();

		$arrFields[0] = 'e.`leave_type_name`';
		$arrFields[1] = 'a.`leave_request_id`';
		$arrFields[2] = 'd.`emp_firstname`';
		$arrFields[3] = 'a.`employee_id`';
		$arrFields[4] = 'd.`emp_lastname`';

		$arrTables[0] = "`hs_hr_leave_requests` a";
		$arrTables[1] = "`hs_hr_emp_reportto` c";
		$arrTables[2] = "`hs_hr_employee` d";
		$arrTables[3] = "`hs_hr_leavetype` e";
		$arrTables[4] = "`hs_hr_leave` f";

		$joinConditions[1] = "(a.`employee_id` = c.`erep_sub_emp_number`)";
		$joinConditions[2] = "(a.`employee_id` = d.`emp_number`)";
		$joinConditions[3] = "(a.`leave_type_id` = e.`leave_type_id`)";
		$joinConditions[4] = "(a.`leave_request_id` = f.`leave_request_id`)";

		$leaveStates = implode(',', $leaveStatuses);
		
		$selectConditions[0] = "c.`erep_sup_emp_number` = '".$supervisorId."'";
        $selectConditions[1] = "(f. `leave_status` IN ($leaveStates))";
        $selectConditions[2] = "(f.`leave_date` >= '$fromDate' AND f.`leave_date` <= '$toDate')"; 

		$query = $sqlBuilder->selectFromMultipleTable($arrFields, $arrTables, $joinConditions, $selectConditions, null, null, null, $limit, null, true);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection -> executeQuery($query);
		
		$leaveArr = $this->_buildObjArr($result, true, $leaveStatuses,$fromDate,$toDate);

		return $leaveArr;

	}
	
	public function countLeaveRequestsSupervisor($supervisorId,$leaveStatuses, $fromDate, $toDate) {

		$sqlBuilder = new SQLQBuilder();

		$arrFields[0] = 'COUNT(*)';

		$arrTables[0] = "`hs_hr_leave_requests` a";
		$arrTables[1] = "`hs_hr_emp_reportto` c";
		$arrTables[2] = "`hs_hr_employee` d";
		$arrTables[3] = "`hs_hr_leave` f";

		$joinConditions[1] = "(a.`employee_id` = c.`erep_sub_emp_number`)";
		$joinConditions[2] = "(a.`employee_id` = d.`emp_number`)";
		$joinConditions[3] = "(a.`leave_request_id` = f.`leave_request_id`)";

		$leaveStates = implode(',', $leaveStatuses);
		
		$selectConditions[0] = "c.`erep_sup_emp_number` = '".$supervisorId."'";
        $selectConditions[1] = "(f. `leave_status` IN ($leaveStates))";
        $selectConditions[2] = "(f.`leave_date` >= '$fromDate' AND f.`leave_date` <= '$toDate')"; 

		$query = $sqlBuilder->selectFromMultipleTable($arrFields, $arrTables, $joinConditions, $selectConditions);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection -> executeQuery($query);
		
		$row = mysql_fetch_array($result);
		
		if (!empty($row)) {
			return $row[0]; 
		} else {
			return 0;
		}
		
	}

/**
 * This retrives alreadyt taken leaves.
 */

	public function retriveLeaveTaken() {

		$sqlBuilder = new SQLQBuilder();

		$arrFields[0] = 'a.`leave_id`';
		$arrFields[1] = 'a.`leave_date`';
		$arrFields[2] = 'b.`emp_firstname`';
		$arrFields[3] = 'b.`emp_lastname`';
		$arrFields[4] = 'a.`leave_length_hours`';
		$arrFields[5] = 'a.`leave_comments`';
		$arrFields[6] = 'a.`leave_type_id`';
		$arrFields[7] = 'a.`employee_id`';

		$arrTables[0] = "`hs_hr_leave` a";
		$arrTables[1] = "`hs_hr_employee` b";

		$joinConditions[1] = "a.`employee_id` = b.`employee_id`";

		$selectConditions[1] = "a.`leave_status` = '3'";

		$query = $sqlBuilder->selectFromMultipleTable($arrFields, $arrTables, $joinConditions, $selectConditions);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection -> executeQuery($query);

		$leaveArr = $this->_buildObjArr($result, true);

		return $leaveArr;
	}

	/**
	 * Calculates required length of leave.
	 *
	 * @param integer $length - leave lenth
	 * @param integer $timeOff - time off for that day
	 * @return integer $reqiredLength - length of leave required.
	 */
	protected function _leaveLength($length, $timeOff) {
		$factor = 1;
		if ($length < 0) {
			$factor = 1;
		}

		$length = abs($length);
		if ($timeOff > $length) {
			return 0;
		}
		$requiredLength = $length-$timeOff;

		return $requiredLength*$factor;
	}

	public function cancelLeave($id = null) {
		return $this->changeLeaveStatus($id);
	}

	public function changeLeaveStatus($id = null, $adminApproval = false) {
		if (isset($id)) {
			$this->setLeaveRequestId($id);
		}

		$newStatus = $this->getLeaveStatus();
        $comments = $this->getLeaveComments();

		$tmpLeave = new Leave();
		$tmpLeaveArr = $tmpLeave->retrieveLeave($this->getLeaveRequestId());

		$ok = true;

		if(! is_null($tmpLeaveArr)){
			foreach ($tmpLeaveArr as $leave) {
				if (!$adminApproval || !($leave->getLeaveStatus() == Leave::LEAVE_STATUS_LEAVE_HOLIDAY || $leave->getLeaveStatus() == Leave::LEAVE_STATUS_LEAVE_WEEKEND)){
					$leave->setLeaveStatus($newStatus);
                    $leave->setLeaveComments($comments);
					$res = $leave->changeLeaveStatus();
					if (!$res) {
						$ok = false;
					}
				}
			}
		}
		return $ok;

	}

	/**
	 * @param $filterLeaveStatus array Array of leave statuses to include. If set, only
	 *                                 leaves with these statuses are returned.
	 * @param $fromDate Date Start date to search
	 * @param $toDate Date End date to search
	 */
	protected function _buildObjArr($result, $supervisor=false, $filterLeaveStatus = null, $fromDate = null, $toDate = null) {

		$objArr = null;

		while ($row = mysql_fetch_row($result)) {

			$tmpLeaveRequestArr = new LeaveRequests();

			$tmpLeaveRequestArr->setLeaveTypeName($row[0]);
			$tmpLeaveRequestArr->setLeaveRequestId($row[1]);

			$tmpLeave = new Leave();

			$tmpLeaveArr = $tmpLeave->retrieveLeave($row[1]);

			$noOfDays = 0;
			$hours = 0;

			if (isset($tmpLeaveArr) && !empty($tmpLeaveArr)) {

				$totalLeaves = count($tmpLeaveArr);

				$tmpLeaveRequestArr->setLeaveFromDate($tmpLeaveArr[0]->getLeaveDate());

				if (isset($filterLeaveStatus)) {
					if (in_array($tmpLeaveArr[0]->getLeaveStatus(), $filterLeaveStatus)) {
						$noOfDays = $tmpLeaveArr[0]->getLeaveLengthDays();
						$hours = $tmpLeaveArr[0]->getLeaveLengthHours();
					}
				} else {
				    $noOfDays = $tmpLeaveArr[0]->getLeaveLengthDays();
				    $hours = $tmpLeaveArr[0]->getLeaveLengthHours();
				}

				if (($tmpLeaveArr[0]->getStartTime() != null) && ($tmpLeaveArr[0]->getEndTime() != null)) {
					$tmpLeaveRequestArr->setStartTime($tmpLeaveArr[0]->getStartTime());
					$tmpLeaveRequestArr->setEndTime($tmpLeaveArr[0]->getEndTime());
				}

				if ($totalLeaves > 1) {
					$tmpLeaveRequestArr->setLeaveToDate($tmpLeaveArr[$totalLeaves-1]->getLeaveDate());

					$status = $tmpLeaveArr[0]->getLeaveStatus();
					$comments = $tmpLeaveArr[0]->getLeaveComments();
					$commentsDiffer = false;

					for ($i=1; $i<$totalLeaves; $i++) {

						if ($tmpLeaveArr[$i]->getLeaveLengthHours() > 0) {

							if (isset($filterLeaveStatus)) {
								if (in_array($tmpLeaveArr[$i]->getLeaveStatus(), $filterLeaveStatus)) {
									$noOfDays += $tmpLeaveArr[$i]->getLeaveLengthDays();
									$hours += $tmpLeaveArr[$i]->getLeaveLengthHours();
								}
							} else {
							    $noOfDays += $tmpLeaveArr[$i]->getLeaveLengthDays();
							    $hours += $tmpLeaveArr[$i]->getLeaveLengthHours();
							}

							if ($status != $tmpLeaveArr[$i]->getLeaveStatus()) {
								$status = self::LEAVEREQUESTS_MULTIPLESTATUSES;
							}

						}

						if ($comments != $tmpLeaveArr[$i]->getLeaveComments()) {
							$commentsDiffer = true;
						}
					}

					$tmpLeaveRequestArr->setLeaveComments($comments);
					$tmpLeaveRequestArr->setCommentsDiffer($commentsDiffer);

					$tmpLeaveRequestArr->setLeaveStatus($status);
				} else {

					$tmpLeaveRequestArr->setLeaveStatus($tmpLeaveArr[0]->getLeaveStatus());
					$tmpLeaveRequestArr->setLeaveComments($tmpLeaveArr[0]->getLeaveComments());
				}

				$tmpLeaveRequestArr->setNoDays(number_format($noOfDays,2));
				$tmpLeaveRequestArr->setLeaveLengthHours(number_format($hours,2));


				/* Check that at least one leave in the list contains a status in
				 * $filterLeaveStatus.
				 */
				$skip = false;

				if (isset($filterLeaveStatus)) {
					$skip = true;
					for ($i=0; $i<$totalLeaves; $i++) {
						if (in_array($tmpLeaveArr[$i]->getLeaveStatus(), $filterLeaveStatus)) {
							$skip = false;
							break;
						}
					}
				} else if ($supervisor &&  $tmpLeaveRequestArr->getLeaveStatus() == self::LEAVE_STATUS_LEAVE_TAKEN) {
					$skip = true;
				}


				// Find the leave requets for the given data range
				if (isset ($toDate) && !$skip) {

					$endDate = $tmpLeaveRequestArr->getLeaveToDate();
					if (empty ($endDate)) {
						$endDate = $tmpLeaveRequestArr->getLeaveFromDate();
					} else {
						$endDate = $tmpLeaveRequestArr->getLeaveToDate();
					}

					if (strtotime($endDate) >= strtotime($fromDate) && strtotime($tmpLeaveRequestArr->getLeaveFromDate()) <= strtotime($toDate)) {
						$skip = false;
					} else {
						$skip = true;
					}
				}


				if (!$skip) {
					if ($supervisor) {
						$tmpLeaveRequestArr->setEmployeeName("{$row[2]} {$row[4]}");
						$tmpLeaveRequestArr->setEmployeeId($row[3]);
					}
					$objArr[] = $tmpLeaveRequestArr;
				}
			}
		}

		return $objArr;
	}

	/**
	 * Apply leave for multiple days
	 *
	 */
	public function applyLeaveRequest() {
		$res = $this->_addLeaveRequest();
		$resQuta  = $this->_addLeaveQuota();

		if ($res && $resQuta) {
			$res = $this->_applyLeaves();
		}
		return $res;
	}

	/**
	 * Apply leave Quota multiple Years
	 *
	 */

	private function _addLeaveQuota(){


                $fromYearArray  = explode("-" , $this->getLeaveFromDate()) ;
                $toYearArray    = explode("-" , $this->getLeaveToDate()) ;

                if(trim($fromYearArray[0])  == trim($toYearArray[0])){


                        $leaveQuata = new LeaveQuota() ;
                        $leaveQuata->setEmployeeId($this->getEmployeeId());
                        $leaveQuata->setLeaveTypeId($this->getLeaveTypeId());
                        $leaveQuata->setNoOfDaysAllotted(0);
                        $leaveQuata->setYear(trim($fromYearArray[0]));
                        if($leaveQuata->addLeaveQuotaAdmin()) return true ;
                        else return false ;



                }else{

                        $leaveQuata = new LeaveQuota() ;
                        $leaveQuata->setEmployeeId($this->getEmployeeId());
                        $leaveQuata->setLeaveTypeId($this->getLeaveTypeId());
                        $leaveQuata->setNoOfDaysAllotted(0);


                        $leaveQuata->setYear(trim($fromYearArray[0]));
                        $quotaFrom	= $leaveQuata->addLeaveQuotaAdmin();


                        $leaveQuata->setYear(trim($toYearArray[0]));
                        $quotaTo	= $leaveQuata->addLeaveQuotaAdmin();


                        if(($quotaFrom) && ($quotaTo)) return true ;
                        else return false ;


                }

		}


	/**
	 * Does actual leave applying
	 *
	 */
	private function _applyLeaves() {
		$from = strtotime($this->getLeaveFromDate());
		$to = strtotime($this->getLeaveToDate());

		$res = true;
		$days = $this->getLeaveLengthDays();
		$hours = $this->getLeaveLengthHours();
		for ($timeStamp=$from; $timeStamp<=$to; $timeStamp=$this->_incDate($timeStamp)) {
			$this->setLeaveDate(date('Y-m-d', $timeStamp));
			$this->setLeaveLengthDays($days);
			$this->setLeaveLengthHours($hours);
			$res = $res && $this->_addLeave();
		}

		return $res;
	}

	/**
	 * Date increment
	 *
	 * @param int $timestamp
	 */
	private function _incDate($timestamp) {

		return strtotime("+1 day", $timestamp);

	}

	/**
	 * Adds Record to Leave Request
	 *
	 * @access private
	 */
	private function _addLeaveRequest() {

		$newId = UniqueIDGenerator::getInstance()->getNextID('hs_hr_leave_requests', 'leave_request_id');
		$this->setLeaveRequestId($newId);

		$this->_getLeaveTypeName();
		$this->setDateApplied(date('Y-m-d'));

		$arrRecordsList[0] = $this->getLeaveRequestId();
		$arrRecordsList[1] = "'".$this->getLeaveTypeId()."'";
		$arrRecordsList[2] = "'".$this->getLeaveTypeName()."'";
		$arrRecordsList[3] = "'". $this->getDateApplied()."'";
		$arrRecordsList[4] = "'". $this->getEmployeeId() . "'";

		$arrTable = "`hs_hr_leave_requests`";

		$sqlBuilder = new SQLQBuilder();

		$query = $sqlBuilder->simpleInsert($arrTable, $arrRecordsList);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection -> executeQuery($query);

		return $result;

	}

	/**
	 *
	 * function _changeLeaveStatus, access is private, will not be documented
	 *
	 * @access private
	 */
	protected function _changeLeaveStatus() {

		$sqlBuilder = new SQLQBuilder();

		$table = "`hs_hr_leave`";

		$changeFields[0] = "`leave_status`";
		$changeFields[1] = "`leave_comments`";

		$changeValues[0] = $this->getLeaveStatus();
		$changeValues[1] = "'".$this->getLeaveComments()."'";

		$updateConditions[0] = "`leave_request_id` = ".$this->getLeaveRequestId();

		$query = $sqlBuilder->simpleUpdate($table, $changeFields, $changeValues, $updateConditions);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		if (isset($result) && (mysql_affected_rows() > 0)) {
			return true;
		};

		return false;
	}

}
?>
