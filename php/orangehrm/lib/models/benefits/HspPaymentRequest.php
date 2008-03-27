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
require_once ROOT_PATH . '/lib/logs/LogFileWriter.php';

class HspPaymentRequest {

	const HSP_PAYMENT_REQUEST_DB_TABLE = 'hs_hr_hsp_payment_request';
	const DB_FIELD_ID = 'id';
	const DB_FIELD_HSP_ID = 'hsp_id';
	const DB_FIELD_EMPLOYEE_ID = 'employee_id';
	const DB_FIELD_DATE_INCURRED = 'date_incurred';
	const DB_FIELD_PROVIDER_NAME = 'provider_name';
	const DB_FIELD_PERSON_INCURRING_EXPENSE = 'person_incurring_expense';
	const DB_FIELD_EXPENSE_DESCRIPTION = 'expense_description';
	const DB_FIELD_EXPENSE_AMOUNT = 'expense_amount';
	const DB_FIELD_PAYMENT_MADE_TO = 'payment_made_to';
	const DB_FIELD_THIRD_PARTY_ACCOUNT_NUMBER = 'third_party_account_number';
	const DB_FIELD_MAIL_ADDRESS = 'mail_address';
	const DB_FIELD_COMMENTS = 'comments';
	const DB_FIELD_DATE_PAID = 'date_paid';
	const DB_FIELD_CHECK_NUMBER = 'check_number';
	const DB_FIELD_STATUS = 'status';
	const DB_FIELD_HR_NOTES = 'hr_notes';

	const EMP_CHILDREN_DB_TABLE = 'hs_hr_emp_children';
	const DB_FIELD_EMP_NUMBER = 'emp_number';
	const DB_FIELD_EMP_CHILDREN_NAME ='ec_name';
	const EMP_DEPENDENT_DB_TABLE = 'hs_hr_emp_dependents';
	const DB_FIELD_EMP_DEPENDENT_NAME = 'ed_name';

	const HSP_PAYMENT_REQUEST_STATUS_SUBMITTED = 0;
	const HSP_PAYMENT_REQUEST_STATUS_PAID = 1;
	const HSP_PAYMENT_REQUEST_STATUS_DENIED = 2;
	const HSP_PAYMENT_REQUEST_STATUS_DELETED = 3;

	private $id;
	private $hspId;
	private $employeeId;
	private $dateIncurred;
	private $providerName;
	private $personIncurringExpense;
	private $expenseDescription;
	private $expenseAmount;
	private $paymentMadeTo;
	private $thirdPartyAccountNumber;
	private $mailAddress;
	private $comments;
	private $datePaid;
	private $checkNumber;
	private $status;
	private $hrNotes;
	private $paperWorkSubmitted;

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id=$id;
	}

	public function getHspId() {
		return $this->hspId;
	}

	public function setHspId($hspId) {
		$this->hspId=$hspId;
	}

	public function getEmployeeId() {
		return $this->employeeId;
	}

	public function setEmployeeId($employeeId) {
		$this->employeeId=$employeeId;
	}

	public function getDateIncurred() {
		return $this->dateIncurred;
	}

	public function setDateIncurred($dateIncurred) {
		$this->dateIncurred=$dateIncurred;
	}

	public function getProviderName() {
		return $this->providerName;
	}

	public function setProviderName($providerName) {
		$this->providerName=$providerName;
	}

	public function getPersonIncurringExpense() {
		return $this->personIncurringExpense;
	}

	public function setPersonIncurringExpense($personIncurringExpense) {
		$this->personIncurringExpense=$personIncurringExpense;
	}

	public function getExpenseDescription() {
		return $this->expenseDescription;
	}

	public function setExpenseDescription($expenseDescription) {
		$this->expenseDescription=$expenseDescription;
	}

	public function getExpenseAmount() {
		return $this->expenseAmount;
	}

	public function setExpenseAmount($expenseAmount) {
		$this->expenseAmount=$expenseAmount;
	}

	public function getPaymentMadeTo() {
		return $this->paymentMadeTo;
	}

	public function setPaymentMadeTo($paymentMadeTo) {
		$this->paymentMadeTo=$paymentMadeTo;
	}

	public function getThirdPartyAccountNumber() {
		return $this->thirdPartyAccountNumber;
	}

	public function setThirdPartyAccountNumber($thirdPartyAccountNumber) {
		$this->thirdPartyAccountNumber=$thirdPartyAccountNumber;
	}

	public function getMailAddress() {
		return $this->mailAddress;
	}

	public function setMailAddress($mailAddress) {
		$this->mailAddress=$mailAddress;
	}

	public function getComments() {
		return $this->comments;
	}

	public function setComments($comments) {
		$this->comments=$comments;
	}

	public function getDatePaid() {
		return $this->datePaid;
	}

	public function setDatePaid($datePaid) {
		$this->datePaid=$datePaid;
	}

	public function getCheckNumber() {
		return $this->checkNumber;
	}

	public function setCheckNumber($checkNumber) {
		$this->checkNumber=$checkNumber;
	}

	public function getStatus() {
		return $this->status;
	}

	public function setStatus($status) {
		$this->status=$status;
	}

	public function getHrNotes() {
		return $this->hrNotes;
	}

	public function setHrNotes($hrNotes) {
		$this->hrNotes=$hrNotes;
	}

	public function getPaperWorkSubmitted() {
		return $this->paperWorkSubmitted;
	}

	public function setPaperWorkSubmitted($paperWorkSubmitted) {
		$this->paperWorkSubmitted=$paperWorkSubmitted;
	}

	public static function getHspRequest($id) {
		if (!CommonFunctions::isValidId($id)) {
			throw new HspPaymentRequest("Invalid id", HspPaymentRequest::INVALID_ID);
		}

		$sqlBuilder = new SQLQBuilder();

		$selectTable = "`".self::HSP_PAYMENT_REQUEST_DB_TABLE."`";

		$selectFields[] = "`".self::DB_FIELD_ID."`";
		$selectFields[] = "`".self::DB_FIELD_HSP_ID."`";
		$selectFields[] = "`".self::DB_FIELD_EMPLOYEE_ID."`";
		$selectFields[] = "`".self::DB_FIELD_DATE_INCURRED."`";
		$selectFields[] = "`".self::DB_FIELD_PROVIDER_NAME."`";
		$selectFields[] = "`".self::DB_FIELD_PERSON_INCURRING_EXPENSE."`";
		$selectFields[] = "`".self::DB_FIELD_EXPENSE_DESCRIPTION."`";
		$selectFields[] = "`".self::DB_FIELD_EXPENSE_AMOUNT."`";
		$selectFields[] = "`".self::DB_FIELD_PAYMENT_MADE_TO."`";
		$selectFields[] = "`".self::DB_FIELD_THIRD_PARTY_ACCOUNT_NUMBER."`";
		$selectFields[] = "`".self::DB_FIELD_MAIL_ADDRESS."`";
		$selectFields[] = "`".self::DB_FIELD_COMMENTS."`";
		$selectFields[] = "`".self::DB_FIELD_DATE_PAID."`";
		$selectFields[] = "`".self::DB_FIELD_CHECK_NUMBER."`";
		$selectFields[] = "`".self::DB_FIELD_STATUS."`";
		$selectFields[] = "`".self::DB_FIELD_HR_NOTES."`";

		$selectOrder = "ASC";
		$selectOrderBy = "`".self::DB_FIELD_ID."`";

		$selectConditions[] = "`".self::DB_FIELD_ID."` = $id";

		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions, $selectOrderBy, $selectOrder);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		$requests = self::_buildObjArr($result);

		if (count($requests) == 1) {
			return $requests[0];
		} if (count($requests) > 1) {
			throw new HspPaymentRequest(HspPaymentRequestException::INVALID_ROW_COUNT, 'Got more than one row. Primary key should be unique');
		} else {
			return null;
		}
	}

	public function addHspRequest() {

		$this->id = UniqueIDGenerator::getInstance()->getNextID(self::HSP_PAYMENT_REQUEST_DB_TABLE, self::DB_FIELD_ID);

		$arrTable = '`'.self::HSP_PAYMENT_REQUEST_DB_TABLE.'`';

		$insertFields[] = '`'.self::DB_FIELD_ID.'`';
		$insertFields[] = '`'.self::DB_FIELD_HSP_ID.'`';
		$insertFields[] = '`'.self::DB_FIELD_EMPLOYEE_ID.'`';
		$insertFields[] = '`'.self::DB_FIELD_DATE_INCURRED.'`';
		$insertFields[] = '`'.self::DB_FIELD_PROVIDER_NAME.'`';
		$insertFields[] = '`'.self::DB_FIELD_PERSON_INCURRING_EXPENSE.'`';
		$insertFields[] = '`'.self::DB_FIELD_EXPENSE_DESCRIPTION.'`';
		$insertFields[] = '`'.self::DB_FIELD_EXPENSE_AMOUNT.'`';
		$insertFields[] = '`'.self::DB_FIELD_PAYMENT_MADE_TO.'`';
		$insertFields[] = '`'.self::DB_FIELD_THIRD_PARTY_ACCOUNT_NUMBER.'`';
		$insertFields[] = '`'.self::DB_FIELD_MAIL_ADDRESS.'`';
		$insertFields[] = '`'.self::DB_FIELD_COMMENTS.'`';
		$insertFields[] = '`'.self::DB_FIELD_STATUS.'`';

		$arrRecordsList[] = $this->id;
		$arrRecordsList[] = "'". $this->hspId."'";
		$arrRecordsList[] = "'". $this->employeeId."'";
		$arrRecordsList[] = 1;
		$arrRecordsList[] = "'".$this->dateIncurred."'";
		$arrRecordsList[] = "'".$this->providerName."'";
		$arrRecordsList[] = "'". $this->personIncurringExpense."'";
		$arrRecordsList[] = "'". $this->expenseDescription."'";
		$arrRecordsList[] = "'". $this->expenseAmount."'";
		$arrRecordsList[] = "'".$this->paymentMadeTo."'";
		$arrRecordsList[] = "'".$this->thirdPartyAccountNumber."'";
		$arrRecordsList[] = "'".$this->mailAddress."'";
		$arrRecordsList[] = "'". $this->comments."'";
		$arrRecordsList[] = "'". $this->status."'";

		if ($this->datePaid != null) {
			$insertFields[] = '`'.self::DB_FIELD_DATE_PAID.'`';
			$arrRecordsList[] = "'".$this->datePaid."'";
		}
		if ($this->checkNumber != null) {
			$insertFields[] = '`'.self::DB_FIELD_CHECK_NUMBER.'`';
			$arrRecordsList[] = "'". $this->checkNumber."'";
		}
		if ($this->hrNotes != null) {
			$insertFields[] = '`'.self::DB_FIELD_HR_NOTES.'`';
			$arrRecordsList[] = "'". $this->hrNotes."'";
		}

		$sqlBuilder = new SQLQBuilder();
		$query = $sqlBuilder->simpleInsert($arrTable, $arrRecordsList, $insertFields);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection -> executeQuery($query);

		//if ($result) {
		//	return mysql_affected_rows();
		//} else {
		//	throw new HspPaymentRequestException("Error in SQL Query", HspPaymentRequestException::ERROR_IN_DB_QUERY);
		//}

		if ($result) {
			return true;
		} else {
			return false;
		}
	}

	private function _getHsp() {
		if ($this->hspId != null) {
			return;
		}

		$hsp = new Hsp();
		$hsp->setEmployeeId($this->getEmployeeId());
		$hsp->setAllotmentId($this->getAllotmentId());

		$hspArr = $hsp->fetchHsps();

		if (is_array($hspArr) && isset($hspArr[0])) {
			$this->hspId = $hspArr[0]->getId();
		} else {
			throw new HspPaymentRequestException("No hsp", HspPaymentRequestException::NO_HSP);
		}
	}

	public static function listUnPaidHspRequests() {
		$sqlBuilder = new SQLQBuilder();

		$selectTable = "`".self::HSP_PAYMENT_REQUEST_DB_TABLE."`";

		$selectFields[] = "`".self::DB_FIELD_ID."`";
		$selectFields[] = "`".self::DB_FIELD_HSP_ID."`";
		$selectFields[] = "`".self::DB_FIELD_EMPLOYEE_ID."`";
		$selectFields[] = "`".self::DB_FIELD_DATE_INCURRED."`";
		$selectFields[] = "`".self::DB_FIELD_PROVIDER_NAME."`";
		$selectFields[] = "`".self::DB_FIELD_PERSON_INCURRING_EXPENSE."`";
		$selectFields[] = "`".self::DB_FIELD_EXPENSE_DESCRIPTION."`";
		$selectFields[] = "`".self::DB_FIELD_EXPENSE_AMOUNT."`";
		$selectFields[] = "`".self::DB_FIELD_PAYMENT_MADE_TO."`";
		$selectFields[] = "`".self::DB_FIELD_THIRD_PARTY_ACCOUNT_NUMBER."`";
		$selectFields[] = "`".self::DB_FIELD_MAIL_ADDRESS."`";
		$selectFields[] = "`".self::DB_FIELD_COMMENTS."`";
		$selectFields[] = "`".self::DB_FIELD_DATE_PAID."`";
		$selectFields[] = "`".self::DB_FIELD_CHECK_NUMBER."`";
		$selectFields[] = "`".self::DB_FIELD_STATUS."`";
		$selectFields[] = "`".self::DB_FIELD_HR_NOTES."`";

		$selectOrder = "ASC";
		$selectOrderBy = "`".self::DB_FIELD_ID."`";

		$selectConditions[] = "`".self::DB_FIELD_STATUS."` = ".self::HSP_PAYMENT_REQUEST_STATUS_SUBMITTED;

		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions, $selectOrderBy, $selectOrder);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		$requests = self::_buildObjArr($result);

		if (count($requests) > 0) {
			return $requests;
		} else {
			return null;
		}
	}

	public static function listEmployeeHspRequests($year, $employeeId, $paid=false) {
		if (!CommonFunctions::isValidId($employeeId)) {
			throw new HspPaymentRequestException("Invalid employee id", HspPaymentRequestException::INVALID_EMPLOYEE_ID);
		}

		$sqlBuilder = new SQLQBuilder();

		$selectTable = "`".self::HSP_PAYMENT_REQUEST_DB_TABLE."`";

		$selectFields[] = "`".self::DB_FIELD_ID."`";
		$selectFields[] = "`".self::DB_FIELD_HSP_ID."`";
		$selectFields[] = "`".self::DB_FIELD_EMPLOYEE_ID."`";
		$selectFields[] = "`".self::DB_FIELD_DATE_INCURRED."`";
		$selectFields[] = "`".self::DB_FIELD_PROVIDER_NAME."`";
		$selectFields[] = "`".self::DB_FIELD_PERSON_INCURRING_EXPENSE."`";
		$selectFields[] = "`".self::DB_FIELD_EXPENSE_DESCRIPTION."`";
		$selectFields[] = "`".self::DB_FIELD_EXPENSE_AMOUNT."`";
		$selectFields[] = "`".self::DB_FIELD_PAYMENT_MADE_TO."`";
		$selectFields[] = "`".self::DB_FIELD_THIRD_PARTY_ACCOUNT_NUMBER."`";
		$selectFields[] = "`".self::DB_FIELD_MAIL_ADDRESS."`";
		$selectFields[] = "`".self::DB_FIELD_COMMENTS."`";
		$selectFields[] = "`".self::DB_FIELD_DATE_PAID."`";
		$selectFields[] = "`".self::DB_FIELD_CHECK_NUMBER."`";
		$selectFields[] = "`".self::DB_FIELD_STATUS."`";
		$selectFields[] = "`".self::DB_FIELD_HR_NOTES."`";

		$selectOrder = "ASC";
		$selectOrderBy = "`".self::DB_FIELD_ID."`";

		if (true) {
			$selectConditions[] = "(`".self::DB_FIELD_STATUS."` = ".self::HSP_PAYMENT_REQUEST_STATUS_PAID .
			" OR `".self::DB_FIELD_STATUS."` = " . self::HSP_PAYMENT_REQUEST_STATUS_SUBMITTED . ")";
			//$selectConditions[] = "`".self::DB_FIELD_STATUS."` = ".self::HSP_PAYMENT_REQUEST_STATUS_SUBMITTED;
		} else {
			$selectConditions[] = "`".self::DB_FIELD_STATUS."` IN (".self::HSP_PAYMENT_REQUEST_STATUS_SUBMITTED.", ".self::HSP_PAYMENT_REQUEST_STATUS_PAID.")";
		}
		$selectConditions[] = "`".self::DB_FIELD_EMPLOYEE_ID."` = $employeeId";
		$selectConditions[] = "`".self::DB_FIELD_DATE_INCURRED."` BETWEEN  DATE('$year-01-01') AND DATE('$year-12-31')";

		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions, $selectOrderBy, $selectOrder);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		$requests = self::_buildObjArr($result);

		if (count($requests) > 0) {
			return $requests;
		} else {
			return null;
		}
	}

	public static function listEmployeeHspRequestsPaid($year, $employeeId) {
		if (!CommonFunctions::isValidId($employeeId)) {
			throw new HspPaymentRequestException("Invalid employee id", HspPaymentRequestException::INVALID_EMPLOYEE_ID);
		}

		$sqlBuilder = new SQLQBuilder();

		$selectTable = "`".self::HSP_PAYMENT_REQUEST_DB_TABLE."`";

		$selectFields[] = "`".self::DB_FIELD_ID."`";
		$selectFields[] = "`".self::DB_FIELD_HSP_ID."`";
		$selectFields[] = "`".self::DB_FIELD_EMPLOYEE_ID."`";
		$selectFields[] = "`".self::DB_FIELD_DATE_INCURRED."`";
		$selectFields[] = "`".self::DB_FIELD_PROVIDER_NAME."`";
		$selectFields[] = "`".self::DB_FIELD_PERSON_INCURRING_EXPENSE."`";
		$selectFields[] = "`".self::DB_FIELD_EXPENSE_DESCRIPTION."`";
		$selectFields[] = "`".self::DB_FIELD_EXPENSE_AMOUNT."`";
		$selectFields[] = "`".self::DB_FIELD_PAYMENT_MADE_TO."`";
		$selectFields[] = "`".self::DB_FIELD_THIRD_PARTY_ACCOUNT_NUMBER."`";
		$selectFields[] = "`".self::DB_FIELD_MAIL_ADDRESS."`";
		$selectFields[] = "`".self::DB_FIELD_COMMENTS."`";
		$selectFields[] = "`".self::DB_FIELD_DATE_PAID."`";
		$selectFields[] = "`".self::DB_FIELD_CHECK_NUMBER."`";
		$selectFields[] = "`".self::DB_FIELD_STATUS."`";
		$selectFields[] = "`".self::DB_FIELD_HR_NOTES."`";

		$selectOrder = "ASC";
		$selectOrderBy = "`".self::DB_FIELD_ID."`";

		$selectConditions[] = "(`".self::DB_FIELD_STATUS."` = ".self::HSP_PAYMENT_REQUEST_STATUS_PAID . ")";
		$selectConditions[] = "`".self::DB_FIELD_EMPLOYEE_ID."` = $employeeId";
		$selectConditions[] = "`".self::DB_FIELD_DATE_INCURRED."` BETWEEN  DATE('$year-01-01') AND DATE('$year-12-31')";

		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions, $selectOrderBy, $selectOrder);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		$requests = self::_buildObjArr($result);

		if (count($requests) > 0) {
			return $requests;
		} else {
			return null;
		}
	}

	public static function totalGrantedPayments($employeeId, $year, $type) {
		if (!CommonFunctions::isValidId($employeeId)) {
			throw new HspPaymentRequestException("Invalid employee id", HspPaymentRequestException::INVALID_EMPLOYEE_ID);
		}

		$sqlBuilder = new SQLQBuilder();

		$selectTable = "`".self::HSP_PAYMENT_REQUEST_DB_TABLE."`";

		$selectFields[] = "SUM(`".self::DB_FIELD_EXPENSE_AMOUNT."`)";

		$selectOrder = "ASC";
		$selectOrderBy = "`".self::DB_FIELD_ID."`";

		$selectConditions[] = "`".self::DB_FIELD_STATUS."` = ".self::HSP_PAYMENT_REQUEST_STATUS_PAID;

		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions, $selectOrderBy, $selectOrder);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		$requests = self::_buildObjArr($result);

		if (count($requests) > 0) {
			return $requests;
		} else {
			return null;
		}
	}

	public function payHspRequest() {
		$request = self::getHspRequest($this->id);

		if (($request == null) || ($request->getStatus() == self::HSP_PAYMENT_REQUEST_STATUS_DELETED)) {
			throw new HspPaymentRequestException("HSP Request not found", HspPaymentRequestException::HSP_REQUEST_NOT_FOUND);
		}

		if ($request->getStatus() == self::HSP_PAYMENT_REQUEST_STATUS_PAID) {
			throw new HspPaymentRequestException("Already paid", HspPaymentRequestException::ALREADY_PAID);
		}

		if ($this->datePaid == null) {
			throw new HspPaymentRequestException("Date paid not specified", HspPaymentRequestException::INVALID_REQUEST);
		}

		if ($this->checkNumber == null) {
			throw new HspPaymentRequestException("Check paid not specified", HspPaymentRequestException::INVALID_REQUEST);
		}

		$this->status = self::HSP_PAYMENT_REQUEST_STATUS_PAID;

		return $this->_update();
	}

	public function deleteHspRequest() {
		$request = self::getHspRequest($this->id);

		if (($request == null) || ($request->getStatus() == self::HSP_PAYMENT_REQUEST_STATUS_DELETED)) {
			throw new HspPaymentRequestException("HSP Request not found", HspPaymentRequestException::HSP_REQUEST_NOT_FOUND);
		}

		if ($request->getStatus() == self::HSP_PAYMENT_REQUEST_STATUS_PAID) {
			throw new HspPaymentRequestException("Already paid", HspPaymentRequestException::ALREADY_PAID);
		}

		$this->status = self::HSP_PAYMENT_REQUEST_STATUS_DELETED;

		return $this->_update();
	}

	public function denyHspRequest() {
		$request = self::getHspRequest($this->id);

		if (($request == null) || ($request->getStatus() == self::HSP_PAYMENT_REQUEST_STATUS_DELETED)) {
			throw new HspPaymentRequestException("HSP Request not found", HspPaymentRequestException::HSP_REQUEST_NOT_FOUND);
		}

		if ($request->getStatus() == self::HSP_PAYMENT_REQUEST_STATUS_PAID) {
			throw new HspPaymentRequestException("Already paid", HspPaymentRequestException::ALREADY_PAID);
		}

		$this->status = self::HSP_PAYMENT_REQUEST_STATUS_DENIED;

		return $this->_update();
	}

	public function updateRequest() {
		$request = self::getHspRequest($this->id);

		if (($request == null) || ($request->getStatus() == self::HSP_PAYMENT_REQUEST_STATUS_DELETED)) {
			throw new HspPaymentRequestException("HSP Request not found", HspPaymentRequestException::HSP_REQUEST_NOT_FOUND);
		}

		if ($request->getStatus() == self::HSP_PAYMENT_REQUEST_STATUS_PAID) {
			throw new HspPaymentRequestException("Already paid", HspPaymentRequestException::ALREADY_PAID);
		}

		return $this->_update();
	}

	private function _update() {
		if (!CommonFunctions::isValidId($this->id)) {
			throw new HspPaymentRequest("Invalid id", HspPaymentRequest::INVALID_ID);
		}

		$arrTable = '`'.self::HSP_PAYMENT_REQUEST_DB_TABLE.'`';

		if ($this->dateIncurred != null) {
			$updateFields[] = '`'.self::DB_FIELD_DATE_INCURRED.'`';
			$arrRecordsList[] = "'".$this->dateIncurred."'";
		}
		if ($this->providerName != null) {
			$updateFields[] = '`'.self::DB_FIELD_PROVIDER_NAME.'`';
			$arrRecordsList[] = "'".$this->providerName."'";
		}
		if ($this->personIncurringExpense != null) {
			$updateFields[] = '`'.self::DB_FIELD_PERSON_INCURRING_EXPENSE.'`';
			$arrRecordsList[] = "'". $this->personIncurringExpense."'";
		}
		if ($this->expenseDescription != null) {
			$updateFields[] = '`'.self::DB_FIELD_EXPENSE_DESCRIPTION.'`';
			$arrRecordsList[] = "'". $this->expenseDescription."'";
		}
		if ($this->expenseAmount != null) {
			$updateFields[] = '`'.self::DB_FIELD_EXPENSE_AMOUNT.'`';
			$arrRecordsList[] = "'". $this->expenseAmount."'";
		}
		if ($this->paymentMadeTo != null) {
			$updateFields[] = '`'.self::DB_FIELD_PAYMENT_MADE_TO.'`';
			$arrRecordsList[] = "'".$this->paymentMadeTo."'";
		}
		if ($this->thirdPartyAccountNumber != null) {
			$updateFields[] = '`'.self::DB_FIELD_THIRD_PARTY_ACCOUNT_NUMBER.'`';
			$arrRecordsList[] = "'".$this->thirdPartyAccountNumber."'";
		}
		if ($this->mailAddress != null) {
			$updateFields[] = '`'.self::DB_FIELD_MAIL_ADDRESS.'`';
			$arrRecordsList[] = "'".$this->mailAddress."'";
		}
		if ($this->comments != null) {
			$updateFields[] = '`'.self::DB_FIELD_COMMENTS.'`';
			$arrRecordsList[] = "'". $this->comments."'";
		}
		if ($this->status != null) {
			$updateFields[] = '`'.self::DB_FIELD_STATUS.'`';
			$arrRecordsList[] = "'". $this->status."'";
		}
		if ($this->datePaid != null) {
			$updateFields[] = '`'.self::DB_FIELD_DATE_PAID.'`';
			$arrRecordsList[] = "'".$this->datePaid."'";
		}
		if ($this->checkNumber != null) {
			$updateFields[] = '`'.self::DB_FIELD_CHECK_NUMBER.'`';
			$arrRecordsList[] = "'". $this->checkNumber."'";
		}
		if ($this->hrNotes != null) {
			$updateFields[] = '`'.self::DB_FIELD_HR_NOTES.'`';
			$arrRecordsList[] = "'". $this->hrNotes."'";
		}

		$updateConditions[0] = "`".self::DB_FIELD_ID."` = '".$this->id."'";

		$sqlBuilder = new SQLQBuilder();
		$query = $sqlBuilder->simpleUpdate($arrTable, $updateFields, $arrRecordsList, $updateConditions);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection -> executeQuery($query);

		if ($result === false) {
			throw new HspPaymentRequestException("Error in update", HspPaymentRequestException::ERROR_IN_DB_QUERY);
		}

		return mysql_affected_rows();
	}

	private static function _buildObjArr($result) {

		$objArr = array();

		while ($row = mysql_fetch_assoc($result)) {
			$tmpArr = new HspPaymentRequest();
			$tmpArr->setId($row[self::DB_FIELD_ID]);
			$tmpArr->setHspId($row[self::DB_FIELD_HSP_ID]);
			$tmpArr->setEmployeeId($row[self::DB_FIELD_EMPLOYEE_ID]);
			$tmpArr->setDateIncurred($row[self::DB_FIELD_DATE_INCURRED]);
			$tmpArr->setProviderName($row[self::DB_FIELD_PROVIDER_NAME]);
			$tmpArr->setPersonIncurringExpense($row[self::DB_FIELD_PERSON_INCURRING_EXPENSE]);
			$tmpArr->setExpenseDescription($row[self::DB_FIELD_EXPENSE_DESCRIPTION]);
			$tmpArr->setExpenseAmount($row[self::DB_FIELD_EXPENSE_AMOUNT]);
			$tmpArr->setPaymentMadeTo($row[self::DB_FIELD_PAYMENT_MADE_TO]);
			$tmpArr->setThirdPartyAccountNumber($row[self::DB_FIELD_THIRD_PARTY_ACCOUNT_NUMBER]);
			$tmpArr->setMailAddress($row[self::DB_FIELD_MAIL_ADDRESS]);
			$tmpArr->setComments($row[self::DB_FIELD_COMMENTS]);
			$tmpArr->setDatePaid($row[self::DB_FIELD_DATE_PAID]);
			$tmpArr->setCheckNumber($row[self::DB_FIELD_CHECK_NUMBER]);
			$tmpArr->setStatus($row[self::DB_FIELD_STATUS]);
			$tmpArr->setHrNotes($row[self::DB_FIELD_HR_NOTES]);

			$objArr[] = $tmpArr;
		}

		return $objArr;
	}
	/**
	* Get Dependants of a employee
	* @param int $empId ID of the employee
	* @return array $dependant array of names of dependents
	* */
	public static function fetchDependants($empId) {
		$sqlBuilder = new SQLQBuilder();

		$selectTable = "`".self::EMP_DEPENDENT_DB_TABLE."`";

		$selectFields[] = "`".self::DB_FIELD_EMP_DEPENDENT_NAME."`";

		$selectOrder = "ASC";
		$selectOrderBy = "`".self::DB_FIELD_EMP_DEPENDENT_NAME."`";

		$selectConditions[] = "`".self::DB_FIELD_EMP_NUMBER."` = ".$empId;

		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions, $selectOrderBy, $selectOrder);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		$dependents = null;

		if (mysql_num_rows($result) > 0) {
			while($row = mysql_fetch_array($result)) {
				$dependents[] = $row[0];
			}
		}
		return $dependents;
	}

	/**
	* Get children of a employee
	* @param int $empId ID of the employee
	* @return array $children array of names of childrens
	* */
	public static function fetchChildren($empId) {
		$sqlBuilder = new SQLQBuilder();

		$selectTable = "`".self::EMP_CHILDREN_DB_TABLE."`";

		$selectFields[] = "`".self::DB_FIELD_EMP_CHILDREN_NAME."`";

		$selectOrder = "ASC";
		$selectOrderBy = "`".self::DB_FIELD_EMP_CHILDREN_NAME."`";

		$selectConditions[] = "`".self::DB_FIELD_EMP_NUMBER."` = ".$empId;

		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions, $selectOrderBy, $selectOrder);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		$children = null;

		if (mysql_num_rows($result) > 0) {
			while($row = mysql_fetch_array($result)) {
				$children[] = $row[0];
			}
		}
		return $children;
	}

	/**
	 * Get hsp request details - dateInquried, expenseDescription,
	 * personInquringExpense, amount
	 * @param int $hspId
	 * @return array
	 */
	public function fetchHspRequestDetails($hspId) {
		$sqlBuilder = new SQLQBuilder();

		$selectTable = "`".self::HSP_PAYMENT_REQUEST_DB_TABLE."`";

		$selectFields[] = "`".self::DB_FIELD_DATE_INCURRED."`";
		$selectFields[] = "`".self::DB_FIELD_EXPENSE_DESCRIPTION."`";
		$selectFields[] = "`".self::DB_FIELD_PERSON_INCURRING_EXPENSE."`";
		$selectFields[] = "`".self::DB_FIELD_EXPENSE_AMOUNT."`";

		$selectOrder = "ASC";
		$selectOrderBy = "`".self::DB_FIELD_DATE_INCURRED."`";

		$selectConditions[] = "`".self::DB_FIELD_ID."` = '".$hspId."'";

		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions, $selectOrderBy, $selectOrder);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		$detail = null;
		if (mysql_num_rows($result) > 0) {
			$i = 0;
			while($row = mysql_fetch_array($result)) {
				$detail = $row;
			}
		}
		return $detail;
	}

	public function isDataChangedByAdmin($hspRequest) {
		$isChanged = false;
		$msg = 'HR Admin Changing HSP Request (ID-' . $hspRequest->getId().')';
		$dateInqTemp = $hspRequest->getDateIncurred();
		if(!isset($dateInqTemp)) {
			return $isChanged;
		}
		if($this->dateIncurred != $hspRequest->getDateIncurred()) {
			$isChanged = true;
			$msg = $msg . "'\r\nDate Incurred: " . $this->dateIncurred . " -> " . $hspRequest->getDateIncurred();
		}
		if($this->providerName != $hspRequest->getProviderName()) {
			$isChanged = true;
			$msg = $msg . "\r\nProvider Name: " . $this->providerName . " -> " . $hspRequest->getProviderName();
		}
		if($this->personIncurringExpense != $hspRequest->getPersonIncurringExpense()) {
			$isChanged = true;
			$msg = $msg . "\r\nPerson Incurring Expense: " . $this->personIncurringExpense . " -> " . $hspRequest->getPersonIncurringExpense();
		}
		if($this->expenseDescription != $hspRequest->getExpenseDescription()) {
			$isChanged = true;
			$msg = $msg . "\r\nExpense Description: " . $this->expenseDescription . " -> " . $hspRequest->getExpenseDescription();
		}
		if($this->expenseAmount != $hspRequest->getExpenseAmount()) {
			$isChanged = true;
			$msg = $msg . "\r\nExpense Amount: " . $this->expenseAmount . " -> " . $hspRequest->getExpenseAmount();
		}
		if($this->paymentMadeTo != $hspRequest->getPaymentMadeTo()) {
			$isChanged = true;
			$msg = $msg . "\r\nPayment Made To: " . $this->paymentMadeTo . " -> " . $hspRequest->getPaymentMadeTo();
		}
		if($this->thirdPartyAccountNumber != $hspRequest->getThirdPartyAccountNumber()) {
			$isChanged = true;
			$msg = $msg . "\r\nThird Party Account Number: " . $this->thirdPartyAccountNumber . " -> " . $hspRequest->getThirdPartyAccountNumber();
		}
		if($this->mailAddress != $hspRequest->getMailAddress()) {
			$isChanged = true;
			$msg = $msg . "\r\nMail Address: " . $this->mailAddress . " -> " . $hspRequest->getMailAddress();
		}
		if($this->comments != $hspRequest->getComments()) {
			$isChanged = true;
			$msg = $msg . "\r\nComments: " . $this->comments . " -> " . $hspRequest->getComments();
		}

		if (!$isChanged) {
			return $isChanged;
		}else {
			return $msg;
		}

	}

	/**
	 * For a given employee and a HSP scheme, if there are new approved payments from last updated date,
	 * this function returns the sum of expense amounts of those payments.
	 */

	public static function calculateNewHspUsed($empId, $hspPlanId, $lasUpdatedDate, $currentYear=true) {

		if ($currentYear) {

			$currentYearStart = date('Y')."-01-01";

		    if ($lasUpdatedDate < $currentYearStart) {
		        $lasUpdatedDate = $currentYearStart;
		    }

		}

	    $selectTable = "`".self::HSP_PAYMENT_REQUEST_DB_TABLE."`";
		$selectFields[0] = "SUM(".self::DB_FIELD_EXPENSE_AMOUNT.")";
		$selectConditions[0] = "`".self::DB_FIELD_EMPLOYEE_ID."` = '".$empId."'";
		$selectConditions[1] = "`".self::DB_FIELD_HSP_ID."` = '".$hspPlanId."'";
		$selectConditions[2] = "`".self::DB_FIELD_DATE_PAID."` > '".$lasUpdatedDate."'";
		$selectConditions[3] = "`".self::DB_FIELD_STATUS."` = ".self::HSP_PAYMENT_REQUEST_STATUS_PAID;

		$sqlBuilder = new SQLQBuilder();
		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($query);

		if ($result) {

			$resultArray = $dbConnection->dbObject->getArray($result);

			if ($resultArray[0] != null) {
			    return $resultArray[0];
			} else {
			    return 0;
			}

		} else {
		    return 0;
		}



	}
}

class HspPaymentRequestException extends Exception {
	const ERROR_IN_DB_QUERY = 1;
	const INVALID_ROW_COUNT = 2;
	const INVALID_ID = 3;
	const HSP_NOT_FOUND = 4;
	const HSP_REQUEST_NOT_FOUND = 5;
	const ALREADY_PAID = 6;
	const INVALID_EMPLOYEE_ID = 7;
	const INVALID_REQUEST = 8;
	const NO_HSP = 9;
	const HSP_TERMINATED = 10;
	const HSP_NOT_ENOUGH_BALANCE_REMAINING = 11;
	const EXCEED_LIMIT = 12;
	const INVALID_YEAR = 13;
	const INVALID_DATE = 14;
}
?>
