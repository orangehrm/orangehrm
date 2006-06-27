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

require_once ROOT_PATH . '/lib/models/hrfunct/EmpLang.php';

class EXTRACTOR_EmpLanguage {
	
	var $txtEmpId;
	var $cmbLanCode;
	var $cmbLanType;
	var $cmbRat;
	var $cmbRatGrd;
	
	function EXTRACTOR_EmpLanguage() {

		$this->emplan= new EmpLanguage();
	}

	function parseData($postArr) {	
			
			$this->emplan->setEmpId(trim($postArr['txtEmpID']));
   			$this->emplan->setEmpLangCode(trim($postArr['cmbLanCode']));
   			$this->emplan->setEmpLangType(trim($postArr['cmbLanType']));
   			$this->emplan->setEmpLangRatGrd(trim($postArr['cmbRatGrd']));
   			
		
			return $this->emplan;
	}
	/*function reloadData($postArr) {	
			
			$this->txtEmpId = (trim($postArr['txtEmpID']));
			$this->cmbLanCode = $postArr['cmbLanCode'];
			$this->cmbLanType = $postArr['cmbLanType'];
			$this->cmbRatGrd = $postArr['cmbRatGrd'];
			
		
			return $this;
	}	*/	
	
	
}
?>
