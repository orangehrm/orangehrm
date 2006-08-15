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

require_once ROOT_PATH . '/lib/models/hrfunct/EmpInfo.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpTax.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpPassPort.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpBank.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpAttach.php';

class EXTRACTOR_EmpEss{
	
			var $cmbEmpTitle;	
			var $txtEmpCallName;
			var $txtEmpSurname;
			var $txtEmpMaidenName;
			var $txtEmpInitials;
			var $txtEmpNamByIni;
			var $txtEmpFullName;
			var $txtEmpOtherName;
			
			//personal
			var $txtNICDate;
			var $cmbNation	;
			var $cmbReligion;
			var $DOB;
			var $cmbBloodGrp;
			var $txtBirthPlace;
			var $cmbMarital;
			var $optGender;
			var $txtMarriedDate;
															
			//contact			
			var $txtPermHouseNo;
			var $txtPermStreet1;
			var $txtPermStreet2;
			var $txtPermCityTown;
			var $txtPermPostCode;
			var $txtPermTelep;
			var $txtPermMobile;
			var $txtPermFax	;
			var $txtPermEmail;
			var $cmbPermCountry;
			var $cmbPermProvince;
			var $cmbPermDistrict;
			var $cmbPermElectorate;

	function EXTRACTOR_EmpEss() {

		$this->parent_empinfo = new EmpInfo();
	}

	
			
	function parseEditData($postArr) {	
		
	if($postArr['main']=='1') {
		$this->parent_empinfo -> setEmpId(trim($postArr['txtEmpID']));
		$this->parent_empinfo -> setEmpTitle(($postArr['cmbEmpTitle']));
		$this->parent_empinfo -> setEmpCallName(trim($postArr['txtEmpCallName']));
		$this->parent_empinfo -> setEmpSurname(trim($postArr['txtEmpSurname']));
		$this->parent_empinfo -> setEmpMaidenName(trim($postArr['txtEmpMaidenName']));
		$this->parent_empinfo -> setEmpInitials(trim($postArr['txtEmpInitials']));
		$this->parent_empinfo -> setEmpNamByIni(trim($postArr['txtEmpNamByIni']));
		$this->parent_empinfo -> setEmpFullName(trim($postArr['txtEmpFullName']));
		$this->parent_empinfo -> setEmpOtherName(trim($postArr['txtEmpOtherName']));
		
		$objectArr['EmpMain'] = $this->parent_empinfo;
	}
	
	//personal
	if($postArr['personalFlag']=='1') {
		$this->parent_empinfo -> setEmpId(trim($postArr['txtEmpID']));
		$this->parent_empinfo -> setEmpNICNo(trim($postArr['txtNICNo']));
		$this->parent_empinfo -> setEmpNICDate(trim($postArr['txtNICDate']));
		$this->parent_empinfo -> setEmpDOB(trim($postArr['DOB']));
		$this->parent_empinfo -> setEmpBirthPlace(trim($postArr['txtBirthPlace']));
		$this->parent_empinfo -> setEmpGender(trim($postArr['optGender']));
		$this->parent_empinfo -> setEmpBloodGrp(($postArr['cmbBloodGrp']));
		$this->parent_empinfo -> setEmpNation(($postArr['cmbNation']));
		$this->parent_empinfo -> setEmpReligion(($postArr['cmbReligion']));
		$this->parent_empinfo -> setEmpMarital(($postArr['cmbMarital']));
		$this->parent_empinfo -> setEmpMarriedDate(trim($postArr['txtMarriedDate']));
		
		$objectArr['EmpPers'] = $this->parent_empinfo;
	}
				
	
		
			//contact
	if($postArr['contactFlag']=='1') {
		$this->parent_empinfo -> setEmpId(trim($postArr['txtEmpID']));
		$this->parent_empinfo -> setEmpPermHouseNo(trim($postArr['txtPermHouseNo']));
		$this->parent_empinfo -> setEmpPermStreet1(trim($postArr['txtPermStreet1']));
		$this->parent_empinfo -> setEmpPermStreet2(trim($postArr['txtPermStreet2']));
		$this->parent_empinfo -> setEmpPermCityTown(trim($postArr['txtPermCityTown']));
		$this->parent_empinfo -> setEmpPermPostCode(trim($postArr['txtPermPostCode']));
		$this->parent_empinfo -> setEmpPermTelephone(trim($postArr['txtPermTelep']));
		$this->parent_empinfo -> setEmpPermMobile(trim($postArr['txtPermMobile']));
		$this->parent_empinfo -> setEmpPermFax(trim($postArr['txtPermFax']));
		$this->parent_empinfo -> setEmpPermEmail(trim($postArr['txtPermEmail']));
		$this->parent_empinfo -> setEmpPermCountry(($postArr['cmbPermCountry']));
		$this->parent_empinfo -> setEmpPermProvince(($postArr['cmbPermProvince']));
		$this->parent_empinfo -> setEmpPermDistrict(($postArr['cmbPermDistrict']));
		$this->parent_empinfo -> setEmpPermElectorate(($postArr['cmbPermElectorate']));
		
		$objectArr['EmpPermRes'] = $this->parent_empinfo;
		}
		
	return $objectArr;
  }

	function parseCountryData($postArr) {
		$this->parent_empinfo -> setEmpId(trim($_POST['txtEmpID']));
		$this->parent_empinfo->setEmpTaxCountry($_POST['cmbTaxCountry']);
		
		return $this->parent_empinfo;
	}
	
	function reloadData($postArr) {	
			
			$this->cmbEmpTitle		    =	(trim($postArr['cmbEmpTitle']));
			$this->txtEmpCallName		=	(trim($postArr['txtEmpCallName']));
			$this->txtEmpSurname		=	(trim($postArr['txtEmpSurname']));
			$this->txtEmpMaidenName		=	(trim($postArr['txtEmpMaidenName']));
			$this->txtEmpInitials		=	(trim($postArr['txtEmpInitials']));
			$this->txtEmpNamByIni		=	(trim($postArr['txtEmpNamByIni']));
			$this->txtEmpFullName		=	(trim($postArr['txtEmpFullName']));
			$this->txtEmpOtherName		=	(trim($postArr['txtEmpOtherName']));
			
			//personal
			$this->txtNICDate		=	(trim($postArr['txtNICDate']));
			$this->cmbNation		=	(trim($postArr['cmbNation']));
			$this->cmbReligion		=	(trim($postArr['cmbReligion']));
			$this->DOB				=	(trim($postArr['DOB']));
			$this->cmbBloodGrp		=	(trim($postArr['cmbBloodGrp']));
			$this->txtBirthPlace	=	(trim($postArr['txtBirthPlace']));
			$this->cmbMarital		=	(trim($postArr['cmbMarital']));
			$this->optGender		=	(trim($postArr['optGender']));
			$this->txtMarriedDate	=	(trim($postArr['txtMarriedDate']));
			
			
			
			//contact
			$this->txtPermHouseNo		=	(trim($postArr['txtPermHouseNo']));
			$this->txtPermStreet1		=	(trim($postArr['txtPermStreet1']));
			$this->txtPermStreet2		=	(trim($postArr['txtPermStreet2']));
			$this->txtPermCityTown		=	(trim($postArr['txtPermCityTown']));
			$this->txtPermPostCode		=	(trim($postArr['txtPermPostCode']));
			$this->txtPermTelep			=	(trim($postArr['txtPermTelep']));
			$this->txtPermMobile		=	(trim($postArr['txtPermMobile']));
			$this->txtPermFax			=	(trim($postArr['txtPermFax']));
			$this->txtPermEmail			=	(trim($postArr['txtPermEmail']));
			$this->cmbPermCountry		=	$postArr['cmbPermCountry'];
			$this->cmbPermProvince		=	$postArr['cmbPermProvince'];
			$this->cmbPermDistrict		=	(($postArr['cmbPermDistrict']));
			$this->cmbPermElectorate	=	(($postArr['cmbPermElectorate']));
			$this->cmbBank				= 	$postArr['cmbBank'];
			
			return $this;
	}
	
}
?>
