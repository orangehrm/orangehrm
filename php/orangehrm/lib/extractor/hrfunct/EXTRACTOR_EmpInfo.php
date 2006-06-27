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
	
			/*var $txtEmpLastName;
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
			
			//job info
			var $txtDatJoin;
			var $chkConfirmFlag;
			var $txtResigDat	;
			var $txtRetireDat;
			var $cmbSalGrd	;
			var $cmbCorpTit	;
			var $cmbDesig;
			var $cmbCostCode;
			var $txtWorkHours;
			var $txtJobPref;
			
			//job stat
			var $cmbType;
			var $cmbStatutory;
			var $cmbCat;
			var $txtStartDat;
			var $chkConToPermFlag ;
			var $txtConToPermDat;
			var $chkHRActivFlag;
			var $txtPayActivFlag;
			var $chkTimeAttActivFlag ;
			
			//workstation	   
			var $cmbLocation;
			var $txtHiCode;
			var $cmbTaxCountry;
			*/
			//tax
			var $cmbTaxExempt;
			var $chkTaxOnTaxFlag;
			var $txtTaxID;
			var $chkEPFEligibleFlag	;
			var $txtEPFNo;
			var $optCFundCBFundFlag;
			var $txtEPFEmployeePercen;
			var $txtEPFEmployerPercen;
			var $chkETFEligibleFlag;
			var $txtETFNo;
			var $txtETFEmployeePercen;
			var $txtETFDat;
			var $chkMSPSEligibleFlag;
			var $txtMSPSEmployeePercen;
			var $txtMSPSEmployerPercen ;
			
		/*	//contact			
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
*/
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
		//job info
		$this->parent_empinfo -> setEmpJobTitle(trim($postArr['cmbJobTitle']));
		$this->parent_empinfo -> setEmpStatus(trim($postArr['cmbType']));
		$this->parent_empinfo -> setEmpEEOCat(trim($postArr['cmbEEOCat']));
		$this->parent_empinfo -> setEmpLocation(($postArr['cmbLocation']));
		$this->parent_empinfo -> setEmpJoinedDate(($postArr['txtJoinedDate']));
		
	/*	//job stat
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
		//workstation	   
		//$this->parent_empinfo -> setEmpLoc($postArr['cmbLocation']);
		$this->parent_empinfo -> setEmpPrefLoc($postArr['txtHiCode']);
		$this->parent_empinfo -> setEmpTaxCountry($postArr['cmbTaxCountry']);
		//tax
		if(isset($postArr['skipTax']) && $postArr['skipTax']=='0') {
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
		}var $;
	
		*/
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
		$this->parent_empinfo -> setEmpOtherEmail(($postArr['txtOtherEmail']));
		
		
		$objectArr['EmpInfo'] =  $this->parent_empinfo;
			
		
		if(isset($_POST['passportFlag']) && $_POST['passportFlag']==1 && $_POST['txtPPNo']!='') {
			$pport= new EmpPassPort();
			
			$pport->setEmpId($_POST['txtEmpID']);
			$pport->setEmpPPSeqNo(1);
			$pport->setEmpPPNo(trim($_POST['txtPPNo']));
			$pport->setEmpPPIssDat(trim($_POST['txtPPIssDat']));
			$pport->setEmpI9ReviewDat(trim($_POST['txtI9ReviewDat']));
			$pport->setEmpPPExpDat(trim($_POST['txtPPExpDat']));
			$pport->setEmpPPComment(trim($_POST['txtComments']));
			$pport->setEmppassportflag($_POST['PPType']);
			$pport->setEmpNationality($_POST['cmbPPCountry']);
			$pport->setEmpI9Status(trim($_POST['txtI9status']));
			
			$objectArr['EmpPassPort'] = $pport;
		}
		
		if(isset($_POST['dependentsFlag']) && $_POST['dependentsFlag']==1 && $_POST['txtDepName']!='') {
			$dep= new EmpDependents();
			
			$dep->setEmpId($_POST['txtEmpID']);
			$dep->setEmpDSeqNo(1);
			$dep->setEmpDepName(trim($_POST['txtDepName']));
			$dep->setEmpDepRel(trim($_POST['txtRelShip']));
						
			$objectArr['EmpDependents'] = $dep;
		}
		
		if(isset($_POST['childrenFlag']) && $_POST['childrenFlag']==1 && $_POST['txtChiName']!='') {
			$chi= new EmpChildren();
			
			$chi->setEmpId($_POST['txtEmpID']);
			$chi->setEmpCSeqNo(1);
			$chi->setEmpChiName(trim($_POST['txtChiName']));
			$chi->setEmpDOB(trim($_POST['ChiDOB']));
						
			$objectArr['EmpChildren'] = $chi;
		}
		
		if(isset($_POST['econtactFlag']) && $_POST['econtactFlag']==1 && $_POST['txtEConName']!='') {
			$econ= new EmpEmergencyCon();
			
			$econ->setEmpId($_POST['txtEmpID']);
			$econ->setEmpECSeqNo(1);
			$econ->setEmpEConName(trim($_POST['txtEConName']));
			$econ->setEmpEConRel(trim($_POST['txtEConRel']));
			$econ->setEmpEConHmTel(trim($_POST['txtEConHmTel']));
			$econ->setEmpEConMobile(trim($_POST['txtEConMobile']));
			$econ->setEmpEConWorkTel(trim($_POST['txtEConWorkTel']));
						
			$objectArr['EmpEContact'] = $econ;
		}
		
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
			
		if(isset($_POST['attachmentFlag']) && $_POST['attachmentFlag']==1 && $_FILES['ufile']['size']>0) {
			$attachment = new EmpAttach();
			
			//file info
			$fileName=$_FILES['ufile']['name'];
			$tmpName  = $_FILES['ufile']['tmp_name'];
			$fileSize = $_FILES['ufile']['size'];
			$fileType = $_FILES['ufile']['type'];

			//file read
			$fp = fopen($tmpName,'r');
			$contents = fread($fp,filesize($tmpName));
			$contents = addslashes($contents);
			fclose($fp);
			
			if(!get_magic_quotes_gpc())
				$fileName=addslashes($fileName);
				
			$attachment->setEmpId($_POST['txtEmpID']);
			$attachment->setEmpAttId(1);
			$attachment->setEmpAttDesc(trim($_POST['txtAttDesc']));
			$attachment->setEmpAttFilename($fileName);
			$attachment->setEmpAttSize($fileSize);
			$attachment->setEmpAttachment($contents);
			$attachment->setEmpAttType($fileType);
			
			$objectArr['EmpAttach'] = $attachment;
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
	if($postArr['contactFlag']=='1') {
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
