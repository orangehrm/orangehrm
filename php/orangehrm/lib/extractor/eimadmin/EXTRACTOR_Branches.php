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

require_once ROOT_PATH . '/lib/models/eimadmin/Branches.php';

class EXTRACTOR_Branches{
	
	function EXTRACTOR_Branches() {

		$this->parent_branch = new Branches();
	}

	function parseAddData($postArr) {	
			
			$this->parent_branch -> setBrchId($this->parent_branch ->getLastRecord());
			$this->parent_branch -> setBrchBankId(trim($postArr['cmbBankID']));
			$this->parent_branch -> setBrchDesc(trim($postArr['txtBranchesDesc']));
			$this->parent_branch -> setBrchAddr(trim($postArr['txtBranchesAddr']));
			$this->parent_branch -> setBrchSlipTrf(isset($postArr['chkBrchSlipTrf'])?'1':'0');
			$this->parent_branch -> setBrchClrHos(trim($postArr['txtBranchesClrHos']));
		
			return $this->parent_branch;	
		
	}

	function parseEditData($postArr) {	
			
			$this->parent_branch -> setBrchId(trim($postArr['txtBranchesID']));
			$this->parent_branch -> setBrchBankId(trim($postArr['cmbBankID']));
			$this->parent_branch -> setBrchDesc(trim($postArr['txtBranchesDesc']));
			$this->parent_branch -> setBrchAddr(trim($postArr['txtBranchesAddr']));
			$this->parent_branch -> setBrchSlipTrf(isset($postArr['chkBrchSlipTrf'])?'1':'0');
			$this->parent_branch -> setBrchClrHos(trim($postArr['txtBranchesClrHos']));
		
			return $this->parent_branch;	
	}
	
}
?>
