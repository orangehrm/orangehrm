<?
/*
// OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
// all the essential functionalities required for any enterprise. 
// Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com

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
			$this->econ->setEmpECSeqNo(trim($postArr['txtECSeqNo']));
			$this->econ->setEmpEConName(trim($postArr['txtEConName']));
			$this->econ->setEmpEConRel(trim($postArr['txtEConRel']));
			$this->econ->setEmpEConHmTel(trim($postArr['txtEConHmTel']));
			$this->econ->setEmpEConMobile(trim($postArr['txtEConMobile']));
			$this->econ->setEmpEConWorkTel($postArr['txtEConWorkTel']);
			
									
			return $this->econ;
	}
	
	function reloadData($postArr) {	
	
			$this->txtEmpID			=	($postArr['txtEmpID']);
			$this->txtECSeqNo		=	(trim($postArr['txtECSeqNo']));
			$this->txtEConName		=	(trim($postArr['txtEConName']));
			$this->txtEConRel		=	(trim($postArr['txtEConRel']));
			$this->txtEConHmTel		=	(trim($postArr['txtEConHmTel']));
			$this->txtEConMobile	=	(trim($postArr['txtEConMobile']));
			$this->txtEConWorkTel	=	(trim($postArr['txtEConWorkTel']));
			
			
			return $this;
	}
		
	
}
?>
