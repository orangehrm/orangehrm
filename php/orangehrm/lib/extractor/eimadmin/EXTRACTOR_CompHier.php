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

require_once ROOT_PATH . '/lib/models/eimadmin/CompHier.php';

class EXTRACTOR_CompHier {
	
	function EXTRACTOR_CompHier() {

		$this->parent_comphier = new CompHierachy();
	}

	function parseAddData($postArr) {	
			
			$this->parent_comphier -> setHiId($this->parent_comphier ->getLastRecord());
			$this->parent_comphier -> setHiDesc(trim($postArr['txtHiDesc']));
			$this->parent_comphier -> setHiRelat($postArr['cmbHiRelat']);
			$this->parent_comphier -> setEmpId($postArr['cmbEmpID']);
			$this->parent_comphier -> setDefLev($postArr['cmbDefLev']);
			$this->parent_comphier -> setTelep($postArr['txtTelep']);
			$this->parent_comphier -> setFax($postArr['txtFax']);
			$this->parent_comphier -> setEmail($postArr['txtEmail']);
			$this->parent_comphier -> setUrl($postArr['txtUrl']);
			$this->parent_comphier -> setLogo($postArr['txtLogo']);
			$this->parent_comphier -> setLoc($postArr['cmbLoc']);
			
			return $this->parent_comphier;
	}

	function parseEditData($postArr) {	
			
			$this->parent_comphier -> setHiId(trim($postArr['txtHiID']));
			$this->parent_comphier -> setHiDesc(trim($postArr['txtHiDesc']));
			$this->parent_comphier -> setHiRelat($postArr['cmbHiRelat']);
			$this->parent_comphier -> setEmpId($postArr['cmbEmpID']);
			$this->parent_comphier -> setDefLev($postArr['cmbDefLev']);
			$this->parent_comphier -> setTelep($postArr['txtTelep']);
			$this->parent_comphier -> setFax($postArr['txtFax']);
			$this->parent_comphier -> setEmail($postArr['txtEmail']);
			$this->parent_comphier -> setUrl($postArr['txtUrl']);
			$this->parent_comphier -> setLogo($postArr['txtLogo']);
			$this->parent_comphier -> setLoc($postArr['cmbLoc']);
		
			return $this->parent_comphier;
	}
	
	function parseData($postArr) {

			$this->parent_comphier -> setHiId($postArr[0]);
			$this->parent_comphier -> setHiDesc('');
			$this->parent_comphier -> setHiRelat($postArr[1]);
			$this->parent_comphier -> setEmpId(0);
			$this->parent_comphier -> setDefLev($postArr[2]);
			$this->parent_comphier -> setTelep('');
			$this->parent_comphier -> setFax('');
			$this->parent_comphier -> setEmail('');
			$this->parent_comphier -> setUrl('');
			$this->parent_comphier -> setLogo('');
			$this->parent_comphier -> setLoc('');
			
			return $this->parent_comphier;
		
	}
	
}
?>
