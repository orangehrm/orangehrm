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

require_once ROOT_PATH . '/lib/models/hrfunct/EmpExCur.php';

class EXTRACTOR_EmpExCur{
	
	
	var $txtEmpID;
	var $txtEmpExCurID;
	var $cmbExtCurCat;
	var $cmbExtCurTyp;
	var $txtEmpExCurAch;
	
	function EXTRACTOR_EmpExCur() {

		$this->empextcur = new EmpExCur();
	}

	function parseData($postArr) {	
			
			$this->empextcur->setEmpId(trim($postArr['txtEmpID']));
   			$this->empextcur->setEmpECActSeqNo(trim($postArr['txtEmpExCurID']));
   			$this->empextcur->setEmpECCatCode(trim($postArr['cmbExtCurCat']));
   			$this->empextcur->setEmpECTypeCode(trim($postArr['cmbExtCurTyp']));
   			$this->empextcur->setEmpECAchmnt(trim($postArr['txtEmpExCurAch']));
		
			return $this->empextcur;	
		
	}

	function reloadData($postArr) {	
			
			$this->txtEmpID         = (trim($postArr['txtEmpID']));
   			$this->txtEmpExCurID	= (trim($postArr['txtEmpExCurID']));
   			$this->cmbExtCurCat		= (trim($postArr['cmbExtCurCat']));
   			$this->cmbExtCurTyp		= (trim($postArr['cmbExtCurTyp']));
   			$this->txtEmpExCurAch	= (trim($postArr['txtEmpExCurAch']));
		
			return $this;	
	}
	
}
?>
