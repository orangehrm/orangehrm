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

require_once ROOT_PATH . '/lib/models/hrfunct/EmpEmergencyCon.php';

class EXTRACTOR_EmpEmergencyCon {
		
	var $empId;
	var $empECSeqNo;
	var $empEConName;
	var $empEConRel;
	var $empEConHmTel;
	var $empEConMobile;
	var $empEConWorkTel;
	
	
	function EXTRACTOR_EmpEmergencyCon() {
		$this->econ = new EmpEmergencyCon();
	}
	
	function parseData($postArr) {
			
			$this->econ->setEmpId($postArr['txtEmpID']);
			$this->econ->setEmpECSeqNo(CommonFunctions::cleanParam($postArr['txtECSeqNo']));
			$this->econ->setEmpEConName(CommonFunctions::cleanParam($postArr['txtEConName'], 100));
			$this->econ->setEmpEConRel(CommonFunctions::cleanParam($postArr['txtEConRel'], 100));
			$this->econ->setEmpEConHmTel(CommonFunctions::cleanParam($postArr['txtEConHmTel'], 100));
			$this->econ->setEmpEConMobile(CommonFunctions::cleanParam($postArr['txtEConMobile'], 100));
			$this->econ->setEmpEConWorkTel(CommonFunctions::cleanParam($postArr['txtEConWorkTel'], 100));
			
									
			return $this->econ;
	}
	
	function reloadData($postArr) {	
	
			$this->txtEmpID			=	($postArr['txtEmpID']);
			$this->txtECSeqNo		=	(CommonFunctions::cleanParam($postArr['txtECSeqNo']));
			$this->txtEConName		=	(CommonFunctions::cleanParam($postArr['txtEConName'], 100));
			$this->txtEConRel		=	(CommonFunctions::cleanParam($postArr['txtEConRel'], 100));
			$this->txtEConHmTel		=	(CommonFunctions::cleanParam($postArr['txtEConHmTel'], 100));
			$this->txtEConMobile	=	(CommonFunctions::cleanParam($postArr['txtEConMobile'], 100));
			$this->txtEConWorkTel	=	(CommonFunctions::cleanParam($postArr['txtEConWorkTel'], 100));
			
			
			return $this;
	}
		
	
}
?>
