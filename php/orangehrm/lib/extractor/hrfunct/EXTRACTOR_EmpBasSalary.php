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

require_once ROOT_PATH . '/lib/models/hrfunct/EmpBasSalary.php';

class EXTRACTOR_EmpBasSalary{

	var $txtEmpID;
	var $txtSalGrdId;
	var $cmbCurrCode;
	var $txtBasSal;

	function EXTRACTOR_EmpBasSalary() {

		$this->empbassal = new EmpBasSalary();
			}

	function parseData($postArr) {

			$this->empbassal->setEmpId(CommonFunctions::cleanParam($postArr['txtEmpID']));
			$this->empbassal->setEmpSalGrdCode(CommonFunctions::cleanParam($postArr['cmbSalaryGrade']));
			$this->empbassal->setEmpCurrCode($postArr['cmbCurrCode']);
			$this->empbassal->setEmpBasSal(CommonFunctions::cleanParam($postArr['txtBasSal'], 100));
			$payPeriod = CommonFunctions::cleanParam($postArr['cmbPayPeriod']);
			if ($payPeriod > 0) {
				$this->empbassal->setPayPeriod($payPeriod);
			}
			if(isset($postArr['oldSalaryGrade']) && isset($postArr['oldCurrency'])){
				$oldObject = new EmpBasSalary();
				$oldObject->setEmpSalGrdCode(CommonFunctions::cleanParam($postArr['oldSalaryGrade']));
				$oldObject->setEmpCurrCode(CommonFunctions::cleanParam($postArr['oldCurrency']));
				$array = array();
				$array['new'] =  $this->empbassal;
				$array['old'] = $oldObject;
				return $array;
			}else{
				return $this->empbassal;
			}
	}

	function reloadData($postArr) {

			$this->txtEmpID = (CommonFunctions::cleanParam($postArr['txtEmpID']));
			$this->txtSalGrdId = (CommonFunctions::cleanParam($postArr['txtSalGrdId']));
			$this->cmbCurrCode = CommonFunctions::cleanParam($postArr['cmbCurrCode']);
			$this->txtBasSal = (CommonFunctions::cleanParam($postArr['txtBasSal'], 100));

			return $this;
	}

}
?>
