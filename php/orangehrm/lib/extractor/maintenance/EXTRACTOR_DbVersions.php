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

require_once ROOT_PATH . '/lib/models/maintenance/dbversions.php';

class EXTRACTOR_DbVersions{
	
	function EXTRACTOR_DbVersions() {

		$this->new_dbversion = new DbVersions();
	}

	function parseAddData($postArr) {	
			
			$this->new_dbversion ->setdbVersionId($this->new_dbversion ->getLastRecord());
	    	$this->new_dbversion ->setdbVersionName(trim($postArr['txtName']));
			$this->new_dbversion ->setDescription(trim($postArr['txtDescription']));
			$this->new_dbversion ->setCreatedUser($_SESSION['user']);
			$this->new_dbversion ->setModifiedUser($_SESSION['user']);
			$this->new_dbversion ->setDateEntered(date('Y-m-d'));
		
			return $this->new_dbversion;
	}

	function parseEditData($postArr) {	
			
			$this->new_dbversion ->setdbVersionId(trim($postArr['txtID']));
	    	$this->new_dbversion ->setdbVersionName(trim($postArr['txtName']));
			$this->new_dbversion ->setDescription(trim($postArr['txtDescription']));
			$this->new_dbversion ->setModifiedUser($_SESSION['user']);
			$this->new_dbversion ->setDateModified(date('Y-m-d'));
		
		
			return $this->new_dbversion;
	}
	
}
?>
