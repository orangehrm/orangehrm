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
 require_once ROOT_PATH . '/lib/models/eimadmin/JobSpec.php';

 class EXTRACTOR_JobSpec {

	public function parseAddData($postArr) {
		return $this->_parseCommonData($postArr);
	}

	public function parseEditData($postArr) {

		$jobSpec = $this->_parseCommonData($postArr);
		if (isset($postArr['txtId']) && !empty($postArr['txtId'])) {
			$jobSpec->setId(trim($postArr['txtId']));
		}

		return $jobSpec;
	}

	private function _parseCommonData($postArr) {

		$jobSpec = new JobSpec();

		if (isset($postArr['txtFieldName']) && !empty($postArr['txtFieldName'])) {
			$jobSpec->setName(trim($postArr['txtFieldName']));
		}

		if (isset($postArr['txtDesc']) && !empty($postArr['txtDesc'])) {
			$jobSpec->setDesc(trim($postArr['txtDesc']));
		}

		if (isset($postArr['txtDuties']) && !empty($postArr['txtDuties'])) {
			$jobSpec->setDuties(trim($postArr['txtDuties']));
		}

		return $jobSpec;
	}

}
?>