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
 */
 require_once ROOT_PATH . '/lib/models/recruitment/JobApplication.php';

 class EXTRACTOR_JobApplication {

	/**
	 * Parse data from interface and return JobApplication Object
	 * @param Array $postArr Array containing POST values
	 * @return JobApplication Job Application object
	 */
	public function parseData($postArr) {

		$application = new JobApplication();
		if (isset($postArr['txtId']) && !empty($postArr['txtId'])) {
			$application->setId(trim($postArr['txtId']));
		}

		if (isset($postArr['txtVacancyId']) && !empty($postArr['txtVacancyId'])) {
			$application->setVacancyId(trim($postArr['txtVacancyId']));
		}

		if (isset($postArr['txtFirstName']) && !empty($postArr['txtFirstName'])) {
			$application->setFirstName(trim($postArr['txtFirstName']));
		}

		if (isset($postArr['txtMiddleName']) && !empty($postArr['txtMiddleName'])) {
			$application->setMiddleName(trim($postArr['txtMiddleName']));
		}

		if (isset($postArr['txtLastName']) && !empty($postArr['txtLastName'])) {
			$application->setLastName(trim($postArr['txtLastName']));
		}

		if (isset($postArr['txtStreet1']) && !empty($postArr['txtStreet1'])) {
			$application->setStreet1(trim($postArr['txtStreet1']));
		}
		if (isset($postArr['txtStreet2']) && !empty($postArr['txtStreet2'])) {
			$application->setStreet2(trim($postArr['txtStreet2']));
		}
		if (isset($postArr['txtCity']) && !empty($postArr['txtCity'])) {
			$application->setCity(trim($postArr['txtCity']));
		}
		if (isset($postArr['txtCountry']) && !empty($postArr['txtCountry'])) {
			$application->setCountry(trim($postArr['txtCountry']));
		}
		if (isset($postArr['txtProvince']) && !empty($postArr['txtProvince'])) {
			$application->setProvince(trim($postArr['txtProvince']));
		}
		if (isset($postArr['txtZip']) && !empty($postArr['txtZip'])) {
			$application->setZip(trim($postArr['txtZip']));
		}

		if (isset($postArr['txtPhone']) && !empty($postArr['txtPhone'])) {
			$application->setPhone(trim($postArr['txtPhone']));
		}

		if (isset($postArr['txtMobile']) && !empty($postArr['txtMobile'])) {
			$application->setMobile(trim($postArr['txtMobile']));
		}

		if (isset($postArr['txtEmail']) && !empty($postArr['txtEmail'])) {
			$application->setEmail(trim($postArr['txtEmail']));
		}

		if (isset($postArr['txtQualifications']) && !empty($postArr['txtQualifications'])) {
			$application->setQualifications(trim($postArr['txtQualifications']));
		}

		if ($_FILES['txtResume']['size'] > 0) {

			if ($_FILES['txtResume']['error'] > 0) {
			    $application->resumeData['error'] = $_FILES['txtResume']['error'];
			} else {
				$application->resumeData['name'] = $_FILES['txtResume']['name'];
				$application->resumeData['tmpName'] = $_FILES['txtResume']['tmp_name'];
				$application->resumeData['extension'] = strtolower(array_pop(explode(".", $_FILES['txtResume']['name'])));
				$application->resumeData['size'] = $_FILES['txtResume']['size'];
			}

		}

		return $application;
	}

}