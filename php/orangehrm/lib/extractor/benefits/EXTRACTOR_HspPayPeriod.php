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

require_once ROOT_PATH . '/lib/models/benefits/HspPayPeriod.php';

class EXTRACTOR_HspPayPeriod {

	public function __construct() {
		// nothing to do
	}

	public static function parseAddData($postArr) {
		$payPeriod = new HspPayPeriod();

		$payPeriod->setStartDate(LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtPayPeriodFromDate']));
		$payPeriod->setEndDate(LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtPayPeriodToDate']));
		$payPeriod->setCloseDate(LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtPayPeriodCloseDate']));
		$payPeriod->setTimesheetAprovalDueDate(LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtPayPeriodTimesheetDueDate']));
		$payPeriod->setCheckDate(LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtPayPeriodCheckDate']));

		return $payPeriod;
	}

	public static function parseEditData($postArr) {
		$payPeriod = new HspPayPeriod();

		$payPeriod->setId($postArr['txtPayPeriodId']);
		$payPeriod->setStartDate(LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtPayPeriodFromDate']));
		$payPeriod->setEndDate(LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtPayPeriodToDate']));
		$payPeriod->setCloseDate(LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtPayPeriodCloseDate']));
		$payPeriod->setTimesheetAprovalDueDate(LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtPayPeriodTimesheetDueDate']));
		$payPeriod->setCheckDate(LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtPayPeriodCheckDate']));

		return $payPeriod;
	}

	public static function parseDeleteData($postArr) {
		$payPeriods = array();
		if (is_array($postArr['chkPayPeriodId'])) {
			for ($i=0; $i<count($postArr['chkPayPeriodId']); $i++) {
				$tmpObj = new PayPeriod();
				$tmpObj->setId($postArr['chkPayPeriodId'][$i]);

				$payPeriods[] = $tmpObj;
			}
		}

		return $payPeriods;
	}
}
?>
