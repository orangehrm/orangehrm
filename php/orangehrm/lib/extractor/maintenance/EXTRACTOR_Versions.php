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

require_once ROOT_PATH . '/lib/models/maintenance/versions.php';

class EXTRACTOR_Versions {
	
	function EXTRACTOR_Versions() {

		$this->new_version= new Versions();
	}

	function parseAddData($postArr) {	
			
			$this->new_version ->setVersionId($this->new_version ->getLastRecord());
			$this->new_version ->setVersionName(trim($postArr['txtName']));
			$this->new_version ->setDateEntered(date("Y-m-d"));
			$this->new_version ->setCreatedUser(trim($_SESSION['user']));
			$this->new_version ->setFileVersion(trim($postArr['cmbFileVersion']));
			$this->new_version ->setdbVersion(trim($postArr['cmbdbVersion']));
			$this->new_version ->setDescription(trim($postArr['txtDescription']));	
		
			return $this->new_version;
	}
			
	function parseEditData($postArr) {	
			
			$this->new_version ->setVersionId(trim($postArr['txtID']));
			$this->new_version ->setVersionName(trim($postArr['txtName']));
			$this->new_version ->setDateEntered(date("Y-m-d"));
			$this->new_version ->setModifiedUser(trim($_SESSION['user']));
			$this->new_version ->setFileVersion(trim($postArr['cmbFileVersion']));
			$this->new_version ->setdbVersion(trim($postArr['cmbdbVersion']));
			$this->new_version ->setDescription(trim($postArr['txtDescription']));	
			$this->new_version ->setDeleted(isset($postArr['chkDeleted'])?'1':'0');
	
		
			return $this->new_version;
	}
	
}
?>
