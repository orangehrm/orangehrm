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
require_once ROOT_PATH . '/lib/common/UniqueIDGenerator.php';
require_once ROOT_PATH . '/lib/common/SearchObject.php';

/**
 * Class representing a Job Application
 */
class JobApplication {

	const TABLE_NAME = 'hs_hr_job_application';

	/** Database fields */
	const DB_FIELD_ID = 'application_id';
	const DB_FIELD_VACANCY_ID = 'vacancy_id';
	const DB_FIELD_FIRSTNAME = 'firstname';
	const DB_FIELD_MIDDLENAME = 'middlename';
	const DB_FIELD_LASTNAME = 'lastname';
	const DB_FIELD_STREET1 = 'street1';
	const DB_FIELD_STREET2 = 'street2';
	const DB_FIELD_CITY = 'city';
	const DB_FIELD_COUNTRY_CODE = 'country_code';
	const DB_FIELD_PROVINCE = 'province';
	const DB_FIELD_ZIP = 'zip';
	const DB_FIELD_PHONE = 'phone';
	const DB_FIELD_MOBILE = 'mobile';
	const DB_FIELD_EMAIL = 'email';
	const DB_FIELD_QUALIFICATIONS = 'qualifications';

	private $dbFields = array(self::DB_FIELD_ID, self::DB_FIELD_VACANCY_ID, self::DB_FIELD_FIRSTNAME,
		self::DB_FIELD_MIDDLENAME, self::DB_FIELD_LASTNAME,	self::DB_FIELD_STREET1,	self::DB_FIELD_STREET2,
		self::DB_FIELD_CITY, self::DB_FIELD_COUNTRY_CODE, self::DB_FIELD_PROVINCE, self::DB_FIELD_ZIP,
		self::DB_FIELD_PHONE, self::DB_FIELD_MOBILE, self::DB_FIELD_EMAIL, self::DB_FIELD_QUALIFICATIONS);

	private $id;
	private $vacancyId;
	private $firstName;
	private $middleName;
	private $lastName;
	private $street1;
	private $street2;
	private $city;
	private $province;
	private $country;
	private $zip;
	private $phone;
	private $mobile;
	private $email;
	private $qualifications;

	/**
	 * Constructor
	 *
	 * @param int $id ID can be null for newly created job applications
	 */
	public function __construct($id = null) {
		$this->id = $id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getId() {
		return $this->id;
	}

	public function setVacancyId($vacancyId) {
		$this->vacancyId = $vacancyId;
	}

	public function getVacancyId() {
		return $this->vacancyId;
	}

	public function setFirstName($firstName) {
		$this->firstName = $firstName;
	}

	public function getFirstName() {
		return $this->firstName;
	}

	public function setMiddleName($middleName) {
		$this->middleName = $middleName;
	}

	public function getMiddleName() {
		return $this->middleName;
	}

	public function setLastName($lastName) {
		$this->lastName = $lastName;
	}

	public function getLastName() {
		return $this->lastName;
	}

	public function setStreet1($street1) {
		$this->street1 = $street1;
	}

	public function getStreet1() {
		return $this->street1;
	}

	public function setStreet2($street2) {
		$this->street2 = $street2;
	}

	public function getStreet2() {
		return $this->street2;
	}

	public function setCity($city) {
		$this->city = $city;
	}

	public function getCity() {
		return $this->city;
	}

	public function setProvince($province) {
		$this->province = $province;
	}

	public function getProvince() {
		return $this->province;
	}

	public function setCountry($country) {
		$this->country = $country;
	}

	public function getCountry() {
		return $this->country;
	}

	public function setZip($zip) {
		$this->zip = $zip;
	}

	public function getZip() {
		return $this->zip;
	}

	public function setPhone($phone) {
		$this->phone = $phone;
	}

	public function getPhone() {
		return $this->phone;
	}

	public function setMobile($mobile) {
		$this->mobile = $mobile;
	}

	public function getMobile() {
		return $this->mobile;
	}

	public function setEmail($email) {
		$this->email = $email;
	}

	public function getEmail() {
		return $this->email;
	}

	public function setQualifications($qualifications) {
		$this->qualifications = $qualifications;
	}

	public function getQualifications() {
	    return $this->qualifications;
	}

	/**
	 * Save JobApplication object to database
	 *
	 * If a new JobApplication, inserts into the database, otherwise, updates
	 * the existing entry.
	 *
	 * @return int Returns the ID of the JobApplication
	 */
    public function save() {

		if (empty($this->firstName) || empty($this->lastName) || empty($this->email) || empty($this->vacancyId)) {
			throw new JobApplicationException("Attributes not set", JobApplicationException::MISSING_PARAMETERS);
		}
		if (!CommonFunctions::isValidId($this->vacancyId)) {
		    throw new JobApplicationException("Invalid vacancy id", JobApplicationException::INVALID_PARAMETER);
		}

		if (isset($this->id)) {

			if (!CommonFunctions::isValidId($this->id)) {
			    throw new JobApplicationException("Invalid id", JobApplicationException::INVALID_PARAMETER);
			}
			return $this->_update();
		} else {
			return $this->_insert();
		}
    }


	/**
	 * Insert new object to database
	 */
	private function _insert() {

		$this->id = UniqueIDGenerator::getInstance()->getNextID(self::TABLE_NAME, self::DB_FIELD_ID);

		$sqlBuilder = new SQLQBuilder();
		$sqlBuilder->table_name = self::TABLE_NAME;
		$sqlBuilder->flg_insert = 'true';
		$sqlBuilder->arr_insert = $this->_getFieldValuesAsArray();
		$sqlBuilder->arr_insertfield = $this->dbFields;

		$sql = $sqlBuilder->addNewRecordFeature2();

		$conn = new DMLFunctions();

		$result = $conn->executeQuery($sql);
		if (!$result || (mysql_affected_rows() != 1)) {
			throw new JobApplicationException("Insert failed. ", JobApplicationException::DB_ERROR);
		}

		return $this->id;
	}

	/**
	 * Update existing object
	 */
	private function _update() {

		$values = $this->_getFieldValuesAsArray();
		$sqlBuilder = new SQLQBuilder();
		$sqlBuilder->table_name = self::TABLE_NAME;
		$sqlBuilder->flg_update = 'true';
		$sqlBuilder->arr_update = $this->dbFields;
		$sqlBuilder->arr_updateRecList = $this->_getFieldValuesAsArray();

		$sql = $sqlBuilder->addUpdateRecord1(0);

		$conn = new DMLFunctions();
		$result = $conn->executeQuery($sql);

		// Here we don't check mysql_affected_rows because update may be called
		// without any changes.
		if (!$result) {
			throw new JobApplicationException("Update failed. SQL=$sql", JobApplicationException::DB_ERROR);
		}
		return $this->id;
	}

	/**
	 * Returns the db field values as an array
	 *
	 * @return Array Array containing field values in correct order.
	 */
	private function _getFieldValuesAsArray() {

		$values[0] = $this->id;
		$values[1] = $this->vacancyId;
		$values[2] = $this->firstName;
		$values[3] = $this->middleName;
		$values[4] = $this->lastName;
		$values[5] = $this->street1;
		$values[6] = $this->street2;
		$values[7] = $this->city;
		$values[8] = $this->country;
		$values[9] = $this->province;
		$values[10] = $this->zip;
		$values[11] = $this->phone;
		$values[12] = $this->mobile;
		$values[13] = $this->email;
		$values[14] = $this->qualifications;

		return $values;
	}

}

class JobApplicationException extends Exception {
	const INVALID_PARAMETER = 0;
	const MISSING_PARAMETERS = 1;
	const DB_ERROR = 2;
}

?>
