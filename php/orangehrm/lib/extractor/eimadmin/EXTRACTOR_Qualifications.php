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

require_once ROOT_PATH . '/lib/models/eimadmin/Education.php';

class EXTRACTOR_Education {
	
	function EXTRACTOR_Education() {

		$this->parent_edu = new Education();
	}

	function parseAddData($postArr) {	
			
			$this->parent_edu -> setEduId($this->parent_edu ->getLastRecord());
			$this->parent_edu -> setEduUni(trim($postArr['txtUni']));
			$this->parent_edu -> setEduDeg(trim($postArr['txtDeg']));
			
			return $this->parent_edu;
	}

	function parseEditData($postArr) {	
		
			$this->parent_edu -> setEduId(trim($postArr['txtEducationID']));
			$this->parent_edu -> setEduUni(trim($postArr['txtUni']));
			$this->parent_edu -> setEduDeg(trim($postArr['txtDeg']));
			
	
			return $this->parent_edu;
	}
	
}
?>
