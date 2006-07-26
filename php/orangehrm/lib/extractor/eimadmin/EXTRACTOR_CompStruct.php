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

require_once ROOT_PATH . '/lib/models/eimadmin/CompStruct.php';

class EXTRACTOR_CompStruct {
	
	function EXTRACTOR_CompStruct() {

		$this->compstruct = new CompStruct();
	}

	function parseAddData($postArr) {	
			
			$this->compstruct -> setrgt(trim($postArr['rgt']));
			$this->compstruct -> setaddStr(trim($postArr['txtTitle']." ".$postArr['cmbType']));
			$this->compstruct -> setstrDesc(trim($postArr['txtDesc']));
			$this->compstruct -> setaddParnt(trim($postArr['txtParnt']));
			$this->compstruct -> setlocation(trim($postArr['cmbLocation']));
			
			return $this->compstruct;
	}
			
	function parseEditData($postArr) {
			
			$this->compstruct -> setid(trim($postArr['rgt']));
			$this->compstruct -> setaddStr(trim($postArr['txtTitle']." ".$postArr['cmbType']));
			$this->compstruct -> setstrDesc(trim($postArr['txtDesc']));
			$this->compstruct -> setlocation(trim($postArr['cmbLocation']));
			
			return $this->compstruct;
	}
	
	function parseDeleteData($postArr) {
			
			$this->compstruct -> setrgt(trim($postArr['rgt']));
			$this->compstruct -> setlft(trim($postArr['lft']));
			
			return $this->compstruct;
	}
	
}
?>
