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
require_once ROOT_PATH . '/lib/common/CommonFunctions.php';

class EXTRACTOR_EmpInfo {

	private $isESS;

	private $txtEmpLastName;
	private $txtEmpFirstName;
	private $txtEmpMiddleName;
	private $txtEmpNickName;

	private $txtNICNo;
	private $cmbNation;
	private $txtSINNo;
	private $DOB;
	private $txtOtherID;
	private $cmbMarital;
	private $chkSmokeFlag;
	private $optGender;
	private $txtLicenNo;
	private $txtLicExpDate;
	private $txtMilitarySer;
	private $cmbEthnicRace;

	private $cmbEEOCat;
	private	$cmbLocation;
	private	$txtJobTitle;
	private $cmbType;
	private $txtJoinedDate;
	private $txtTerminatedDate;
	private $txtTerminationRes;

	private $cmbCountry;
	private $txtStreet1;
	private $cmbCity;
	private $cmbProvince;
	private $txtStreet2;
	private $txtzipCode;
	private $txtHmTelep;
	private $txtMobile;
	private $txtWorkTelep;
	private $txtWorkEmail;
	private $txtOtherEmail;

	public function __construct() {
		$this->parent_empinfo = new EmpInfo();
	}

	public function ESS() {
		$this->isESS = true;
	}

	public function parseAddData($postArr) {

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


	public function parseEditData($postArr) {

		if ($postArr['main']=='1') {

			$this->parent_empinfo -> setEmpId(trim($postArr['txtEmpID']));
			if (isset($postArr['txtEmployeeId'])) {
				$this->parent_empinfo -> setEmployeeID(trim($postArr['txtEmployeeId']));
			}

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
			$this->parent_empinfo -> setEmpDOB(self::_handleEmptyDates($postArr['DOB']));
			$this->parent_empinfo -> setEmpGender(trim($postArr['optGender']));
			$this->parent_empinfo -> setEmpDriLicNo(($postArr['txtLicenNo']));
			$this->parent_empinfo -> setEmpNation(($postArr['cmbNation']));
			$this->parent_empinfo -> setEmpDriLicExpDat(self::_handleEmptyDates($postArr['txtLicExpDate']));
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
			$postArr['txtTermDate']=LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtTermDate']);

			$this->parent_empinfo -> setEmpId(trim($postArr['txtEmpID']));
			$this->parent_empinfo -> setEmpJobTitle(trim($postArr['cmbJobTitle']));
			$this->parent_empinfo -> setEmpStatus(trim($postArr['cmbType']));
			$this->parent_empinfo -> setEmpEEOCat(trim($postArr['cmbEEOCat']));
			$this->parent_empinfo -> setEmpLocation(($postArr['cmbLocation']));
			$this->parent_empinfo -> setEmpJoinedDate(self::_handleEmptyDates($postArr['txtJoinedDate']));
			$this->parent_empinfo ->setEmpTerminatedDate(self::_handleEmptyDates($postArr['txtTermDate']));
			$this->parent_empinfo ->setEmpTerminationReason(($postArr['txtTermReason']));

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
				$this->parent_empinfo->setCustom1(CommonFunctions::escapeHTML(trim($postArr['custom1'])));
			}
			if (isset($postArr['custom2'])) {
				$this->parent_empinfo->setCustom2(CommonFunctions::escapeHTML(trim($postArr['custom2'])));
			}
			if (isset($postArr['custom3'])) {
				$this->parent_empinfo->setCustom3(CommonFunctions::escapeHTML(trim($postArr['custom3'])));
			}
			if (isset($postArr['custom4'])) {
				$this->parent_empinfo->setCustom4(CommonFunctions::escapeHTML(trim($postArr['custom4'])));
			}
			if (isset($postArr['custom5'])) {
				$this->parent_empinfo->setCustom5(CommonFunctions::escapeHTML(trim($postArr['custom5'])));
			}
			if (isset($postArr['custom6'])) {
				$this->parent_empinfo->setCustom6(CommonFunctions::escapeHTML(trim($postArr['custom6'])));
			}
			if (isset($postArr['custom7'])) {
				$this->parent_empinfo->setCustom7(CommonFunctions::escapeHTML(trim($postArr['custom7'])));
			}
			if (isset($postArr['custom8'])) {
				$this->parent_empinfo->setCustom8(CommonFunctions::escapeHTML(trim($postArr['custom8'])));
			}
			if (isset($postArr['custom9'])) {
				$this->parent_empinfo->setCustom9(CommonFunctions::escapeHTML(trim($postArr['custom9'])));
			}
			if (isset($postArr['custom10'])) {
				$this->parent_empinfo->setCustom10(CommonFunctions::escapeHTML(trim($postArr['custom10'])));
			}
			$objectArr['EmpCustomInfo'] = $this->parent_empinfo;
		}

  		return isset($objectArr)? $objectArr : false;

	}

	private static function _handleEmptyDates($date) {

		$date = trim($date);

	    if ($date == "" || $date == "YYYY-mm-DD" || $date == "0000-00-00") {
			return "null";
	    } else {
	        return "'".$date."'";
	    }

	}


}
?>
