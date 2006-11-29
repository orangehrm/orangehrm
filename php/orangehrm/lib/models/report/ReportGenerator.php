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
 		
 		if(isset($this->criteria['EMPNO'])){
			$criteriaValue['EMPNO'] = '\'' .$this->criteria['EMPNO'] . '\'';
			$criteriaField['EMPNO'] = 'a.EMP_NUMBER';
			$criteriaTable['EMPNO'] = 'HS_HR_EMPLOYEE a';
			$criteriaComOper['EMPNO'] = "=";
 		}
 		
 		if(isset($this->criteria['PAYGRD'])){
 			 			
 			$criteriaValue['PAYGRD'] = '\'' . $this->criteria['PAYGRD'] . '\'';
			$criteriaField['PAYGRD'] = 'a.SAL_GRD_CODE';
			$criteriaTable['PAYGRD'] = 'HS_HR_EMPLOYEE a';
			$criteriaComOper['PAYGRD'] = "=";
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
 			
 		
 		// Select fields
 		if(isset($this->field['EMPNO'])==1){	
 			
 			$headingName['EMPNO'] = 'Employee No';
 			$parentTableFieldName['EMPNO']  = 'LPAD(a.`EMP_NUMBER`, '.$this->employeeIdLength.', 0)';
 			$parentTableName['EMPNO']	    = 'HS_HR_EMPLOYEE a';
 			$existingTableFieldName['EMPNO']= 'a.EMP_NUMBER';
 			$existingTableName['EMPNO']		= 'HS_HR_EMPLOYEE a';
 			$parentTableDescription['EMPNO']= 'LPAD(a.`EMP_NUMBER`, '.$this->employeeIdLength.', 0)';
 		}
 		
 		if(isset($this->field['EMPLASTNAME'])==1){	
 			 			
 			$headingName['EMPLASTNAME'] = 'Employee Last Name';
 			$parentTableFieldName['EMPLASTNAME']  = 'a.EMP_LASTNAME';
 			$parentTableName['EMPLASTNAME']	     = 'HS_HR_EMPLOYEE a';
 			$existingTableFieldName['EMPLASTNAME']= 'a.EMP_LASTNAME';
 			$existingTableName['EMPLASTNAME']	 = 'HS_HR_EMPLOYEE a';
 			$parentTableDescription['EMPLASTNAME']= 'a.EMP_LASTNAME';
 		}
 		
 		if(isset($this->field['ADDRESS1'])==1){	
 			 			
 			$headingName['ADDRESS1'] = 'Address';
 			$parentTableFieldName['ADDRESS1']    = 'a.EMP_STREET1';
 			$parentTableName['ADDRESS1']	     = 'HS_HR_EMPLOYEE a';
 			$existingTableFieldName['ADDRESS1']  = 'a.EMP_STREET1';
 			$existingTableName['ADDRESS1']	     = 'HS_HR_EMPLOYEE a';
 			$parentTableDescription['ADDRESS1']  = "CONCAT(a.EMP_STREET1, ' ', a.EMP_STREET2, ' ', a.CITY_CODE, ' ', a.PROVIN_CODE, ' ', a.COUN_CODE, ' ', a.EMP_ZIPCODE)";
 		}   
 		
 		if(isset($this->field['ADDRESS2'])==1){	
 			 			
 			$parentTableFieldName['ADDRESS2']    = 'a.EMP_STREET2';
 			$parentTableName['ADDRESS2']	     = 'HS_HR_EMPLOYEE a';
 			$existingTableFieldName['ADDRESS2']  = 'a.EMP_STREET2';
 			$existingTableName['ADDRESS2']	     = 'HS_HR_EMPLOYEE a';
 			$parentTableDescription['ADDRESS2']  = 'a.EMP_STREET2';
 		}   
 		 		
 		
 		if(isset($this->field['TELENO'])==1){	
 			 			
 			$headingName['TELENO'] = 'Telephone';
 			$parentTableFieldName['TELENO']    = 'a.EMP_HM_TELEPHONE';
 			$parentTableName['TELENO']	       = 'HS_HR_EMPLOYEE a';
 			$existingTableFieldName['TELENO']  = 'a.EMP_HM_TELEPHONE';
 			$existingTableName['TELENO']	   = 'HS_HR_EMPLOYEE a';
 			$parentTableDescription['TELENO']  = 'a.EMP_HM_TELEPHONE';
 		}   
 		
 		if(isset($this->field['MOBILE'])==1){	
 			 			
 			$headingName['MOBILE'] = 'Mobile';
 			$parentTableFieldName['MOBILE']    = 'a.EMP_MOBILE';
 			$parentTableName['MOBILE']	       = 'HS_HR_EMPLOYEE a';
 			$existingTableFieldName['MOBILE']  = 'a.EMP_MOBILE';
 			$existingTableName['MOBILE']	   = 'HS_HR_EMPLOYEE a';
 			$parentTableDescription['MOBILE']  = 'a.EMP_MOBILE';
 		}   
 		
 		if(isset($this->field['AGE'])==1) {	

 			$headingName['AGE'] = 'Date of Birth';
 			$parentTableFieldName['AGE']  	= 'a.EMP_BIRTHDAY';
 			$parentTableName['AGE']	    	= 'HS_HR_EMPLOYEE a';
 			$existingTableFieldName['AGE']	= 'a.EMP_BIRTHDAY';
 			$existingTableName['AGE']		= 'HS_HR_EMPLOYEE a';
 			$parentTableDescription['AGE']  = "DATE_FORMAT(a.EMP_BIRTHDAY, CONCAT(YEAR(a.EMP_BIRTHDAY), '-%m-%d'))"; 
 		}
 		
 		if(isset($this->field['PAYGRD'])==1){	
 			
 			$headingName['PAYGRD'] = 'Salary Grade';
 			$parentTableFieldName['PAYGRD']  = 'b.SAL_GRD_CODE';
 			$parentTableName['PAYGRD']	     = 'HS_PR_SALARY_GRADE b';
 			$existingTableFieldName['PAYGRD']= 'a.SAL_GRD_CODE';
 			$existingTableName['PAYGRD']	 = 'HS_HR_EMPLOYEE a';
 			$parentTableDescription['PAYGRD']  = 'b.SAL_GRD_NAME';
 		}
 		
 		if(isset($this->field['EMPSTATUS'])==1){	
 			 			 			
 			$headingName['EMPSTATUS'] = 'Employee Status';
 			$parentTableFieldName['EMPSTATUS']  = 'd.ESTAT_CODE';
 			$parentTableName['EMPSTATUS']	      = 'HS_HR_EMPSTAT d';
 			$existingTableFieldName['EMPSTATUS']= 'a.EMP_STATUS';
 			$existingTableName['EMPSTATUS']	  = 'HS_HR_EMPLOYEE a';
 			$parentTableDescription['EMPSTATUS']  = 'd.ESTAT_NAME';
 		}
 		
 		if(isset($this->field['SERPIR'])==1){	
 			 			
 			$headingName['SERPIR'] = 'Joined Date';
 			$parentTableFieldName['SERPIR']  	= 'a.JOINED_DATE';
 			$parentTableName['SERPIR']	    	= 'HS_HR_EMPLOYEE a';
 			$existingTableFieldName['SERPIR']	= 'a.JOINED_DATE';
 			$existingTableName['SERPIR']		= 'HS_HR_EMPLOYEE a';
 			$parentTableDescription['SERPIR']  	= "DATE_FORMAT(a.JOINED_DATE, CONCAT(YEAR(a.JOINED_DATE), '-%m-%d'))";
 		}
 		
 		if(isset($this->field['QUL'])==1){	
 			 			
 			$headingName['QUL'] 			= 'Qualifications';
 			$parentTableFieldName['QUL']  	= 'e.EDU_CODE';
 			$parentTableName['QUL']	    	= 'HS_HR_EDUCATION e';
 			$existingTableFieldName['QUL']	= 'c.EDU_CODE';
 			$existingTableName['QUL']		= 'HS_HR_EMP_EDUCATION c';
 			$parentTableDescription['QUL']  = 'CONCAT(e.EDU_DEG, \', \', e.EDU_UNI)';		
 		}
 		
 		if(isset($this->field['JOBTITLE'])==1){	
 			 			
 			$headingName['JOBTITLE'] 				= 'Job Title';
 			$parentTableFieldName['JOBTITLE']  	= 'f.JOBTIT_CODE';
 			$parentTableName['JOBTITLE']	    	= 'HS_HR_JOB_TITLE f';
 			$existingTableFieldName['JOBTITLE']	= 'a.JOB_TITLE_CODE';
 			$existingTableName['JOBTITLE']		= 'HS_HR_EMPLOYEE a';
 			$parentTableDescription['JOBTITLE']  	= 'f.JOBTIT_NAME';	
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
		
					
		if(isset($this->field['EMPSTATUS'])==1){
			
			$jfield = 'EMPSTATUS';
			$joinQ = '('.$joinQ. ' LEFT JOIN '.$parentTableName[$jfield].
					 ' ON '.$existingTableFieldName[$jfield].' = '.
					 $parentTableFieldName[$jfield].')';						
								
		}
			
		if(isset($this->field['QUL'])==1){
			
			$jfield = 'QUL';
			$joinQ = '('.$joinQ.' LEFT JOIN ';
			$joinQ = $joinQ.'('.$existingTableName[$jfield]. ' LEFT JOIN '.$parentTableName[$jfield].
					 ' ON '.$existingTableFieldName[$jfield].' = '.
					 $parentTableFieldName[$jfield].')';
			$joinQ = $joinQ.' ON c.emp_number = a.emp_number )';
			
								
		}
			
		if(isset($this->field['JOBTITLE'])==1){
			
			$jfield = 'JOBTITLE';
			$joinQ = '('.$joinQ. ' LEFT JOIN '.$parentTableName[$jfield].
					 ' ON '.$existingTableFieldName[$jfield].' = '.
					 $parentTableFieldName[$jfield].')';						
								
		}
		
		if(isset($this->field['PAYGRD'])==1){
			
			$jfield = 'PAYGRD';
			$joinQ = '('.$joinQ. ' LEFT JOIN '.$parentTableName[$jfield].
					 ' ON '.$existingTableFieldName[$jfield].' = '.
					 $parentTableFieldName[$jfield].')';						
								
		}
		
		$SQL1 = $SQL1 .$joinQ;
				
		$SQL1 = $SQL1 . ' WHERE ' ;
				
				/*$parent = array_values($parentTableFieldName);
				$exsist = array_values($existingTableFieldName);
				
				for ($i=0;$i<count($parent); $i++) 
				 	if($parent[$i] != $exsist[$i])
				 		$SQL1 = $SQL1 . $parent[$i] . ' = '. $exsist[$i] . ' AND ';*/
				 		
				$criteriaValue = array_values($criteriaValue);
				$criteriaField = array_values($criteriaField);
				$criteriaComOper = array_values($criteriaComOper);
				 
				$countCriteriaVal = count($criteriaValue);
				
				for ($i=0;$i < count($criteriaValue); $i++){ 
					if ($i == ($countCriteriaVal - 1))  
						$SQL1 = $SQL1 . $criteriaField[$i] . ' ' . $criteriaComOper[$i] . ' ' . $criteriaValue[$i] . ' ';		
					else 
						$SQL1 = $SQL1 . $criteriaField[$i] . ' ' . $criteriaComOper[$i] . ' ' . $criteriaValue[$i] . ' AND ';		
				}


			return strtolower($SQL1);
		}
		
		function  reportDisplay($repDetails) {
			
			$columns = count($repDetails[0]);
			$rows	 = count($repDetails);
			
			require_once(ROOT_PATH . '/templates/report/report.php');
		}
}