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

require_once ROOT_PATH . '/lib/models/benefits/Hsp.php';

class EXTRACTOR_Hsp {

	public function __construct() {
		// nothing to do
	}

	public static function parseSaveData($postArr) {
		$hspArr = array();

		for ($i=0; $i<count($postArr['txtHspId']); $i++) {
			$tmpHsp = new Hsp();
			if (!empty($postArr['txtHspId'][$i])) {
				$tmpHsp->setId($postArr['txtHspId'][$i]);
			}
			$tmpHsp->setAllotmentId($postArr['txtAllotmentId'][$i]);
			$tmpHsp->setEmployeeId($postArr['txtEmployeeId'][$i]);
			$tmpHsp->setHspValue($postArr['txtHspValue'][$i]);
			$tmpHsp->setEditedStatus($postArr['editedStatus'][$i]);

			if(isset($postArr['txtAmountPerDay'][$i])) {
				if($postArr['txtAmountPerDay'][$i]  != $postArr['initialAmountPerDay'][$i]){
					$tmpHsp->setEditedStatus(1);
				}
				$tmpHsp->setAmountPerDay($postArr['txtAmountPerDay'][$i]);
			}

			$editedStatus = $tmpHsp->getEditedStatus();
			if (isset($postArr['payDays'][$i]) && $editedStatus == 0) {
					$amountPerDay = ($postArr['txtHspValue'][$i] - $postArr['txtTotalAcrued'][$i]) / $postArr['payDays'][$i];
					$tmpHsp->setAmountPerDay($amountPerDay);


			} else if(isset($postArr['txtAmountPerDay'][$i])){
				$tmpHsp->setAmountPerDay($postArr['txtAmountPerDay'][$i]);
			}
			$tmpHsp->setTotalAcrued($postArr['txtTotalAcrued'][$i]);

			$hspArr[] = $tmpHsp;

		}

		return $hspArr;
	}
}
?>
