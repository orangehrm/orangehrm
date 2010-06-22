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

require_once ROOT_PATH . '/lib/models/hrfunct/EmpEducation.php';

class EXTRACTOR_EmpEducation {

	public function __construct() {

		$this->empeducation = new EmpEducation();
	}
    
	public function parseData($postArr) {

		$postArr['txtEmpEduStartDate']=LocaleUtil::getInstance()->convertToStandardDateFormat(CommonFunctions::cleanParam($postArr['txtEmpEduStartDate']));
		$postArr['txtEmpEduEndDate']=LocaleUtil::getInstance()->convertToStandardDateFormat(CommonFunctions::cleanParam($postArr['txtEmpEduEndDate']));

		$this->empeducation->setEmpId(CommonFunctions::cleanParam($postArr['txtEmpID']));
   		$this->empeducation->setEduCode(CommonFunctions::cleanParam($postArr['cmbEduCode'], 13));
   		$this->empeducation->setEduMajor(CommonFunctions::cleanParam($postArr['txtEmpEduMajor'], 100));
   		$this->empeducation->setEduYear(empty($postArr['txtEmpEduYear'])?'null':CommonFunctions::cleanParam($postArr['txtEmpEduYear']));
   		$this->empeducation->setEduGPA(CommonFunctions::cleanParam($postArr['txtEmpEduGPA'], 25));
   		$this->empeducation->setEduStartDate(self::_handleEmptyDates($postArr['txtEmpEduStartDate']));
   		$this->empeducation->setEduEndDate(self::_handleEmptyDates($postArr['txtEmpEduEndDate']));

		return $this->empeducation;
	}

	private static function _handleEmptyDates($date) {

		$date = trim($date);

	    if ($date == "" || $date == "YYYY-mm-DD" || $date == "0000-00-00") {
			return 'null';
	    } else {
	        return "'".$date."'";
	    }

	}

}
?>
