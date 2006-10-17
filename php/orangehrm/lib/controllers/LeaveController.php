<?
/*
 *
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
 * all the essential functionalities required for any enterprise. 
 * Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
 */


//the model objects are included here

require_once ROOT_PATH . '/lib/models/leave/Leave.php';

require_once ROOT_PATH . '/lib/common/TemplateMerger.php';

class LeaveController {
	
	private $indexCode;
	private $id;
	private $objLeave;
	
	public function setId($id) {
		$this->id = $id;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function setObjLeave($obj) {
		$this->objLeave = $obj;
	}
	
	public function getObjLeave() {
		return $this->objLeave;
	}


	public function __construct() {
		//nothing to do
	}
	
	//public function

	public function viewLeaves($modifier="employee") {
		$this->setObjLeave(new Leave());		
			
		switch ($modifier) {
			case "employee": $this->_viewLeavesEmployee();
		}
	}
	
	public function changeStatus($modifier="cancel") {
		$this->setObjLeave(new Leave());
		
		switch ($modifier) {
			case "cancel": $res = $this->_cancelLeave();
		}
		
		if ($res) {
			$message="SUCCESS";
		} else {
			$message="FAILURE";
		}
		
		//$this->redirect($message);
	}
	
	private function _viewLeavesEmployee() {
		$tmpObj = $this->getObjLeave();
		$tmpObj = $tmpObj->retriveLeaveEmployee($this->getId());
		
		$path = "/templates/leave/leaveList.php";
		
		$template = new TemplateMerger($tmpObj, $path);
		
		$template->display();		
	}
	
	private function _cancelLeave() {
		$tmpObj = $this->getObjLeave();
		
		return $tmpObj->cancelLeave($this->getId());		
	}

	public function redirect($message=null) {
		if (isset($message)) {
			if (preg_match('/&/', $_SERVER['HTTP_REFERER']) > 0) {
				$message = "&message=".$message;
				$url = preg_split('/(&||\?)message=[A-Za-z0-9]*/', $_SERVER['HTTP_REFERER']);				
			} else {
				$message = "?message=".$message;
			}
		}		
		
		header("Location: ".$url[0].$message);
	}
	
	public function addLeave() {
		$tmpObj = $this->getObjLeave();
		$res = $tmpObj->applyLeave();
	}
}
?>