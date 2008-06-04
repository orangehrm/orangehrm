<?php
/*
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
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
 */

require_once ROOT_PATH . '/lib/models/hrfunct/EmpInfo.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpEmergencyCon.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpPassPort.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpDependents.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpChildren.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpAttach.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpPhoto.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpTax.php';
require_once ROOT_PATH . '/lib/extractor/hrfunct/EXTRACTOR_EmpPhoto.php';

class EXTRACTOR_EmpInfo {

	var $isESS;

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

	function ESS() {
		$this->isESS = true;
	}

	function EXTRACTOR_EmpInfo() {

		$this->parent_empinfo = new EmpInfo();
	}

	function parseAddData($postArr) {

		$this->parent_empinfo -> setEmployeeID(trim($postArr['txtEmployeeId']));
		$this->parent_empinfo -> setEmpLastName(($postArr['txtEmpLastName']));
		$this->parent_empinfo -> setEmpFirstName(trim($postArr['txtEmpFirstName']));
		$this->parent_empinfo -> setEmpNickName(trim($postArr['txtEmpNickName']));
		$this->parent_empinfo -> setEmpMiddleName(trim($postArr['txtEmpMiddleName']));

		$objectArr['EmpInfo'] =  $this->parent_empinfo;
           
        $photoExtractor = new EXTRACTOR_EmpPhoto();
        $photo = $photoExtractor->parseData();
        if (!empty($photo)) {     
                $objectArr['EmpPhoto'] = $photo;                       
        }

        return isset($objectArr)? $objectArr : false;
	}


	function parseEditData($postArr) {

	if ($postArr['main']=='1') {

		$this->parent_empinfo -> setEmpId(trim($postArr['txtEmpID']));
		if (isset($postArr['txtEmployeeId'])) {
			$this->parent_empinfo -> setEmployeeID(trim($postArr['txtEmployeeId']));
		}
		//$this->parent_empinfo -> setEmployeeID(trim($postArr['txtEmpID']));

		$this->parent_empinfo -> setEmpLastName(($postArr['txtEmpLastName']));
		$this->parent_empinfo -> setEmpFirstName(trim($postArr['txtEmpFirstName']));
		$this->parent_empinfo -> setEmpNickName(trim($postArr['txtEmpNickName']));
		$this->parent_empinfo -> setEmpMiddleName(trim($postArr['txtEmpMiddleName']));


		$objectArr['EmpMain'] = $this->parent_empinfo;
	}

	//personal
	if ($postArr['personalFlag']=='1') {

		$postArr['DOB']=LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['DOB']);
		$postArr['txtLicExpDate']=LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtLicExpDate']);

		$this->parent_empinfo -> setEmpId(trim($postArr['txtEmpID']));
		$this->parent_empinfo -> setEmpSINNo(trim($postArr['txtSINNo']));
		$this->parent_empinfo -> setEmpSSNNo(trim($postArr['txtNICNo']));
		$this->parent_empinfo -> setEmpDOB(trim($postArr['DOB']));
		$this->parent_empinfo -> setEmpGender(trim($postArr['optGender']));
		$this->parent_empinfo -> setEmpDriLicNo(($postArr['txtLicenNo']));
		$this->parent_empinfo -> setEmpNation(($postArr['cmbNation']));
		$this->parent_empinfo -> setEmpDriLicExpDat(($postArr['txtLicExpDate']));
		$this->parent_empinfo -> setEmpOthID(trim($postArr['txtOtherID']));
		$this->parent_empinfo -> setEmpMarital(($postArr['cmbMarital']));
		$this->parent_empinfo -> setEmpMilitary(trim($postArr['txtMilitarySer']));
		$this->parent_empinfo -> setEmpsmoker(isset($postArr['chkSmokeFlag'])?'1':'0');
		$this->parent_empinfo -> setEmpEthnicRace(($postArr['cmbEthnicRace']));

		$objectArr['EmpPers'] = $this->parent_empinfo;
	}

	//job info
	if ($postArr['jobFlag']=='1' && !(isset($this->isESS) && $this->isESS)) {

		$postArr['txtJoinedDate']=LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtJoinedDate']);

		$this->parent_empinfo -> setEmpId(trim($postArr['txtEmpID']));
		$this->parent_empinfo -> setEmpJobTitle(trim($postArr['cmbJobTitle']));
		$this->parent_empinfo -> setEmpStatus(trim($postArr['cmbType']));
		$this->parent_empinfo -> setEmpEEOCat(trim($postArr['cmbEEOCat']));
		$this->parent_empinfo -> setEmpLocation(($postArr['cmbLocation']));
		$this->parent_empinfo -> setEmpJoinedDate(($postArr['txtJoinedDate']));

		$objectArr['EmpJobInfo'] = $this->parent_empinfo;
	}

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

	if($postArr['taxFlag']=='1') {
		$taxInfo = new EmpTax();
		$taxInfo->setEmpNumber(trim($postArr['txtEmpID']));

		$federalTaxStatus = trim($postArr['cmbTaxFederalStatus']);
		if (!empty($federalTaxStatus)) {
			$taxInfo->setFederalTaxStatus($federalTaxStatus);
		}

		$taxInfo->setFederalTaxExceptions(trim($postArr['taxFederalExceptions']));

		$taxState = trim($postArr['cmbTaxState']);
		if (!empty($taxState)) {
			$taxInfo->setTaxState($taxState);
		}

		$stateTaxStatus = trim($postArr['cmbTaxStateStatus']);
		if (!empty($stateTaxStatus)) {
			$taxInfo->setStateTaxStatus($stateTaxStatus);
		}

		$taxInfo->setStateTaxExceptions(trim($postArr['taxStateExceptions']));

		$unemploymentState = trim($postArr['cmbTaxUnemploymentState']);
		if (!empty($unemploymentState)) {
			$taxInfo->setTaxUnemploymentState($unemploymentState);
		}

		$workState = trim($postArr['cmbTaxWorkState']);
		if (!empty($workState)) {
			$taxInfo->setTaxWorkState($workState);
		}

		$objectArr['EmpTaxInfo'] = $taxInfo;
	}

	if($postArr['customFlag']=='1') {
		$this->parent_empinfo -> setEmpId(trim($postArr['txtEmpID']));
		if (isset($postArr['custom1'])) {
			$this->parent_empinfo->setCustom1(trim($postArr['custom1']));
		}
		if (isset($postArr['custom2'])) {
			$this->parent_empinfo->setCustom2(trim($postArr['custom2']));
		}
		if (isset($postArr['custom3'])) {
			$this->parent_empinfo->setCustom3(trim($postArr['custom3']));
		}
		if (isset($postArr['custom4'])) {
			$this->parent_empinfo->setCustom4(trim($postArr['custom4']));
		}
		if (isset($postArr['custom5'])) {
			$this->parent_empinfo->setCustom5(trim($postArr['custom5']));
		}
		if (isset($postArr['custom6'])) {
			$this->parent_empinfo->setCustom6(trim($postArr['custom6']));
		}
		if (isset($postArr['custom7'])) {
			$this->parent_empinfo->setCustom7(trim($postArr['custom7']));
		}
		if (isset($postArr['custom8'])) {
			$this->parent_empinfo->setCustom8(trim($postArr['custom8']));
		}
		if (isset($postArr['custom9'])) {
			$this->parent_empinfo->setCustom9(trim($postArr['custom9']));
		}
		if (isset($postArr['custom10'])) {
			$this->parent_empinfo->setCustom10(trim($postArr['custom10']));
		}
		$objectArr['EmpCustomInfo'] = $this->parent_empinfo;
	}

  return isset($objectArr)? $objectArr : false;
  }

}
?>
