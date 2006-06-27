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

require_once ROOT_PATH . '/lib/models/eimadmin/JDType.php';

class EXTRACTOR_JDType {
	
	function EXTRACTOR_JDType() {

		$this->parent_jdtype = new JDType();
	}

	function parseAddData($postArr) {	
			$this->parent_jdtype -> setJDTypeId($this->parent_jdtype ->getLastRecord());
			$this->parent_jdtype -> setJDTypeDesc(trim($postArr['txtJDTypeDesc']));
			$this->parent_jdtype -> setJDCatId(trim($postArr['cmbJDCatID']));
		
			return $this->parent_jdtype;
	}
			
	function parseEditData($postArr) {	
			
			$this->parent_jdtype -> setJDTypeId(trim($postArr['txtJDTypeID']));
			$this->parent_jdtype -> setJDTypeDesc(trim($postArr['txtJDTypeDesc']));
			$this->parent_jdtype -> setJDCatId(trim($postArr['cmbJDCatID']));
		
			return $this->parent_jdtype;
	}
	
}
?>
