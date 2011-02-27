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
require_once ROOT_PATH . '/lib/confs/sysConf.php';
require_once ROOT_PATH . '/lib/common/CommonFunctions.php';
require_once ROOT_PATH . '/lib/models/eimadmin/export/CSVExportPlugin.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpDirectDebit.php';
require_once ROOT_PATH . '/lib/models/eimadmin/encryption/KeyHandlerOld.php';

/**
 * Class to generate CSV file for export to Millenium Payroll
 */
class MilleniumPayrollExport implements CSVExportPlugin {

	const NAME = "Millenium Payroll Export";

	/**
	 * Get descriptive name for this plugin
	 *
	 * @return Name for this plugin
	 */
	public function getName() {
		return self::NAME;
	}

	/**
	 * Get CSV header
	 *
	 * @return CSV header
	 */
	public function getHeader() {
		$header = "ID,Last Name,First Name,Middle Name,Address 1,Address 2, City,State,Zip,Gender,Birth Date," .
		"SSN,Status,Hire Date,CC1,CC2,CC3,CC4,CC5,TaxForm,WorkState,Emp Type,Clock Number," .
		"Home Phone,Ethnicity,Base Rate,Default Hours,Salary,Pay Frequency,Auto Pay," .
		"FITW Status,FITW Exemptions,SITW State,SITW Status,SITW Exemptions,SITW Exemptions 2," .
		"SUI State,Transit,DD1 Account,DD1 Amount,DD1 Amount Code,DD1 Checking,Transit," .
		"DD2 Account,DD2 Amount,DD2 Amount Code,DD2 Checking";
		return $header;
	}

	/**
	 * Get CSV data as string
	 *
	 * @return string formatted csv data
	 */
	public function getCSVData() {
		$sql = "SELECT hs_hr_employee.emp_number, employee_id, emp_lastname, emp_firstname, emp_middle_name, emp_street1, emp_street2," .
		"city_code,provin_code,emp_zipcode,emp_gender,emp_birthday,emp_ssn_num,emp_status,joined_date, " .
		"tax_federal_status, tax_federal_exceptions, tax_state, tax_state_status, tax_state_exceptions, " .
		"tax_unemp_state,tax_work_state,custom1,custom2,custom3,custom4,custom5,custom6,custom7,custom8,custom9,custom10, " .
		" pay.payperiod_code,sal.ebsal_basic_salary,loc.loc_name,comp.title " .
		" FROM hs_hr_employee " .
		" LEFT JOIN hs_hr_emp_us_tax tax on (tax.emp_number = hs_hr_employee.emp_number) " .
		" LEFT JOIN hs_hr_emp_basicsalary sal on (hs_hr_employee.emp_number = sal.emp_number) " .
		" LEFT JOIN hs_hr_payperiod pay on (sal.payperiod_code = pay.payperiod_code) " .
		" LEFT JOIN hs_hr_compstructtree comp on (hs_hr_employee.work_station = comp.id) " .
		" LEFT JOIN hs_hr_location loc on (comp.loc_code = loc.loc_code) ";
		
		if (KeyHandlerOld::keyExists()) {
			$key = KeyHandlerOld::readKey();
			$sql = str_replace("emp_ssn_num", "IF(`emp_ssn_num` IS NOT NULL, AES_DECRYPT(emp_ssn_num, '$key'), '') AS `emp_ssn_num`", $sql);
			$sql = str_replace("sal.ebsal_basic_salary", "IF(`ebsal_basic_salary` IS NOT NULL, AES_DECRYPT(ebsal_basic_salary, '$key'), '') AS `ebsal_basic_salary`", $sql);
		}

		$conn = new DMLFunctions();
		$result = $conn->executeQuery($sql);

		$csv = "";

		if ($result === false) {
			throw new Exception("Error in query: " . $sql);
		}

		while ($row = mysql_fetch_assoc($result)) {
			$csv .= $this->_getCSVRow($row) . "\n";
		}
		return $csv;
	}

	/**
	 * Get CSV row from data retrieved from the database
	 *
	 * @param array $row Data row from database
	 */
	private function _getCSVRow($row) {

		$genderValues = array (
			1 => "M",
			2 => "F"
		);
		$empStatuses = array (
			"EST000" => "T",
			"EST001" => "A",
			"EST002" => "T"
		);

		$payPeriods = array (
			 1 => "W",
			 2 => "B",
			 3 => "S",
			 4 => "M",
			 5 => "M1"
		);

		$transactionTypes = array (
			EmpDirectDebit::TRANSACTION_TYPE_BLANK => 'Blank',
			EmpDirectDebit::TRANSACTION_TYPE_PERCENTAGE => '%',
			EmpDirectDebit::TRANSACTION_TYPE_FLAT => 'Flat',
			EmpDirectDebit::TRANSACTION_TYPE_FLAT_MINUS => 'Flat-'
		);

		$accountTypes = array (
			EmpDirectDebit::ACCOUNT_TYPE_CHECKING => "Y",
			EmpDirectDebit::ACCOUNT_TYPE_SAVINGS => ""
		);

		$id = $this->_escape($row['employee_id']); // required
		$lastName = $this->_escape($row['emp_lastname']);// required
		$firstName = $this->_escape($row['emp_firstname']);// required
		$middleName = $this->_escape($row['emp_middle_name']);// optional
		$address1 = $this->_escape($row['emp_street1']);// optional
		$address2 = $this->_escape($row['emp_street2']);// optional
		$city = $this->_escape($row['city_code']);// optional
		$state = $this->_escape($row['provin_code']);// optional
		$zip = $this->_escape($row['emp_zipcode']);// required
		$gender = $this->_escape($this->_getValueFromMap($genderValues, $row['emp_gender']));// optional
		$birthDate = $this->_escape($this->_convertDate($row['emp_birthday']));// optional
		$ssn = $this->_escape($row['emp_ssn_num']);// required
		$status = $this->_escape($this->_getValueFromMap($empStatuses, $row['emp_status']));//required
		$hireDate = $this->_escape($this->_convertDate($row['joined_date']));//required
		$cc1 = $this->_escape($row['title']);//required (if set up in company)
		$cc2 = $this->_escape($row['loc_name']);//required (if set up in company)
		$cc3 = $this->_escape($row['custom1']);//required (if set up in company)
		$cc4 = $this->_escape($row['custom2']);//required (if set up in company)
		$cc5 = $this->_escape($row['custom3']);//required (if set up in company)
		$taxForm = "";//optional
		$workState = $this->_escape($row['tax_work_state']);//required
		$empType = "";//optional
		$clockNumber = "";//optional
		$homePhone = "";//optional
		$ethnicity = "";//optional
		$baseRate = "";//optional
		$defaultHours = "";//optional
		$salary = $this->_escape($row['ebsal_basic_salary']);// required (if salary and not hourly)
		$payFrequency = $this->_escape($this->_getValueFromMap($payPeriods, $row['payperiod_code']));// required
		$autoPay = "";//optional
		$FITWStatus = $this->_escape($row['tax_federal_status']);//required
		$FITWExemptions = $this->_escape($row['tax_federal_exceptions']);//required
		$SITWState = $this->_escape($row['tax_state']);//required
		$SITWStatus = $this->_escape($row['tax_state_status']);//required
		$SITWExemptions = $this->_escape($row['tax_state_exceptions']);//required
		$SITWExemptions2 = "";//optional
		$SUIState = $this->_escape($row['tax_unemp_state']);//required
		$transit1 = "";//required if defined
		$DD1Account = "";//required if defined
		$DD1Amount = "";//required if defined
		$DD1AmountCode = "";//required if defined
		$DD1Checking = "";//required if defined
		$transit2 = "";//required if defined
		$DD2Account = "";//required if defined
		$DD2Amount = "";//required if defined
		$DD2AmountCode = "";//required if defined
		$DD2Checking = "";//required if defined

		// Get direct debit information
		$dd = new EmpDirectDebit();
		$ddList = $dd->getEmployeeDirectDebit($row['emp_number']);

		if (count($ddList) > 0) {
			$transit1 = $this->_escape($ddList[0]->getRoutingNumber());
			$DD1Account = $this->_escape($ddList[0]->getAccount());
			$DD1Amount = $this->_escape($ddList[0]->getAmount());
			$DD1AmountCode = $this->_escape($this->_getValueFromMap($transactionTypes, $ddList[0]->getTransactionType()));
			$DD1Checking = $this->_escape($this->_getValueFromMap($accountTypes, $ddList[0]->getAccountType()));
		}

		if (count($ddList) > 1) {
			$transit2 = $this->_escape($ddList[1]->getRoutingNumber());
			$DD2Account = $this->_escape($ddList[1]->getAccount());
			$DD2Amount = $this->_escape($ddList[1]->getAmount());
			$DD2AmountCode = $this->_escape($this->_getValueFromMap($transactionTypes, $ddList[1]->getTransactionType()));
			$DD2Checking = $this->_escape($this->_getValueFromMap($accountTypes, $ddList[1]->getAccountType()));
		}

		$csvRow = "$id,$lastName,$firstName,$middleName,$address1,$address2,$city,$state,$zip,$gender,$birthDate," .
		"$ssn,$status,$hireDate,$cc1,$cc2,$cc3,$cc4,$cc5,$taxForm,$workState,$empType,$clockNumber," .
		"$homePhone,$ethnicity,$baseRate,$defaultHours,$salary,$payFrequency,$autoPay," .
		"$FITWStatus,$FITWExemptions,$SITWState,$SITWStatus,$SITWExemptions,$SITWExemptions2," .
		"$SUIState,$transit1,$DD1Account,$DD1Amount,$DD1AmountCode,$DD1Checking,$transit2," .
		"$DD2Account,$DD2Amount,$DD2AmountCode,$DD2Checking";

		return $csvRow;
	}

	/**
	 * Convert date to mm/dd/yyyy format
	 *
	 * @param string $date Formatted date
	 * @return string date formatted in mm/dd/yyyy format
	 */
	private function _convertDate($date) {

		if ($date != "0000-00-00") {
			$timestamp = strtotime($date);
			$formattedDate = date("m/d/Y", $timestamp);
		} else {
			$formattedDate = "";
		}
		return $formattedDate;
	}

	private function _escape($value) {

		$escapedValue = $value;
		if (strpos($escapedValue, ",") !== false) {
			if (strpos($escapedValue, '"') !== false) {
				$escapedValue = str_replace('"', '""', $escapedValue);
			}
			$escapedValue = '"' . $escapedValue . '"';
		}

		return $escapedValue;
	}

	/**
	 * Get the value for the given key from the map (array)
	 *
	 * @param array $map Associative array
	 * @param string $key Key to look for
	 *
	 * @return string value for given key from array or an empty string if not found.
	 */
	private function _getValueFromMap($map, $key) {

		$value = "";
		if (array_key_exists($key, $map)) {
			$value = $map[$key];
		}
		return $value;
	}
}
?>
