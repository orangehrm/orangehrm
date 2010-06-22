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

require_once ROOT_PATH . '/lib/models/hrfunct/EmpLicenses.php';

class EXTRACTOR_EmpLicenses{

	private $txtEmpId;
	private $cmbLicCode;
	private $txtEmpLicDat;
	private $txtEmpreDat;

	public function __construct() {
		$this->emplicen = new EmpLicenses();
	}

	public function parseData($postArr) {

		$postArr['txtEmpLicDat']=LocaleUtil::getInstance()->convertToStandardDateFormat(CommonFunctions::cleanParam($postArr['txtEmpLicDat']));
		$postArr['txtEmpreDat']=LocaleUtil::getInstance()->convertToStandardDateFormat(CommonFunctions::cleanParam($postArr['txtEmpreDat']));

		$this->emplicen->setEmpId(CommonFunctions::cleanParam($postArr['txtEmpID']));
    	$this->emplicen->setEmpLicCode(CommonFunctions::cleanParam($postArr['cmbLicCode'], 100));
    	$this->emplicen->setEmpLicDat(self::_handleEmptyDates($postArr['txtEmpLicDat']));
    	$this->emplicen->setEmpLicrenewalDat(self::_handleEmptyDates($postArr['txtEmpreDat']));

		return $this->emplicen;

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
