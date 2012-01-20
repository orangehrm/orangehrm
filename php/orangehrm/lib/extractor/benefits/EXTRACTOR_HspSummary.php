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

require_once ROOT_PATH . '/lib/models/benefits/HspSummary.php';

class EXTRACTOR_HspSummary {

	public static function parseHspSaveData($postArr) {

		for ($i=0; $i<count($postArr['hidSummaryId']); $i++) {

			$hspSummaryObj = new HspSummary();

			$hspSummaryObj->setSummaryId($postArr['hidSummaryId'][$i]);

			if (!empty($postArr['hidEmployeeId'][$i]) && is_numeric($postArr['hidEmployeeId'][$i])) {
				$hspSummaryObj->setEmployeeId($postArr['hidEmployeeId'][$i]);
			} else {
				$hspSummaryObj->setEmployeeId(null);
			}

			if (!empty($postArr['txtAnnualLimit'][$i]) && is_numeric($postArr['txtAnnualLimit'][$i])) {
				$hspSummaryObj->setAnnualLimit($postArr['txtAnnualLimit'][$i]);
			} else {
				$hspSummaryObj->setAnnualLimit(0);
			}

			if (!empty($postArr['txtEmployerAmount'][$i]) && is_numeric($postArr['txtEmployerAmount'][$i])) {
				$hspSummaryObj->setEmployerAmount($postArr['txtEmployerAmount'][$i]);
			} else {
				$hspSummaryObj->setEmployerAmount(0);
			}

			if (!empty($postArr['txtEmployeeAmount'][$i]) && is_numeric($postArr['txtEmployeeAmount'][$i])) {
				$hspSummaryObj->setEmployeeAmount($postArr['txtEmployeeAmount'][$i]);
			} else {
				$hspSummaryObj->setEmployeeAmount(0);
			}

			if (!empty($postArr['txtTotalAccrued'][$i]) && is_numeric($postArr['txtTotalAccrued'][$i])) {
				$hspSummaryObj->setTotalAccrued($postArr['txtTotalAccrued'][$i]);
			} else {
				$hspSummaryObj->setTotalAccrued(0);
			}

			if (!empty($postArr['txtTotalUsed'][$i]) && is_numeric($postArr['txtTotalUsed'][$i])) {
				$hspSummaryObj->setTotalUsed($postArr['txtTotalUsed'][$i]);
			} else {
				$hspSummaryObj->setTotalUsed(0);
			}

			$summaryObjArr[] = $hspSummaryObj;

		}

	return $summaryObjArr;

	}

	public static function parseSearchData($postArr) {

		if (isset($postArr['hidEmpNo'])) {
		    return $postArr['hidEmpNo'];
		}

	}
}
?>
