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

require_once ROOT_PATH . '/lib/models/maintenance/fileversions.php';

class EXTRACTOR_FileVersions {
	
	function EXTRACTOR_FileVersions() {

			$this->new_fileversions = new FileVersions();
	}

	function parseAddData($postArr) {	
			$this->new_fileversions ->setFileVersionId($this->new_fileversions->getLastRecord());
			$this->new_fileversions ->setFileVersionName(trim($postArr['txtName']));
			$this->new_fileversions ->setDescription(trim($postArr['txtDescription']));
			$this->new_fileversions ->setCreatedUser($_SESSION['user']);
			$this->new_fileversions ->setModifiedUser($_SESSION['user']);
			$this->new_fileversions ->setDateEntered(date('Y-m-d'));
			$this->new_fileversions ->setModule($postArr['cmbModule']);
		
			return $this->new_fileversions;
	}

	function parseEditData($postArr) {	
			
			$this->new_fileversions ->setFileVersionId(trim($postArr['txtID']));
			$this->new_fileversions ->setFileVersionName(trim($postArr['txtName']));
			$this->new_fileversions ->setDescription(trim($postArr['txtDescription']));
			$this->new_fileversions ->setModifiedUser($_SESSION['user']);
			$this->new_fileversions ->setDateModified(date('Y-m-d'));
			$this->new_fileversions ->setModule($postArr['cmbModule']);
			
			return $this->new_fileversions;
	}
	
}
?>
