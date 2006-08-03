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
require_once ROOT_PATH . '/lib/models/hrfunct/EmpEmergencyCon.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpPassPort.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpDependents.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpChildren.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpBank.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpAttach.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpPhoto.php';

class EXTRACTOR_EmpInfo {
	
	
	var $txtEmpLastName;
	var $txtEmpFirstName;
	var $txtEmpMiddleName;
	var $txtEmpNickName;
	
	
	var $txtNICNo;
	var $cmbNation;
	var $txtSINNo;
	var $DOB;
	var $txtOtherID;
	var $cmbMarital;
	var $chkSmokeFlag;
	var $optGender;
	var $txtLicenNo;
	var $txtLicExpDate;
	var $txtMilitarySer;
	var $cmbEthnicRace;
	
	var $cmbEEOCat;
	var	$cmbLocation;
	var	$txtJobTitle;
	var $cmbType;
	var $txtJoinedDate;
	
	var $cmbCountry;
	var $txtStreet1;
	var $cmbCity;
	var $cmbProvince;
	var $txtStreet2;
	var $txtzipCode;
	var $txtHmTelep;
	var $txtMobile;
	var $txtWorkTelep;
	var $txtWorkEmail;
	var $txtOtherEmail;
	
	function EXTRACTOR_EmpInfo() {

		$this->parent_empinfo = new EmpInfo();
	}

	function parseAddData($postArr) {	
			
		$this->parent_empinfo -> setEmpId($this->parent_empinfo->getLastRecord());
		$this->parent_empinfo -> setEmpLastName(($postArr['txtEmpLastName']));
		$this->parent_empinfo -> setEmpFirstName(trim($postArr['txtEmpFirstName']));
		$this->parent_empinfo -> setEmpNickName(trim($postArr['txtEmpNickName']));
		$this->parent_empinfo -> setEmpMiddleName(trim($postArr['txtEmpMiddleName']));
		
		//personal
/*		$this->parent_empinfo -> setEmpSINNo(trim($postArr['txtSINNo']));
		$this->parent_empinfo -> setEmpSSNNo(trim($postArr['txtNICNo']));
		$this->parent_empinfo -> setEmpDOB(trim($postArr['DOB']));
		$this->parent_empinfo -> setEmpOthID(trim($postArr['txtOtherID']));
		$this->parent_empinfo -> setEmpGender(trim($postArr['optGender']));
		$this->parent_empinfo -> setEmpDriLicNo(($postArr['txtLicenNo']));
		$this->parent_empinfo -> setEmpNation(($postArr['cmbNation']));
		$this->parent_empinfo -> setEmpDriLicExpDat(($postArr['txtLicExpDate']));
		$this->parent_empinfo -> setEmpMarital(($postArr['cmbMarital']));
		$this->parent_empinfo -> setEmpMilitary(trim($postArr['txtMilitarySer']));
		$this->parent_empinfo -> setEmpsmoker(isset($postArr['chkSmokeFlag'])?'1':'0');
		$this->parent_empinfo -> setEmpEthnicRace(($postArr['cmbEthnicRace']));
		//job info
		$this->parent_empinfo -> setEmpJobTitle(trim($postArr['cmbJobTitle']));
		$this->parent_empinfo -> setEmpStatus(trim($postArr['cmbType']));
		$this->parent_empinfo -> setEmpEEOCat(trim($postArr['cmbEEOCat']));
		$this->parent_empinfo -> setEmpLocation(($postArr['cmbLocation']));
		$this->parent_empinfo -> setEmpJoinedDate(($postArr['txtJoinedDate']));
		
		//contact
		$this->parent_empinfo -> setEmpStreet1(trim($postArr['txtStreet1']));
		$this->parent_empinfo -> setEmpStreet2(trim($postArr['txtStreet2']));
		$this->parent_empinfo -> setEmpCity(trim($postArr['cmbCity']));
		$this->parent_empinfo -> setEmpProvince(trim($postArr['cmbProvince']));
		$this->parent_empinfo -> setEmpCountry(trim($postArr['cmbCountry']));
		$this->parent_empinfo -> setEmpZipCode(trim($postArr['txtzipCode']));
		$this->parent_empinfo -> setEmpHomeTelephone(trim($postArr['txtHmTelep']));
		$this->parent_empinfo -> setEmpMobile(trim($postArr['txtMobile']));
		$this->parent_empinfo -> setEmpWorkTelephone(trim($postArr['txtWorkTelep']));
		$this->parent_empinfo -> setEmpWorkEmail(($postArr['txtWorkEmail']));
		$this->parent_empinfo -> setEmpOtherEmail(($postArr['txtOtherEmail'])); */
		
		
		$objectArr['EmpInfo'] =  $this->parent_empinfo;
		
		if($_FILES['photofile']['size']>0 && stristr($_FILES['photofile']['type'],'image') != false) {
				
				$photo = new EmpPicture();
				
					//file info
					$fileName = $_FILES['photofile']['name'];
					$tmpName  = $_FILES['photofile']['tmp_name'];
					$fileSize = $_FILES['photofile']['size'];
					$fileType = $_FILES['photofile']['type'];
		
					//file read
					$fp = fopen($tmpName,'r');
					$contents = fread($fp,filesize($tmpName));
					$contents = addslashes($contents);
					fclose($fp);
					
					if(!get_magic_quotes_gpc())
						$fileName=addslashes($fileName);
						
				$photo->setEmpId($postArr['txtEmpID']);
				$photo->setEmpPicture($contents);
				$photo->setEmpFilename($fileName);
				$photo->setEmpPicType($fileType);
				$photo->setEmpPicSize($fileSize);
									
				$objectArr['EmpPhoto'] = $photo;
		}
	return $objectArr;
	}
	
			
	function parseEditData($postArr) {	
		
	if($postArr['main']=='1') {
		
		$this->parent_empinfo -> setEmpId(trim($postArr['txtEmpID']));
		//$this->parent_empinfo -> setEmpId($this->parent_empinfo->getLastRecord());
		$this->parent_empinfo -> setEmpLastName(($postArr['txtEmpLastName']));
		$this->parent_empinfo -> setEmpFirstName(trim($postArr['txtEmpFirstName']));
		$this->parent_empinfo -> setEmpNickName(trim($postArr['txtEmpNickName']));
		$this->parent_empinfo -> setEmpMiddleName(trim($postArr['txtEmpMiddleName']));
		
		$objectArr['EmpMain'] = $this->parent_empinfo;
	}
	
	//personal
	if($postArr['personalFlag']=='1') {
		$this->parent_empinfo -> setEmpId(trim($postArr['txtEmpID']));
		$this->parent_empinfo -> setEmpSINNo(trim($postArr['txtSINNo']));
		$this->parent_empinfo -> setEmpSSNNo(trim($postArr['txtNICNo']));
		$this->parent_empinfo -> setEmpDOB(trim($postArr['DOB']));
		$this->parent_empinfo -> setEmpOthID(trim($postArr['txtOtherID']));
		$this->parent_empinfo -> setEmpGender(trim($postArr['optGender']));
		$this->parent_empinfo -> setEmpDriLicNo(($postArr['txtLicenNo']));
		$this->parent_empinfo -> setEmpNation(($postArr['cmbNation']));
		$this->parent_empinfo -> setEmpDriLicExpDat(($postArr['txtLicExpDate']));
		$this->parent_empinfo -> setEmpMarital(($postArr['cmbMarital']));
		$this->parent_empinfo -> setEmpMilitary(trim($postArr['txtMilitarySer']));
		$this->parent_empinfo -> setEmpsmoker(isset($postArr['chkSmokeFlag'])?'1':'0');
		$this->parent_empinfo -> setEmpEthnicRace(($postArr['cmbEthnicRace']));
		
		$objectArr['EmpPers'] = $this->parent_empinfo;
	}
				
	//job info
	if($postArr['jobFlag']=='1') {
		$this->parent_empinfo -> setEmpId(trim($postArr['txtEmpID']));
		$this->parent_empinfo -> setEmpJobTitle(trim($postArr['cmbJobTitle']));
		$this->parent_empinfo -> setEmpStatus(trim($postArr['cmbType']));
		$this->parent_empinfo -> setEmpEEOCat(trim($postArr['cmbEEOCat']));
		$this->parent_empinfo -> setEmpLocation(($postArr['cmbLocation']));
		$this->parent_empinfo -> setEmpJoinedDate(($postArr['txtJoinedDate']));
		
		$objectArr['EmpJobInfo'] = $this->parent_empinfo;

		//job stat
	/*$this->parent_empinfo -> setEmpId(trim($postArr['txtEmpID']));
		$this->parent_empinfo -> setEmpType(($postArr['cmbType']));
		$this->parent_empinfo -> setEmpStatutory(($postArr['cmbStatutory']));
		$this->parent_empinfo -> setEmpCat(($postArr['cmbCat']));
		$this->parent_empinfo -> setEmpStartDat(trim($postArr['txtStartDat']));
		$this->parent_empinfo -> setEmpEndDat(trim($postArr['txtEndDat']));
		$this->parent_empinfo -> setEmpConToPermFlag(isset($postArr['chkConToPermFlag'])?'1':'0');
		$this->parent_empinfo -> setEmpConToPermDat(trim($postArr['txtConToPermDat']));
		$this->parent_empinfo -> setEmpHRActivFlag(isset($postArr['chkHRActivFlag'])?'1':'0');
		$this->parent_empinfo -> setEmpPayActivFlag(isset($postArr['txtPayActivFlag'])?'1':'0');
		$this->parent_empinfo -> setEmpTimAttActivFlag(isset($postArr['chkTimeAttActivFlag'])?'1':'0');
		
		$objectArr['EmpJobStat'] = $this->parent_empinfo;*/
	}
	
	//tax
/*	if($postArr['taxFlag']=='1') {
		$this->parent_empinfo -> setEmpId(trim($postArr['txtEmpID']));
		$this->parent_empinfo -> setEmpTaxExempt(($postArr['cmbTaxExempt']));
		$this->parent_empinfo -> setEmpTaxOnTaxFlag(isset($postArr['chkTaxOnTaxFlag'])?'1':'0');
		$this->parent_empinfo -> setEmpTaxID(trim($postArr['cmbTaxID']));
		$this->parent_empinfo -> setEmpEPFEligibleFlag(isset($postArr['chkEPFEligibleFlag'])?'1':'0');
		$this->parent_empinfo -> setEmpEPFNo(trim($postArr['txtEPFNo']));
		$this->parent_empinfo -> setCFundCBFundFlag(trim($postArr['optCFundCBFundFlag']));
		$this->parent_empinfo -> setEPFEmployeePercen(trim($postArr['txtEPFEmployeePercen']));
		$this->parent_empinfo -> setEPFEmployerPercen(trim($postArr['txtEPFEmployerPercen']));
		$this->parent_empinfo -> setETFEligibleFlag(isset($postArr['chkETFEligibleFlag'])?'1':'0');
		$this->parent_empinfo -> setEmpETFNo(trim($postArr['txtETFNo']));
		$this->parent_empinfo -> setETFEmployeePercen(trim($postArr['txtETFEmployeePercen']));
		$this->parent_empinfo -> setETFDat(trim($postArr['txtETFDat']));
		$this->parent_empinfo -> setMSPSEligibleFlag(isset($postArr['chkMSPSEligibleFlag'])?'1':'0');
		$this->parent_empinfo -> setMSPSEmployeePercen(trim($postArr['txtMSPSEmployeePercen']));
		$this->parent_empinfo -> setMSPSEmployerPercen(trim($postArr['txtMSPSEmployerPercen']));
		
		$objectArr['EmpTax'] = $this->parent_empinfo;
	}
*/
		//$this->parent_empinfo -> setEmpLoc($postArr['cmbLocation']);
		//$this->parent_empinfo -> setEmpPrefLoc($postArr['txtHiCode']);
		
		//$objectArr['EmpWrkStation'] = $this->parent_empinfo;
		
			//contact			
	if($postArr['contactFlag']=='1' || !isset($postArr['contactFlag'])) {
		$this->parent_empinfo -> setEmpId(trim($postArr['txtEmpID']));
		$this->parent_empinfo -> setEmpStreet1(trim($postArr['txtStreet1']));
		$this->parent_empinfo -> setEmpStreet2(trim($postArr['txtStreet2']));
		$this->parent_empinfo -> setEmpCity(trim($postArr['cmbCity']));
		$this->parent_empinfo -> setEmpProvince(trim($postArr['cmbProvince']));
		$this->parent_empinfo -> setEmpCountry(trim($postArr['cmbCountry']));
		$this->parent_empinfo -> setEmpZipCode(trim($postArr['txtzipCode']));
		$this->parent_empinfo -> setEmpHomeTelephone(trim($postArr['txtHmTelep']));
		$this->parent_empinfo -> setEmpMobile(trim($postArr['txtMobile']));
		$this->parent_empinfo -> setEmpWorkTelephone(trim($postArr['txtWorkTelep']));
		$this->parent_empinfo -> setEmpWorkEmail(($postArr['txtWorkEmail']));
		$this->parent_empinfo -> setEmpOtherEmail(($postArr['txtOtherEmail']));
		
		
		$objectArr['EmpPermRes'] = $this->parent_empinfo;
		}
	
	return $objectArr;
  }

	function parseCountryData($postArr) {
		$this->parent_empinfo -> setEmpId(trim($_POST['txtEmpID']));
		$this->parent_empinfo->setEmpTaxCountry($_POST['cmbTaxCountry']);
		
		return $this->parent_empinfo;
	}
	
/*	function reloadData($postArr) {	
			
			$this->cmbEmpTitle		    =	(trim($postArr['cmbEmpTitle']));
			$this->txtEmpCallName		=	(trim($postArr['txtEmpCallName']));
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
			
			//job info
			$this->txtDatJoin		=	(trim($postArr['txtDatJoin']));
			$this->chkConfirmFlag	=	(isset($postArr['chkConfirmFlag'])?'1':'0');
			$this->txtResigDat		=	(trim($postArr['txtResigDat']));
			$this->txtRetireDat		=	(trim($postArr['txtRetireDat']));
			$this->cmbSalGrd		=	$postArr['cmbSalGrd'];
			$this->cmbCorpTit		=	$postArr['cmbCorpTit'];
			$this->cmbDesig			=	(($postArr['cmbDesig']));
			$this->cmbCostCode		=	(($postArr['cmbCostCode']));
			$this->txtWorkHours		=	(trim($postArr['txtWorkHours']));
			$this->txtJobPref		=	(trim($postArr['txtJobPref']));
			
			//job stat
			$this->cmbType			=	(($postArr['cmbType']));
			$this->cmbStatutory		=	(($postArr['cmbStatutory']));
			$this->cmbCat			=	(($postArr['cmbCat']));
			$this->txtStartDat		= 	(trim($postArr['txtStartDat']));
			$this->chkConToPermFlag =	(isset($postArr['chkConToPermFlag'])?'1':'0');
			$this->txtConToPermDat	= 	(trim($postArr['txtConToPermDat']));
			$this->chkHRActivFlag	=	(isset($postArr['chkHRActivFlag'])?'1':'0');
			$this->txtPayActivFlag	=	(isset($postArr['txtPayActivFlag'])?'1':'0');
			$this->chkTimeAttActivFlag = (isset($postArr['chkTimeAttActivFlag'])?'1':'0');	
			
			//workstation	   
			$this->cmbLocation		=	($postArr['cmbLocation']);
			$this->txtHiCode		=	($postArr['txtHiCode']);
			$this->cmbTaxCountry	=	$postArr['cmbTaxCountry'];
			
			//tax
			$this->cmbTaxExempt			=	(($postArr['cmbTaxExempt']));
			$this->chkTaxOnTaxFlag		=	(isset($postArr['chkTaxOnTaxFlag'])?'1':'0');
			$this->txtTaxID				=	(trim($postArr['txtTaxID']));
			$this->chkEPFEligibleFlag	=	(isset($postArr['chkEPFEligibleFlag'])?'1':'0');
			$this->txtEPFNo				=	(trim($postArr['txtEPFNo']));
			$this->optCFundCBFundFlag	=	(trim($postArr['optCFundCBFundFlag']));
			$this->txtEPFEmployeePercen	=	(trim($postArr['txtEPFEmployeePercen']));
			$this->txtEPFEmployerPercen	=	(trim($postArr['txtEPFEmployerPercen']));
			$this->chkETFEligibleFlag	=	(isset($postArr['chkETFEligibleFlag'])?'1':'0');
			$this->txtETFNo				=	(trim($postArr['txtETFNo']));
			$this->txtETFEmployeePercen	=	(trim($postArr['txtETFEmployeePercen']));
			$this->txtETFDat			=	(trim($postArr['txtETFDat']));
			$this->chkMSPSEligibleFlag	=	(isset($postArr['chkMSPSEligibleFlag'])?'1':'0');
			$this->txtMSPSEmployeePercen  =	(trim($postArr['txtMSPSEmployeePercen']));
			$this->txtMSPSEmployerPercen  =	(trim($postArr['txtMSPSEmployerPercen']));
			
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
			/*$this->cmbPermDistrict		=	(($postArr['cmbPermDistrict']));
			$this->cmbPermElectorate	=	(($postArr['cmbPermElectorate']));
			$this->cmbBank				= 	$postArr['cmbBank'];
			
			return $this;
	}
	*/
}
?>
