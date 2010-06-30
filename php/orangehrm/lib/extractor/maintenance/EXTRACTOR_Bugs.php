<?php
/*
// OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
// all the essential functionalities required for any enterprise. 
// Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com

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

require_once ROOT_PATH . '/lib/models/maintenance/bugs.php';

class EXTRACTOR_Bugs {
	
	function EXTRACTOR_Bugs() {

		$this->new_bug = new Bugs();
	}

	function parseAddData($postArr) {	
	
			$this->new_bug ->setDescription(trim($postArr['txtDescription']));
			$this->new_bug ->setFoundInrelease(trim($postArr['artifact_group_id']));
			$this->new_bug ->setModule(trim($postArr['cmbModule']));
			$this->new_bug ->setName(trim($postArr['summary']));
			$this->new_bug ->setPriority(trim($postArr['priority']));
			$this->new_bug ->setSource(trim($postArr['category_id']));
			$this->new_bug ->setEmail(trim($postArr['txtEmail']));
			$this->new_bug ->setCsrfToken(trim($postArr['token']));
			
			return $this->new_bug;
	}
			
	function parseEditData($postArr) {	
			
			$this->new_bug ->setBugId(trim($postArr['txtID']));
			$this->new_bug ->setBugNumber(trim($postArr['txtBugNumber']));
			$this->new_bug ->setDateModified(date("Y-m-d"));
			$this->new_bug ->setDeleted(isset($postArr['chkDeleted'])?'1':'0');
			$this->new_bug ->setDescription(trim($postArr['txtDescription']));
			$this->new_bug ->setFixedInRelease(trim($postArr['cmbFixedRelease']));
			$this->new_bug ->setModifiedUserId(trim($_SESSION['user']));
			$this->new_bug ->setName(trim($postArr['txtName']));
			$this->new_bug ->setPriority(trim($postArr['cmbPriority']));
			$this->new_bug ->setResolution(trim($postArr['cmbResolution']));
			$this->new_bug ->setStatus(trim($postArr['cmbStatus']));
			$this->new_bug ->setWorkLog(trim($postArr['txtWorkLog']));
			$this->new_bug ->setModule(trim($postArr['txtModule']));
			$this->new_bug ->setSource(trim($postArr['txtSource']));
			
			return $this->new_bug;
	}
	
}
?>
