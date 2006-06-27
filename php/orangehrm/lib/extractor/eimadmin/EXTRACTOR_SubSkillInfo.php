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

require_once ROOT_PATH . '/lib/models/eimadmin/SubSkillInfo.php';

class EXTRACTOR_SubSkillInfo {
	
	function EXTRACTOR_SubSkillInfo() {

		$this->parent_subskillinfo = new SubSkillInfo();
	}

	function parseAddData($postArr) {	
			
			$this->parent_subskillinfo -> setSubSkillInfoId($this ->parent_subskillinfo ->getLastRecord());
			$this->parent_subskillinfo -> setSubSkillInfoName(trim($postArr['txtSubSkillInfoName']));
			$this->parent_subskillinfo -> setSubSkillInfoDesc(trim($postArr['txtSubSkillInfoDesc']));
			$this->parent_subskillinfo -> setSkillId(trim($postArr['selSkillId']));
	
			return $this->parent_subskillinfo ;
	}
			
	function parseEditData($postArr) {	
			
			$this->parent_subskillinfo -> setSubSkillInfoId(trim($postArr['txtSubSkillInfoId']));
			$this->parent_subskillinfo -> setSubSkillInfoName(trim($postArr['txtSubSkillInfoName']));
			$this->parent_subskillinfo -> setSubSkillInfoDesc(trim($postArr['txtSubSkillInfoDesc']));
			$this->parent_subskillinfo -> setSkillId(trim($postArr['selSkillId']));
	
			return $this->parent_subskillinfo;
	}
	
}
?>
