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
 require_once ROOT_PATH . '/lib/models/recruitment/JobVacancy.php';

 class EXTRACTOR_JobVacancy {

	/**
	 * Parse data from interface and return JobVacancy Object
	 * @param Array $postArr Array containing POST values
	 * @return JobVacancy Job Vacancy object
	 */
	public function parseData($postArr) {

		$vacancy = new JobVacancy();

		if (isset($postArr['txtId']) && !empty($postArr['txtId'])) {
			$vacancy->setId(trim($postArr['txtId']));
		}

		if (isset($postArr['cmbJobTitle']) && !empty($postArr['cmbJobTitle'])) {
			$vacancy->setJobTitleCode(trim($postArr['cmbJobTitle']));
		}

		if (isset($postArr['cmbHiringManager']) && !empty($postArr['cmbHiringManager'])) {
			$vacancy->setManagerId(trim($postArr['cmbHiringManager']));
		}

		if (isset($postArr['txtDesc']) && !empty($postArr['txtDesc'])) {
			$vacancy->setDescription(trim($postArr['txtDesc']));
		}

		if (isset($postArr['active']) && !empty($postArr['active'])) {
			$vacancy->setActive(true);
		}

		return $vacancy;
	}

}
?>