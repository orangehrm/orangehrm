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

require_once ROOT_PATH . '/lib/models/eimadmin/ExtraCurrActCat.php';

class EXTRACTOR_ExtraCurrActCat {
	
	function EXTRACTOR_ExtraCurrActCat() {

			$this->parent_extracurrcat = new ExtraCurrActCat();
	}

	function parseAddData($postArr) {	
			
				$this->parent_extracurrcat -> setExtraId($this->parent_extracurrcat ->getLastRecord());
				$this->parent_extracurrcat -> setExtraDescription(trim($postArr['txtExtraCurrDesc']));
		
				return $this->parent_extracurrcat;
	}

	function parseEditData($postArr) {	
			
				$this->parent_extracurrcat -> setExtraId(trim($postArr['txtExtraCurrID']));
				$this->parent_extracurrcat -> setExtraDescription(trim($postArr['txtExtraCurrDesc'])); 
			
				return $this->parent_extracurrcat;
	}
	
}
?>
