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
require_once ROOT_PATH . '/lib/models/eimadmin/import/CSVImportPlugin.php';
require_once ROOT_PATH . '/lib/models/eimadmin/CustomImport.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpDirectDebit.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpInfo.php';
require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';


/**
 * Class to generate CSV file for import to based on user defined
 * CustomImport.
 */
class CustomizableCSVImport implements CSVImportPlugin {

	const MAX_VAR_LENGTH_TO_PRINT = 25;

	/** Custom import object */
	private $customImport;
	private $usStateList;

	/**
	 * Construct instance of Customizable CSV Import
	 */
	public function __construct($id) {
		$this->customImport = CustomImport :: getCustomImport($id);
		if (empty ($this->customImport)) {
			throw new Exception("CustomImport with id = $id not found!");
		}
		$provinceInfo = new ProvinceInfo();
		$states = $provinceInfo->getProvinceCodes('US');
		$this->usStateList = array();

		foreach ($states as $state) {
			$this->usStateList[$state[1]] =	$state[2];
		}
	}

	/**
	 * Get descriptive name for this plugin
	 *
	 * @return Name for this plugin
	 */
	public function getName() {
		return $this->customImport->getName();
	}

	/** Get number of header rows to skip */
	public function getNumHeaderRows() {
		$hasHeader = $this->customImport->getContainsHeader();
		return $hasHeader ? 1 : 0;
	}

	/** Get number of csv columns expected */
	public function getNumColumns() {
		return $this->customImport->getFieldCount();
	}

	/**
	 * Import CSV data to the system
	 *
	 * @param array data Array containing one row of CSV data
	 */
	public function importCSVData($data) {

		$empinfo = new EmpInfo();

		$firstName = $this->_getField(CustomImport :: FIELD_FIRSTNAME, $data);
		$lastName = $this->_getField(CustomImport :: FIELD_LASTNAME, $data);

		if (empty ($firstName) || empty ($lastName)) {
			$compFields = implode(',', $this->customImport->getCompulsaryFields());
			throw new CSVImportException("Following fields are compulsary: " . $compFields, CSVImportException :: COMPULSARY_FIELDS_MISSING_DATA);
		}

		$empinfo->setEmpFirstName($firstName);
		$empinfo->setEmpLastName($lastName);

		$middleName = $this->_getField(CustomImport :: FIELD_MIDDLENAME, $data);
		$empinfo->setEmpMiddleName($middleName);

		$empId = $this->_getField(CustomImport :: FIELD_EMPID, $data);
		if (empty ($empId)) {
			$empId = $empinfo->getLastId();
		}

		// check for duplicate employee ID
		if ($empinfo->checkIfEmpIDInUse($empId)) {
			throw new CSVImportException("Employee ID is in use: $empId", CSVImportException::DUPLICATE_EMPLOYEE_ID);
		}
		$empinfo->setEmployeeID($empId);

		// Check for duplicate employee name
		if ($empinfo->checkForEmployeeWithSameName($lastName, $firstName, $middleName)) {
			throw new CSVImportException("Employee with same name exists: $lastName, $firstName $middleName", CSVImportException::DUPLICATE_EMPLOYEE_NAME);
		}

		$dobValue = $this->_getField(CustomImport :: FIELD_BIRTHDATE, $data);
		$dob = self::_getFormattedDate($dobValue);
		if ($dob) {
			$empinfo->setEmpDOB($dob);
		}

		$custom1 = $this->_getField(CustomImport :: FIELD_CUSTOM1, $data);
		if ($custom1) {
			$empinfo->setCustom1($custom1);
		}

		$custom2 = $this->_getField(CustomImport :: FIELD_CUSTOM2, $data);
		if ($custom2) {
			$empinfo->setCustom1($custom2);
		}

		$custom3 = $this->_getField(CustomImport :: FIELD_CUSTOM3, $data);
		if ($custom3) {
			$empinfo->setCustom1($custom3);
		}

		$custom4 = $this->_getField(CustomImport :: FIELD_CUSTOM4, $data);
		if ($custom4) {
			$empinfo->setCustom1($custom4);
		}

		$custom5 = $this->_getField(CustomImport :: FIELD_CUSTOM5, $data);
		if ($custom5) {
			$empinfo->setCustom1($custom5);
		}

		$custom6 = $this->_getField(CustomImport :: FIELD_CUSTOM6, $data);
		if ($custom6) {
			$empinfo->setCustom1($custom6);
		}

		$custom7 = $this->_getField(CustomImport :: FIELD_CUSTOM7, $data);
		if ($custom7) {
			$empinfo->setCustom1($custom7);
		}

		$custom8 = $this->_getField(CustomImport :: FIELD_CUSTOM8, $data);
		if ($custom8) {
			$empinfo->setCustom1($custom8);
		}

		$custom9 = $this->_getField(CustomImport :: FIELD_CUSTOM9, $data);
		if ($custom9) {
			$empinfo->setCustom1($custom9);
		}

		$custom10 = $this->_getField(CustomImport :: FIELD_CUSTOM10, $data);
		if ($custom10) {
			$empinfo->setCustom1($custom10);
		}

		$joinedValue = $this->_getField(CustomImport :: FIELD_JOINEDDATE, $data);
		$joined = self::_getFormattedDate($joinedValue);
		if ($joined) {
			$empinfo->setEmpJoinedDate($joined);
		}

		$genderValues = array (
			1 => "M",
			2 => "F"
		);

		$genderVal = $this->_getField(CustomImport :: FIELD_GENDER, $data, false);
		if (!empty($genderVal)) {
			$gender = self::_getKeyFromMap($genderValues, $genderVal);

			if (empty($gender)) {
				throw new CSVImportException("Invalid value for gender: $genderVal", CSVImportException::INVALID_TYPE);
			}
			$empinfo->setEmpGender($gender);
		}

		$ssn = $this->_getField(CustomImport :: FIELD_SSN, $data);
		if ($ssn) {
			$empinfo->setEmpSSNNo($ssn);
		}

		$street1 = $this->_getField(CustomImport :: FIELD_STREET1, $data);
		if ($street1) {
			$empinfo->setEmpStreet1($street1);
		}

		$street2 = $this->_getField(CustomImport :: FIELD_STREET2, $data);
		if ($street2) {
			$empinfo->setEmpStreet2($street2);
		}

		$city = $this->_getField(CustomImport :: FIELD_CITY, $data);
		if ($city) {
			$empinfo->setEmpCity($city);
		}

		$state = $this->_getField(CustomImport :: FIELD_STATE, $data);
		if ($state) {
			$empinfo->setEmpProvince($state);
		}

		$zipCode = $this->_getField(CustomImport :: FIELD_ZIP, $data);
		if ($zipCode) {
			$empinfo->setEmpZipCode($zipCode);
		}

		$homePhone = $this->_getField(CustomImport :: FIELD_HOME_PHONE, $data);
		if ($homePhone) {
			$empinfo->setEmpHomeTelephone($homePhone);
		}

		$mobile = $this->_getField(CustomImport :: FIELD_MOBILE_PHONE, $data);
		if ($mobile) {
			$empinfo->setEmpMobile($mobile);
		}

		$workPhone = $this->_getField(CustomImport :: FIELD_WORK_PHONE, $data);
		if ($workPhone) {
			$empinfo->setEmpWorkTelephone($workPhone);
		}

		$workEmail = $this->_getField(CustomImport :: FIELD_WORK_EMAIL, $data);
		if ($workEmail) {
			$empinfo->setEmpWorkEmail($workEmail);
		}

		$otherEmail = $this->_getField(CustomImport :: FIELD_OTHER_EMAIL, $data);
		if ($otherEmail) {
			$empinfo->setEmpOtherEmail($otherEmail);
		}

		$drivingLicence = $this->_getField(CustomImport :: FIELD_DRIVING_LIC, $data);
		if ($drivingLicence) {
			$empinfo->setEmpDriLicNo($drivingLicence);
		}

		$workStation = $this->_getField(CustomImport :: FIELD_WORKSTATION, $data);
		if (!empty($workStation)) {
			$workStationID = $this->_getCompStructure($workStation);
			$empinfo->setEmpLocation($workStationID);
		} else {
			$empinfo->setEmpLocation('');
		}

		// First direct debit account
		$routing = $this->_getField(CustomImport::FIELD_DD1ROUTING, $data);
		$account = $this->_getField(CustomImport::FIELD_DD1ACCOUNT, $data);
		$amount = $this->_getField(CustomImport::FIELD_DD1AMOUNT, $data);
		$accountType = $this->_getField(CustomImport::FIELD_DD1CHECKING, $data, false);
		$transactionType = $this->_getField(CustomImport::FIELD_DD1AMOUNTCODE, $data, false);

		$dd1 = $this->_getDirectDeposit($routing, $account, $amount, $accountType, $transactionType);

		// Second direct debit account
		$routing = $this->_getField(CustomImport::FIELD_DD2ROUTING, $data);
		$account = $this->_getField(CustomImport::FIELD_DD2ACCOUNT, $data);
		$amount = $this->_getField(CustomImport::FIELD_DD2AMOUNT, $data);
		$accountType = $this->_getField(CustomImport::FIELD_DD2CHECKING, $data, false);
		$transactionType = $this->_getField(CustomImport::FIELD_DD2AMOUNTCODE, $data, false);

		$dd2 = $this->_getDirectDeposit($routing, $account, $amount, $accountType, $transactionType);

		// Employee Tax information
		$federalTaxStatus = $this->_getField(CustomImport::FIELD_FITWSTATUS, $data);
		$fedEx = $this->_getField(CustomImport::FIELD_FITWEXCEMPTIONS, $data);
		$taxState = $this->_getField(CustomImport::FIELD_SITWSTATE, $data, false);
		$stateTaxStatus = $this->_getField(CustomImport::FIELD_SITWSTATUS, $data);
		$stateEx = $this->_getField(CustomImport::FIELD_SITWEXCEMPTIONS, $data);
		$unemploymentState = $this->_getField(CustomImport::FIELD_SUISTATE, $data, false);
		$workState = $this->_getField(CustomImport::FIELD_WORKSTATE, $data, false);

		$taxInfo = $this-> _getTaxInfo($federalTaxStatus, $fedEx, $taxState, $stateTaxStatus, $stateEx, $unemploymentState, $workState);

		$result = $empinfo->addEmpMain();
		if (!$result) {
			throw new CSVImportException("Error inserting employee", CSVImportException::UNKNOWN_ERROR);
		}
		$empNumber = $empinfo->getEmpId();

		$empinfo->setEmpEthnicRace(0);
		$empinfo->setEmpNation(0);
		$result = $empinfo->updateEmpPers();
		if (!$result) {
			throw new CSVImportException("Error inserting personal info", CSVImportException::UNKNOWN_ERROR);
		}

		$result = $empinfo->updateEmpContact();
		if (!$result) {
			throw new CSVImportException("Error inserting contact details", CSVImportException::UNKNOWN_ERROR);
		}

		$empinfo->setEmpStatus('0');
		$empinfo->setEmpJobTitle('0');
		$empinfo->setEmpEEOCat('0');

		$result = $empinfo->updateEmpJobInfo();
		if (!$result) {
			throw new CSVImportException("Error inserting job details", CSVImportException::UNKNOWN_ERROR);
		}

		// Save tax information
		if (!empty($taxInfo)) {
			$taxInfo->setEmpNumber($empNumber);
			$taxInfo->updateEmpTax();
		}

		// Save Direct Debit information
		if (!empty($dd1)) {
			$dd1->setEmpNumber($empNumber);
			$dd1->add();
		}

		if (!empty($dd2)) {
			$dd2->setEmpNumber($empNumber);
			$dd2->add();
		}

		return $empNumber;
	}

	/**
	 * Get company structure
	 *
	 * Throws exception if matching company structure location not found.
	 *
	 * @param string $workStation
	 * @return $string ID of matching company structure location.
	 */
	private function _getCompStructure($workStation) {

		$conn = new DMLFunctions();
		$workStation = mysql_real_escape_string($workStation);
		$sql = "SELECT id FROM `hs_hr_compstructtree` WHERE `title` LIKE '" . $workStation . "%'";

		$result = $conn->executeQuery($sql);
		if ($result && mysql_num_rows($result) == 1) {
			$row = mysql_fetch_row($result);
			return $row[0];
		} else {
			throw new CSVImportException("Company structure: " . $workStation . " not found", CSVImportException::MISSING_WORKSTATION);
		}
	}

	/**
	 * Get the given field from the data row
	 * @param string $fieldName The field name
	 * @param array $data Array containing data for one CSV row
	 * @param boolean $checkLength Should the length be checked?
	 *
	 * @return string value of field or null if field not defined
	 */
	private function _getField($fieldName, $data, $checkLength = true) {
		$value = null;

		$assignedFields = $this->customImport->getAssignedFields();
		$key = array_search($fieldName, $assignedFields);

		if ($key !== FALSE) {
			$value = $data[$key];

			if ($checkLength) {
				$customImport = new CustomImport();
				if (!$customImport->checkFieldLength($fieldName, $value)) {

					if (strlen($value) > self::MAX_VAR_LENGTH_TO_PRINT) {
						$valueSample = substr($value, 0, self::MAX_VAR_LENGTH_TO_PRINT) . '...';
					} else {
						$valueSample = $value;
					}

					throw new CSVImportException("Value '{$valueSample}' exceeds max length for field: $fieldName", CSVImportException::FIELD_TOO_LONG);
				}
			}
		}
		return $value;
	}

	/**
	 * Format and return date
	 * @param string $date date string
	 * @return string formatted date or null if not a valid date
	 */
	private static function _getFormattedDate($date) {

		$formattedDate = null;
		$fmt = str_replace(' ', '/', $date);
		$fmt = str_replace('-', '/', $date);
		$dateStamp = strtotime($fmt);
		if ($dateStamp !== FALSE) {
			$formattedDate = date("Y-m-d", $dateStamp);
		}
		return $formattedDate;
	}

	/**
	 * Get a DirectDeposit object after validating input
	 * Throws an error on invalid input data
	 *
	 * @return DirectDeposit object or null if none of the data is available.
	 */
	private function _getDirectDeposit($routing, $account, $amount, $accountType, $transactionType) {

		// If all empty, return null
		if (empty($routing) && empty($account)  && empty($amount) && empty($accountType) && empty($transactionType)) {
			return null;
		}

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

		$accountType = self::_getKeyFromMap($accountTypes, $accountType);
		$transactionType = self::_getKeyFromMap($transactionTypes, $transactionType);

		if (empty($routing) || empty($account)  || empty($amount) || empty($accountType) || empty($transactionType)) {

			$invalid = array();
			if (empty($routing)) {
				$invalid[] = "Routing Number";
			}
			if (empty($account)) {
				$invalid[] = "Account";
			}
			if (empty($amount)) {
				$invalid[] = "Amount";
			}
			if (empty($accountType)) {
				$invalid[] = "AccountType";
			}
			if (empty($transactionType)) {
				$invalid[] = "TransactionType";
			}

			$msg = implode(',', $invalid);

			// If at least one parameter is specifiec, all the rest should be specified as well.
			throw new CSVImportException("Direct Debit data not complete invalid. Check following fields: $msg", CSVImportException::DD_DATA_INCOMPLETE);
		} else {

			// Validate routing number and amount
			if (!CommonFunctions::isInt($routing)) {
				throw new CSVImportException("Routing number not an integer: $routing", CSVImportException::INVALID_TYPE);
			}

			if (!is_numeric($amount)) {
				throw new CSVImportException("Amount not a number: $amount", CSVImportException::INVALID_TYPE);
			}

			$dd = new EmpDirectDebit();
			$dd->setRoutingNumber($routing);
			$dd->setAccount($account);
			$dd->setAmount($amount);
			$dd->setAccountType($accountType);
			$dd->setTransactionType($transactionType);
			return $dd;
		}
	}

	private function _getTaxInfo($federalTaxStatus, $fedEx, $taxState, $stateTaxStatus, $stateEx, $unemploymentState, $workState) {

		$allStatuses = array(EmpTax::TAX_STATUS_MARRIED,EmpTax::TAX_STATUS_SINGLE,
						EmpTax::TAX_STATUS_NONRESIDENTALIEN, EmpTax::TAX_STATUS_NOTAPPLICABLE);

		$taxInfo = new EmpTax();
		if (!empty($federalTaxStatus)) {
			if (array_search($federalTaxStatus, $allStatuses) === FALSE) {
				throw new CSVImportException("Invalid Federal tax status value $federalTaxStatus", CSVImportException::INVALID_TYPE);
			}
			$taxInfo->setFederalTaxStatus($federalTaxStatus);
		}

  		if (!empty($fedEx)) {
			if (!CommonFunctions::isInt($fedEx)) {
				throw new CSVImportException("Federal tax exemptions should be an integer: $fedEx", CSVImportException::INVALID_TYPE);
			}

			$taxInfo->setFederalTaxExceptions($fedEx);
		}
		if (!empty($taxState)) {
			$state = $this->_getUSState($taxState);
			$taxInfo->setTaxState($state);
		}
		if (!empty($stateTaxStatus)) {
			if (array_search($federalTaxStatus, $allStatuses) === FALSE) {
				throw new CSVImportException("Invalid State tax status value $stateTaxStatus", CSVImportException::INVALID_TYPE);
			}
			$taxInfo->setStateTaxStatus($stateTaxStatus);
		}
		if (!empty($stateEx)) {
			if (!CommonFunctions::isInt($stateEx)) {
				throw new CSVImportException("State tax exemptions should be an integer: $stateEx", CSVImportException::INVALID_TYPE);
			}
			$taxInfo->setStateTaxExceptions($stateEx);
		}
		if (!empty($unemploymentState)) {
			$state = $this->_getUSState($unemploymentState);
			$taxInfo->setTaxUnemploymentState($state);
		}
		if (!empty($workState)) {
			$state = $this->_getUSState($workState);
			$taxInfo->setTaxWorkState($state);
		}
		return $taxInfo;
	}

	/**
	 * Check if passed value is a US State name or abbreviation and return the
	 * correct state abbreviation. Throws an exception if not a correct state abbreviation
	 *
	 * @param string $state State name or abreviation
	 * @return string state abbreviation
	 */
	private function _getUSState($state) {

		if (array_key_exists($state, $this->usStateList)) {
			return $state;
		}

		$key = array_search($state, $this->usStateList);
		if ($key === false) {
			throw new CSVImportException("Invalid value for state: $state", CSVImportException::INVALID_TYPE);
		}
		return $key;
	}

	/**
	 * Get the key for the given value from the map (array)
	 *
	 * @param array $map Associative array
	 * @param string $value Value to look for
	 *
	 * @return string value of the key or null if not found.
	 */
	private static function _getKeyFromMap($map, $value) {

		$key = array_search($value, $map);
		return ($key === FALSE) ? null : $key;
	}
}
?>
