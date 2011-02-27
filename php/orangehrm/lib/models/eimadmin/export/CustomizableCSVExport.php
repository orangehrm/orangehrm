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
require_once ROOT_PATH . '/lib/models/eimadmin/CustomExport.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpDirectDebit.php';
require_once ROOT_PATH . '/lib/models/eimadmin/export/CSVField.php';
require_once ROOT_PATH . '/lib/models/eimadmin/encryption/KeyHandlerOld.php';

/**
 * Class to generate CSV file for export to based on user defined
 * CustomExport.
 */
class CustomizableCSVExport implements CSVExportPlugin {

	const DD_ROUTING = 'Routing';
    const DD_ACCOUNT = 'Account';
    const DD_AMOUNT = 'Amount';
    const DD_AMOUNTCODE = 'AmountCode';
    const DD_CHECKING = 'Checking';

	/** Custom export object */
	private $customExport;

	/** Array mapping CSV field names to DB field names */
	private $fieldMap;

	/**
	 * Construct instance of Customizable CSV Export
	 */
	public function __construct($id) {
		$this->customExport = CustomExport::getCustomExport($id);
		if (empty($this->customExport)) {
			throw new Exception("CustomExport with id = $id not found!");
		}
		$this->fieldMap = self::_getFieldMap();
	}

	/**
	 * Get descriptive name for this plugin
	 *
	 * @return Name for this plugin
	 */
	public function getName() {
		return $this->customExport->getName();
	}

	/**
	 * Get CSV header
	 *
	 * @return CSV header
	 */
	public function getHeader() {
		$headerArr = $this->customExport->getHeadings();
		if (empty($headerArr)) {
			$headerArr = $this->customExport->getAssignedFields();
		}

		$header = implode(",", $headerArr);
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
		" pay.payperiod_code,sal.ebsal_basic_salary,loc.loc_name,comp.title as workstation" .
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



		// Get direct debit information
		$dd = new EmpDirectDebit();
		$ddList = $dd->getEmployeeDirectDebit($row['emp_number']);

		$csvRow = "";
		$firstField = true;

		$assignedFields = $this->customExport->getAssignedFields();

		foreach ($assignedFields as $field) {

			$csvField = $this->fieldMap[$field];
			$value = $csvField->getValue($row, $ddList);

			if ($firstField) {
				$firstField = false;
				$csvRow = $value;
			} else {
				$csvRow .= ',' . $value;
			}
		}
		return $csvRow;
	}

	/**
	 * Get mapping of CSV field names to DB field names
	 * @return Array Array mapping CSV field names to DB field names
	 */
	private static function _getFieldMap() {

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

		$map = array(CustomExport::FIELD_EMPID => new CSVField('employee_id', CSVField::FIELD_TYPE_DIRECT),
              CustomExport::FIELD_LASTNAME => new CSVField('emp_lastname', CSVField::FIELD_TYPE_DIRECT),
              CustomExport::FIELD_FIRSTNAME =>  new CSVField('emp_firstname', CSVField::FIELD_TYPE_DIRECT),
              CustomExport::FIELD_MIDDLENAME => new CSVField('emp_middle_name', CSVField::FIELD_TYPE_DIRECT),
              CustomExport::FIELD_STREET1 => new CSVField('emp_street1', CSVField::FIELD_TYPE_DIRECT),
              CustomExport::FIELD_STREET2 => new CSVField('emp_street2', CSVField::FIELD_TYPE_DIRECT),
              CustomExport::FIELD_CITY => new CSVField('city_code', CSVField::FIELD_TYPE_DIRECT),
              CustomExport::FIELD_STATE => new CSVField('provin_code', CSVField::FIELD_TYPE_DIRECT),
              CustomExport::FIELD_ZIP => new CSVField('emp_zipcode', CSVField::FIELD_TYPE_DIRECT),
              CustomExport::FIELD_GENDER => new CSVField('emp_gender', CSVField::FIELD_TYPE_FROMMAP, $genderValues),
              CustomExport::FIELD_BIRTHDATE => new CSVField('emp_birthday', CSVField::FIELD_TYPE_DATE),
              CustomExport::FIELD_SSN => new CSVField('emp_ssn_num', CSVField::FIELD_TYPE_DIRECT),
              CustomExport::FIELD_EMPSTATUS => new CSVField('emp_status', CSVField::FIELD_TYPE_DIRECT, $empStatuses),
              CustomExport::FIELD_JOINEDDATE => new CSVField('joined_date', CSVField::FIELD_TYPE_DATE),
              CustomExport::FIELD_WORKSTATION => new CSVField('workstation', CSVField::FIELD_TYPE_DIRECT),
              CustomExport::FIELD_LOCATION => new CSVField('loc_code', CSVField::FIELD_TYPE_DIRECT),
              CustomExport::FIELD_CUSTOM1 => new CSVField('custom1', CSVField::FIELD_TYPE_DIRECT),
              CustomExport::FIELD_CUSTOM2 => new CSVField('custom2', CSVField::FIELD_TYPE_DIRECT),
              CustomExport::FIELD_CUSTOM3 => new CSVField('custom3', CSVField::FIELD_TYPE_DIRECT),
              CustomExport::FIELD_CUSTOM4 => new CSVField('custom4', CSVField::FIELD_TYPE_DIRECT),
              CustomExport::FIELD_CUSTOM5 => new CSVField('custom5', CSVField::FIELD_TYPE_DIRECT),
              CustomExport::FIELD_CUSTOM6 => new CSVField('custom6', CSVField::FIELD_TYPE_DIRECT),
              CustomExport::FIELD_CUSTOM7 => new CSVField('custom7', CSVField::FIELD_TYPE_DIRECT),
              CustomExport::FIELD_CUSTOM8 => new CSVField('custom8', CSVField::FIELD_TYPE_DIRECT),
              CustomExport::FIELD_CUSTOM9 => new CSVField('custom9', CSVField::FIELD_TYPE_DIRECT),
              CustomExport::FIELD_CUSTOM10 => new CSVField('custom10', CSVField::FIELD_TYPE_DIRECT),
              CustomExport::FIELD_WORKSTATE => new CSVField('tax_work_state', CSVField::FIELD_TYPE_DIRECT),
              CustomExport::FIELD_SALARY => new CSVField('ebsal_basic_salary', CSVField::FIELD_TYPE_DIRECT),
              CustomExport::FIELD_PAYFREQUENCY => new CSVField('payperiod_code', CSVField::FIELD_TYPE_FROMMAP, $payPeriods),
              CustomExport::FIELD_FITWSTATUS => new CSVField('tax_federal_status', CSVField::FIELD_TYPE_DIRECT),
              CustomExport::FIELD_FITWEXCEMPTIONS => new CSVField('tax_federal_exceptions', CSVField::FIELD_TYPE_DIRECT),
              CustomExport::FIELD_SITWSTATE => new CSVField('tax_state', CSVField::FIELD_TYPE_DIRECT),
              CustomExport::FIELD_SITWSTATUS => new CSVField('tax_state_status', CSVField::FIELD_TYPE_DIRECT),
              CustomExport::FIELD_SITWEXCEMPTIONS => new CSVField('tax_state_exceptions', CSVField::FIELD_TYPE_DIRECT),
              CustomExport::FIELD_SUISTATE => new CSVField('tax_unemp_state', CSVField::FIELD_TYPE_DIRECT),

              CustomExport::FIELD_DD1ROUTING => new CSVField('DD1_' . self::DD_ROUTING, CSVField::FIELD_TYPE_DIRECT_DEBIT),
              CustomExport::FIELD_DD1ACCOUNT => new CSVField('DD1_' . self::DD_ACCOUNT, CSVField::FIELD_TYPE_DIRECT_DEBIT),
              CustomExport::FIELD_DD1AMOUNT => new CSVField('DD1_' . self::DD_AMOUNT, CSVField::FIELD_TYPE_DIRECT_DEBIT),
              CustomExport::FIELD_DD1AMOUNTCODE => new CSVField('DD1_' . self::DD_AMOUNTCODE, CSVField::FIELD_TYPE_DIRECT_DEBIT),
              CustomExport::FIELD_DD1CHECKING => new CSVField('DD1_' . self::DD_CHECKING, CSVField::FIELD_TYPE_DIRECT_DEBIT),

              CustomExport::FIELD_DD2ROUTING => new CSVField('DD2_' . self::DD_ROUTING, CSVField::FIELD_TYPE_DIRECT_DEBIT),
              CustomExport::FIELD_DD2ACCOUNT => new CSVField('DD2_' . self::DD_ACCOUNT, CSVField::FIELD_TYPE_DIRECT_DEBIT),
              CustomExport::FIELD_DD2AMOUNT => new CSVField('DD2_' . self::DD_AMOUNT, CSVField::FIELD_TYPE_DIRECT_DEBIT),
              CustomExport::FIELD_DD2AMOUNTCODE => new CSVField('DD2_' . self::DD_AMOUNTCODE, CSVField::FIELD_TYPE_DIRECT_DEBIT),
              CustomExport::FIELD_DD2CHECKING => new CSVField('DD2_' . self::DD_CHECKING, CSVField::FIELD_TYPE_DIRECT_DEBIT));

    	return $map;
	}


}
?>
