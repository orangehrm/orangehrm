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

require_once ROOT_PATH . '/lib/models/hrfunct/EmpPassPort.php';

class EXTRACTOR_EmpPassPort {
	
	var $empId;
	var $empPPSeqNo;
	var $empNationality;
	var $empI9Status;
	var $empI9ReviewDat;
	var $empPPIssDat;
	var $empPPExpDat;
	var $emppassportflag;
	var $emppassComm;
	var $empPPNo;
	
	
	function EXTRACTOR_EmpPassPort() {

		$this->pport = new EmpPassPort();
	}

	function parseData($postArr) {	
			
			$this->pport->setEmpId($postArr['txtEmpID']);
			$this->pport->setEmpPPSeqNo(trim($postArr['txtPPSeqNo']));
			$this->pport->setEmpPPNo(trim($postArr['txtPPNo']));
			$this->pport->setEmpPPIssDat(trim($postArr['txtPPIssDat']));
			$this->pport->setEmpPPExpDat(trim($postArr['txtPPExpDat']));
			$this->pport->setEmpPPComment(trim($postArr['txtComments']));
			$this->pport->setEmppassportflag($postArr['PPType']);
			$this->pport->setEmpI9Status($postArr['txtI9status']);
			$this->pport->setEmpI9ReviewDat($postArr['txtI9ReviewDat']);
			$this->pport->setEmpNationality($postArr['cmbPPCountry']);
									
			return $this->pport;
	}
	
	function reloadData($postArr) {	
	
			$this->txtEmpID		=	($postArr['txtEmpID']);
			$this->txtPPSeqNo	=	(trim($postArr['txtPPSeqNo']));
			$this->txtPPNo		=	(trim($postArr['txtPPNo']));
			$this->txtPPIssDat	=	(trim($postArr['txtPPIssDat']));
			$this->txtPPExpDat	=	(trim($postArr['txtPPExpDat']));
			$this->txtComments	=	(trim($postArr['txtComments']));
			$this->PPComment	=	(trim($postArr['PPComment']));
			$this->txtI9status	=	($postArr['txtI9status']);
			$this->PPType		=	($postArr['PPType']);
			$this->cmbPPCountry	=	($postArr['cmbPPCountry']);
			$this->txtI9ReviewDat	=	(trim($postArr['txtI9ReviewDat']));
			
			return $this;
	}
		
	
}
?>
