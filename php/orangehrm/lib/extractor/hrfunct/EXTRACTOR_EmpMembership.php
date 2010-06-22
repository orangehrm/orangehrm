<?php
/*
// OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
// all the essential functionalities required for any enterprise.
// Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com

// OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
// the GNU General Public License as published by the Free Software Foundation; either
// version 2 of the License, or (at your option) any later version.

// OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
// without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU General Public License for more details.

// You should have received a copy of the GNU General Public License along with this program;
// if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
// Boston, MA  02110-1301, USA
*/

require_once ROOT_PATH . '/lib/models/hrfunct/EmpMembership.php';

class EXTRACTOR_EmpMembership {

	private $txtEmpid;
	private $cmbMemCode;
	private $cmbMemTypeCode;
	private $cmbMemSubOwn;
	private $txtMemSubAmount;
	private $txtMemCommDat;
	private $txtMemRenDat;

	public function __construct() {
		$this->empmemship = new EmpMembership();
	}

	public function parseData($postArr) {

		$postArr['txtMemCommDat']=LocaleUtil::getInstance()->convertToStandardDateFormat(CommonFunctions::cleanParam($postArr['txtMemCommDat']));
		$postArr['txtMemRenDat']=LocaleUtil::getInstance()->convertToStandardDateFormat(CommonFunctions::cleanParam($postArr['txtMemRenDat']));

		$this->empmemship->setEmpId(CommonFunctions::cleanParam($postArr['txtEmpID']));
		$this->empmemship->setEmpMemCode(CommonFunctions::cleanParam($postArr['cmbMemCode'], 13));
		$this->empmemship->setEmpMemTypeCode(CommonFunctions::cleanParam($postArr['cmbMemTypeCode'], 13));
		$this->empmemship->setEmpMemSubOwn(CommonFunctions::cleanParam($postArr['cmbMemSubOwn'], 20));
        $subAmount = CommonFunctions::cleanParam($postArr['txtMemSubAmount']);
		$subAmount = ($subAmount == "") ? 0 : $subAmount;

		$this->empmemship->setEmpMemSubAmount($subAmount);
		$this->empmemship->setEmpMemCommDat(self::_handleEmptyDates($postArr['txtMemCommDat']));
		$this->empmemship->setEmpMemRenDat(self::_handleEmptyDates($postArr['txtMemRenDat']));

		return $this->empmemship;

	}


	public function reloadData($postArr) {

		$this->txtEmpid= (CommonFunctions::cleanParam($postArr['txtEmpID']));
		$this->cmbMemCode = CommonFunctions::cleanParam($postArr['cmbMemCode'], 13);
		$this->cmbMemTypeCode = CommonFunctions::cleanParam($postArr['cmbMemTypeCode'], 13);
		$this->cmbMemSubOwn = CommonFunctions::cleanParam($postArr['cmbMemSubOwn'], 20);
        $subAmount = CommonFunctions::cleanParam($postArr['txtMemSubAmount']);
		$this->txtMemSubAmount = $subAmount == "" ? 0 : $subAmount;
		$this->txtMemCommDat = self::_handleEmptyDates($postArr['txtMemCommDat']);
		$this->txtMemRenDat = self::_handleEmptyDates($postArr['txtMemRenDat']);

		return $this;

	}

	private static function _handleEmptyDates($date) {

		$date = trim($date);

	    if ($date == "" || $date == "YYYY-mm-DD" || $date == "0000-00-00") {
			return "null";
	    } else {
	        return "'".$date."'";
	    }

	}

}
?>
