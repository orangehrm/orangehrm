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

require_once ROOT_PATH . '/lib/models/hrfunct/EmpWorkExp.php';

class EXTRACTOR_EmpWorkExp{

	public function EXTRACTOR_EmpWorkExp() {

		$this->empwrkexp = new EmpWorkExp();
	}

	public function parseData($postArr) {

		$postArr['txtEmpExpFromDate']=LocaleUtil::getInstance()->convertToStandardDateFormat(CommonFunctions::cleanParam($postArr['txtEmpExpFromDate']));
		$postArr['txtEmpExpToDate']=LocaleUtil::getInstance()->convertToStandardDateFormat(CommonFunctions::cleanParam($postArr['txtEmpExpToDate']));

		$this->empwrkexp->setEmpId(CommonFunctions::cleanParam($postArr['txtEmpID']));
    	$this->empwrkexp->setEmpExpSeqNo(CommonFunctions::cleanParam($postArr['txtEmpExpID']));
    	$this->empwrkexp->setEmpExpEmployer(CommonFunctions::cleanParam($postArr['txtEmpExpEmployer'], 100));
    	$this->empwrkexp->setEmpExpJobTitle(CommonFunctions::cleanParam($postArr['txtEmpExpJobTitle'], 120));
    	$this->empwrkexp->setEmpExpFromDate(self::_handleEmptyDates($postArr['txtEmpExpFromDate']));
    	$this->empwrkexp->setEmpExpToDate(self::_handleEmptyDates($postArr['txtEmpExpToDate']));
    	$this->empwrkexp->setEmpExpComments(CommonFunctions::cleanParam($postArr['txtEmpExpComments'], 200));
		$this->empwrkexp->setEmpExpInternal(isset($postArr['chkEmpExpInternal']) ? 1 : 0);

		return $this->empwrkexp;
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
