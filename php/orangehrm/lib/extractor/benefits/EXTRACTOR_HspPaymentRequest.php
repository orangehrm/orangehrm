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

require_once ROOT_PATH . '/lib/models/benefits/HspPaymentRequest.php';

class EXTRACTOR_HspPaymentRequest {

	public function __construct() {
		// nothing to do
	}

	public static function parseSaveData($postArr) {

		$hspPaymentRequest = new HspPaymentRequest();

		if (!empty($postArr['txtId'])) {
			$hspPaymentRequest->setId($postArr['txtId']);
		}
		if (!empty($postArr['txtEmployeeId'])) {
			$hspPaymentRequest->setEmployeeId($postArr['txtEmployeeId']);
		}

		if (!empty($postArr['cmbPlanName'])) {
			$hspPlanName = $postArr['cmbPlanName'];
		} else {
			if (!empty($postArr['hidPlanName'])) {
				$hspPlanName = $postArr['hidPlanName'];
			}
		}
		$hspPaymentRequest->setHspId(DefineHsp::getHspPlanId($hspPlanName));

		
		if (!empty($postArr['txtDateIncurred'])) {
			$hspPaymentRequest->setDateIncurred($postArr['txtDateIncurred']);
		}
		if (!empty($postArr['txtProviderName'])) {
			$hspPaymentRequest->setProviderName($postArr['txtProviderName']);
		}
		if (!empty($postArr['txtPersonIncurringExpense'])) {
			$hspPaymentRequest->setPersonIncurringExpense($postArr['txtPersonIncurringExpense']);
		}
		if (!empty($postArr['txtExpenseDescription'])) {
			$hspPaymentRequest->setExpenseDescription($postArr['txtExpenseDescription']);
		}
		if (!empty($postArr['txtExpenseAmount'])) {
			$hspPaymentRequest->setExpenseAmount($postArr['txtExpenseAmount']);
		}
		if (!empty($postArr['txtPaymentMadeTo'])) {
			$hspPaymentRequest->setPaymentMadeTo($postArr['txtPaymentMadeTo']);
		}
		if (!empty($postArr['txtThirdPartyAccountNumber'])) {
			$hspPaymentRequest->setThirdPartyAccountNumber($postArr['txtThirdPartyAccountNumber']);
		}
		if (!empty($postArr['txtMailAddress'])) {
			$hspPaymentRequest->setMailAddress($postArr['txtMailAddress']);
		}
		if (!empty($postArr['txtComments'])) {
			$hspPaymentRequest->setComments($postArr['txtComments']);
		}
		if (!empty($postArr['txtDatePaid'])) {
			$hspPaymentRequest->setDatePaid($postArr['txtDatePaid']);
		}
		if (!empty($postArr['txtCheckNumber'])) {
			$hspPaymentRequest->setCheckNumber($postArr['txtCheckNumber']);
		}
		if (!empty($postArr['checkPaperworkSubmitted'])) {
			$hspPaymentRequest->setPaperWorkSubmitted($postArr['checkPaperworkSubmitted']);
		}
		if (!empty($postArr['txtHrNotes'])) {
			$hspPaymentRequest->setHrNotes($postArr['txtHrNotes']);
		}

		return $hspPaymentRequest;
	}
}
?>
