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

require_once ROOT_PATH . '/lib/models/maintenance/Users.php';

class EXTRACTOR_Users{

	function EXTRACTOR_Users() {

		$this->users = new Users();
	}

	function parseAddData($postArr) {

		 	$this->users -> setUserName(trim($postArr['txtUserName']));
		 	$this->users -> setUserPassword(md5(trim($postArr['txtUserPassword'])));
		 	$this->users -> setUserEmpID($postArr['cmbUserEmpID']);
		 	$this->users -> setUserIsAdmin(isset($postArr['chkUserIsAdmin']) ? 'Yes' : 'No');
		 	$this->users -> setUserDateEntered(date("Y-m-d"));
		 	$this->users -> setUserCreatedBy($_SESSION['user']);
		 	$this->users -> setUserStatus($postArr['cmbUserStatus']);
		 	$this->users -> setUserGroupID($postArr['cmbUserGroupID']);

			return $this->users;

	}

	function parseEditData($postArr) {

			$this->users -> setUserID($postArr['txtUserID']);
		 	$this->users -> setUserName(trim($postArr['txtUserName']));
		 	$this->users -> setUserEmpID($postArr['cmbUserEmpID']);
		 	$this->users -> setUserIsAdmin(isset($postArr['chkUserIsAdmin']) ? 'Yes' : 'No');
		 	$this->users -> setUserDateModified(date("Y-m-d"));
			$this->users -> setUserModifiedBy($_SESSION['user']);
		 	$this->users -> setUserStatus($postArr['cmbUserStatus']);
		 	$this->users -> setUserGroupID($postArr['cmbUserGroupID']);

		 	if ($_SESSION['isAdmin'] == 'Yes' && $_SESSION['ldap'] == "enabled") {
		 		if (isset($postArr['txtUserPassword']) && ($postArr['txtUserPassword'] == $postArr['txtUserConfirmPassword']) && is_string($postArr['txtUserPassword'])) {
		 			$this->users -> setUserPassword(md5(trim($postArr['txtUserPassword'])));
		 		}
		 	} else if ($_SESSION['isAdmin'] == 'Yes') {
		 		if (isset($postArr['txtUserPassword']) && ($postArr['txtUserPassword'] == $postArr['txtUserConfirmPassword']) && is_string($postArr['txtUserPassword']) && (strlen($postArr['txtUserPassword']) > 3)) {
		 			$this->users -> setUserPassword(md5(trim($postArr['txtUserPassword'])));
		 		}
		 	}

			return $this->users;
	}

}
?>
