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

require_once ROOT_PATH . '/lib/models/maintenance/Users.php';

class EXTRACTOR_Users{
	
	function EXTRACTOR_Users() {

		$this->users = new Users();
	}

	function parseAddData($postArr) {	
		
			$this->users ->setUserID($this->users->getLastRecord());
		 	$this->users -> setUserName(trim($postArr['txtUserName']));
		 	$this->users -> setUserPassword(trim($postArr['txtUserPassword'])); 
		 	$this->users -> setUserFirstName(trim($postArr['txtUserFirstName'])); 
		 	$this->users -> setUserLastName(trim($postArr['txtUserLastName'])); 
		 	$this->users -> setUserEmpID($postArr['cmbUserEmpID']); 
		 	$this->users -> setUserIsAdmin(isset($postArr['chkUserIsAdmin']) ? 'Yes' : 'No'); 
		 	$this->users -> setUserDesc(trim($postArr['txtUserDescription'])); 
		 	$this->users -> setUserDateEntered(date("Y-m-d")); 
		 	$this->users -> setUserCreatedBy($_SESSION['user']); 
		 	$this->users -> setUserDepartment(trim($postArr['txtUserDepartment'])); 
		 	$this->users -> setUserPhoneHome(trim($postArr['txtUserPhoneHome'])); 
		 	$this->users -> setUserPhoneMobile(trim($postArr['txtUserPhoneMobile'])); 
			$this->users -> setUserPhoneWork(trim($postArr['txtUserPhoneWork'])); 
			$this->users -> setUserEmail1(trim($postArr['txtUserEmail1'])); 
			$this->users -> setUserEmail2(trim($postArr['txtUserEmail2'])); 
			$this->users -> setUserStatus($postArr['cmbUserStatus']);
		 	$this->users -> setUserAddress(trim($postArr['txtUserAddress'])); 
		 	$this->users -> setUserDeleted(isset($postArr['chkUserDeleted']) ? '1' : '0'); 
		 	$this->users -> setUserGroupID($postArr['cmbUserGroupID']); 
		 
			return $this->users;	
		
	}

	function parseEditData($postArr) {	
			
			$this->users -> setUserID($postArr['txtUserID']);
		 	$this->users -> setUserName(trim($postArr['txtUserName']));
		 	$this->users -> setUserPassword(trim($postArr['txtUserPassword'])); 
		 	$this->users -> setUserFirstName(trim($postArr['txtUserFirstName'])); 
		 	$this->users -> setUserLastName(trim($postArr['txtUserLastName'])); 
		 	$this->users -> setUserEmpID($postArr['cmbUserEmpID']); 
		 	$this->users -> setUserIsAdmin(isset($postArr['chkUserIsAdmin']) ? 'Yes' : 'No'); 
		 	$this->users -> setUserDesc(trim($postArr['txtUserDescription'])); 
		 	$this->users -> setUserDateModified(date("Y-m-d")); 
			$this->users -> setUserModifiedBy($_SESSION['user']); 
		 	$this->users -> setUserDepartment(trim($postArr['txtUserDepartment'])); 
		 	$this->users -> setUserPhoneHome(trim($postArr['txtUserPhoneHome'])); 
		 	$this->users -> setUserPhoneMobile(trim($postArr['txtUserPhoneMobile'])); 
		 	$this->users -> setUserPhoneWork(trim($postArr['txtUserPhoneWork'])); 
		 	$this->users -> setUserEmail1(trim($postArr['txtUserEmail1'])); 
		 	$this->users -> setUserEmail2(trim($postArr['txtUserEmail2'])); 
		 	$this->users -> setUserStatus($postArr['cmbUserStatus']);
		 	$this->users -> setUserAddress(trim($postArr['txtUserAddress'])); 
		 	$this->users -> setUserDeleted(isset($postArr['chkUserDeleted']) ? '1' : '0'); 
		 	$this->users -> setUserGroupID($postArr['cmbUserGroupID']); 
		 	
			return $this->users;	
	}
	
}
?>
