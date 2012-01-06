<?php
/*
// OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
// all the essential functionalities required for any enterprise.
// Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com

// OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
// the GNU General Public License as published by the Free Software Foundation; either
// version 2 of the License, or (at your option) any later version.

// OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
// without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU General Public License for more details.

// You should have received a copy of the GNU General Public License along with this program;
// if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
// Boston, MA  02110-1301, USA
*/

require_once ROOT_PATH . '/lib/confs/Conf.php';
require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';
require_once ROOT_PATH . '/lib/dao/SQLQBuilder.php';
require_once ROOT_PATH . '/lib/common/CommonFunctions.php';
require_once ROOT_PATH . '/lib/logs/LogFileWriter.php';

class EmpDirectDebit {

	const TABLE_NAME = 'hs_hr_emp_directdebit';
	const DB_FIELD_EMP_NUMBER = 'emp_number';
	const DB_FIELD_SEQNO = 'dd_seqno';
	const DB_FIELD_ROUTING_NUM = 'dd_routing_num';
	const DB_FIELD_ACCOUNT = 'dd_account';
	const DB_FIELD_AMOUNT = 'dd_amount';
	const DB_FIELD_ACCOUNT_TYPE = 'dd_account_type';
	const DB_FIELD_TRANSACTION_TYPE = 'dd_transaction_type';

	const ACCOUNT_TYPE_CHECKING = 'CHECKING';
	const ACCOUNT_TYPE_SAVINGS = 'SAVINGS';

	const TRANSACTION_TYPE_BLANK = 'BLANK';
	const TRANSACTION_TYPE_PERCENTAGE = 'PERC';
	const TRANSACTION_TYPE_FLAT = 'FLAT';
	const TRANSACTION_TYPE_FLAT_MINUS = 'FLATMINUS';

	protected $empNumber;
	protected $ddSeqNo;
	protected $routingNumber;
	protected $account;
	protected $amount;
	protected $accountType;
	protected $transactionType;

	/**
	 * Constructor
	 *
	 */
	public function __construct() {
	}

	public function setEmpNumber($empNumber) {
		$this->empNumber = $empNumber;
	}

	public function getEmpNumber() {
		return $this->empNumber;
	}

	public function setDDSeqNo($ddSeqNo) {
		$this->ddSeqNo = $ddSeqNo;
	}

	public function getDDSeqNo() {
		return $this->ddSeqNo;
	}

	public function setRoutingNumber($routingNumber) {
		$this->routingNumber = $routingNumber;
	}

	public function getRoutingNumber() {
		return $this->routingNumber;
	}

	public function setAccount($account) {
		$this->account = $account;
	}

	public function getAccount() {
		return $this->account;
	}

	public function setAmount($amount) {
		$this->amount = $amount;
	}

	public function getAmount() {
		return $this->amount;
	}

	public function setAccountType($accountType) {
		$this->accountType = $accountType;
	}

	public function getAccountType() {
		return $this->accountType;
	}

	public function setTransactionType($transactionType) {
		$this->transactionType = $transactionType;
	}

	public function getTransactionType() {
		return $this->transactionType;
	}

	/**
	 * Delete the given direct debit information from the database
	 *
	 * @param string $empNumber Employee number
	 * @param array $ddToDelete direct debit sequence numbers to delete
	 */
	public function delete($empNumber, $ddToDelete) {

		$arrFieldList[0] = self::DB_FIELD_EMP_NUMBER;
		$arrFieldList[1] = self::DB_FIELD_SEQNO;

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = self::TABLE_NAME;
		$sql_builder->flg_delete = 'true';
		$sql_builder->arr_delete = $arrFieldList;

		$arr[1] = $ddToDelete;
		for($c=0; count($arr[1])>$c; $c++) {
			if($arr[1][$c] != NULL) {
				$arr[0][$c] = $empNumber;
			}
		}
		$sqlQString = $sql_builder->deleteRecord($arr);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($sqlQString);
		return $result;
	}

	/**
	 * Add the direct debit information to the database
	 */
	public function add() {

		$nextSeqNum = $this->getNextSeqNum($this->empNumber);

		$fields[0] = self::DB_FIELD_EMP_NUMBER;
		$fields[1] = self::DB_FIELD_SEQNO;
		$fields[2] = self::DB_FIELD_ROUTING_NUM;
		$fields[3] = self::DB_FIELD_ACCOUNT;
		$fields[4] = self::DB_FIELD_AMOUNT;
		$fields[5] = self::DB_FIELD_ACCOUNT_TYPE;
		$fields[6] = self::DB_FIELD_TRANSACTION_TYPE;

		$values[0] = "'{$this->empNumber}'";
		$values[1] = "'{$nextSeqNum}'";
		$values[2] = "'{$this->routingNumber}'";
		$values[3] = "'{$this->account}'";
		$values[4] = "'{$this->amount}'";
		$values[5] = "'{$this->accountType}'";
		$values[6] = "'{$this->transactionType}'";

		$sqlBuilder = new SQLQBuilder();
		$sqlBuilder->table_name = self::TABLE_NAME;
		$sqlBuilder->flg_insert = 'true';
		$sqlBuilder->arr_insert = $values;
		$sqlBuilder->arr_insertfield = $fields;

		$sql = $sqlBuilder->addNewRecordFeature2();

		$conn = new DMLFunctions();
		$result = $conn->executeQuery($sql);

		if (!$result || (mysql_affected_rows() != 1)) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Update the direct debit information in the database
	 */
	public function update() {

		$fields[0] = self::DB_FIELD_EMP_NUMBER;
		$fields[1] = self::DB_FIELD_SEQNO;
		$fields[2] = self::DB_FIELD_ROUTING_NUM;
		$fields[3] = self::DB_FIELD_ACCOUNT;
		$fields[4] = self::DB_FIELD_AMOUNT;
		$fields[5] = self::DB_FIELD_ACCOUNT_TYPE;
		$fields[6] = self::DB_FIELD_TRANSACTION_TYPE;

		$values[0] = "'{$this->empNumber}'";
		$values[1] = "'{$this->ddSeqNo}'";
		$values[2] = "'{$this->routingNumber}'";
		$values[3] = "'{$this->account}'";
		$values[4] = "'{$this->amount}'";
		$values[5] = "'{$this->accountType}'";
		$values[6] = "'{$this->transactionType}'";

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = self::TABLE_NAME;
		$sql_builder->flg_update = 'true';
		$sql_builder->arr_update = $fields;
		$sql_builder->arr_updateRecList = $values;

		$sqlQString = $sql_builder->addUpdateRecord1(1);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection -> executeQuery($sqlQString);

		return $result;


	}

	/**
	 * Get the direct debit information with given sequence number
	 *
	 * @param string $empNumber The employee number
	 * @param int $ddSeqNo The direct debit sequence number
	 *
	 * @return null if not found or EmpDirectDebit object if found
	 */
	public function getDirectDebit($empNumber, $ddSeqNo) {

		$arrFieldList[0] = self::DB_FIELD_EMP_NUMBER;
		$arrFieldList[1] = self::DB_FIELD_SEQNO;
		$arrFieldList[2] = self::DB_FIELD_ROUTING_NUM;
		$arrFieldList[3] = self::DB_FIELD_ACCOUNT;
		$arrFieldList[4] = self::DB_FIELD_AMOUNT;
		$arrFieldList[5] = self::DB_FIELD_ACCOUNT_TYPE;
		$arrFieldList[6] = self::DB_FIELD_TRANSACTION_TYPE;

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = self::TABLE_NAME;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->selectOneRecordFiltered(array($empNumber, $ddSeqNo), 1);

		$dd = null;

		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($sqlQString);

		if ($result && mysql_num_rows($result) == 1) {
			$line = mysql_fetch_assoc($result);
			$dd = $this->_buildObject($line);
		}

		return $dd;
	}

	/**
	 * Get assigned direct debit instructions for the given employee
	 *
	 * @param string $empNumber Employee number
	 * @return array of employee direct debit instructions. Empty array if none found
	 */
	public function getEmployeeDirectDebit($empNumber) {

		$arrFieldList[0] = self::DB_FIELD_EMP_NUMBER;
		$arrFieldList[1] = self::DB_FIELD_SEQNO;
		$arrFieldList[2] = self::DB_FIELD_ROUTING_NUM;
		$arrFieldList[3] = self::DB_FIELD_ACCOUNT;
		$arrFieldList[4] = self::DB_FIELD_AMOUNT;
		$arrFieldList[5] = self::DB_FIELD_ACCOUNT_TYPE;
		$arrFieldList[6] = self::DB_FIELD_TRANSACTION_TYPE;

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = self::TABLE_NAME;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->selectOneRecordFiltered($empNumber);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($sqlQString);

		$ddebit = array();

		if ($result && mysql_num_rows($result) > 0) {
			while($line = mysql_fetch_assoc($result)) {;
				$ddebit[] = $this->_buildObject($line);
			}
		}

		return $ddebit;
	}

	/**
	 * Get next direct debit sequence number for given employee
	 *
	 * @param string $empNumber The employee number
	 *
	 * @return next sequence number
	 */
	function getNextSeqNum($empNumber) {

		if (!CommonFunctions::isValidId($empNumber)) {
			throw new EmpDirectDebitException("Invalid emp_number = $empNumber");
		}

		$sql = "SELECT MAX(" . self::DB_FIELD_SEQNO . ") + 1 FROM " . self::TABLE_NAME .
		       " WHERE " . self::DB_FIELD_EMP_NUMBER . " = " . $empNumber;
		$conn = new DMLFunctions();
		$result = $conn->executeQuery($sql);

		if (!$result) {
			$errMsg = mysql_error();
			throw new EmpDirectDebitException("Error looking for existing MAX Seqence ID. SQL = $sql. Msg = $errMsg");
		}

		$row = mysql_fetch_array($result, MYSQL_NUM);
		if (empty($row)) {
			throw new IDGeneratorException("Error looking for existing MAX Sequence ID. SQL = $sql");
		}
		$nextSeqNum = $row[0];
		if (empty($nextSeqNum)) {
			$nextSeqNum = 1;
		}

		return $nextSeqNum;
	}


	/**
	 * Build a EmpDirectDebit object from the database results row
	 *
	 * @param array $row associative array containing database data
	 *
	 * @return EmpDirectDebit object
	 */
	private function _buildObject($row) {
		$dd = new EmpDirectDebit();

		$dd->setEmpNumber($row[self::DB_FIELD_EMP_NUMBER]);
		$dd->setDDSeqNo($row[self::DB_FIELD_SEQNO]);
		$dd->setRoutingNumber($row[self::DB_FIELD_ROUTING_NUM]);
		$dd->setAccount($row[self::DB_FIELD_ACCOUNT]);
		$dd->setAmount($row[self::DB_FIELD_AMOUNT]);
		$dd->setAccountType($row[self::DB_FIELD_ACCOUNT_TYPE]);
		$dd->setTransactionType($row[self::DB_FIELD_TRANSACTION_TYPE]);

		return $dd;
	}
}

class EmpDirectDebitException extends Exception {
}

?>
