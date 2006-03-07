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

require_once OpenSourceEIM . '/lib/Confs/Conf.php';
require_once OpenSourceEIM . '/lib/Models/DMLFunctions.php';
require_once OpenSourceEIM . '/lib/Models/SQLQBuilder.php';
require_once OpenSourceEIM . '/lib/CommonMethods/CommonFunctions.php';

class EmpInfo {

	var $tableName = 'HS_HR_EMPLOYEE';
	var $empId;
	var $empTitle;
	var $empCallName;
	var $empSurname;
	var $empMaidenName;
	var $empInitials;
	var $empNamByIni;
	var $empFullName;
	var $empOtherName;
	//personal
	var $empNICNo;
	var $empNICDate;
	var $empDOB;
	var $empBirthPlace;
	var $empGender;
	var $empBloodGrp;
	var $empNation;
	var $empReligion;
	var $empMarital;
	var $empMarriedDate;
	//job info
	var $empDatJoin;
	var $empConfirmFlag;
	var $empResigDat;
	var $empRetireDat;
	var $empSalGrd;
	var $empCorpTit;
	var $empDesig;
	var $empCostCode;
	var $empWorkHours;
	var $empJobPref;
	//job stat
	var $empType;
	var $empStatutory;
	var $empCat;
	var $empStartDat;
	var $empEndDat;
	var $empConToPermFlag;
	var $empConToPermDat;
	var $empHRActivFlag;
	var $empPayActivFlag;
	var $empTimAttActivFlag;
	//payroll
	var $empPayrolNo;
	var $empBarCodeNo;
	var $empPaymentTypeFlag;
	//work station
	var $empLoc;
	var $empPrefLoc;
	//tax
	var $empTaxCountry;
	var $empTaxExempt;
	var $empTaxOnTaxFlag;
	var $empTaxID;
	var $empEPFEligibeFlag;
	var $empEPFNo;
	var $CFundCBFundFlag;
	var $epfEmployeePercen;
	var $epfEmployerPercen;
	var $ETFEligibleFlag;
	var $empETFNo;
	var $etfEmployeePercen;
	var $ETFDat;
	var $MSPSEligibleFlag;
	var $mspsEmployeePercen;
	var $mspsEmployerPercen;
	//permanent contacts
	var $empPermHouseNo;
	var $empPermStreet1;
	var $empPermStreet2;
	var $empPermCityTown;
	var $empPermPostCode;
	var $empPermTelephone;
	var $empPermMobile;
	var $empPermFax;
	var $empPermEmail;
	var $empPermCountry;
	var $empPermProvince;
	var $empPermDistrict;
	var $empPermElectorate;
	//permanent contacts
	var $empTempHouseNo;
	var $empTempStreet1;
	var $empTempStreet2;
	var $empTempCityTown;
	var $empTempPostCode;
	var $empTempTelephone;
	var $empTempMobile;
	var $empTempFax;
	var $empTempEmail;
	var $empTempCountry;
	var $empTempProvince;
	var $empTempDistrict;
	var $empTempElectorate;
	//off contact
	var $empGenLine;
	var $empExt;
	var $empEmail;

	var $arrayDispList;
	var $singleField;

	function EmpInfo() {
		
	}

	function setEmpId($empId) {
	
	$this->empId=$empId;
	}
	
	function setEmpTitle($empTitle) {
	$this->empTitle=$empTitle;
	}
	
	function setEmpCallName($empCallName) {
	$this->empCallName=$empCallName;
	}
	
	function setEmpSurname($empSurname) {
	$this->empSurname=$empSurname;
	}
	
	function setEmpMaidenName($empMaidenName) {
	$this->empMaidenName=$empMaidenName;
	}

	function setEmpInitials($empInitials) {
	$this->empInitials=$empInitials;
	}
	
	function setEmpNamByIni($empNamByIni) {
	$this->empNamByIni=$empNamByIni;
	}
	
	function setEmpFullName($empFullName) {
	$this->empFullName=$empFullName;
	}
	
	function setEmpOtherName($empOtherName) {
	$this->empOtherName=$empOtherName;
	}
	
	//personal
	function setEmpNICNo($empNICNo) {
	$this->empNICNo=$empNICNo;
	}
	
	function setEmpNICDate($empNICDate) {
	$this->empNICDate=$empNICDate;
	}
	
	function setEmpDOB($empDOB) {
	$this->empDOB=$empDOB;
	}
	
	function setEmpBirthPlace($empBirthPlace) {
	$this->empBirthPlace=$empBirthPlace;
	}
	
	function setEmpGender($empGender) {
	$this->empGender=$empGender;
	}
	
	function setEmpBloodGrp($empBloodGrp) {
	$this->empBloodGrp=$empBloodGrp;
	}
	
	function setEmpNation($empNation) {
	$this->empNation=$empNation;
	}
	
	function setEmpReligion($empReligion) {
	$this->empReligion=$empReligion;
	}
	
	function setEmpMarital($empMarital) {
	$this->empMarital=$empMarital;
	}
	
	function setEmpMarriedDate($empMarriedDate) {
	$this->empMarriedDate=$empMarriedDate;
	}
	
	//job info
	function setEmpDatJoin($empDatJoin) {
	$this->empDatJoin=$empDatJoin;
	}
	
	function setEmpConfirmFlag($empConfirmFlag) {
	$this->empConfirmFlag=$empConfirmFlag;
	}
	
	function setEmpResigDat($empResigDat) {
	$this->empResigDat=$empResigDat;
	}
	
	function setEmpRetireDat($empRetireDat) {
	$this->empRetireDat=$empRetireDat;
	}
	
	function setEmpSalGrd($empSalGrd) {
	$this->empSalGrd=$empSalGrd;
	}
	
	function setEmpCorpTit($empCorpTit) {
	$this->empCorpTit=$empCorpTit;
	}
	
	function setEmpDesig($empDesig) {
	$this->empDesig=$empDesig;
	}
	
	function setEmpCostCode($empCostCode) {
	$this->empCostCode=$empCostCode;
	}
	
	function setEmpWorkHours($empWorkHours) {
	$this->empWorkHours=$empWorkHours;
	}
	
	function setEmpJobPref($empJobPref) {
	$this->empJobPref=$empJobPref;
	}
	
	//job stat
	function setEmpType($empType) {
	$this->empType=$empType;
	}
	
	function setEmpStatutory($empStatutory) {
	$this->empStatutory=$empStatutory;
	}
	
	function setEmpCat($empCat) {
	$this->empCat=$empCat;
	}
	
	function setEmpStartDat($empStartDat) {
	$this->empStartDat=$empStartDat;
	}
	
	function setEmpEndDat($empEndDat) {
	$this->empEndDat=$empEndDat;
	}
	
	function setEmpConToPermFlag($empConToPermFlag) {
	$this->empConToPermFlag=$empConToPermFlag;
	}
	
	function setEmpConToPermDat($empConToPermDat) {
	$this->empConToPermDat=$empConToPermDat;
	}
	
	function setEmpHRActivFlag($empHRActivFlag) {
	$this->empHRActivFlag=$empHRActivFlag;
	}
	
	function setEmpPayActivFlag($empPayActivFlag) {
	$this->empPayActivFlag=$empPayActivFlag;
	}
	
	function setEmpTimAttActivFlag($empTimAttActivFlag) {
	$this->empTimAttActivFlag=$empTimAttActivFlag;
	}
	
	//payroll
	function setEmpPayrolNo($empPayrolNo) {
	$this->empPayrolNo=$empPayrolNo;
	}
	
	function setEmpBarCodeNo($empBarCodeNo) {
	$this->empBarCodeNo=$empBarCodeNo;
	}
	
	function setEmpPayementTypeFlag($empPaymentTypeFlag) {
	$this->empPaymentTypeFlag=$empPaymentTypeFlag;
	}
	//work station
	function setEmpLoc($empLoc) {
	$this->empLoc=$empLoc;
	}
	
	function setEmpPrefLoc($empPrefLoc) {
	$this->empPrefLoc=$empPrefLoc;
	}

	function setEmpTaxCountry($empTaxCountry) {
	$this->empTaxCountry=$empTaxCountry;
	}
	//tax
	function setEmpTaxExempt($empTaxExempt) {
	$this->empTaxExempt=$empTaxExempt;
	}
	
	function setEmpTaxOnTaxFlag($empTaxOnTaxFlag) {
	$this->empTaxOnTaxFlag=$empTaxOnTaxFlag;
	}
	
	function setEmpTaxID($empTaxID) {
	$this->empTaxID=$empTaxID;
	}
	
	function setEmpEPFEligibleFlag($empEPFEligibeFlag) {
	$this->empEPFEligibeFlag=$empEPFEligibeFlag;
	}
	
	function setEmpEPFNo($empEPFNo) {
	$this->empEPFNo=$empEPFNo;
	}
	
	function setCFundCBFundFlag($CFundCBFundFlag) {
	$this->CFundCBFundFlag=$CFundCBFundFlag;
	}
	
	function setEPFEmployeePercen($EmployeePercen) {
	$this->EmployeePercen=$EmployeePercen;
	}
	
	function setEPFEmployerPercen($EmployerPercen) {
	$this->EmployerPercen=$EmployerPercen;
	}
	
	function setETFEligibleFlag($ETFEligibleFlag) {
	$this->ETFEligibleFlag=$ETFEligibleFlag;
	}
	
	function setEmpETFNo($empETFNo) {
	$this->empETFNo=$empETFNo;
	}
	
	function setETFEmployeePercen($etfEmployeePercen) {
	$this->etfEmployeePercen=$etfEmployeePercen;
	}
	
	function setETFDat($ETFDat) {
	$this->ETFDat=$ETFDat;
	}
	
	function setMSPSEligibleFlag($MSPSEligibleFlag) {
	$this->MSPSEligibleFlag=$MSPSEligibleFlag;
	}
	
	function setMSPSEmployeePercen($mspsEmployeePercen) {
	$this->mspsEmployeePercen=$mspsEmployeePercen;
	}
	
	function setMSPSEmployerPercen($mspsEmployerPercen) {
	$this->mspsEmployerPercen=$mspsEmployerPercen;
	}
	
	//permanent contacts
	function setEmpPermHouseNo($empPermHouseNo) {
	$this->empPermHouseNo=$empPermHouseNo;
	}
	
	function setEmpPermStreet1($empPermStreet1) {
	$this->empPermStreet1=$empPermStreet1;
	}
	
	function setEmpPermStreet2($empPermStreet2) {
	$this->empPermStreet2=$empPermStreet2;
	}
	
	function setEmpPermCityTown($empPermCityTown) {
	$this->empPermCityTown=$empPermCityTown;
	}
	
	function setEmpPermPostCode($empPermPostCode) {
	$this->empPermPostCode=$empPermPostCode;
	}
	
	function setEmpPermTelephone($empPermTelephone) {
	$this->empPermTelephone=$empPermTelephone;
	}
	
	function setEmpPermMobile($empPermMobile) {
	$this->empPermMobile=$empPermMobile;
	}
	
	function setEmpPermFax($empPermFax) {
	$this->empPermFax=$empPermFax;
	}
	
	function setEmpPermEmail($empPermEmail) {
	$this->empPermEmail=$empPermEmail;
	}
	
	function setEmpPermCountry($empPermCountry) {
	$this->empPermCountry=$empPermCountry;
	}
	
	function setEmpPermProvince($empPermProvince) {
	$this->empPermProvince=$empPermProvince;
	}
	
	function setEmpPermDistrict($empPermDistrict) {
	$this->empPermDistrict=$empPermDistrict;
	}
	
	function setEmpPermElectorate($empPermElectorate) {
	$this->empPermElectorate=$empPermElectorate;
	}
	//temp contacts
	function setEmpTempHouseNo($empTempHouseNo) {
	$this->empTempHouseNo=$empTempHouseNo;
	}
	
	function setEmpTempStreet1($empTempStreet1) {
	$this->empTempStreet1=$empTempStreet1;
	}
	
	function setEmpTempStreet2($empTempStreet2) {
	$this->empTempStreet2=$empTempStreet2;
	}
	
	function setEmpTempCityTown($empTempCityTown) {
	$this->empTempCityTown=$empTempCityTown;
	}
	
	function setEmpTempPostCode($empTempPostCode) {
	$this->empTempPostCode=$empTempPostCode;
	}
	
	function setEmpTempTelephone($empTempTelephone) {
	$this->empTempTelephone=$empTempTelephone;
	}
	
	function setEmpTempMobile($empTempMobile) {
	$this->empTempMobile=$empTempMobile;
	}
	
	function setEmpTempFax($empTempFax) {
	$this->empTempFax=$empTempFax;
	}
	
	function setEmpTempEmail($empTempEmail) {
	$this->empTempEmail=$empTempEmail;
	}
	
	function setEmpTempCountry($empTempCountry) {
	$this->empTempCountry=$empTempCountry;
	}
	
	function setEmpTempProvince($empTempProvince) {
	$this->empTempProvince=$empTempProvince;
	}
	
	function setEmpTempDistrict($empTempDistrict) {
	$this->empTempDistrict=$empTempDistrict;
	}
	
	function setEmpTempElectorate($empTempElectorate) {
	$this->empTempElectorate=$empTempElectorate;
	}
	//off contact
	function setEmpGenLine($empGenLine) {
	$this->empGenLine=$empGenLine;
	}
	
	function setEmpExt($empExt) {
	$this->empExt=$empExt;
	}
	
	function setEmpEmail($empEmail) {
	$this->empEmail=$empEmail;
	}

//////////////
	function getEmpId() {
	
	return $this->empId;
	}
	
	function getEmpTitle() {
	return $this->empTitle;
	}
	
	function getEmpCallName() {
	return $this->empCallName;
	}
	
	function getEmpSurname() {
	return $this->empSurname;
	}
	
	function getEmpMaidenName() {
	return $this->empMaidenName;
	}

	function getEmpInitials() {
	return $this->empInitials;
	}
	
	function getEmpNamByIni() {
	return $this->empNamByIni;
	}
	
	function getEmpFullName() {
	return $this->empFullName;
	}
	
	function getEmpOtherName() {
	return $this->empOtherName;
	}
	
	//personal
	function getEmpNICNo() {
	return $this->empNICNo;
	}
	
	function getEmpNICDate() {
	return $this->empNICDate;
	}
	
	function getEmpDOB() {
	return $this->empDOB;
	}
	
	function getEmpBirthPlace() {
	return $this->empBirthPlace;
	}
	
	function getEmpGender() {
	return $this->empGender;
	}
	
	function getEmpBloodGrp() {
	return $this->empBloodGrp;
	}
	
	function getEmpNation() {
	return $this->empNation;
	}
	
	function getEmpReligion() {
	return $this->empReligion;
	}
	
	function getEmpMarital() {
	return $this->empMarital;
	}
	
	function getEmpMarriedDate() {
	return $this->empMarriedDate;
	}
	
	//job info
	function getEmpDatJoin() {
	return $this->empDatJoin;
	}
	
	function getEmpConfirmFlag() {
	return $this->empConfirmFlag;
	}
	
	function getEmpResigDat() {
	return $this->empResigDat;
	}
	
	function getEmpRetireDat() {
	return $this->empRetireDat;
	}
	
	function getEmpSalGrd() {
	return $this->empSalGrd;
	}
	
	function getEmpCorpTit() {
	return $this->empCorpTit;
	}
	
	function getEmpDesig() {
	return $this->empDesig;
	}
	
	function getEmpCostCode() {
	return $this->empCostCode;
	}
	
	function getEmpWorkHours() {
	return $this->empWorkHours;
	}
	
	function getEmpJobPref() {
	return $this->empJobPref;
	}
	
	//job stat
	function getEmpType() {
	return $this->empType;
	}
	
	function getEmpStatutory() {
	return $this->empStatutory;
	}
	
	function getEmpCat() {
	return $this->empCat;
	}
	
	function getEmpStartDat() {
	return $this->empStartDat;
	}
	
	function getEmpEndDat() {
	return $this->empEndDat;
	}
	
	function getEmpConToPermFlag() {
	return $this->empConToPermFlag;
	}
	
	function getEmpConToPermDat() {
	return $this->empConToPermDat;
	}
	
	function getEmpHRActivFlag() {
	return $this->empHRActivFlag;
	}
	
	function getEmpPayActivFlag() {
	return $this->empPayActivFlag;
	}
	
	function getEmpTimAttActivFlag() {
	return $this->empTimAttActivFlag;
	}
	
	//payroll
	function getEmpPayrolNo() {
	return $this->empPayrolNo;
	}
	
	function getEmpBarCodeNo() {
	return $this->empBarCodeNo;
	}
	
	function getEmpPayementTypeFlag() {
	return $this->empPaymentTypeFlag;
	}
	//work station
	function getEmpLoc() {
	return $this->empLoc;
	}
	
	function getEmpPrefLoc() {
	return $this->empPrefLoc;
	}
	//tax
	function getEmpTaxCountry() {
	return $this->empTaxCountry;
	}

	function getEmpTaxExempt() {
	return $this->empTaxExempt;
	}
	
	function getEmpTaxOnTaxFlag() {
	return $this->empTaxOnTaxFlag;
	}
	
	function getEmpTaxID($empTaxID) {
	return $this->empTaxID=$empTaxID;
	}
	
	function getEmpEPFEligibleFlag() {
	return $this->empEPFEligibeFlag;
	}
	
	function getEmpEPFNo() {
	return $this->empEPFNo;
	}
	
	function getCFundCBFundFlag() {
	return $this->CFundCBFundFlag;
	}
	
	function getEPFEmployeePercen() {
	return $this->EmployeePercen;
	}
	
	function getEPFEmployerPercen() {
	return $this->EmployerPercen;
	}
	
	function getETFEligibleFlag() {
	return $this->ETFEligibleFlag;
	}
	
	function getEmpETFNo() {
	return $this->empETFNo;
	}
	
	function getETFEmployeePercen() {
	return $this->etfEmployeePercen;
	}
	
	function getETFDat() {
	return $this->ETFDat;
	}
	
	function getMSPSEligibleFlag() {
	return $this->MSPSEligibleFlag;
	}
	
	function getMSPSEmployeePercen() {
	return $this->mspsEmployeePercen;
	}
	
	function getMSPSEmployerPercen() {
	return $this->mspsEmployerPercen;
	}
	
	//permanent contacts
	function getEmpPermHouseNo() {
	return $this->empPermHouseNo;
	}
	
	function getEmpPermStreet1() {
	return $this->empPermStreet1;
	}
	
	function getEmpPermStreet2() {
	return $this->empPermStreet2;
	}
	
	function getEmpPermCityTown() {
	return $this->empPermCityTown;
	}
	
	function getEmpPermPostCode() {
	return $this->empPermPostCode;
	}
	
	function getEmpPermTelephone() {
	return $this->empPermTelephone;
	}
	
	function getEmpPermMobile() {
	return $this->empPermMobile;
	}
	
	function getEmpPermFax() {
	return $this->empPermFax;
	}
	
	function getEmpPermEmail() {
	return $this->empPermEmail;
	}
	
	function getEmpPermCountry() {
	return $this->empPermCountry;
	}
	
	function getEmpPermProvince() {
	return $this->empPermProvince;
	}
	
	function getEmpPermDistrict() {
	return $this->empPermDistrict;
	}
	
	function getEmpPermElectorate() {
	return $this->empPermElectorate;
	}
	//temp contacts
	function getEmpTempHouseNo() {
	return $this->empTempHouseNo;
	}
	
	function getEmpTempStreet1() {
	return $this->empTempStreet1;
	}
	
	function getEmpTempStreet2() {
	return $this->empTempStreet2;
	}
	
	function getEmpTempCityTown() {
	return $this->empTempCityTown;
	}
	
	function getEmpTempPostCode() {
	return $this->empTempPostCode;
	}
	
	function getEmpTempTelephone() {
	return $this->empTempTelephone;
	}
	
	function getEmpTempMobile() {
	return $this->empTempMobile;
	}
	
	function getEmpTempFax() {
	return $this->empTempFax;
	}
	
	function getEmpTempEmail() {
	return $this->empTempEmail;
	}
	
	function getEmpTempCountry() {
	return $this->empTempCountry;
	}
	
	function getEmpTempProvince() {
	return $this->empTempProvince;
	}
	
	function getEmpTempDistrict() {
	return $this->empTempDistrict;
	}
	
	function getEmpTempElectorate() {
	return $this->empTempElectorate;
	}
	//off contact
	function getEmpGenLine() {
	return $this->empGenLine;
	}
	
	function getEmpExt() {
	return $this->empExt;
	}
	
	function getEmpEmail() {
	return $this->empEmail;
	}

/////////////

	function getListofEmployee($pageNO=0,$schStr='',$mode=0) {
		
		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_FULLNAME';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->passResultSetMessage($pageNO,$schStr,$mode);
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$i=0;
		
		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {
		 	
	    	$arrayDispList[$i][0] = $line[0];
	    	$arrayDispList[$i][1] = $line[1];
	    	$i++;
	    	
	     }
	     
	     if (isset($arrayDispList)) {
	     
			return $arrayDispList;
			
		} else {
		
			$arrayDispList = '';
			return $arrayDispList;
			
		}
	}

	function countEmployee($schStr='',$mode=0) {
		
		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_FULLNAME';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->countResultset($schStr,$mode);
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$line = mysql_fetch_array($message2, MYSQL_NUM);
		 	
	    	return $line[0];
	}

	function getLastRecord() {
		
		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
				
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
	
		$sqlQString = $sql_builder->selectOneRecordOnly();
	
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$common_func = new CommonFunctions();
		
		if (isset($message2)) {
			
			$i=0;
		
		while ($line = mysql_fetch_array($message2, MYSQL_ASSOC)) {		
			foreach ($line as $col_value) {
			$this->singleField = $col_value;
			}		
		}
			
		return $common_func->explodeString($this->singleField,"EMP");
				
		}
		
	}	
	
	function delEmployee($arrList) {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_delete = 'true';
		$sql_builder->arr_delete = $arrFieldList;

		$sqlQString = $sql_builder->deleteRecord($arrList);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

	}

	function filterEmpMain($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_TITLE';
		$arrFieldList[2] = 'EMP_CALLING_NAME';
		$arrFieldList[3] = 'EMP_SURNAME';
		$arrFieldList[4] = 'EMP_MAIDEN_NAME';
		$arrFieldList[5] = 'EMP_MIDDLE_INI';
		$arrFieldList[6] = 'EMP_NAMES_BY_INI';
		$arrFieldList[7] = 'EMP_FULLNAME';
		$arrFieldList[8] = 'EMP_OTHER_NAMES';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->selectOneRecordFiltered($this->getID);
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$i=0;
		
		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {
		 	
	    	$arrayDispList[$i][0] = $line[0];
	    	$arrayDispList[$i][1] = $line[1];
	    	$arrayDispList[$i][2] = $line[2];
	    	$arrayDispList[$i][3] = $line[3];
	    	$arrayDispList[$i][4] = $line[4];
	    	$arrayDispList[$i][5] = $line[5];
	    	$arrayDispList[$i][6] = $line[6];
	    	$arrayDispList[$i][7] = $line[7];
	    	$arrayDispList[$i][8] = $line[8];
	    	$i++;
	    	
	     }
	     
	     if (isset($arrayDispList)) {
	     
			return $arrayDispList;
			
		} else {
		
			$arrayDispList = '';
			return $arrayDispList;
			
		}
				
	}


	function addEmpMain($taxSkip) {
		
		$arrRecordsList[0] = "'". $this->getEmpId() . "'";
		$arrRecordsList[1] = "'". $this->getEmpTitle() . "'";
		$arrRecordsList[2] = "'". $this->getEmpCallName() . "'";
		$arrRecordsList[3] = "'". $this->getEmpSurname() . "'";
		$arrRecordsList[4] = "'". $this->getEmpMaidenName() . "'";
		$arrRecordsList[5] = "'". $this->getEmpInitials() . "'";
		$arrRecordsList[6] = "'". $this->getEmpNamByIni() . "'";
		$arrRecordsList[7] = "'". $this->getEmpFullName() . "'";
		$arrRecordsList[8] = "'". $this->getEmpOtherName() . "'";
		//personal
		$arrRecordsList[9] = "'". $this->getEmpNICNo() . "'";
		$arrRecordsList[10] = "'". $this->getEmpNICDate() . "'";
		$arrRecordsList[11] = "'". $this->getEmpDOB() . "'";
		$arrRecordsList[12] = "'". $this->getEmpBirthPlace() . "'";
		$arrRecordsList[13] = "'". $this->getEmpGender() . "'";
		$arrRecordsList[14] = "'". $this->getEmpBloodGrp() . "'";
		$arrRecordsList[15] = ($this->getEmpNation()=='0') ? 'null' : "'". $this->getEmpNation() . "'";
		$arrRecordsList[16] = ($this->getEmpReligion()=='0') ? 'null' :"'". $this->getEmpReligion() . "'";
		$arrRecordsList[17] = "'". $this->getEmpMarital() . "'";
		$arrRecordsList[18] = "'". $this->getEmpMarriedDate() . "'";
		//job info
		$arrRecordsList[19] = "'". $this->getEmpDatJoin() . "'";
		$arrRecordsList[20] = "'". $this->getEmpConfirmFlag() . "'";
		$arrRecordsList[21] = "'". $this->getEmpResigDat() . "'";
		$arrRecordsList[22] = "'". $this->getEmpRetireDat() . "'";
		$arrRecordsList[23] = ($this->getEmpSalGrd()=='0') ? 'null' : "'". $this->getEmpSalGrd() . "'";
		$arrRecordsList[24] = ($this->getEmpCorpTit()=='0') ? 'null' : "'". $this->getEmpCorpTit() . "'";
		$arrRecordsList[25] = ($this->getEmpDesig()=='0') ? 'null' : "'". $this->getEmpDesig() . "'";
		$arrRecordsList[26] = ($this->getEmpCostCode()=='0') ? 'null' : "'". $this->getEmpCostCode() . "'";
		$arrRecordsList[27] = "'". $this->getEmpWorkHours() . "'";
		$arrRecordsList[28] = "'". $this->getEmpJobPref() . "'";
		//job stat
		$arrRecordsList[29] = "'". $this->getEmpType() . "'";
		$arrRecordsList[30] = ($this->getEmpStatutory()=='0') ? 'null' : "'". $this->getEmpStatutory() . "'";
		$arrRecordsList[31] = ($this->getEmpCat()=='0') ? 'null' :"'". $this->getEmpCat() . "'";
		$arrRecordsList[32] = "'". $this->getEmpStartDat() . "'";
		$arrRecordsList[33] = "'". $this->getEmpEndDat() . "'";
		$arrRecordsList[34] = "'". $this->getEmpConToPermFlag() . "'";
		$arrRecordsList[35] = "'". $this->getEmpConToPermDat() . "'";
		$arrRecordsList[36] = "'". $this->getEmpHRActivFlag() . "'";
		$arrRecordsList[37] = "'". $this->getEmpPayActivFlag() . "'";
		$arrRecordsList[38] = "'". $this->getEmpTimAttActivFlag() . "'";
		$arrRecordsList[39] = "'". $this->getEmpTaxCountry() . "'";
		$arrRecordsList[40] = ($this->getEmpLoc()=='0') ? 'null' : "'". $this->getEmpLoc() . "'";
		$arrRecordsList[41] = ($this->getEmpPrefLoc()=='0') ? 'null' :"'". $this->getEmpPrefLoc() . "'";
		
		//tax
		$i=42;
		if($taxSkip==0) {
		$arrRecordsList[$i++] = "'". $this->getEmpTaxExempt() . "'";
		$arrRecordsList[$i++] = "'". $this->getEmpTaxOnTaxFlag() . "'";
		$arrRecordsList[$i++] = "'". $this->getEmpTaxID() . "'";
		$arrRecordsList[$i++] = "'". $this->getEmpEPFEligibleFlag() . "'";
		$arrRecordsList[$i++] = "'". $this->getEmpEPFNo() . "'";
		$arrRecordsList[$i++] = "'". $this->getCFundCBFundFlag() . "'";
		$arrRecordsList[$i++] = "'". $this->getEPFEmployeePercen() . "'";
		$arrRecordsList[$i++] = "'". $this->getEPFEmployerPercen() . "'";
		$arrRecordsList[$i++] = "'". $this->getETFEligibleFlag() . "'";
		$arrRecordsList[$i++] = "'". $this->getEmpETFNo() . "'";
		$arrRecordsList[$i++] = "'". $this->getETFEmployeePercen() . "'";
		$arrRecordsList[$i++] = "'". $this->getETFDat() . "'";
		$arrRecordsList[$i++] = "'". $this->getMSPSEligibleFlag() . "'";
		$arrRecordsList[$i++] = "'". $this->getMSPSEmployeePercen() . "'";
		$arrRecordsList[$i++] = "'". $this->getMSPSEmployerPercen() . "'";
	    }
	    
		//contacts
		$arrRecordsList[$i++] = "'". $this->getEmpPermHouseNo() . "'";
		$arrRecordsList[$i++] = "'". $this->getEmpPermStreet1() . "'";
		$arrRecordsList[$i++] = "'". $this->getEmpPermStreet2() . "'";
		$arrRecordsList[$i++] = "'". $this->getEmpPermCityTown() . "'";
		$arrRecordsList[$i++] = "'". $this->getEmpPermPostCode() . "'";
		$arrRecordsList[$i++] = "'". $this->getEmpPermTelephone() . "'";
		$arrRecordsList[$i++] = "'". $this->getEmpPermMobile() . "'";
		$arrRecordsList[$i++] = "'". $this->getEmpPermFax() . "'";
		$arrRecordsList[$i++] = "'". $this->getEmpPermEmail() . "'";
		$arrRecordsList[$i++] = ($this->getEmpPermCountry()=='0') ? 'null' :"'". $this->getEmpPermCountry() . "'";
		$arrRecordsList[$i++] = ($this->getEmpPermProvince()=='0') ? 'null' :"'". $this->getEmpPermProvince() . "'";
		$arrRecordsList[$i++] = ($this->getEmpPermDistrict()=='0') ? 'null' :"'". $this->getEmpPermDistrict() . "'";
		$arrRecordsList[$i++] = ($this->getEmpPermElectorate()=='0') ? 'null' :"'". $this->getEmpPermElectorate() . "'";
		
		
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_TITLE';
		$arrFieldList[2] = 'EMP_CALLING_NAME';
		$arrFieldList[3] = 'EMP_SURNAME';
		$arrFieldList[4] = 'EMP_MAIDEN_NAME';
		$arrFieldList[5] = 'EMP_MIDDLE_INI';
		$arrFieldList[6] = 'EMP_NAMES_BY_INI';
		$arrFieldList[7] = 'EMP_FULLNAME';
		$arrFieldList[8] = 'EMP_OTHER_NAMES';
		//personal
		$arrFieldList[9] = 'EMP_NIC_NO';
		$arrFieldList[10] = 'EMP_NIC_DATE';
		$arrFieldList[11] = 'EMP_BIRTHDAY';
		$arrFieldList[12] = 'EMP_BIRTHPLACE';
		$arrFieldList[13] = 'EMP_GENDER';
		$arrFieldList[14] = 'EMP_BLOOD_GROUP';
		$arrFieldList[15] = 'NAT_CODE';
		$arrFieldList[16] = 'RLG_CODE';
		$arrFieldList[17] = 'EMP_MARITAL_STATUS';
		$arrFieldList[18] = 'EMP_MARRIED_DATE';
		//job info
		$arrFieldList[19] = 'EMP_DATE_JOINED';
		$arrFieldList[20] = 'EMP_CONFIRM_FLG';
		$arrFieldList[21] = 'EMP_RESIGN_DATE';
		$arrFieldList[22] = 'EMP_RETIRE_DATE';
		$arrFieldList[23] = 'SAL_GRD_CODE';
		$arrFieldList[24] = 'CT_CODE';
		$arrFieldList[25] = 'DSG_CODE';
		$arrFieldList[26] = 'CENTRE_CODE';
		$arrFieldList[27] = 'EMP_WORKHOURS';
		$arrFieldList[28] = 'EMP_JOB_PREFERENCE';
		//job stat
		$arrFieldList[29] = 'EMP_TYPE';
		$arrFieldList[30] = 'STAFFCAT_CODE';
		$arrFieldList[31] = 'CAT_CODE';
		$arrFieldList[32] = 'EMP_CONTARCT_START_DATE';
		$arrFieldList[33] = 'EMP_CONTRACT_END_DATE';
		$arrFieldList[34] = 'EMP_CONT_TO_PERM_FLG';
		$arrFieldList[35] = 'EMP_CONT_TO_PERM_DATE';
		$arrFieldList[36] = 'EMP_ACTIVE_HRM_FLG';
		$arrFieldList[37] = 'EMP_ACTIVE_PAYROLL_FLG';
		$arrFieldList[38] = 'EMP_ACTIVE_ATT_FLG';
		$arrFieldList[39] = 'EMP_TAX_COUNTRY';
		$arrFieldList[40] = 'LOC_CODE';
		$arrFieldList[41] = 'EMP_PREF_WORK_STATION';
		
		//tax
		$i=42;
		if($taxSkip==0) {
		$arrFieldList[$i++] = 'EMP_PAYE_TAX_EXEMPT';
		$arrFieldList[$i++] = 'EMP_TAXONTAX_FLG';
		$arrFieldList[$i++] = 'EMP_TAX_ID_NUMBER';
		$arrFieldList[$i++] = 'EMP_EPF_ELIGIBLE_FLG';
		$arrFieldList[$i++] = 'EMP_EPF_NUMBER';
		$arrFieldList[$i++] = 'EMP_EPF_PAYMENT_TYPE_FLG';
		$arrFieldList[$i++] = 'EMP_EPF_EMPLOYEE_AMOUNT';
		$arrFieldList[$i++] = 'EMP_EPF_EMPLOYER_AMOUNT';
		$arrFieldList[$i++] = 'EMP_ETF_ELIGIBLE_FLG';
		$arrFieldList[$i++] = 'EMP_ETF_NUMBER';
		$arrFieldList[$i++] = 'EMP_ETF_EMPLOYEE_AMOUNT';
		$arrFieldList[$i++] = 'EMP_ETF_DATE';
		$arrFieldList[$i++] = 'EMP_MSPS_ELIGIBLE_FLG';
		$arrFieldList[$i++] = 'EMP_MSPS_EMPLOYEE_AMOUNT';
		$arrFieldList[$i++] = 'EMP_MSPS_EMPLOYER_AMOUNT';
		}
		
		//contacts
		$arrFieldList[$i++] = 'EMP_PER_ADDRESS1';
		$arrFieldList[$i++] = 'EMP_PER_ADDRESS2';
		$arrFieldList[$i++] = 'EMP_PER_ADDRESS3';
		$arrFieldList[$i++] = 'EMP_PER_CITY';
		$arrFieldList[$i++] = 'EMP_PER_POSTALCODE';
		$arrFieldList[$i++] = 'EMP_PER_TELEPHONE';
		$arrFieldList[$i++] = 'EMP_PER_MOBILE';
		$arrFieldList[$i++] = 'EMP_PER_FAX';
		$arrFieldList[$i++] = 'EMP_PER_EMAIL';
		$arrFieldList[$i++] = 'EMP_PER_COU_CODE';
		$arrFieldList[$i++] = 'EMP_PER_PROVINCE_CODE';
		$arrFieldList[$i++] = 'EMP_PER_DISTRICT_CODE';
		$arrFieldList[$i++] = 'EMP_PER_ELECTORATE_CODE';
								
		
		$tableName = 'HS_HR_EMPLOYEE';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_insert = 'true';
		$sql_builder->arr_insert = $arrRecordsList;		
		$sql_builder->arr_insertfield = $arrFieldList;		
			
	
		$sqlQString = $sql_builder->addNewRecordFeature2();
	
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		 return $message2;
		 echo $message2;
				
	}
	
	function updateEmpMain() {
		
		$arrRecordsList[0] = "'". $this->getEmpId() . "'";
		$arrRecordsList[1] = "'". $this->getEmpTitle() . "'";
		$arrRecordsList[2] = "'". $this->getEmpCallName() . "'";
		$arrRecordsList[3] = "'". $this->getEmpSurname() . "'";
		$arrRecordsList[4] = "'". $this->getEmpMaidenName() . "'";
		$arrRecordsList[5] = "'". $this->getEmpInitials() . "'";
		$arrRecordsList[6] = "'". $this->getEmpNamByIni() . "'";
		$arrRecordsList[7] = "'". $this->getEmpFullName() . "'";
		$arrRecordsList[8] = "'". $this->getEmpOtherName() . "'";
		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_TITLE';
		$arrFieldList[2] = 'EMP_CALLING_NAME';
		$arrFieldList[3] = 'EMP_SURNAME';
		$arrFieldList[4] = 'EMP_MAIDEN_NAME';
		$arrFieldList[5] = 'EMP_MIDDLE_INI';
		$arrFieldList[6] = 'EMP_NAMES_BY_INI';
		$arrFieldList[7] = 'EMP_FULLNAME';
		$arrFieldList[8] = 'EMP_OTHER_NAMES';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_update = 'true';
		$sql_builder->arr_update = $arrFieldList;	
		$sql_builder->arr_updateRecList = $arrRecordsList;	
	
		$sqlQString = $sql_builder->addUpdateRecord1();
	
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		return $message2;
	}
	

	function getNationCodes() {

		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_NATIONALITY';
		$arrFieldList[0] = 'NAT_CODE';
		$arrFieldList[1] = 'NAT_NAME';

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultSetMessage();

		$dbConnection = new DMLFunctions();
       		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$common_func = new CommonFunctions();

		$i=0;

		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {

	    	$arrayDispList[$i][0] = $line[0];
	    	$arrayDispList[$i][1] = $line[1];


	    	$i++;

	     }

	     if (isset($arrayDispList)) {

	       	return $arrayDispList;

	     } else {

	     	//Handle Exceptions
	     	//Create Logs

	     }

	}
	
	function getReligionCodes() {

		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_RELIGION';
		$arrFieldList[0] = 'RLG_CODE';
		$arrFieldList[1] = 'RLG_NAME';

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultSetMessage();

		$dbConnection = new DMLFunctions();
       		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$common_func = new CommonFunctions();

		$i=0;

		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {

	    	$arrayDispList[$i][0] = $line[0];
	    	$arrayDispList[$i][1] = $line[1];


	    	$i++;

	     }

	     if (isset($arrayDispList)) {

	       	return $arrayDispList;

	     } else {

	     	//Handle Exceptions
	     	//Create Logs

	     }

	}

	function filterEmpPers($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		
		$arrFieldList[1] = 'EMP_NIC_NO';
		$arrFieldList[2] = 'EMP_NIC_DATE';
		$arrFieldList[3] = 'EMP_BIRTHDAY';
		$arrFieldList[4] = 'EMP_BIRTHPLACE';
		$arrFieldList[5] = 'EMP_GENDER';
		$arrFieldList[6] = 'EMP_BLOOD_GROUP';
		$arrFieldList[7] = 'NAT_CODE';
		$arrFieldList[8] = 'RLG_CODE';
		$arrFieldList[9] = 'EMP_MARITAL_STATUS';
		$arrFieldList[10] = 'EMP_MARRIED_DATE';
		
		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->selectOneRecordFiltered($this->getID);
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$i=0;
		
		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {
		 	
	    	for($c=0; count($arrFieldList) > $c ; $c++)	
				$arrayDispList[$i][$c] = $line[$c];

	    	$i++;
	     }
	     
	     if (isset($arrayDispList)) {
	     
			return $arrayDispList;
			
		} else {
		
			$arrayDispList = '';
			return $arrayDispList;
			
		}
				
	}

	function updateEmpPers() {
		
		$arrRecordsList[0] = "'". $this->getEmpId() . "'";
		$arrRecordsList[1] = "'". $this->getEmpNICNo() . "'";
		$arrRecordsList[2] = "'". $this->getEmpNICDate() . "'";
		$arrRecordsList[3] = "'". $this->getEmpDOB() . "'";
		$arrRecordsList[4] = "'". $this->getEmpBirthPlace() . "'";
		$arrRecordsList[5] = "'". $this->getEmpGender() . "'";
		$arrRecordsList[6] = "'". $this->getEmpBloodGrp() . "'";
		$arrRecordsList[7] = ($this->getEmpNation()=='0') ? 'null' : "'". $this->getEmpNation() . "'";    
		$arrRecordsList[8] = ($this->getEmpReligion()=='0') ? 'null' :"'". $this->getEmpReligion() . "'"; 
		$arrRecordsList[9] = "'". $this->getEmpMarital() . "'";
		$arrRecordsList[10] = "'". $this->getEmpMarriedDate() . "'";
		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_NIC_NO';
		$arrFieldList[2] = 'EMP_NIC_DATE';
		$arrFieldList[3] = 'EMP_BIRTHDAY';
		$arrFieldList[4] = 'EMP_BIRTHPLACE';
		$arrFieldList[5] = 'EMP_GENDER';
		$arrFieldList[6] = 'EMP_BLOOD_GROUP';
		$arrFieldList[7] = 'NAT_CODE';
		$arrFieldList[8] = 'RLG_CODE';
		$arrFieldList[9] = 'EMP_MARITAL_STATUS';
		$arrFieldList[10] = 'EMP_MARRIED_DATE';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_update = 'true';
		$sql_builder->arr_update = $arrFieldList;	
		$sql_builder->arr_updateRecList = $arrRecordsList;	
	
		$sqlQString = $sql_builder->addUpdateRecord1();
	
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		return $message2;
	}

	function filterEmpJobInfo($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		
		$arrFieldList[1] = 'EMP_DATE_JOINED';
		$arrFieldList[2] = 'EMP_CONFIRM_FLG';
		$arrFieldList[3] = 'EMP_RESIGN_DATE';
		$arrFieldList[4] = 'EMP_RETIRE_DATE';
		$arrFieldList[5] = 'SAL_GRD_CODE';
		$arrFieldList[6] = 'CT_CODE';
		$arrFieldList[7] = 'DSG_CODE';
		$arrFieldList[8] = 'CENTRE_CODE';
		$arrFieldList[9] = 'EMP_WORKHOURS';
		$arrFieldList[10] = 'EMP_JOB_PREFERENCE';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->selectOneRecordFiltered($this->getID);
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$i=0;
		
		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {
	    		for($c=0; count($arrFieldList) > $c ; $c++)	
					$arrayDispList[$i][$c] = $line[$c];
					
	    		$i++;
	     }
	     
	     if (isset($arrayDispList)) {
	     
			return $arrayDispList;
			
		} else {
		
			$arrayDispList = '';
			return $arrayDispList;
			
		}
	}

	function updateEmpJobInfo() {
		
		$arrRecordsList[0] = "'". $this->getEmpId() . "'";
		$arrRecordsList[1] = "'". $this->getEmpDatJoin() . "'";
		$arrRecordsList[2] = "'". $this->getEmpConfirmFlag() . "'";
		$arrRecordsList[3] = "'". $this->getEmpResigDat() . "'";
		$arrRecordsList[4] = "'". $this->getEmpRetireDat() . "'";
		$arrRecordsList[5] = ($this->getEmpSalGrd()=='0') ? 'null' : "'". $this->getEmpSalGrd() . "'";    
		$arrRecordsList[6] = ($this->getEmpCorpTit()=='0') ? 'null' : "'". $this->getEmpCorpTit() . "'";  
		$arrRecordsList[7] = ($this->getEmpDesig()=='0') ? 'null' : "'". $this->getEmpDesig() . "'";      
		$arrRecordsList[8] = ($this->getEmpCostCode()=='0') ? 'null' : "'". $this->getEmpCostCode() . "'";
		$arrRecordsList[9] = "'". $this->getEmpWorkHours() . "'";
		$arrRecordsList[10] = "'". $this->getEmpJobPref() . "'";
		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_DATE_JOINED';
		$arrFieldList[2] = 'EMP_CONFIRM_FLG';
		$arrFieldList[3] = 'EMP_RESIGN_DATE';
		$arrFieldList[4] = 'EMP_RETIRE_DATE';
		$arrFieldList[5] = 'SAL_GRD_CODE';
		$arrFieldList[6] = 'CT_CODE';
		$arrFieldList[7] = 'DSG_CODE';
		$arrFieldList[8] = 'CENTRE_CODE';
		$arrFieldList[9] = 'EMP_WORKHOURS';
		$arrFieldList[10] = 'EMP_JOB_PREFERENCE';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_update = 'true';
		$sql_builder->arr_update = $arrFieldList;	
		$sql_builder->arr_updateRecList = $arrRecordsList;	
	
		$sqlQString = $sql_builder->addUpdateRecord1();
	
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		return $message2;
	}
	
	function getSalGrdCodes() {

		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_PR_SALARY_GRADE';
		$arrFieldList[0] = 'SAL_GRD_CODE';
		$arrFieldList[1] = 'SAL_GRD_NAME';

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultSetMessage();

		$dbConnection = new DMLFunctions();
       		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$common_func = new CommonFunctions();

		$i=0;

		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {

	    	$arrayDispList[$i][0] = $line[0];
	    	$arrayDispList[$i][1] = $line[1];


	    	$i++;

	     }

	     if (isset($arrayDispList)) {

	       	return $arrayDispList;

	     } else {

	     	//Handle Exceptions
	     	//Create Logs

	     }

	}


	function getCorpTitles($getID) {
		
		$this->getID = $getID;
		$arrFieldList[0] = 'SAL_GRD_CODE';
		$arrFieldList[1] = 'CT_CODE';
		$arrFieldList[2] = 'CT_NAME';


		$tableName = 'HS_HR_CORPORATE_TITLE';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->selectOneRecordFiltered($this->getID);
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$i=0;
		
		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {
		 	
	    	$arrayDispList[$i][0] = $line[0];
	    	$arrayDispList[$i][1] = $line[1];
	    	$arrayDispList[$i][2] = $line[2];
	    	$i++;
	    	
	     }
	     
	     if (isset($arrayDispList)) {
	     
			return $arrayDispList;
			
		} else {
		
			$arrayDispList = '';
			return $arrayDispList;
			
		}
				
	}


	function getCostCodes() {

		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_PR_COST_CENTRE';
		$arrFieldList[0] = 'CENTRE_CODE';
		$arrFieldList[1] = 'CENTRE_NAME';

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultSetMessage();

		$dbConnection = new DMLFunctions();
       		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$common_func = new CommonFunctions();

		$i=0;

		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {

	    	$arrayDispList[$i][0] = $line[0];
	    	$arrayDispList[$i][1] = $line[1];


	    	$i++;

	     }

	     if (isset($arrayDispList)) {

	       	return $arrayDispList;

	     } else {

	     	//Handle Exceptions
	     	//Create Logs

	     }

	}

	function getDes($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_DESIGNATION';
        $arrFieldList[0] = 'CT_CODE';
		$arrFieldList[1] = 'DSG_CODE';
		$arrFieldList[2] = 'DSG_NAME';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->selectOneRecordFiltered($this->getID);
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$i=0;
		
		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {
		 	
	    	$arrayDispList[$i][0] = $line[0];
	    	$arrayDispList[$i][1] = $line[1];
	    	$arrayDispList[$i][2] = $line[2];
	    	$i++;
	    	
	     }
	     
	     if (isset($arrayDispList)) {
	     
			return $arrayDispList;
			
		} else {
		
			$arrayDispList = '';
			return $arrayDispList;
			
		}
				
	}


	function filterEmpJobStat($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';

		$arrFieldList[1] = 'EMP_TYPE';
		$arrFieldList[2] = 'STAFFCAT_CODE';
		$arrFieldList[3] = 'CAT_CODE';
		$arrFieldList[4] = 'EMP_CONTARCT_START_DATE';
		$arrFieldList[5] = 'EMP_CONTRACT_END_DATE';
		$arrFieldList[6] = 'EMP_CONT_TO_PERM_FLG';
		$arrFieldList[7] = 'EMP_CONT_TO_PERM_DATE';
		$arrFieldList[8] = 'EMP_ACTIVE_HRM_FLG';
		$arrFieldList[9] = 'EMP_ACTIVE_PAYROLL_FLG';
		$arrFieldList[10] = 'EMP_ACTIVE_ATT_FLG';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->selectOneRecordFiltered($this->getID);
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$i=0;
		
		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {
	    		for($c=0; count($arrFieldList) > $c ; $c++)	
					$arrayDispList[$i][$c] = $line[$c];
					
	    		$i++;
	     }
	     
	     if (isset($arrayDispList)) {
	     
			return $arrayDispList;
			
		} else {
		
			$arrayDispList = '';
			return $arrayDispList;
			
		}
	}

	function updateEmpJobStat() {
		
		$arrRecordsList[0] = "'". $this->getEmpId() . "'";
		$arrRecordsList[1] = "'". $this->getEmpType() . "'";
		$arrRecordsList[2] = ($this->getEmpStatutory()=='0') ? 'null' : "'". $this->getEmpStatutory() . "'";
		$arrRecordsList[3] = ($this->getEmpCat()=='0') ? 'null' :"'". $this->getEmpCat() . "'";             
		$arrRecordsList[4] = "'". $this->getEmpStartDat() . "'";
		$arrRecordsList[5] = "'". $this->getEmpEndDat() . "'";
		$arrRecordsList[6] = "'". $this->getEmpConToPermFlag() . "'";
		$arrRecordsList[7] = "'". $this->getEmpConToPermDat() . "'";
		$arrRecordsList[8] = "'". $this->getEmpHRActivFlag() . "'";
		$arrRecordsList[9] = "'". $this->getEmpPayActivFlag() . "'";
		$arrRecordsList[10] = "'". $this->getEmpTimAttActivFlag() . "'";
		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_TYPE';
		$arrFieldList[2] = 'STAFFCAT_CODE';
		$arrFieldList[3] = 'CAT_CODE';
		$arrFieldList[4] = 'EMP_CONTARCT_START_DATE';
		$arrFieldList[5] = 'EMP_CONTRACT_END_DATE';
		$arrFieldList[6] = 'EMP_CONT_TO_PERM_FLG';
		$arrFieldList[7] = 'EMP_CONT_TO_PERM_DATE';
		$arrFieldList[8] = 'EMP_ACTIVE_HRM_FLG';
		$arrFieldList[9] = 'EMP_ACTIVE_PAYROLL_FLG';
		$arrFieldList[10] = 'EMP_ACTIVE_ATT_FLG';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_update = 'true';
		$sql_builder->arr_update = $arrFieldList;	
		$sql_builder->arr_updateRecList = $arrRecordsList;	
	
		$sqlQString = $sql_builder->addUpdateRecord1();
	
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		return $message2;
	}

	function getStatCodes() {

		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_STAFFCAT';
		$arrFieldList[0] = 'STAFFCAT_CODE';
		$arrFieldList[1] = 'STAFFCAT_NAME';

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultSetMessage();

		$dbConnection = new DMLFunctions();
       		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$common_func = new CommonFunctions();

		$i=0;

		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {

	    	$arrayDispList[$i][0] = $line[0];
	    	$arrayDispList[$i][1] = $line[1];


	    	$i++;

	     }

	     if (isset($arrayDispList)) {

	       	return $arrayDispList;

	     } else {

	     	//Handle Exceptions
	     	//Create Logs
	     }
	}

	function getCatCodes() {

		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_CATEGORY';
		$arrFieldList[0] = 'CAT_CODE';
		$arrFieldList[1] = 'CAT_NAME';

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultSetMessage();

		$dbConnection = new DMLFunctions();
       		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$common_func = new CommonFunctions();

		$i=0;

		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {

	    	$arrayDispList[$i][0] = $line[0];
	    	$arrayDispList[$i][1] = $line[1];


	    	$i++;

	     }

	     if (isset($arrayDispList)) {

	       	return $arrayDispList;

	     } else {

	     	//Handle Exceptions
	     	//Create Logs

	     }

	}

	function filterEmpWrkStaion($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_TITLE';
		$arrFieldList[2] = 'EMP_CALLING_NAME';
		$arrFieldList[3] = 'EMP_SURNAME';
		$arrFieldList[4] = 'EMP_MAIDEN_NAME';
		$arrFieldList[5] = 'EMP_MIDDLE_INI';
		$arrFieldList[6] = 'EMP_NAMES_BY_INI';
		$arrFieldList[7] = 'EMP_FULLNAME';
		$arrFieldList[8] = 'EMP_OTHER_NAMES';

		$arrFieldList[9] = 'LOC_CODE';
		$arrFieldList[10] = 'EMP_PREF_WORK_STATION';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->selectOneRecordFiltered($this->getID);
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$i=0;
		
		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {
	    		for($c=0; count($arrFieldList) > $c ; $c++)	
					$arrayDispList[$i][$c] = $line[$c];
					
	    		$i++;
	     }
	     
	     if (isset($arrayDispList)) {
	     
			return $arrayDispList;
			
		} else {
		
			$arrayDispList = '';
			return $arrayDispList;
			
		}
	}

	function updateEmpWrkStation() {
		
		$arrRecordsList[0] = "'". $this->getEmpId() . "'";
		$arrRecordsList[1] = ($this->getEmpLoc()=='0') ? 'null' : "'". $this->getEmpLoc() . "'";       
		$arrRecordsList[2] = ($this->getEmpPrefLoc()=='0') ? 'null' :"'". $this->getEmpPrefLoc() . "'";
		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'LOC_CODE';
		$arrFieldList[2] = 'EMP_PREF_WORK_STATION';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_update = 'true';
		$sql_builder->arr_update = $arrFieldList;	
		$sql_builder->arr_updateRecList = $arrRecordsList;	
	
		$sqlQString = $sql_builder->addUpdateRecord1();
	
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		return $message2;
	}

	function getLocCodes() {

		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_LOCATION';
		$arrFieldList[0] = 'LOC_CODE';
		$arrFieldList[1] = 'LOC_NAME';

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultSetMessage();

		$dbConnection = new DMLFunctions();
       		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$common_func = new CommonFunctions();

		$i=0;

		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {

	    	$arrayDispList[$i][0] = $line[0];
	    	$arrayDispList[$i][1] = $line[1];


	    	$i++;

	     }

	     if (isset($arrayDispList)) {

	       	return $arrayDispList;

	     } else {

	     	//Handle Exceptions
	     	//Create Logs

	     }

	}

	function filterEmpTax($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';

		$arrFieldList[1] = 'EMP_PAYE_TAX_EXEMPT';
		$arrFieldList[2] = 'EMP_TAXONTAX_FLG';
		$arrFieldList[3] = 'EMP_TAX_ID_NUMBER';
		$arrFieldList[4] = 'EMP_EPF_ELIGIBLE_FLG';
		$arrFieldList[5] = 'EMP_EPF_NUMBER';
		$arrFieldList[6] = 'EMP_EPF_PAYMENT_TYPE_FLG';
		$arrFieldList[7] = 'EMP_EPF_EMPLOYEE_AMOUNT';
		$arrFieldList[8] = 'EMP_EPF_EMPLOYER_AMOUNT';
		$arrFieldList[9] = 'EMP_ETF_ELIGIBLE_FLG';
		$arrFieldList[10] = 'EMP_ETF_NUMBER';
		$arrFieldList[11] = 'EMP_ETF_EMPLOYEE_AMOUNT';
		$arrFieldList[12] = 'EMP_ETF_DATE';
		$arrFieldList[13] = 'EMP_MSPS_ELIGIBLE_FLG';
		$arrFieldList[14] = 'EMP_MSPS_EMPLOYEE_AMOUNT';
		$arrFieldList[15] = 'EMP_MSPS_EMPLOYER_AMOUNT';
		$arrFieldList[16] = 'EMP_TAX_COUNTRY';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->selectOneRecordFiltered($this->getID);
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$i=0;
		
		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {
	    		for($c=0; count($arrFieldList) > $c ; $c++)	
					$arrayDispList[$i][$c] = $line[$c];
					
	    		$i++;
	     }
	     
	     if (isset($arrayDispList)) {
	     
			return $arrayDispList;
			
		} else {
		
			$arrayDispList = '';
			return $arrayDispList;
			
		}
	}

	function updateEmpTax() {
		
		$arrRecordsList[0] = "'". $this->getEmpId() . "'";
		$arrRecordsList[1] = "'". $this->getEmpTaxExempt() . "'";
		$arrRecordsList[2] = "'". $this->getEmpTaxOnTaxFlag() . "'";
		$arrRecordsList[3] = "'". $this->getEmpTaxID() . "'";
		$arrRecordsList[4] = "'". $this->getEmpEPFEligibleFlag() . "'";
		$arrRecordsList[5] = "'". $this->getEmpEPFNo() . "'";
		$arrRecordsList[6] = "'". $this->getCFundCBFundFlag() . "'";
		$arrRecordsList[7] = "'". $this->getEPFEmployeePercen() . "'";
		$arrRecordsList[8] = "'". $this->getEPFEmployerPercen() . "'";
		$arrRecordsList[9] = "'". $this->getETFEligibleFlag() . "'";
		$arrRecordsList[10] = "'". $this->getEmpETFNo() . "'";
		$arrRecordsList[11] = "'". $this->getETFEmployeePercen() . "'";
		$arrRecordsList[12] = "'". $this->getETFDat() . "'";
		$arrRecordsList[13] = "'". $this->getMSPSEligibleFlag() . "'";
		$arrRecordsList[14] = "'". $this->getMSPSEmployeePercen() . "'";
		$arrRecordsList[15] = "'". $this->getMSPSEmployerPercen() . "'";
		
		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_PAYE_TAX_EXEMPT';
		$arrFieldList[2] = 'EMP_TAXONTAX_FLG';
		$arrFieldList[3] = 'EMP_TAX_ID_NUMBER';
		$arrFieldList[4] = 'EMP_EPF_ELIGIBLE_FLG';
		$arrFieldList[5] = 'EMP_EPF_NUMBER';
		$arrFieldList[6] = 'EMP_EPF_PAYMENT_TYPE_FLG';
		$arrFieldList[7] = 'EMP_EPF_EMPLOYEE_AMOUNT';
		$arrFieldList[8] = 'EMP_EPF_EMPLOYER_AMOUNT';
		$arrFieldList[9] = 'EMP_ETF_ELIGIBLE_FLG';
		$arrFieldList[10] = 'EMP_ETF_NUMBER';
		$arrFieldList[11] = 'EMP_ETF_EMPLOYEE_AMOUNT';
		$arrFieldList[12] = 'EMP_ETF_DATE';
		$arrFieldList[13] = 'EMP_MSPS_ELIGIBLE_FLG';
		$arrFieldList[14] = 'EMP_MSPS_EMPLOYEE_AMOUNT';
		$arrFieldList[15] = 'EMP_MSPS_EMPLOYER_AMOUNT';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_update = 'true';
		$sql_builder->arr_update = $arrFieldList;	
		$sql_builder->arr_updateRecList = $arrRecordsList;	
	
		$sqlQString = $sql_builder->addUpdateRecord1();
	
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		return $message2;
	}

	function filterEmpPermRes($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';

		$arrFieldList[1] = 'EMP_PER_ADDRESS1';
		$arrFieldList[2] = 'EMP_PER_ADDRESS2';
		$arrFieldList[3] = 'EMP_PER_ADDRESS3';
		$arrFieldList[4] = 'EMP_PER_CITY';
		$arrFieldList[5] = 'EMP_PER_POSTALCODE';
		$arrFieldList[6] = 'EMP_PER_TELEPHONE';
		$arrFieldList[7] = 'EMP_PER_MOBILE';
		$arrFieldList[8] = 'EMP_PER_FAX';
		$arrFieldList[9] = 'EMP_PER_EMAIL';
		$arrFieldList[10] = 'EMP_PER_COU_CODE';
		$arrFieldList[11] = 'EMP_PER_PROVINCE_CODE';
		$arrFieldList[12] = 'EMP_PER_DISTRICT_CODE';
		$arrFieldList[13] = 'EMP_PER_ELECTORATE_CODE';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->selectOneRecordFiltered($this->getID);
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$i=0;
		
		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {
	    		for($c=0; count($arrFieldList) > $c ; $c++)	
					$arrayDispList[$i][$c] = $line[$c];
					
	    		$i++;
	     }
	     
	     if (isset($arrayDispList)) {
	     
			return $arrayDispList;
			
		} else {
		
			$arrayDispList = '';
			return $arrayDispList;
			
		}
	}

	function updateEmpPermRes() {
		
		$arrRecordsList[0] = "'". $this->getEmpId() . "'";
		$arrRecordsList[1] = "'". $this->getEmpPermHouseNo() . "'";
		$arrRecordsList[2] = "'". $this->getEmpPermStreet1() . "'";
		$arrRecordsList[3] = "'". $this->getEmpPermStreet2() . "'";
		$arrRecordsList[4] = "'". $this->getEmpPermCityTown() . "'";
		$arrRecordsList[5] = "'". $this->getEmpPermPostCode() . "'";
		$arrRecordsList[6] = "'". $this->getEmpPermTelephone() . "'";
		$arrRecordsList[7] = "'". $this->getEmpPermMobile() . "'";
		$arrRecordsList[8] = "'". $this->getEmpPermFax() . "'";
		$arrRecordsList[9] = "'". $this->getEmpPermEmail() . "'";
		$arrRecordsList[10] = ($this->getEmpPermCountry()=='0') ? 'null' :"'". $this->getEmpPermCountry() . "'";      
		$arrRecordsList[11] = ($this->getEmpPermProvince()=='0') ? 'null' :"'". $this->getEmpPermProvince() . "'";    
		$arrRecordsList[12] = ($this->getEmpPermDistrict()=='0') ? 'null' :"'". $this->getEmpPermDistrict() . "'";    
		$arrRecordsList[13] = ($this->getEmpPermElectorate()=='0') ? 'null' :"'". $this->getEmpPermElectorate() . "'";
		
		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_PER_ADDRESS1';
		$arrFieldList[2] = 'EMP_PER_ADDRESS2';
		$arrFieldList[3] = 'EMP_PER_ADDRESS3';
		$arrFieldList[4] = 'EMP_PER_CITY';
		$arrFieldList[5] = 'EMP_PER_POSTALCODE';
		$arrFieldList[6] = 'EMP_PER_TELEPHONE';
		$arrFieldList[7] = 'EMP_PER_MOBILE';
		$arrFieldList[8] = 'EMP_PER_FAX';
		$arrFieldList[9] = 'EMP_PER_EMAIL';
		$arrFieldList[10] = 'EMP_PER_COU_CODE';
		$arrFieldList[11] = 'EMP_PER_PROVINCE_CODE';
		$arrFieldList[12] = 'EMP_PER_DISTRICT_CODE';
		$arrFieldList[13] = 'EMP_PER_ELECTORATE_CODE';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_update = 'true';
		$sql_builder->arr_update = $arrFieldList;	
		$sql_builder->arr_updateRecList = $arrRecordsList;	
	
		$sqlQString = $sql_builder->addUpdateRecord1();
	
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		return $message2;
	}

	function getCountryCodes() {

		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_COUNTRY';
		$arrFieldList[0] = 'COU_CODE';
		$arrFieldList[1] = 'COU_NAME';

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultSetMessage();

		$dbConnection = new DMLFunctions();
       		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$common_func = new CommonFunctions();

		$i=0;

		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {

	    	$arrayDispList[$i][0] = $line[0];
	    	$arrayDispList[$i][1] = $line[1];


	    	$i++;

	     }

	     if (isset($arrayDispList)) {

	       	return $arrayDispList;

	     } else {

	     	//Handle Exceptions
	     	//Create Logs

	     }

	}

	function getProvinceCodes($getID) {

		$this->getID = $getID;
		$tableName = 'HS_HR_PROVINCE';
		$arrFieldList[0] = 'COU_CODE';
		$arrFieldList[1] = 'PROVINCE_CODE';
		$arrFieldList[2] = 'PROVINCE_NAME';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->selectOneRecordFiltered($this->getID);
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$i=0;
		
		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {
	    		for($c=0; count($arrFieldList) > $c ; $c++)	
					$arrayDispList[$i][$c] = $line[$c];
					
	    		$i++;
	     }
	     
	     if (isset($arrayDispList)) {
	     
			return $arrayDispList;
			
		} else {
		
			$arrayDispList = '';
			return $arrayDispList;
			
		}
	}

	function getDistrictCodes($getID) {

		$this->getID = $getID;
		$tableName = 'HS_HR_DISTRICT';
		$arrFieldList[0] = 'PROVINCE_CODE';
		$arrFieldList[1] = 'DISTRICT_CODE';
		$arrFieldList[2] = 'DISTRICT_NAME';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->selectOneRecordFiltered($this->getID);
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$i=0;
		
		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {
	    		for($c=0; count($arrFieldList) > $c ; $c++)	
					$arrayDispList[$i][$c] = $line[$c];
					
	    		$i++;
	     }
	     
	     if (isset($arrayDispList)) {
	     
			return $arrayDispList;
			
		} else {
		
			$arrayDispList = '';
			return $arrayDispList;
			
		}
	}

	function getElectorateCodes() {

		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_ELECTORATE';
		$arrFieldList[0] = 'ELECTORATE_CODE';
		$arrFieldList[1] = 'ELECTORATE_NAME';

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultSetMessage();

		$dbConnection = new DMLFunctions();
       		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$common_func = new CommonFunctions();

		$i=0;

		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {

	    	$arrayDispList[$i][0] = $line[0];
	    	$arrayDispList[$i][1] = $line[1];


	    	$i++;

	     }

	     if (isset($arrayDispList)) {

	       	return $arrayDispList;

	     } else {

	     	//Handle Exceptions
	     	//Create Logs

	     }

	}
/////////////
	function filterEmpTempRes($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_TITLE';
		$arrFieldList[2] = 'EMP_CALLING_NAME';
		$arrFieldList[3] = 'EMP_SURNAME';
		$arrFieldList[4] = 'EMP_MAIDEN_NAME';
		$arrFieldList[5] = 'EMP_MIDDLE_INI';
		$arrFieldList[6] = 'EMP_NAMES_BY_INI';
		$arrFieldList[7] = 'EMP_FULLNAME';
		$arrFieldList[8] = 'EMP_OTHER_NAMES';

		$arrFieldList[9] = 'EMP_TEM_ADDRESS1';
		$arrFieldList[10] = 'EMP_TEM_ADDRESS2';
		$arrFieldList[11] = 'EMP_TEM_ADDRESS3';
		$arrFieldList[12] = 'EMP_TEM_CITY';
		$arrFieldList[13] = 'EMP_TEM_POSTALCODE';
		$arrFieldList[14] = 'EMP_TEM_TELEPHONE';
		$arrFieldList[15] = 'EMP_TEM_MOBILE';
		$arrFieldList[16] = 'EMP_TEM_FAX';
		$arrFieldList[17] = 'EMP_TEM_EMAIL';
		$arrFieldList[18] = 'EMP_TEM_COU_CODE';
		$arrFieldList[19] = 'EMP_TEM_PROVINCE_CODE';
		$arrFieldList[20] = 'EMP_TEM_DISTRICT_CODE';
		$arrFieldList[21] = 'EMP_TEM_ELECTORATE_CODE';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->selectOneRecordFiltered($this->getID);
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$i=0;
		
		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {
	    		for($c=0; count($arrFieldList) > $c ; $c++)	
					$arrayDispList[$i][$c] = $line[$c];
					
	    		$i++;
	     }
	     
	     if (isset($arrayDispList)) {
	     
			return $arrayDispList;
			
		} else {
		
			$arrayDispList = '';
			return $arrayDispList;
			
		}
	}

	function updateEmpTempRes() {
		
		$arrRecordsList[0] = "'". $this->getEmpId() . "'";
		$arrRecordsList[1] = "'". $this->getEmpTempHouseNo() . "'";
		$arrRecordsList[2] = "'". $this->getEmpTempStreet1() . "'";
		$arrRecordsList[3] = "'". $this->getEmpTempStreet2() . "'";
		$arrRecordsList[4] = "'". $this->getEmpTempCityTown() . "'";
		$arrRecordsList[5] = "'". $this->getEmpTempPostCode() . "'";
		$arrRecordsList[6] = "'". $this->getEmpTempTelephone() . "'";
		$arrRecordsList[7] = "'". $this->getEmpTempMobile() . "'";
		$arrRecordsList[8] = "'". $this->getEmpTempFax() . "'";
		$arrRecordsList[9] = "'". $this->getEmpTempEmail() . "'";
		$arrRecordsList[10] = "'". $this->getEmpTempCountry() . "'";
		$arrRecordsList[11] = "'". $this->getEmpTempProvince() . "'";
		$arrRecordsList[12] = "'". $this->getEmpTempDistrict() . "'";
		$arrRecordsList[13] = "'". $this->getEmpTempElectorate() . "'";
		
		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_TEM_ADDRESS1';
		$arrFieldList[2] = 'EMP_TEM_ADDRESS2';
		$arrFieldList[3] = 'EMP_TEM_ADDRESS3';
		$arrFieldList[4] = 'EMP_TEM_CITY';
		$arrFieldList[5] = 'EMP_TEM_POSTALCODE';
		$arrFieldList[6] = 'EMP_TEM_TELEPHONE';
		$arrFieldList[7] = 'EMP_TEM_MOBILE';
		$arrFieldList[8] = 'EMP_TEM_FAX';
		$arrFieldList[9] = 'EMP_TEM_EMAIL';
		$arrFieldList[10] = 'EMP_TEM_COU_CODE';
		$arrFieldList[11] = 'EMP_TEM_PROVINCE_CODE';
		$arrFieldList[12] = 'EMP_TEM_DISTRICT_CODE';
		$arrFieldList[13] = 'EMP_TEM_ELECTORATE_CODE';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_update = 'true';
		$sql_builder->arr_update = $arrFieldList;	
		$sql_builder->arr_updateRecList = $arrRecordsList;	
	
		$sqlQString = $sql_builder->addUpdateRecord1();
	
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		return $message2;
	}

	function filterEmpOff($getID) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_TITLE';
		$arrFieldList[2] = 'EMP_CALLING_NAME';
		$arrFieldList[3] = 'EMP_SURNAME';
		$arrFieldList[4] = 'EMP_MAIDEN_NAME';
		$arrFieldList[5] = 'EMP_MIDDLE_INI';
		$arrFieldList[6] = 'EMP_NAMES_BY_INI';
		$arrFieldList[7] = 'EMP_FULLNAME';
		$arrFieldList[8] = 'EMP_OTHER_NAMES';

		$arrFieldList[9] = 'EMP_OFFICE_PHONE';
		$arrFieldList[10] = 'EMP_OFFICE_EXTN';
		$arrFieldList[11] = 'EMP_OFFICE_EMAIL';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->selectOneRecordFiltered($this->getID);
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$i=0;
		
		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {
	    		for($c=0; count($arrFieldList) > $c ; $c++)	
					$arrayDispList[$i][$c] = $line[$c];
					
	    		$i++;
	     }
	     
	     if (isset($arrayDispList)) {
	     
			return $arrayDispList;
			
		} else {
		
			$arrayDispList = '';
			return $arrayDispList;
			
		}
	}

	function updateEmpOff() {
		
		$arrRecordsList[0] = "'". $this->getEmpId() . "'";
		$arrRecordsList[1] = "'". $this->getEmpGenLine() . "'";
		$arrRecordsList[2] = "'". $this->getEmpExt() . "'";
		$arrRecordsList[3] = "'". $this->getEmpEmail() . "'";
		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_OFFICE_PHONE';
		$arrFieldList[2] = 'EMP_OFFICE_EXTN';
		$arrFieldList[3] = 'EMP_OFFICE_EMAIL';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_update = 'true';
		$sql_builder->arr_update = $arrFieldList;	
		$sql_builder->arr_updateRecList = $arrRecordsList;	
	
		$sqlQString = $sql_builder->addUpdateRecord1();
	
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		return $message2;
	}
	

	function getHierarchyDef() {
		
		$tableName = 'HS_HR_COMPANY_HIERARCHY_DEF';			
		$arrFieldList[0] = 'DEF_LEVEL';
		$arrFieldList[1] = 'DEF_NAME';
		
		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->passResultSetMessage();
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$i=0;
		
		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {
		 	
	    	$arrayDispList[$i][0] = $line[0];
	    	$arrayDispList[$i][1] = $line[1];
	    	$i++;
	    	
	     }
	     
	     if (isset($arrayDispList)) {
	    
	     	return $arrayDispList;
	        
			
		} else {
		
			$arrayDispList = '';
			return $arrayDispList;
			
		}
	}

	function getCompHier($getID,$schKey) {
		
		$this->getID = $getID;
		$tableName = 'HS_HR_COMPANY_HIERARCHY';
        $arrFieldList[0] = 'DEF_LEVEL';
        $arrFieldList[1] = 'HIE_RELATIONSHIP';
		$arrFieldList[2] = 'HIE_CODE';
		$arrFieldList[3] = 'HIE_NAME';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->selectOneRecordFiltered($this->getID,$schKey);
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$i=0;
		
		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {
		 	
	    	$arrayDispList[$i][0] = $line[0];
	    	$arrayDispList[$i][1] = $line[1];
	    	$arrayDispList[$i][2] = $line[2];
	    	$arrayDispList[$i][3] = $line[3];
	    	$i++;
	    	
	     }
	     
	     if (isset($arrayDispList)) {
	     
			return $arrayDispList;
			
		} else {
		
			$arrayDispList = '';
			return $arrayDispList;
			
		}
				
	}

	function getLocations() {
		
		$tableName = 'HS_HR_LOCATION';			
		$arrFieldList[0] = 'LOC_CODE';
		$arrFieldList[1] = 'LOC_NAME';
		
		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;		
			
		$sqlQString = $sql_builder->passResultSetMessage();
		
		//echo $sqlQString;		
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		$i=0;
		
		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {
		 	
	    	$arrayDispList[$i][0] = $line[0];
	    	$arrayDispList[$i][1] = $line[1];
	    	$i++;
	    	
	     }
	     
	     if (isset($arrayDispList)) {
	     
			return $arrayDispList;
			
		} else {
		
			$arrayDispList = '';
			return $arrayDispList;
			
		}
	}

	function updateEmpTaxCountry() {
		
		$arrRecordsList[0] = "'". $this->getEmpId() . "'";
		$arrRecordsList[1] = "'". $this->getEmpTaxCountry() . "'";
		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_TAX_COUNTRY';

		$sql_builder = new SQLQBuilder();
		
		$sql_builder->table_name = $tableName;
		$sql_builder->flg_update = 'true';
		$sql_builder->arr_update = $arrFieldList;	
		$sql_builder->arr_updateRecList = $arrRecordsList;	
	
		$sqlQString = $sql_builder->addUpdateRecord1();
	
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
		
		return $message2;
	}
	
}

?>
