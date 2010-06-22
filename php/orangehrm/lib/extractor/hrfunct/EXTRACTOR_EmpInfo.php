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

		$this->parent_empinfo -> setEmployeeID(CommonFunctions::cleanParam($postArr['txtEmployeeId'], 50));
		$this->parent_empinfo -> setEmpLastName(CommonFunctions::cleanParam($postArr['txtEmpLastName'], 100));
		$this->parent_empinfo -> setEmpFirstName(CommonFunctions::cleanParam($postArr['txtEmpFirstName'], 100));
		$this->parent_empinfo -> setEmpNickName(CommonFunctions::cleanParam($postArr['txtEmpNickName'], 100));
		$this->parent_empinfo -> setEmpMiddleName(CommonFunctions::cleanParam($postArr['txtEmpMiddleName'], 100));

		$objectArr['EmpInfo'] =  $this->parent_empinfo;

        $photoExtractor = new EXTRACTOR_EmpPhoto();
        $photo = $photoExtractor->parseData();
        if (!empty($photo)) {
                $objectArr['EmpPhoto'] = $photo;
        }

        return isset($objectArr)? $objectArr : false;
	}


	public function parseEditData($postArr) {

		if ($postArr['main']=='1') { // TODO: Check whether this code block is in use. Same content was appended to personalFlag

			$this->parent_empinfo -> setEmpId(CommonFunctions::cleanParam($postArr['txtEmpID']));
			if (isset($postArr['txtEmployeeId'])) {
				$this->parent_empinfo -> setEmployeeID(CommonFunctions::cleanParam($postArr['txtEmployeeId'], 50));
			}

			$this->parent_empinfo -> setEmpLastName(CommonFunctions::cleanParam($postArr['txtEmpLastName'], 100));
			$this->parent_empinfo -> setEmpFirstName(CommonFunctions::cleanParam($postArr['txtEmpFirstName'], 100));
			$this->parent_empinfo -> setEmpNickName(CommonFunctions::cleanParam($postArr['txtEmpNickName'], 100));
			$this->parent_empinfo -> setEmpMiddleName(CommonFunctions::cleanParam($postArr['txtEmpMiddleName'], 100));


			$objectArr['EmpMain'] = $this->parent_empinfo;
			
		}

		//personal
		if ($postArr['personalFlag']=='1') {
			
			if (isset($postArr['DOB'])) {

                $dob = CommonFunctions::cleanParam($postArr['DOB']);
			    $dob=LocaleUtil::getInstance()->convertToStandardDateFormat($dob);
			    $this->parent_empinfo -> setEmpDOB(self::_handleEmptyDates($dob));
			}

			$postArr['txtLicExpDate']=LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtLicExpDate']);

			$this->parent_empinfo -> setEmpId(CommonFunctions::cleanParam($postArr['txtEmpID']));
			$this->parent_empinfo -> setEmpSINNo(CommonFunctions::cleanParam($postArr['txtSINNo'], 100));
			$this->parent_empinfo -> setEmpSSNNo(CommonFunctions::cleanParam($postArr['txtNICNo'], 100));
			$this->parent_empinfo -> setEmpGender(CommonFunctions::cleanParam($postArr['optGender']));
			$this->parent_empinfo -> setEmpDriLicNo(CommonFunctions::cleanParam($postArr['txtLicenNo'], 100));
			$this->parent_empinfo -> setEmpNation(CommonFunctions::cleanParam($postArr['cmbNation']));
			$this->parent_empinfo -> setEmpDriLicExpDat(self::_handleEmptyDates(CommonFunctions::cleanParam($postArr['txtLicExpDate'])));
			$this->parent_empinfo -> setEmpOthID(CommonFunctions::cleanParam($postArr['txtOtherID'], 100));
			$this->parent_empinfo -> setEmpMarital(($postArr['cmbMarital']));
			$this->parent_empinfo -> setEmpMilitary(CommonFunctions::cleanParam($postArr['txtMilitarySer'], 100));
			$this->parent_empinfo -> setEmpsmoker(isset($postArr['chkSmokeFlag'])?'1':'0');
			$this->parent_empinfo -> setEmpEthnicRace(CommonFunctions::cleanParam($postArr['cmbEthnicRace']));

			$objectArr['EmpPers'] = $this->parent_empinfo;
			
			$this->parent_empinfo -> setEmpId(CommonFunctions::cleanParam($postArr['txtEmpID']));
			if (isset($postArr['txtEmployeeId'])) {
				$this->parent_empinfo -> setEmployeeID(CommonFunctions::cleanParam($postArr['txtEmployeeId'], 50));
			}

			$this->parent_empinfo -> setEmpLastName(CommonFunctions::cleanParam($postArr['txtEmpLastName'], 100));
			$this->parent_empinfo -> setEmpFirstName(CommonFunctions::cleanParam($postArr['txtEmpFirstName'], 100));
			$this->parent_empinfo -> setEmpNickName(CommonFunctions::cleanParam($postArr['txtEmpNickName'], 100));
			$this->parent_empinfo -> setEmpMiddleName(CommonFunctions::cleanParam($postArr['txtEmpMiddleName'], 100));

			$objectArr['EmpMain'] = $this->parent_empinfo;

		}

		//job info
		if ($postArr['jobFlag']=='1' && !(isset($this->isESS) && $this->isESS)) {

			$postArr['txtJoinedDate']=LocaleUtil::getInstance()->convertToStandardDateFormat(CommonFunctions::cleanParam($postArr['txtJoinedDate']));
			$postArr['txtTermDate']=LocaleUtil::getInstance()->convertToStandardDateFormat(CommonFunctions::cleanParam($postArr['txtTermDate']));

			$this->parent_empinfo -> setEmpId(CommonFunctions::cleanParam($postArr['txtEmpID']));
			$this->parent_empinfo -> setEmpJobTitle(CommonFunctions::cleanParam($postArr['cmbJobTitle'], 13));
			if (isset($postArr['cmbType'])) {
				$this->parent_empinfo -> setEmpStatus(CommonFunctions::cleanParam($postArr['cmbType'], 13));
			} else {
			    $this->parent_empinfo -> setEmpStatus(CommonFunctions::cleanParam($postArr['hidType'], 13));
			}
			$this->parent_empinfo -> setEmpEEOCat(CommonFunctions::cleanParam($postArr['cmbEEOCat'], 13));
			$this->parent_empinfo -> setEmpLocation(CommonFunctions::cleanParam($postArr['cmbLocation']));
			$this->parent_empinfo -> setEmpJoinedDate(self::_handleEmptyDates($postArr['txtJoinedDate']));
			$this->parent_empinfo ->setEmpTerminatedDate(self::_handleEmptyDates($postArr['txtTermDate']));
			$this->parent_empinfo ->setEmpTerminationReason(CommonFunctions::cleanParam($postArr['txtTermReason'], 256));

			$objectArr['EmpJobInfo'] = $this->parent_empinfo;
		}

		if($postArr['contactFlag']=='1') {
			$this->parent_empinfo -> setEmpId(CommonFunctions::cleanParam($postArr['txtEmpID']));
			$this->parent_empinfo -> setEmpStreet1(CommonFunctions::cleanParam($postArr['txtStreet1'], 100));
			$this->parent_empinfo -> setEmpStreet2(CommonFunctions::cleanParam($postArr['txtStreet2'], 100));
			$this->parent_empinfo -> setEmpCity(CommonFunctions::cleanParam($postArr['cmbCity'], 100));
			$this->parent_empinfo -> setEmpProvince(CommonFunctions::cleanParam($postArr['cmbProvince'], 100));
			$this->parent_empinfo -> setEmpCountry(CommonFunctions::cleanParam($postArr['cmbCountry'], 100));
			$this->parent_empinfo -> setEmpZipCode(CommonFunctions::cleanParam($postArr['txtzipCode'], 20));
			$this->parent_empinfo -> setEmpHomeTelephone(CommonFunctions::cleanParam($postArr['txtHmTelep'], 50));
			$this->parent_empinfo -> setEmpMobile(CommonFunctions::cleanParam($postArr['txtMobile'], 50));
			$this->parent_empinfo -> setEmpWorkTelephone(CommonFunctions::cleanParam($postArr['txtWorkTelep'], 50));
			$this->parent_empinfo -> setEmpWorkEmail(CommonFunctions::cleanParam($postArr['txtWorkEmail'], 50));
			$this->parent_empinfo -> setEmpOtherEmail(CommonFunctions::cleanParam($postArr['txtOtherEmail'], 50));


			$objectArr['EmpPermRes'] = $this->parent_empinfo;
		}

		if($postArr['taxFlag']=='1') {
			$taxInfo = new EmpTax();
			$taxInfo->setEmpNumber(CommonFunctions::cleanParam($postArr['txtEmpID']));

			$federalTaxStatus = CommonFunctions::cleanParam($postArr['cmbTaxFederalStatus'], 13);
			if (!empty($federalTaxStatus)) {
				$taxInfo->setFederalTaxStatus($federalTaxStatus);
			}

			$taxInfo->setFederalTaxExceptions(CommonFunctions::cleanParam($postArr['taxFederalExceptions']));

			$taxState = CommonFunctions::cleanParam($postArr['cmbTaxState'], 13);
			if (!empty($taxState)) {
				$taxInfo->setTaxState($taxState);
			}

			$stateTaxStatus = CommonFunctions::cleanParam($postArr['cmbTaxStateStatus'], 13);
			if (!empty($stateTaxStatus)) {
				$taxInfo->setStateTaxStatus($stateTaxStatus);
			}

			$taxInfo->setStateTaxExceptions(CommonFunctions::cleanParam($postArr['taxStateExceptions']));

			$unemploymentState = CommonFunctions::cleanParam($postArr['cmbTaxUnemploymentState'], 13);
			if (!empty($unemploymentState)) {
				$taxInfo->setTaxUnemploymentState($unemploymentState);
			}

			$workState = CommonFunctions::cleanParam($postArr['cmbTaxWorkState'], 13);
			if (!empty($workState)) {
				$taxInfo->setTaxWorkState($workState);
			}

			$objectArr['EmpTaxInfo'] = $taxInfo;
		}

		if($postArr['customFlag']=='1') {
			$this->parent_empinfo -> setEmpId(trim($postArr['txtEmpID']));
			if (isset($postArr['custom1'])) {
				$this->parent_empinfo->setCustom1(CommonFunctions::cleanParam($postArr['custom1']));
			}
			if (isset($postArr['custom2'])) {
				$this->parent_empinfo->setCustom2(CommonFunctions::cleanParam($postArr['custom2']));
			}
			if (isset($postArr['custom3'])) {
				$this->parent_empinfo->setCustom3(CommonFunctions::cleanParam($postArr['custom3']));
			}
			if (isset($postArr['custom4'])) {
				$this->parent_empinfo->setCustom4(CommonFunctions::cleanParam($postArr['custom4']));
			}
			if (isset($postArr['custom5'])) {
				$this->parent_empinfo->setCustom5(CommonFunctions::cleanParam($postArr['custom5']));
			}
			if (isset($postArr['custom6'])) {
				$this->parent_empinfo->setCustom6(CommonFunctions::cleanParam($postArr['custom6']));
			}
			if (isset($postArr['custom7'])) {
				$this->parent_empinfo->setCustom7(CommonFunctions::cleanParam($postArr['custom7']));
			}
			if (isset($postArr['custom8'])) {
				$this->parent_empinfo->setCustom8(CommonFunctions::cleanParam($postArr['custom8']));
			}
			if (isset($postArr['custom9'])) {
				$this->parent_empinfo->setCustom9(CommonFunctions::cleanParam($postArr['custom9']));
			}
			if (isset($postArr['custom10'])) {
				$this->parent_empinfo->setCustom10(CommonFunctions::cleanParam($postArr['custom10']));
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
