<?php
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

require_once ROOT_PATH . '/lib/models/hrfunct/EmpQual.php';

class EXTRACTOR_EmpQualification {
	
	var $cmbQualCode;
	var $txtEmpID;
	var $txtQualInst;
	var $txtQualYear;
	var $cmbQualStat;
	var $txtQualComment;
	var $TypeCode;
	
	function EXTRACTOR_EmpQualification() {

		$this->empqual = new EmpQualification();
	}

	function parseData($postArr) {	
			
			$this->empqual->setEmpQualId(trim($postArr['cmbQualCode']));
			$this->empqual->setEmpId(trim($postArr['txtEmpID']));
			$this->empqual->setEmpQualInst(trim($postArr['txtQualInst']));
			$this->empqual->setEmpQualYear(trim($postArr['txtQualYear']));
			$this->empqual->setEmpQualStat(trim($postArr['cmbQualStat']));
			$this->empqual->setEmpQualComment(trim($postArr['txtQualComment']));
		
			return $this->empqual;
	}
			
	function reloadData($postArr) {	
			
			$this->cmbQualCode		=	(trim($postArr['cmbQualCode']));
			$this->txtEmpID			=	(trim($postArr['txtEmpID']));
			$this->TypeCode			=	(trim($postArr['TypeCode']));
			$this->txtQualInst		=	(trim($postArr['txtQualInst']));
			$this->txtQualYear		=	(trim($postArr['txtQualYear']));
			$this->cmbQualStat		=	(trim($postArr['cmbQualStat']));
			$this->txtQualComment	=	(trim($postArr['txtQualComment']));
			
			return $this;
	}
	
}
?>
