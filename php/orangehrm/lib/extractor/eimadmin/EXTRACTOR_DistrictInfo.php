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

require_once ROOT_PATH . '/lib/models/eimadmin/DistrictInfo.php';

class EXTRACTOR_DistrictInfo {

	var $txtDistrictID;
	var $txtDistrctDesc;
	var $cmbCountry;
	var $selProvince;
	
	function EXTRACTOR_DistrictInfo() {
		$this->parent_districtinfo = new DistrictInfo();
	}

	function parseAddData($postArr) {	
			
			$this->parent_districtinfo -> setDistrictInfoId($this->parent_districtinfo ->getLastRecord());
			$this->parent_districtinfo -> setDistrictInfoDesc(trim($postArr['txtDistrictDesc']));
			$this->parent_districtinfo -> setProvinceId(trim($postArr['selProvinceID']));
		
			return $this->parent_districtinfo;
	}

	function parseEditData($postArr) {	
			
			$this->parent_districtinfo -> setDistrictInfoId(trim($postArr['txtDistrictID']));
			$this->parent_districtinfo -> setDistrictInfoDesc(trim($postArr['txtDistrictDesc']));
			$this->parent_districtinfo -> setProvinceId(trim($postArr['selProvinceID']));
		
			return $this->parent_districtinfo;
	}
	
	function reloadData($postArr) {	
			
			$this->txtDistrictDesc = (trim($postArr['txtDistrictDesc']));
			$this->cmbCountry = $postArr['cmbCountry'];
			$this->selProvince = $postArr['selProvinceID'];
		
			return $this;
	}
}
?>
