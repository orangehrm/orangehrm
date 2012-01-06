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

/**
 * Interface to be implemented by CSV import plugins
 */
interface CSVImportPlugin {


	/** Get descriptive name for this plugin */
	public function getName();

	/** Get number of header rows to skip */
	public function getNumHeaderRows();

	/** Get number of csv columns expected */
	public function getNumColumns();

	/**
	 * Import CSV data to the system
	 *
	 * @param array dataRow Array containing one row of CSV data
	 */
	public function importCSVData($dataRow);
}

class CSVImportException extends Exception {
	const IMPORT_DATA_NOT_RECEIVED = 0;
	const COMPULSARY_FIELDS_MISSING_DATA = 1;
	const MISSING_WORKSTATION = 2;
	const UNKNOWN_ERROR = 3;
	const DD_DATA_INCOMPLETE = 4;
	const INVALID_TYPE = 5;
	const DUPLICATE_EMPLOYEE_ID = 6;
	const DUPLICATE_EMPLOYEE_NAME = 7;
	const FIELD_TOO_LONG = 8;

}

?>
