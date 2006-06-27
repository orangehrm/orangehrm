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

require_once ROOT_PATH . '/lib/models/hrfunct/EmpMembership.php';

class EXTRACTOR_EmpMembership{
	
	var $txtEmpid;
	var $cmbMemCode;
	var $cmbMemTypeCode;
	var $cmbMemSubOwn;
	var $txtMemSubAmount;
	var $txtMemCommDat;
	var $txtMemRenDat;
	
	function EXTRACTOR_EmpMembership() {

		$this->empmemship = new EmpMembership();
	}

	function parseData($postArr) {	
			
			$this->empmemship->setEmpId(trim($postArr['txtEmpID']));
			$this->empmemship->setEmpMemCode(trim($postArr['cmbMemCode']));
			$this->empmemship->setEmpMemTypeCode(trim($postArr['cmbMemTypeCode']));
			$this->empmemship->setEmpMemSubOwn(trim($postArr['cmbMemSubOwn']));
			$this->empmemship->setEmpMemSubAmount(trim($postArr['txtMemSubAmount']));
			$this->empmemship->setEmpMemCommDat(trim($postArr['txtMemCommDat']));
			$this->empmemship->setEmpMemRenDat(trim($postArr['txtMemRenDat']));
   
		
			return $this->empmemship;	
		
	}

	
	function reloadData($postArr) {	
			
			$this->txtEmpid= (trim($postArr['txtEmpID']));
			$this->cmbMemCode = $postArr['cmbMemCode'];
			$this->cmbMemTypeCode = $postArr['cmbMemTypeCode'];
			$this->cmbMemSubOwn = $postArr['cmbMemSubOwn'];
			$this->txtMemSubAmount = $postArr['txtMemSubAmount'];
			$this->txtMemCommDat = $postArr['txtMemCommDat'];
			$this->txtMemRenDat = $postArr['txtMemRenDat'];
			
		
			return $this;
	}		
}
?>
