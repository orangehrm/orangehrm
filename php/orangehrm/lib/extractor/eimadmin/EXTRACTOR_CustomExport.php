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
 require_once ROOT_PATH . '/lib/models/eimadmin/CustomExport.php';

 class EXTRACTOR_CustomExport {

	function parseAddData($postArr) {
		$export = new CustomExport();

		if (isset($postArr['txtFieldName']) && !empty($postArr['txtFieldName'])) {
			$export->setName(trim($postArr['txtFieldName']));
		}

		if (isset($postArr['cmbAssignedFields']) && is_array($postArr['cmbAssignedFields'])) {
			$export->setAssignedFields($postArr['cmbAssignedFields']);
		}

		if (isset($postArr['headerValues']) && is_array($postArr['headerValues'])) {
			$export->setHeadings($postArr['headerValues']);
		}

		return $export;
	}

	function parseEditData($postArr) {
		$export = new CustomExport();

		if (isset($postArr['txtId']) && !empty($postArr['txtId'])) {
			$export->setId(trim($postArr['txtId']));
		}

		if (isset($postArr['txtFieldName']) && !empty($postArr['txtFieldName'])) {
			$export->setName(trim($postArr['txtFieldName']));
		}

		if (isset($postArr['cmbAssignedFields']) && is_array($postArr['cmbAssignedFields'])) {
			$export->setAssignedFields($postArr['cmbAssignedFields']);
		}

		if (isset($postArr['headerValues']) && is_array($postArr['headerValues'])) {
			$export->setHeadings($postArr['headerValues']);
		}

		return $export;
	}
}
?>