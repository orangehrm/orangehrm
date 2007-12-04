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

require_once ROOT_PATH . '/lib/models/hrfunct/EmpChildren.php';

class EXTRACTOR_EmpChildren {

	var $empId;
	var $empCSeqNo;
	var $empChiName;
	var $empDOB;



	function EXTRACTOR_EmpChildren() {

		$this->chi = new EmpChildren();
	}

	function parseData($postArr) {

		$postArr['ChiDOB']=LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['ChiDOB']);

		$this->chi->setEmpId($postArr['txtEmpID']);
		$this->chi->setEmpCSeqNo(trim($postArr['txtCSeqNo']));
		$this->chi->setEmpChiName(trim($postArr['txtChiName']));
		$this->chi->setEmpDOB(trim($postArr['ChiDOB']));

			return $this->chi;
	}

	function reloadData($postArr) {

		$postArr['ChiDOB']=LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['ChiDOB']);

		$this->txtEmpID		=	($postArr['txtEmpID']);
		$this->txtDSeqNo	=	(trim($postArr['txtDSeqNo']));
		$this->txtChiName	=	(trim($postArr['txtChiName']));
		$this->DOB			=	(trim($postArr['ChiDOB']));

		return $this;
	}


}
?>
