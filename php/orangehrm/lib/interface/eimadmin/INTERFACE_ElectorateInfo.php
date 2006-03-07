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

require_once OpenSourceEIM . '/lib/Models/eimadmin/ElectorateInfo.php';

class INTERFACE_ElectorateInfo {
	
	function INTERFACE_ElectorateInfo() {

		$this->parent_electorateinfo = new ElectorateInfo();
		$this->locRights=$_SESSION['localRights'];
	}

	function parseData($postArr) {	
		if (($postArr['sqlState'] == 'NewRecord') && $this->locRights['add']) {
			$this->parent_electorateinfo -> setElectorateInfoId($this->parent_electorateinfo->getLastRecord());
			$this->parent_electorateinfo -> setElectorateInfoDesc(trim($postArr['txtElectorateInfoDesc']));
			$message = $this->parent_electorateinfo ->addElectorateInfo();
				
			// Checking whether the $message Value returned is 1 or 0
			if ($message) { 
				
				$showMsg = "Addition%Successful!"; //If $message is 1 setting up the 
				
				$uniqcode = $_GET['uniqcode'];
				$pageID = $postArr['pageID'];
				header("Location: ./view.php?message=$showMsg&uniqcode=$uniqcode&pageID=$pageID");
				
			} else {
				
				$showMsg = "Addition%Unsuccessful!";
				
				$uniqcode = $_GET['uniqcode'];
				$pageID = $postArr['pageID'];
				header("Location: ./electorateinformation.php?msg=$showMsg&capturemode=addmode&uniqcode=$uniqcode&pageID=$pageID");
			}
			
		} else if (($postArr['sqlState'] == 'UpdateRecord') && $this->locRights['edit']) {
			
			$this->parent_electorateinfo -> setElectorateInfoId(trim($postArr['txtElectorateInfoId']));
			$this->parent_electorateinfo -> setElectorateInfoDesc(trim($postArr['txtElectorateInfoDesc']));
			$message = $this->parent_electorateinfo ->updateElectorateInfo();
			
			// Checking whether the $message Value returned is 1 or 0
			if ($message) { 
				
				$showMsg = "Updation%Successful!"; //If $message is 1 setting up the 
				
				$uniqcode = $_GET['uniqcode'];
				$pageID = $postArr['pageID'];
				header("Location: ./view.php?message=$showMsg&uniqcode=$uniqcode&pageID=$pageID");
				
			} else {
				
				$showMsg = "Updation%Unsuccessful!";
				
				$uniqcode = $_GET['uniqcode'];
				$pageID = $postArr['pageID'];
				$id = $_GET['id'];
				header("Location: ./electorateinformation.php?msg=$showMsg&id=$id&capturemode=updatemode&uniqcode=$uniqcode&pageID=$pageID");
			}
		}
	}
	
	function editData($id) {
	 $message = $this->parent_electorateinfo ->filterElectorateInfo($id);
		
		return $message;
	}
}
?>
