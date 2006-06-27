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

require_once ROOT_PATH . '/lib/models/hrfunct/EmpTax.php';

class EXTRACTOR_EmpTax {
	
	var $txtEmpID;
	var $cmbTaxID;
	var $optFedStateFlag;
	var $cmbFedTaxFillStat;
	var $txtFedTaxAllowance;
	var $txtFedTaxExtra;
	var $cmbStateTaxFillStat;
	var $cmbStateTaxState;
	var $txtStateTaxAllowance;
	var $txtStateTaxExtra;
	
	function EXTRACTOR_EmpTax() {

		$this->tax = new EmpTax();
	}

	function parseData($postArr) {	
			
			$this->tax->setEmpId($postArr['txtEmpID']);
			$this->tax->setTaxId($postArr['cmbTaxID']);
			$this->tax->setEmpFedStateFlag($postArr['optFedStateFlag']);
			
			if($postArr['optFedStateFlag']=='1') {
				$this->tax->setEmpTaxFillStat($postArr['cmbFedTaxFillStat']);
				$this->tax->setEmpTaxState('');
				$this->tax->setEmpTaxAllowance($postArr['txtFedTaxAllowance']);
				$this->tax->setEmpTaxExtra($postArr['txtFedTaxExtra']);
				
			} else {
				
				$this->tax->setEmpTaxFillStat($postArr['cmbStateTaxFillStat']);
				$this->tax->setEmpTaxState($postArr['cmbStateTaxState']);
				$this->tax->setEmpTaxAllowance($postArr['txtStateTaxAllowance']);
				$this->tax->setEmpTaxExtra($postArr['txtStateTaxExtra']);
			}
			
			return  $this->tax;
	}
	
	function reloadData($postArr) {	
		
		$this->txtEmpID			=	($postArr['txtEmpID']);
		$this->cmbTaxID		    =	(trim($postArr['cmbTaxID']));
		$this->optFedStateFlag	= 	($postArr['optFedStateFlag']);
	 
		if($postArr['optFedStateFlag'] == '1') {
			
				$this->cmbFedTaxFillStat	=	($postArr['cmbFedTaxFillStat']);
				$this->tax->setEmpTaxState('');
				$this->txtFedTaxAllowance	=	($postArr['txtFedTaxAllowance']);
				$this->txtFedTaxExtra		=	($postArr['txtFedTaxExtra']);
				
			} else {
				
				$this->cmbStateTaxFillStat	=	($postArr['cmbStateTaxFillStat']);
				$this->cmbStateTaxState		=	($postArr['cmbStateTaxState']);
				$this->txtStateTaxAllowance	=	($postArr['txtStateTaxAllowance']);
				$this->txtStateTaxExtra		=	($postArr['txtStateTaxExtra']);
			}
		return $this;
	}
}
?>
