<?php
/*
// OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
// all the essential functionalities required for any enterprise.
// Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com

// OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
// the GNU General Public License as published by the Free Software Foundation; either
// version 2 of the License, or (at your option) any later version.

// OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
// without even the implied warranty of MERCHANTabILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU General Public License for more details.

// You should have received a copy of the GNU General Public License along with this program;
// if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
// Boston, MA  02110-1301, USA
*/

require_once ROOT_PATH . '/lib/confs/sysConf.php';

class ReportGenerator {

	var $criteria;
	var $field;
	var $repID;
	var $repName;
	var $headName;
	var $employeeIdLength;

	function ReportGenerator() {
		$this->criteria=array();
		$this->field=array();

		$sysConfObj = new sysConf();

		$this->employeeIdLength = $sysConfObj->getEmployeeIdLength();
	}

	function ageToYear($age) {

	$currYear=  strftime('%Y');
	$currMonthDate = strftime('-%m-%d');
	$birthYear = (int)$currYear - $age;
	return  $birthYear .  $currMonthDate;

	}

	function reportQueryBuilder(){

		$groupBy = null;

		if(isset($this->criteria['AGE'])){
			$parameter = $this->criteria['AGE'];
			$pointer = explode('|',$parameter);

			$criteriaValue['AGE1'] = '\'' . $this->ageToYear($pointer[1]) . '\'';
			$criteriaField['AGE1'] = 'a.EMP_BIRTHDAY';
			$criteriaTable['AGE1'] = 'HS_HR_EMPLOYEE a';

			switch ($pointer[0]) {
				case '<' 	 : $criteriaComOper['AGE1'] = "<"; break;
				case '>' 	 : $criteriaComOper['AGE1'] = ">"; break;
				case 'range' : $criteriaComOper['AGE1'] = "<";
					          $criteriaValue['AGE2'] = '\'' . $this->ageToYear($pointer[2]) . '\'';
							  $criteriaField['AGE2'] = 'a.EMP_BIRTHDAY';
							  $criteriaTable['AGE2'] = 'HS_HR_EMPLOYEE a';
							  $criteriaComOper['AGE2'] = ">";
			}

		}

 		if(isset($this->criteria['EMPNO'])) {
 			if ($this->criteria['EMPNO'] == '') {
 				unset($this->criteria['EMPNO']);
 			} else {
				$criteriaValue['EMPNO'] = '\'' .$this->criteria['EMPNO'] . '\'';
				$criteriaComOper['EMPNO'] = "=";
 			}
 			$criteriaField['EMPNO'] = 'a.EMP_NUMBER';
			$criteriaTable['EMPNO'] = 'HS_HR_EMPLOYEE a';
 		}

 		if(isset($this->criteria['PAYGRD'])){

 			$criteriaValue['PAYGRD'] = '\'' . $this->criteria['PAYGRD'] . '\'';
			$criteriaField['PAYGRD'] = 'b.SAL_GRD_CODE';
			$criteriaTable['PAYGRD'] = 'HS_PR_SALARY_GRADE b';
			$criteriaComOper['PAYGRD'] = "=";

			$parentTableFieldName['PAYGRD']  = 'b.SAL_GRD_CODE';
 			$parentTableName['PAYGRD']	     = 'HS_PR_SALARY_GRADE b';
 			$existingTableFieldName['PAYGRD']= 'f.SAL_GRD_CODE';
 			$existingTableName['PAYGRD']	 = 'HS_HR_JOB_TITLE f';

 			$parentTableFieldName['JOBTITLE']  	= 'f.JOBTIT_CODE';
 			$parentTableName['JOBTITLE']	    	= 'HS_HR_JOB_TITLE f';
 			$existingTableFieldName['JOBTITLE']	= 'a.JOB_TITLE_CODE';
 			$existingTableName['JOBTITLE']		= 'HS_HR_EMPLOYEE a';
		}

    	if (isset($this->criteria['EMPSTATUS'])){

 			$criteriaValue['EMPSTATUS'] = '\'' . $this->criteria['EMPSTATUS'] . '\'';
			$criteriaField['EMPSTATUS'] = 'a.EMP_STATUS';
			$criteriaTable['EMPSTATUS'] = 'HS_HR_EMPLOYEE a';
			$criteriaComOper['EMPSTATUS'] = "=";
 		}

 		if(isset($this->criteria['SERPIR'])){
 			$parameter = $this->criteria['SERPIR'];
 			$pointer = explode('|',$parameter);

			$criteriaValue['SERPIR1'] = '\'' . $this->ageToYear($pointer[1]) . '\'';
			$criteriaField['SERPIR1'] = 'a.JOINED_DATE';
			$criteriaTable['SERPIR1'] = 'HS_HR_EMPLOYEE a';

			switch ($pointer[0]) {
				case '<' 	 : $criteriaComOper['SERPIR1'] = "<"; break;
				case '>' 	 : $criteriaComOper['SERPIR1'] = ">"; break;
				case 'range' : $criteriaComOper['SERPIR1'] = "<";
					           $criteriaValue['SERPIR2'] = '\'' . $this->ageToYear($pointer[2]) . '\'';
							   $criteriaField['SERPIR2'] = 'a.JOINED_DATE';
							   $criteriaTable['SERPIR2'] = 'HS_HR_EMPLOYEE a';
							   $criteriaComOper['SERPIR2'] = ">";
			}
 		}
 		if(isset($this->criteria['QUL'])){

 			$criteriaValue['QUL'] = '\'' . $this->criteria['QUL'] . '\'';
			$criteriaField['QUL'] = 'c.EDU_CODE';
			$criteriaTable['QUL'] = 'HS_HR_EMP_EDUCATION c';
			$criteriaComOper['QUL'] = "=";
		}

 		if(isset($this->criteria['JOBTITLE'])){

 			$criteriaValue['JOBTITLE'] = '\'' . $this->criteria['JOBTITLE'] . '\'';
			$criteriaField['JOBTITLE'] = 'a.JOB_TITLE_CODE';
			$criteriaTable['JOBTITLE'] = 'HS_HR_EMPLOYEE a';
			$criteriaComOper['JOBTITLE'] = "=";
		}

		if(isset($this->criteria['LANGUAGE'])){

 			$criteriaValue['LANGUAGE'] = '\'' . $this->criteria['LANGUAGE'] . '\'';
			$criteriaField['LANGUAGE'] = 'm.LANG_CODE';
			$criteriaTable['LANGUAGEL'] = 'HS_HR_EMP_LANGUAGE m';
			$criteriaComOper['LANGUAGE'] = "=";

			$parentTableFieldName['LANGUAGES']  	= 'n.LANG_CODE';
 			$parentTableName['LANGUAGES']	    	= 'HS_HR_LANGUAGE n';
 			$existingTableFieldName['LANGUAGES']	= 'm.LANG_CODE';
 			$existingTableName['LANGUAGES']			= 'HS_HR_EMP_LANGUAGE m';
		}

		if(isset($this->criteria['SKILL'])){

 			$criteriaValue['SKILL'] = '\'' . $this->criteria['SKILL'] . '\'';
			$criteriaField['SKILL'] = 'h.SKILL_CODE';
			$criteriaTable['SKILL'] = 'HS_HR_EMP_SKILL h';
			$criteriaComOper['SKILL'] = "=";

			$parentTableFieldName['SKILLS']  	= 'i.SKILL_CODE';
 			$parentTableName['SKILLS']	    	= 'HS_HR_SKILL i';
 			$existingTableFieldName['SKILLS']	= 'h.SKILL_CODE';
 			$existingTableName['SKILLS']		= 'HS_HR_EMP_SKILL h';
		}


 		// Select fields
 		if(isset($this->field['EMPNO'])==1){

 			$headingName['EMPNO'] = 'Employee No';
 			$parentTableFieldName['EMPNO']  = 'LPAD(a.`EMP_NUMBER`, '.$this->employeeIdLength.', 0)';
 			$parentTableName['EMPNO']	    = 'HS_HR_EMPLOYEE a';
 			$existingTableFieldName['EMPNO']= 'a.EMP_NUMBER';
 			$existingTableName['EMPNO']		= 'HS_HR_EMPLOYEE a';
 			$parentTableDescription['EMPNO']= 'a.`EMP_NUMBER`, IFNULL(a.`EMPLOYEE_ID`, LPAD(a.`EMP_NUMBER`, '.$this->employeeIdLength.', 0))';

			$groupBy['EMPNO'] = $parentTableDescription['EMPNO'];
 		}

 		if(isset($this->field['EMPFIRSTNAME'])==1){

 			$headingName['EMPFIRSTNAME'] = 'Employee First Name';
 			$parentTableFieldName['EMPFIRSTNAME']  = 'a.EMP_FIRSTNAME';
 			$parentTableName['EMPFIRSTNAME']	     = 'HS_HR_EMPLOYEE a';
 			$existingTableFieldName['EMPFIRSTNAME']= 'a.EMP_FIRSTNAME';
 			$existingTableName['EMPFIRSTNAME']	 = 'HS_HR_EMPLOYEE a';
 			$parentTableDescription['EMPFIRSTNAME']= 'a.`EMP_NUMBER`, a.EMP_FIRSTNAME';

 			$groupBy['EMPFIRSTNAME'] = $parentTableDescription['EMPFIRSTNAME'];
 		}

 		if(isset($this->field['EMPLASTNAME'])==1){

 			$headingName['EMPLASTNAME'] = 'Employee Last Name';
 			$parentTableFieldName['EMPLASTNAME']  = 'a.EMP_LASTNAME';
 			$parentTableName['EMPLASTNAME']	     = 'HS_HR_EMPLOYEE a';
 			$existingTableFieldName['EMPLASTNAME']= 'a.EMP_LASTNAME';
 			$existingTableName['EMPLASTNAME']	 = 'HS_HR_EMPLOYEE a';
 			$parentTableDescription['EMPLASTNAME']= 'a.`EMP_NUMBER`, a.EMP_LASTNAME';

 			$groupBy['EMPLASTNAME'] = $parentTableDescription['EMPLASTNAME'];
 		}

 		if(isset($this->field['ADDRESS1'])==1){

 			$headingName['ADDRESS1'] = 'Address';
 			$parentTableFieldName['ADDRESS1']    = 'a.EMP_STREET1';
 			$parentTableName['ADDRESS1']	     = 'HS_HR_EMPLOYEE a';
 			$existingTableFieldName['ADDRESS1']  = 'a.EMP_STREET1';
 			$existingTableName['ADDRESS1']	     = 'HS_HR_EMPLOYEE a';
 			$parentTableDescription['ADDRESS1']  = "a.`EMP_NUMBER`, CONCAT(a.EMP_STREET1, ' ', a.EMP_STREET2, ' ', a.CITY_CODE, ' ', a.PROVIN_CODE, ' ', a.COUN_CODE, ' ', a.EMP_ZIPCODE)";

 			$groupBy['ADDRESS1'] = $parentTableDescription['ADDRESS1'];
 		}

 		if(isset($this->field['ADDRESS2'])==1){

 			$parentTableFieldName['ADDRESS2']    = 'a.EMP_STREET2';
 			$parentTableName['ADDRESS2']	     = 'HS_HR_EMPLOYEE a';
 			$existingTableFieldName['ADDRESS2']  = 'a.EMP_STREET2';
 			$existingTableName['ADDRESS2']	     = 'HS_HR_EMPLOYEE a';
 			$parentTableDescription['ADDRESS2']  = 'a.`EMP_NUMBER`, a.EMP_STREET2';

 			$groupBy['ADDRESS2'] = $parentTableDescription['ADDRESS2'];
 		}


 		if(isset($this->field['TELENO'])==1){

 			$headingName['TELENO'] = 'Telephone';
 			$parentTableFieldName['TELENO']    = 'a.EMP_HM_TELEPHONE';
 			$parentTableName['TELENO']	       = 'HS_HR_EMPLOYEE a';
 			$existingTableFieldName['TELENO']  = 'a.EMP_HM_TELEPHONE';
 			$existingTableName['TELENO']	   = 'HS_HR_EMPLOYEE a';
 			$parentTableDescription['TELENO']  = 'a.`EMP_NUMBER`, a.EMP_HM_TELEPHONE';

 			$groupBy['TELENO'] = $parentTableDescription['TELENO'];
 		}

 		if(isset($this->field['MOBILE'])==1){

 			$headingName['MOBILE'] = 'Mobile';
 			$parentTableFieldName['MOBILE']    = 'a.EMP_MOBILE';
 			$parentTableName['MOBILE']	       = 'HS_HR_EMPLOYEE a';
 			$existingTableFieldName['MOBILE']  = 'a.EMP_MOBILE';
 			$existingTableName['MOBILE']	   = 'HS_HR_EMPLOYEE a';
 			$parentTableDescription['MOBILE']  = 'a.`EMP_NUMBER`, a.EMP_MOBILE';

 			$groupBy['MOBILE'] = $existingTableFieldName['MOBILE'];
 		}

 		if(isset($this->field['REPORTTO'])==1){

 			$headingName['REPORTTO'] = 'Report to';
 			$parentTableFieldName['REPORTTO']  = 'j.EMP_NUMBER';
 			$parentTableName['REPORTTO'] = 'HS_HR_EMPLOYEE j';
 			$existingTableFieldName['REPORTTO']= 'g.EREP_SUP_EMP_NUMBER';
 			$existingTableName['REPORTTO']	 = 'HS_HR_EMP_REPORTTO g';
 			$parentTableDescription['REPORTTO']= "g.EREP_SUP_EMP_NUMBER, CONCAT(j.EMP_FIRSTNAME, ' ', j.EMP_LASTNAME)";

 			$groupBy['REPORTTO'] = $existingTableFieldName['REPORTTO'];
 		}

 		if(isset($this->field['REPORTINGMETHOD'])==1){

 			$headingName['REPORTINGMETHOD'] = 'Reporting method';
 			$parentTableFieldName['REPORTINGMETHOD']  = 'g.EREP_SUB_EMP_NUMBER';
 			$parentTableName['REPORTINGMETHOD']	     = 'HS_HR_EMP_REPORTTO g';
 			$existingTableFieldName['REPORTINGMETHOD']= 'a.EMP_NUMBER';
 			$existingTableName['REPORTINGMETHOD']	 = 'HS_HR_EMPLOYEE a';
 			$parentTableDescription['REPORTINGMETHOD']= 'g.EREP_SUP_EMP_NUMBER, g.EREP_REPORTING_MODE';

 			$groupBy['REPORTINGMETHOD'] = $existingTableFieldName['REPORTINGMETHOD'];
 		}

 		if(isset($this->field['AGE'])==1) {

 			$headingName['AGE'] = 'Date of Birth';
 			$parentTableFieldName['AGE']  	= 'a.EMP_BIRTHDAY';
 			$parentTableName['AGE']	    	= 'HS_HR_EMPLOYEE a';
 			$existingTableFieldName['AGE']	= 'a.EMP_BIRTHDAY';
 			$existingTableName['AGE']		= 'HS_HR_EMPLOYEE a';
 			$parentTableDescription['AGE']  = "a.`EMP_NUMBER`, IF(STRCMP(DATE_FORMAT(a.EMP_BIRTHDAY, CONCAT(YEAR(a.EMP_BIRTHDAY), '-%m-%d')), '0-00-00'), DATE_FORMAT(a.EMP_BIRTHDAY, CONCAT(YEAR(a.EMP_BIRTHDAY), '-%m-%d')), 'N/A')";

 			$groupBy['AGE'] = $existingTableFieldName['AGE'];
 		}

 		if(isset($this->field['PAYGRD'])==1){

 			$headingName['PAYGRD'] = 'Salary Grade';
 			$parentTableFieldName['PAYGRD']  = 'b.SAL_GRD_CODE';
 			$parentTableName['PAYGRD']	     = 'HS_PR_SALARY_GRADE b';
 			$existingTableFieldName['PAYGRD']= 'f.SAL_GRD_CODE';
 			$existingTableName['PAYGRD']	 = 'HS_HR_JOB_TITLE f';
 			$parentTableDescription['PAYGRD']  = 'a.`EMP_NUMBER`, b.SAL_GRD_NAME';

 			$this->field['JOBTITLE'] = true;

 			$groupBy['PAYGRD'] = $existingTableFieldName['PAYGRD'];
 		}

 		if(isset($this->field['EMPSTATUS'])==1){

 			$headingName['EMPSTATUS'] = 'Employee Status';
 			$parentTableFieldName['EMPSTATUS']  = 'd.ESTAT_CODE';
 			$parentTableName['EMPSTATUS']	      = 'HS_HR_EMPSTAT d';
 			$existingTableFieldName['EMPSTATUS']= 'a.EMP_STATUS';
 			$existingTableName['EMPSTATUS']	  = 'HS_HR_EMPLOYEE a';
 			$parentTableDescription['EMPSTATUS']  = 'a.`EMP_NUMBER`, d.ESTAT_NAME';

 			$groupBy['EMPSTATUS'] = $existingTableFieldName['EMPSTATUS'];
 		}

 		if(isset($this->field['SERPIR'])==1){

 			$headingName['SERPIR'] = 'Joined Date';
 			$parentTableFieldName['SERPIR']  	= 'a.JOINED_DATE';
 			$parentTableName['SERPIR']	    	= 'HS_HR_EMPLOYEE a';
 			$existingTableFieldName['SERPIR']	= 'a.JOINED_DATE';
 			$existingTableName['SERPIR']		= 'HS_HR_EMPLOYEE a';
 			$parentTableDescription['SERPIR']  	= "a.`EMP_NUMBER`, IF(STRCMP(DATE_FORMAT(a.JOINED_DATE, CONCAT(YEAR(a.JOINED_DATE), '-%m-%d')), '0-00-00'), DATE_FORMAT(a.JOINED_DATE, CONCAT(YEAR(a.JOINED_DATE), '-%m-%d')), 'N/A')";

 			$groupBy['SERPIR'] = $existingTableFieldName['SERPIR'];
 		}

 		if(isset($this->field['SUBDIVISION'])==1){

 			$headingName['SUBDIVISION'] = 'Sub division';
 			$parentTableFieldName['SUBDIVISION']  	= 'l.ID';
 			$parentTableName['SUBDIVISION']	    	= 'HS_HR_COMPSTRUCTTREE l';
 			$existingTableFieldName['SUBDIVISION']	= 'a.WORK_STATION';
 			$existingTableName['SUBDIVISION']		= 'HS_HR_EMPLOYEE a';
 			$parentTableDescription['SUBDIVISION']  = 'a.`EMP_NUMBER`, a.WORK_STATION';

 			$groupBy['SUBDIVISION'] = $existingTableFieldName['SUBDIVISION'];
 		}

 		if(isset($this->field['QUL'])==1){

 			$headingName['QUL'] 			= 'Qualifications';
 			$parentTableFieldName['QUL']  	= 'e.EDU_CODE';
 			$parentTableName['QUL']	    	= 'HS_HR_EDUCATION e';
 			$existingTableFieldName['QUL']	= 'c.EDU_CODE';
 			$existingTableName['QUL']		= 'HS_HR_EMP_EDUCATION c';
 			$parentTableDescription['QUL']  = 'c.EDU_CODE, CONCAT(e.EDU_DEG, \', \', e.EDU_UNI)';

 			$groupBy['QUL'] = $existingTableFieldName['QUL'];
 		}

 		if(isset($this->field['YEAROFPASSING'])==1){

 			$headingName['YEAROFPASSING'] 			= 'Year of passing';
 			$parentTableFieldName['YEAROFPASSING']  	= 'c.EDU_CODE';
 			$parentTableName['YEAROFPASSING']	    	= 'HS_HR_EMP_EDUCATION c';
 			$existingTableFieldName['YEAROFPASSING']	= 'c.EDU_CODE';
 			$existingTableName['YEAROFPASSING']		= 'HS_HR_EMP_EDUCATION c';
 			$parentTableDescription['YEAROFPASSING']  = "c.EDU_CODE, YEAR(c.EDU_END_DATE)";

 			$groupBy['YEAROFPASSING'] = $existingTableFieldName['YEAROFPASSING'];
 		}

 		if(isset($this->field['JOBTITLE'])==1){

 			$headingName['JOBTITLE'] 				= 'Job Title';
 			$parentTableFieldName['JOBTITLE']  	= 'f.JOBTIT_CODE';
 			$parentTableName['JOBTITLE']	    	= 'HS_HR_JOB_TITLE f';
 			$existingTableFieldName['JOBTITLE']	= 'a.JOB_TITLE_CODE';
 			$existingTableName['JOBTITLE']		= 'HS_HR_EMPLOYEE a';
 			$parentTableDescription['JOBTITLE']  	= 'a.`EMP_NUMBER`, f.JOBTIT_NAME';

 			$groupBy['JOBTITLE'] = $existingTableFieldName['JOBTITLE'];
 		}

 		if(isset($this->field['LANGUAGES'])==1){

 			$headingName['LANGUAGES'] 				= 'Languages';
 			$parentTableFieldName['LANGUAGES']  	= 'n.LANG_CODE';
 			$parentTableName['LANGUAGES']	    	= 'HS_HR_LANGUAGE n';
 			$existingTableFieldName['LANGUAGES']	= 'm.LANG_CODE';
 			$existingTableName['LANGUAGES']			= 'HS_HR_EMP_LANGUAGE m';
 			$parentTableDescription['LANGUAGES']  	= 'n.LANG_CODE, n.LANG_NAME';

 			$groupBy['LANGUAGES'] = "m.lang_code, m.emp_number";
 		}

 		if(isset($this->field['SKILLS'])==1){

 			$headingName['SKILLS'] 				= 'Skills';
 			$parentTableFieldName['SKILLS']  	= 'i.SKILL_CODE';
 			$parentTableName['SKILLS']	    	= 'HS_HR_SKILL i';
 			$existingTableFieldName['SKILLS']	= 'h.SKILL_CODE';
 			$existingTableName['SKILLS']		= 'HS_HR_EMP_SKILL h';
 			$parentTableDescription['SKILLS']  	= 'i.SKILL_CODE, i.SKILL_NAME';

 			$groupBy['SKILLS'] = $existingTableFieldName['SKILLS'];
 		}

 		if(isset($this->field['CONTRACT'])==1){

 			$headingName['CONTRACT'] 				= 'Contract';
 			$parentTableFieldName['CONTRACT']  	= 'k.EMP_NUMBER';
 			$parentTableName['CONTRACT']	    	= 'HS_HR_EMP_CONTRACT_EXTEND k';
 			$existingTableFieldName['CONTRACT']	= 'a.EMP_NUMBER';
 			$existingTableName['CONTRACT']		= 'HS_HR_EMPLOYEE a';
 			$parentTableDescription['CONTRACT']  	= "k.ECON_EXTEND_ID, CONCAT(DATE(k.ECON_EXTEND_START_DATE), ' - ', DATE(k.ECON_EXTEND_END_DATE))";

 			$groupBy['CONTRACT'] = $parentTableDescription['CONTRACT'];
 		}

 		if(isset($this->field['WORKEXPERIENCE'])==1){

 			$headingName['WORKEXPERIENCE'] 				= 'Work experience';
 			$parentTableFieldName['WORKEXPERIENCE']  	= 'l.EMP_NUMBER';
 			$parentTableName['WORKEXPERIENCE']	    	= 'HS_HR_EMP_WORK_EXPERIENCE l';
 			$existingTableFieldName['WORKEXPERIENCE']	= 'a.EMP_NUMBER';
 			$existingTableName['WORKEXPERIENCE']		= 'HS_HR_EMPLOYEE a';
 			$parentTableDescription['WORKEXPERIENCE']  	= "l.EEXP_SEQNO, CONCAT(l.EEXP_EMPLOYER, ' - ', l.EEXP_JOBTIT, ' - ', (YEAR(l.EEXP_FROM_DATE)-YEAR(l.EEXP_TO_DATE)), ' yrs')";

 			$groupBy['WORKEXPERIENCE'] = $existingTableFieldName['WORKEXPERIENCE'];
 		}

 		$this->headName = array_values($headingName);
 			//////////////////////////////////

		$SQL1 = ' SELECT ';

				$fieldArrValues = array_values($parentTableDescription);
				$countArrSize = count($fieldArrValues);
				for ($i=0;$i<count($fieldArrValues); $i++)
					if ($i == ($countArrSize - 1))
						$SQL1 = $SQL1 . $fieldArrValues[$i] . ' ';
					else
						$SQL1 = $SQL1 . $fieldArrValues[$i] . ', ';

		$SQL1 = $SQL1 . ' FROM ' ;

		$joinQ = 'hs_hr_employee a';
		$joinTail = '';

		$tableStr = '';

		if(isset($this->field['EMPSTATUS'])==1){

			$jfield = 'EMPSTATUS';
			$joinQ = '('.$joinQ. ' LEFT JOIN '.$parentTableName[$jfield].
					 ' ON '.$existingTableFieldName[$jfield].' = '.
					 $parentTableFieldName[$jfield].')';

			//$groupBy[$jfield] = $existingTableFieldName[$jfield];
		}


		if ((isset($this->field['QUL'])!=1) && (isset($this->field['YEAROFPASSING'])==1)){
			$jfield = 'YEAROFPASSING';
			$joinQ = '('.$joinQ. ' LEFT JOIN '.$parentTableName[$jfield].
					 ' ON '.$existingTableFieldName[$jfield].' = '.
					 $parentTableFieldName[$jfield].')';

			//$groupBy[$jfield] = $existingTableFieldName[$jfield];
		}

		if(isset($this->field['JOBTITLE'])==1){

			$jfield = 'JOBTITLE';
			$joinQ = '('.$joinQ. ' LEFT JOIN '.$parentTableName[$jfield].
					 ' ON '.$existingTableFieldName[$jfield].' = '.
					 $parentTableFieldName[$jfield].')';

			//$groupBy[$jfield] = $existingTableFieldName[$jfield];
		}

		if(isset($this->field['PAYGRD'])){

			$jfield = 'PAYGRD';
			$joinQ = '('.$joinQ. ' LEFT JOIN '.$parentTableName[$jfield].
					 ' ON '.$existingTableFieldName[$jfield].' = '.
					 $parentTableFieldName[$jfield].')';

			//$groupBy[$jfield] = $existingTableFieldName[$jfield];
		} else if (isset($this->criteria['PAYGRD'])) {
			$jfield = 'JOBTITLE';
			$joinQ = '('.$joinQ. ' LEFT JOIN '.$parentTableName[$jfield].
					 ' ON '.$existingTableFieldName[$jfield].' = '.
					 $parentTableFieldName[$jfield].')';
			$jfield = 'PAYGRD';
			$joinQ = '('.$joinQ. ' LEFT JOIN '.$parentTableName[$jfield].
					 ' ON '.$existingTableFieldName[$jfield].' = '.
					 $parentTableFieldName[$jfield].')';

			//$groupBy[$jfield] = $existingTableFieldName[$jfield];
		}

		if ((isset($this->field['REPORTTO'])!=1) && (isset($this->field['REPORTINGMETHOD'])==1)) {
			$jfield = 'REPORTINGMETHOD';
			$joinQ = '('.$joinQ. ' LEFT JOIN '.$parentTableName[$jfield].
					 ' ON '.$existingTableFieldName[$jfield].' = '.
					 $parentTableFieldName[$jfield].')';

			//$groupBy[$jfield] = $existingTableFieldName[$jfield];
		}

		if(isset($this->field['CONTRACT'])==1){

			$jfield = 'CONTRACT';
			$joinQ = '('.$joinQ. ' LEFT JOIN '.$parentTableName[$jfield].
					 ' ON '.$existingTableFieldName[$jfield].' = '.
					 $parentTableFieldName[$jfield].')';

			//$groupBy[$jfield] = $existingTableFieldName[$jfield];
		}

		if(isset($this->field['WORKEXPERIENCE'])==1){

			$jfield = 'WORKEXPERIENCE';
			$joinQ = '('.$joinQ. ' LEFT JOIN '.$parentTableName[$jfield].
					 ' ON '.$existingTableFieldName[$jfield].' = '.
					 $parentTableFieldName[$jfield].')';

			//$groupBy[$jfield] = $existingTableFieldName[$jfield];
		}

		if(isset($this->field['REPORTTO'])==1){

			$jfield = 'REPORTTO';
			$joinQ = '(('.$joinQ.' LEFT JOIN ';
			$joinQ = $joinQ.$existingTableName[$jfield];
			$joinQ = $joinQ.' ON g.erep_sub_emp_number = a.emp_number)';
			$joinQ = $joinQ.' LEFT JOIN '.$parentTableName[$jfield];
			$joinQ = $joinQ.' ON '.$existingTableFieldName[$jfield].' = '.
					 $parentTableFieldName[$jfield].')';

			//$groupBy[$jfield] = $existingTableFieldName[$jfield];
		}

		if(isset($this->field['LANGUAGES'])==1){

			$jfield = 'LANGUAGES';
			$joinQ = '(('.$joinQ.' LEFT JOIN ';
			$joinQ = $joinQ.$existingTableName[$jfield];
			$joinQ = $joinQ.' ON 	m.emp_number = a.emp_number )';
			$joinQ = $joinQ.' LEFT JOIN '.$parentTableName[$jfield].
					 ' ON '.$existingTableFieldName[$jfield].' = '.
					 $parentTableFieldName[$jfield].')';
		} else if (isset($this->criteria['LANGUAGE'])) {

			$jfield = 'LANGUAGES';
			$joinQ = '('.$joinQ.' LEFT JOIN ';
			$joinQ = $joinQ.$existingTableName[$jfield];
			$joinQ = $joinQ.' ON m.emp_number = a.emp_number )';

			$groupBy[$jfield] = "m.lang_code, m.emp_number";
		}

		if(isset($this->field['SKILLS'])==1){

			$jfield = 'SKILLS';
			$joinQ = '(('.$joinQ.' LEFT JOIN ';
			$joinQ = $joinQ.$existingTableName[$jfield];
			$joinQ = $joinQ.' ON h.emp_number = a.emp_number )';
			$joinQ = $joinQ.' LEFT JOIN '.$parentTableName[$jfield].
					 ' ON '.$existingTableFieldName[$jfield].' = '.
					 $parentTableFieldName[$jfield].')';
		} else if (isset($this->criteria['SKILL'])) {

			$jfield = 'SKILLS';
			$joinQ = '('.$joinQ.' LEFT JOIN ';
			$joinQ = $joinQ.$existingTableName[$jfield];
			$joinQ = $joinQ.' ON h.emp_number = a.emp_number )';
		}

		if(isset($this->field['QUL'])==1){

			$jfield = 'QUL';
			$joinQ = '(('.$joinQ.' LEFT JOIN ';
			$joinQ = $joinQ.$existingTableName[$jfield];
			$joinQ = $joinQ.' ON c.emp_number = a.emp_number )';
			$joinQ = $joinQ.' LEFT JOIN '.$parentTableName[$jfield].
					 ' ON '.$existingTableFieldName[$jfield].' = '.
					 $parentTableFieldName[$jfield].')';
		}

		$SQL1 = $SQL1 .$joinQ.$tableStr;

			$countCriteriaVal = 0;
			if (isset($criteriaValue) && is_array($criteriaValue)) {
				$criteriaValue = array_values($criteriaValue);
				$countCriteriaVal = count($criteriaValue);
			}
			if (isset($criteriaField) && is_array($criteriaField)) {
				$criteriaField = array_values($criteriaField);
			}
			if (isset($criteriaComOper) && is_array($criteriaComOper)) {
				$criteriaComOper = array_values($criteriaComOper);
			}

			if ($countCriteriaVal > 0) {
				$SQL1 = $SQL1 . ' WHERE ' ;
			}

			for ($i=0;$i < $countCriteriaVal; $i++){
				if ($i == ($countCriteriaVal - 1))
					$SQL1 = $SQL1 . $criteriaField[$i] . ' ' . $criteriaComOper[$i] . ' ' . $criteriaValue[$i] . ' ';
				else
					$SQL1 = $SQL1 . $criteriaField[$i] . ' ' . $criteriaComOper[$i] . ' ' . $criteriaValue[$i] . ' AND ';
			}

			if (is_array($groupBy)) {
				$SQL1 .= " GROUP BY ".join($groupBy, ", ");
			}

			//echo strtolower($SQL1);
			return strtolower($SQL1);
		}

		function  reportDisplay($repDetails, $empNoField=false) {
			$employee = array();

			if (is_array($repDetails)) {
				$employee = current($repDetails);
			}

			$columns = count($employee);
			$rows	 = count($repDetails);

			require_once(ROOT_PATH . '/templates/report/report.php');
		}
}