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

    /**
     * Constructor
     *
     * @param int $id ID can be null for newly created job applications
     */
    public function __construct() {
        $this->empInfo = new EmpInfo();
    }
    
	public function setESS($ess = true) {
		$this->isESS = $ess;
	}

	public function parseAddData($postArr) {

		$this->empInfo->setEmployeeID(trim($postArr['txtEmployeeId']));
		$this->empInfo->setEmpLastName(($postArr['txtEmpLastName']));
		$this->empInfo->setEmpFirstName(trim($postArr['txtEmpFirstName']));
		$this->empInfo->setEmpNickName(trim($postArr['txtEmpNickName']));
		$this->empInfo->setEmpMiddleName(trim($postArr['txtEmpMiddleName']));

		$objectArr['EmpInfo'] =  $this->empInfo;
           
        $photoExtractor = new EXTRACTOR_EmpPhoto();
        $photo = $photoExtractor->parseData();
        if (!empty($photo)) {     
                $objectArr['EmpPhoto'] = $photo;                       
        }

        return isset($objectArr)? $objectArr : false;
	}


	public function parseEditData($postArr) {

    	if (($postArr['main']=='1') || ($postArr['personalFlag']=='1')) {
 
    		$this->empInfo->setEmpId(trim($postArr['txtEmpID']));
    		if (isset($postArr['txtEmployeeId'])) {
    			$this->empInfo->setEmployeeID(trim($postArr['txtEmployeeId']));
    		}
    		//$this->empInfo->setEmployeeID(trim($postArr['txtEmpID']));
    
    		$this->empInfo->setEmpLastName(($postArr['txtEmpLastName']));
    		$this->empInfo->setEmpFirstName(trim($postArr['txtEmpFirstName']));
    		$this->empInfo->setEmpNickName(trim($postArr['txtEmpNickName']));
    		$this->empInfo->setEmpMiddleName(trim($postArr['txtEmpMiddleName']));
    
    
    		$objectArr['EmpMain'] = $this->empInfo;
    	}

    	//personal
    	if ($postArr['personalFlag']=='1') {
    
    		$postArr['DOB']=LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['DOB']);
    		$postArr['txtLicExpDate']=LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtLicExpDate']);
    
    		$this->empInfo->setEmpId(trim($postArr['txtEmpID']));
    		$this->empInfo->setEmpSINNo(trim($postArr['txtSINNo']));
    		$this->empInfo->setEmpSSNNo(trim($postArr['txtNICNo']));
    		$this->empInfo->setEmpDOB(trim($postArr['DOB']));
    		$this->empInfo->setEmpGender(trim($postArr['optGender']));
    		$this->empInfo->setEmpDriLicNo(($postArr['txtLicenNo']));
    		$this->empInfo->setEmpNation(($postArr['cmbNation']));
    		$this->empInfo->setEmpDriLicExpDat(($postArr['txtLicExpDate']));
    		$this->empInfo->setEmpOthID(trim($postArr['txtOtherID']));
    		$this->empInfo->setEmpMarital(($postArr['cmbMarital']));
    		$this->empInfo->setEmpMilitary(trim($postArr['txtMilitarySer']));
    		$this->empInfo->setEmpsmoker(isset($postArr['chkSmokeFlag'])?'1':'0');
    		$this->empInfo->setEmpEthnicRace(($postArr['cmbEthnicRace']));
    
    		$objectArr['EmpPers'] = $this->empInfo;
    	}

    	//job info
    	if ($postArr['jobFlag']=='1' && !(isset($this->isESS) && $this->isESS)) {
    
    		$postArr['txtJoinedDate']=LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtJoinedDate']);
    		$postArr['txtTermDate']=LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtTermDate']);
    
    		$this->empInfo->setEmpId(trim($postArr['txtEmpID']));
    		$this->empInfo->setEmpJobTitle(trim($postArr['cmbJobTitle']));
    		$this->empInfo->setEmpStatus(trim($postArr['cmbType']));
    		$this->empInfo->setEmpEEOCat(trim($postArr['cmbEEOCat']));
    		$this->empInfo->setEmpLocation(($postArr['cmbLocation']));
    		$this->empInfo->setEmpJoinedDate(($postArr['txtJoinedDate']));
    		$this->empInfo ->setEmpTerminatedDate(($postArr['txtTermDate']));
    		$this->empInfo ->setEmpTerminationReason(($postArr['txtTermReason']));
    
    		$objectArr['EmpJobInfo'] = $this->empInfo;
    	}
    
    	if ($postArr['contactFlag']=='1') {
    		$this->empInfo->setEmpId(trim($postArr['txtEmpID']));
    		$this->empInfo->setEmpStreet1(trim($postArr['txtStreet1']));
    		$this->empInfo->setEmpStreet2(trim($postArr['txtStreet2']));
    		$this->empInfo->setEmpCity(trim($postArr['cmbCity']));
    		$this->empInfo->setEmpProvince(trim($postArr['cmbProvince']));
    		$this->empInfo->setEmpCountry(trim($postArr['cmbCountry']));
    		$this->empInfo->setEmpZipCode(trim($postArr['txtzipCode']));
    		$this->empInfo->setEmpHomeTelephone(trim($postArr['txtHmTelep']));
    		$this->empInfo->setEmpMobile(trim($postArr['txtMobile']));
    		$this->empInfo->setEmpWorkTelephone(trim($postArr['txtWorkTelep']));
    		$this->empInfo->setEmpWorkEmail(($postArr['txtWorkEmail']));
    		$this->empInfo->setEmpOtherEmail(($postArr['txtOtherEmail']));
    
    
    		$objectArr['EmpPermRes'] = $this->empInfo;
		}

    	if ($postArr['taxFlag']=='1') {
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
    
    	if ($postArr['customFlag']=='1') {
    		$this->empInfo->setEmpId(trim($postArr['txtEmpID']));
    		if (isset($postArr['custom1'])) {
    			$this->empInfo->setCustom1(CommonFunctions::escapeHTML(trim($postArr['custom1'])));
    		}
    		if (isset($postArr['custom2'])) {
    			$this->empInfo->setCustom2(CommonFunctions::escapeHTML(trim($postArr['custom2'])));
    		}
    		if (isset($postArr['custom3'])) {
    			$this->empInfo->setCustom3(CommonFunctions::escapeHTML(trim($postArr['custom3'])));
    		}
    		if (isset($postArr['custom4'])) {
    			$this->empInfo->setCustom4(CommonFunctions::escapeHTML(trim($postArr['custom4'])));
    		}
    		if (isset($postArr['custom5'])) {
    			$this->empInfo->setCustom5(CommonFunctions::escapeHTML(trim($postArr['custom5'])));
    		}
    		if (isset($postArr['custom6'])) {
    			$this->empInfo->setCustom6(CommonFunctions::escapeHTML(trim($postArr['custom6'])));
    		}
    		if (isset($postArr['custom7'])) {
    			$this->empInfo->setCustom7(CommonFunctions::escapeHTML(trim($postArr['custom7'])));
    		}
    		if (isset($postArr['custom8'])) {
    			$this->empInfo->setCustom8(CommonFunctions::escapeHTML(trim($postArr['custom8'])));
    		}
    		if (isset($postArr['custom9'])) {
    			$this->empInfo->setCustom9(CommonFunctions::escapeHTML(trim($postArr['custom9'])));
    		}
    		if (isset($postArr['custom10'])) {
    			$this->empInfo->setCustom10(CommonFunctions::escapeHTML(trim($postArr['custom10'])));
    		}
    		$objectArr['EmpCustomInfo'] = $this->empInfo;
    	}

        return isset($objectArr)? $objectArr : false;
    }
}
?>
