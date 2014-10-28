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
class PimCsvDataImport extends CsvDataImport {

	private $employeeService;
	private $nationalityService;
	private $countryService;

	public function import($data) {

		if ($data[0] == "" || $data[2] == "" || strlen($data[0]) > 30 || strlen($data[2]) > 30) {
			return false;
		}
		$employee = new Employee();
		$employee->setFirstName($data[0]);
		if (strlen($data[1]) <= 30) {
			$employee->setMiddleName($data[1]);
		}
		$employee->setLastName($data[2]);

		if (strlen($data[3]) <= 50) {
			$employee->setEmployeeId($data[3]);
		}
		if (strlen($data[4]) <= 30) {
			$employee->setOtherId($data[4]);
		}
		if (strlen($data[5]) <= 30) {
			$employee->setLicenseNo($data[5]);
		}
		if ($this->isValidDate($data[6])) {
			$employee->setEmpDriLiceExpDate($data[6]);
		}

		if (strtolower($data[7]) == 'male') {
			$employee->setEmpGender('1');
		} else if (strtolower($data[7]) == 'female') {
			$employee->setEmpGender('2');
		}

		if (strtolower($data[8]) == 'single') {
			$employee->setEmpMaritalStatus('Single');
		} else if (strtolower($data[8]) == 'married') {
			$employee->setEmpMaritalStatus('Married');
		} else if (strtolower($data[8]) == 'other') {
			$employee->setEmpMaritalStatus('Other');
		}

		$nationality = $this->isValidNationality($data[9]);
		if (!empty($nationality)) {
			$employee->setNationality($nationality);
		}
		if ($this->isValidDate($data[10])) {
			$employee->setEmpBirthday($data[10]);
		}
		if (strlen($data[11]) <= 70) {
			$employee->setStreet1($data[11]);
		}
		if (strlen($data[12]) <= 70) {
			$employee->setStreet2($data[12]);
		}
		if (strlen($data[13]) <= 70) {
			$employee->setCity($data[13]);
		}
		
		if (strlen($data[15]) <= 10) {
			$employee->setEmpZipcode($data[15]);
		}

		$code = $this->isValidCountry($data[16]);
		if (!empty($code)) {
			$employee->setCountry($code);
			if (strtolower($data[16]) == 'united states') {				
				$code = $this->isValidProvince($data[14]);
				if(!empty($code)){
					$employee->setProvince($code);
				}
			} else if (strlen($data[14]) <= 70) {
				$employee->setProvince($data[14]);
			}
		}
		if (strlen($data[17]) <= 25 && $this->isValidPhoneNumber($data[17])) {
			$employee->setEmpHmTelephone($data[17]);
		}
		if (strlen($data[18]) <= 25 && $this->isValidPhoneNumber($data[18])) {
			$employee->setEmpMobile($data[18]);
		}
		if (strlen($data[19]) <= 25 && $this->isValidPhoneNumber($data[19])) {
			$employee->setEmpWorkTelephone($data[19]);
		}
		if ($this->isValidEmail($data[20]) && strlen($data[20]) <= 50 && $this->isUniqueEmail($data[20])) {
			$employee->setEmpWorkEmail($data[20]);
		}
		if ($this->isValidEmail($data[21]) && strlen($data[21]) <= 50 && $this->isUniqueEmail($data[21])) {
			$employee->setEmpOthEmail($data[21]);
		}

		$empService = new EmployeeService();
		$empService->saveEmployee($employee);
		return true;
	}

	private function isValidEmail($email) {
		return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email);
	}

	private function isUniqueEmail($email) {

		$emailList = $this->getEmployeeService()->getEmailList();
		$isUnique = true;
		foreach ($emailList as $empEmail) {

			if ($empEmail['emp_work_email'] == $email || $empEmail['emp_oth_email'] == $email) {
				$isUnique = false;
			}
		}
		return $isUnique;
	}

	private function isValidDate($date) {
		if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $date)) {
			list($year, $month, $day) = explode('-', $date);
			return checkdate($month, $day, $year);
		} else {
			return false;
		}
	}

	private function isValidNationality($name) {

		$nationalities = $this->getNationalityService()->getNationalityList();

		foreach ($nationalities as $nationality) {
			if (strtolower($nationality->getName()) == strtolower($name)) {
				return $nationality;
			}
		}
	}

	private function isValidCountry($name) {

		$countries = $this->getCountryService()->getCountryList();

		foreach ($countries as $country) {
			if (strtolower($country->cou_name) == strtolower($name)) {
				return $country->cou_code;
			}
		}
	}
	
	private function isValidProvince($name) {

		$provinces = $this->getCountryService()->getProvinceList();
		
		foreach ($provinces as $province) {
			if (strtolower($province->province_name) == strtolower($name)) {
				return $province->province_code;
			}
		}
	}

	public function isValidPhoneNumber($number) {
		if (preg_match('/^\+?[0-9 \-]+$/', $number)) {
			return true;
		}
	}

	public function getCountryService() {
		if (is_null($this->countryService)) {
			$this->countryService = new CountryService();
		}
		return $this->countryService;
	}

	public function getNationalityService() {
		if (is_null($this->nationalityService)) {
			$this->nationalityService = new NationalityService();
		}
		return $this->nationalityService;
	}

	public function getEmployeeService() {
		if (is_null($this->employeeService)) {
			$this->employeeService = new EmployeeService();
			$this->employeeService->setEmployeeDao(new EmployeeDao());
		}
		return $this->employeeService;
	}

}

?>
