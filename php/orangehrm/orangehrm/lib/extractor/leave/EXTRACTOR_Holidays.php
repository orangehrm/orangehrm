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

require_once ROOT_PATH . '/lib/models/leave/Holidays.php';

class EXTRACTOR_Holidays {

	private $parent_Holidays;

	public function __construct() {
		$this->parent_Holidays = new Holidays();
	}

	public function parseAddData($postArr) {

		$postArr['txtDate'] = LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtDate']);

		$this->parent_Holidays->setDescription($postArr['txtDescription']);
		$this->parent_Holidays->setDate($postArr['txtDate']);

		if (isset($_POST['chkRecurring'])) {
			$this->parent_Holidays->setRecurring($postArr['chkRecurring']);
		} else {
			$this->parent_Holidays->setRecurring(Holidays::HOLIDAYS_NOT_RECURRING);
		}

		$this->parent_Holidays->setLength($postArr['sltLeaveLength']);

		return $this->parent_Holidays;
	}


	/**
	 * Pares edit data in the UI form
	 *
	 * @param mixed $postArr
	 * @return Leave[]
	 */
	public function parseEditData($postArr) {

		$postArr['txtDate'] = LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtDate']);

		if (isset($_POST['txtId']) && !empty($postArr['txtId'])) {
			$this->parent_Holidays->setHolidayId($postArr['txtId']);
		}

		$this->parent_Holidays->setDescription($postArr['txtDescription']);
		$this->parent_Holidays->setDate($postArr['txtDate']);

		if (isset($postArr['chkRecurring'])) {
			$this->parent_Holidays->setRecurring($postArr['chkRecurring']);
		} else {
			$this->parent_Holidays->setRecurring(Holidays::HOLIDAYS_NOT_RECURRING);
		}

		$this->parent_Holidays->setLength($postArr['sltLeaveLength']);

		return $this->parent_Holidays;
	}

	/**
	 * Pares delete data in the UI form
	 *
	 * @param mixed $postArr
	 * @return Leave[]
	 */
	public function parseDeleteData($postArr) {
		$objLeave = null;

		if (isset($postArr['deletHoliday'])) {
			for ($i=0; $i < count($postArr['deletHoliday']); $i++) {
				if (!empty($postArr['deletHoliday'][$i])) {
					$tmpObj = new Holidays();
					$tmpObj->setHolidayId($postArr['deletHoliday'][$i]);

					$objLeave[] = $tmpObj;
				}
			}
		}

		return $objLeave;
	}

}
?>