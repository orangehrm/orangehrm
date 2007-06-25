<?php
/*
// OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
// all the essential functionalities required for any enterprise.
// Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com

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

require_once ROOT_PATH . '/lib/confs/Conf.php';
require_once ROOT_PATH . '/lib/confs/sysConf.php';
require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';
require_once ROOT_PATH . '/lib/dao/SQLQBuilder.php';
require_once ROOT_PATH . '/lib/common/CommonFunctions.php';
require_once ROOT_PATH . '/lib/logs/LogFileWriter.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpRepTo.php';

class EmpInfo {

	var $tableName = 'HS_HR_EMPLOYEE';
	var $empId;
	var $employeeId;
	var $empLastName;
	var $empFirstName;
	var $empNickName;
	var $empMiddleName;
	//personal
	var $empDOB;
	var $empGender;
	var $empNation;
	var $empMarital;
	var $empSINNo;
	var $empSSNNo;
	var $empOthID;
	var $empDriLicNo;
	var $empDriLicExpDat;
	var $empMilitary;
	var $smoker;
	var $ethnicRace;

	//job info
	var $empJobTitle;
	var $empStatus;
	var $empEEOCat;
	var $empLocation;
	var $empjoindat;

	//permanent contacts
	var $empStreet1;
	var $empStreet2;
	var $empCity;
	var $empProvince;
	var $empCountry;
	var $empZipCode;
	var $empHomeTelephone;
	var $empMobile;
	var $empWorkTelephone;
	var $empWorkEmail;
	var $empOtherEmail;

	var $employeeIdLength;

/*	//immigration

	*/

	var $arrayDispList;
	var $singleField;

	function EmpInfo() {
		$sysConfObj = new sysConf();

		$this->employeeIdLength = $sysConfObj->getEmployeeIdLength();
	}

	function setEmpId($empId) {

	$this->empId=$empId;
	}

	function setEmployeeId($employeeId) {
	$this->employeeId=$employeeId;
	}

	function setEmpLastName($empLastName) {
	$this->empLastName=$empLastName;
	}

	function setEmpFirstName($empFirstName) {
	$this->empFirstName=$empFirstName;
	}

	function setEmpNickName($empNickName) {
	$this->empNickName=$empNickName;
	}

	function setEmpMiddleName($empMiddleName) {
	$this->empMiddleName=$empMiddleName;
	}

	//personal
	function setEmpSINNo($empSINNo) {
	$this->empSINNo = $empSINNo;
	}

	function setEmpSSNNo($empSSNNo) {
	$this->empSSNNo  =$empSSNNo;
	}

	function setEmpDOB($empDOB) {
	$this->empDOB=$empDOB;
	}

	function setEmpOthID($empOthID) {
	$this->empOthID=$empOthID;
	}

	function setEmpGender($empGender) {
	$this->empGender=$empGender;
	}

	function setEmpDriLicNo($empDriLicNo) {
	$this->empDriLicNo=$empDriLicNo;
	}

	function setEmpNation($empNation) {
	$this->empNation=$empNation;
	}

	function setEmpDriLicExpDat($empDriLicExpDat) {
	$this->empDriLicExpDat=$empDriLicExpDat;
	}

	function setEmpMarital($empMarital) {
	$this->empMarital=$empMarital;
	}

	function setEmpsmoker($smoker) {
	$this->smoker=$smoker;
	}

	function setEmpMilitary($empMilitary) {
	$this->empMilitary=$empMilitary;
	}

	function setEmpEthnicRace($ethnicRace) {
	$this->ethnicRace=$ethnicRace;
	}

	//job info
	//job stat
	function setEmpJobTitle($empJobTitle) {
	$this->empJobTitle=$empJobTitle;
	}

	function setEmpStatus($empStatus) {
	$this->empStatus=$empStatus;
	}

	function setEmpEEOCat($empEEOCat) {
	$this->empEEOCat = $empEEOCat;
	}

	function setEmpLocation($empLocation) {
	$this->empLocation=$empLocation;
	}

	function setEmpJoinedDate($empjoindat) {
	$this->empjoindat=$empjoindat;
	}


	//permanent contacts
	function setEmpStreet1($empStreet1) {
	$this->empStreet1=$empStreet1;
	}

	function setEmpStreet2($empStreet2) {
	$this->empStreet2=$empStreet2;
	}

	function setEmpCity($empCity) {
	$this->empCity=$empCity;
	}

	function setEmpProvince($empProvince) {
	$this->empProvince=$empProvince;
	}

	function setEmpCountry($empCountry) {
	$this->empCountry=$empCountry;
	}

	function setEmpZipCode($empZipCode) {
	$this->empZipCode=$empZipCode;
	}

	function setEmpHomeTelephone($empHomeTelephone) {
	$this->empHomeTelephone=$empHomeTelephone;
	}

	function setEmpMobile($empMobile) {
	$this->empMobile=$empMobile;
	}

	function setEmpWorkTelephone($empWorkTelephone) {
	$this->empWorkTelephone=$empWorkTelephone;
	}

	function setEmpWorkEmail($empWorkEmail) {
	$this->empWorkEmail=$empWorkEmail;
	}

	function setEmpOtherEmail($empOtherEmail) {
	$this->empOtherEmail=$empOtherEmail;
	}
//////////////
	function getEmpId() {
	return $this->empId;

	}

	function getEmployeeId() {
	return $this->employeeId;
	}

	function getEmpLastName() {
	return $this->empLastName;
	}

	function getEmpFirstName() {
	return $this->empFirstName;
	}

	function getEmpNickName() {
	return $this->empNickName;
	}

	function getEmpMiddleName() {
	return $this->empMiddleName;
	}

	//personal
	function getEmpNICNo() {
	return $this->empNICNo;
	}

	function getEmpMarital() {
	return $this->empMarital;
	}

	function getEmpDOB() {
	return $this->empDOB;
	}

	function getEmpSINNo() {
	return $this->empSINNo;
	}

	function getEmpGender() {
	return $this->empGender;
	}

	function getEmpSSNNo() {
	return $this->empSSNNo;
	}

	function getEmpNation() {
	return $this->empNation;
	}

	function getEmpOthID() {
	return $this->empOthID;
	}

	function getEmpDriLicNo() {
	return $this->empDriLicNo;
	}

	function getEmpDriLicExpDat() {
	return $this->empDriLicExpDat;
	}

	function getEmpMilitary() {
	return $this->empMilitary;
	}

	function getEmpsmoker() {
	return $this->smoker;
	}

	function getEmpEthnicRace() {
	return $this->ethnicRace;
	}
	//job info

	function getEmpJobTitle() {
	return $this->empJobTitle;
	}

	function getEmpStatus() {
	return $this->empStatus;
	}

	function getEmpEEOCat() {
	return $this->empEEOCat;
	}

	function getEmpLocation() {
	return $this->empLocation;
	}

	function getEmpJoinedDate() {
	return $this->empjoindat;
	}

	//permanent contacts
	function getEmpStreet1() {
	return $this->empStreet1;
	}

	function getEmpStreet2() {
	return $this->empStreet2;
	}

	function getEmpCity() {
	return $this->empCity;
	}

	function getEmpProvince() {
	return $this->empProvince;
	}

	function getEmpCountry() {
	return $this->empCountry;
	}

	function getEmpZipCode() {
	return $this->empZipCode;
	}

	function getEmpHomeTelephone() {
	return $this->empHomeTelephone;
	}

	function getEmpMobile() {
	return $this->empMobile;
	}

	function getEmpWorkTelephone() {
	return $this->empWorkTelephone;
	}

	function getEmpWorkEmail() {
	return $this->empWorkEmail;
	}

	function getEmpOtherEmail() {
	return $this->empOtherEmail;
	}

/////////////

	function getListofEmployee($pageNO=0,$schStr='',$mode=-1, $sortField=4, $sortOrder='ASC', $supervisorId = null) {

		//$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = "a.`employee_id`";
		$arrFieldList[1] = "a.`emp_firstname`";
		$arrFieldList[2] = "a.`emp_lastname`";
		$arrFieldList[3] = "a.`emp_middle_name`";
		$arrFieldList[4] = "LPAD(a.`emp_number`, ".$this->employeeIdLength.", 0)";
		$arrFieldList[5] = "a.`work_station`";
		$arrFieldList[6] = "c.`jobtit_name`";
		$arrFieldList[7] = "CONCAT(a.`emp_firstname`, ' ', a.`emp_middle_name`, ' ', a.`emp_lastname`)";
		$arrFieldList[8] = "d.`title`";
		$arrFieldList[9] = "e.`estat_name`";

		/* First show direct supervisors then indirect supervisors */
		$arrFieldList[10] = "GROUP_CONCAT(g.`emp_firstname`, ' ', g.`emp_lastname` ORDER BY erep_reporting_mode ) AS Supervisor";

		$sql_builder = new SQLQBuilder();

		$arrTables[0] = "`hs_hr_employee` a";
		$arrTables[1] = "`hs_hr_job_title` c";
		$arrTables[2] = "`hs_hr_compstructtree` d";
		$arrTables[3] = "`hs_hr_empstat` e";
		$arrTables[4] = "`hs_hr_emp_reportto` f";
		$arrTables[5] = "`hs_hr_employee` g";

		$joinConditions[1] = "a.`job_title_code` = c.`jobtit_code`";
		$joinConditions[2] = "a.`work_station` = d.`id`";
		$joinConditions[3] = "a.`emp_status` = e.`estat_code`";
		$joinConditions[4] = "a.`emp_number` = f.`erep_sub_emp_number`";
		$joinConditions[5] = "f.`erep_sup_emp_number` = g.`emp_number`";

		$selectConditions = null;
		$filteredSearch = mysql_real_escape_string($schStr);

		/*
		 * Skip setting select conditions if no search string set, no search mode set
		 * or if searching by supservisor (mode = 8)
		 *
		 * If searching by supervisor, the conditions are set in the outer SELECT statement.
		 */
		if (($mode != -1) && ($mode != 8) && !empty($schStr)) {

            if ($mode == 7) {

                // Special handling for search by subdivision.
                // Get list of workstations with matches in the title or matches higher in the hierachy
                $subdivisionIds = $this->_getMatchingSubdivisionIds($schStr);

                // Create select condition for employees with workstation set to any of the
                // subdivisions
                if (isset($subdivisionIds) && !empty($subdivisionIds)) {
                    $selectConditions[] = "a.`work_station` IN (" . $subdivisionIds . ") ";
                } else {

                    // No subdivisions matches found.
                    return '';
                }
            } else {

                $selectConditions[] = "{$arrFieldList[$mode]} LIKE '" . $filteredSearch . "%'";
            }
		}


		/**
		 * Check whether searching for the employement status,
		 * if not add this to not to show 'Terminated' employees
		 */
		if ($mode != 9) {
			$selectConditions[] = "(a.`emp_status` != 'EST000' OR a.`emp_status` IS NULL)";
		}

		/* If supervisor ID is set, filter by that supervisor */
		if (!empty($supervisorId)) {
			$selectConditions[] = "(f.`erep_sup_emp_number` = '$supervisorId')";
		}

		$sysConst = new sysConf();

		$limit = null;

		if ($pageNO > 0) {
			$pageNO--;
			$pageNO *= $sysConst->itemsPerPage;

			$limit = "{$pageNO}, {$sysConst->itemsPerPage}";
		}

		/* We need to group to get the concatenated list of supervisor names */
		$groupBy = "a.`emp_number` ";

		/* Don't order if searching by supervisor. The order by has
		 * to be added to the outer SELECT.
		 */
		if ($sortField == 10) {

			$selectOrder = null;
			$selectOrderBy = null;
		} else {

			$selectOrder = $sortOrder;
			$selectOrderBy = $arrFieldList[$sortField];
		}

		$sqlQString = $sql_builder->selectFromMultipleTable($arrFieldList, $arrTables, $joinConditions, $selectConditions, null, $selectOrderBy, $selectOrder, null, $groupBy);

		/* Add the outer SELECT */
		$sqlQString = "SELECT * FROM ( $sqlQString ) AS subsel ";

		/* If searching by supervisor add the condition now */
		if ($mode == 8 && !empty($schStr)) {
			$sqlQString .= " WHERE Supervisor LIKE '%${filteredSearch}%' ";
		}

		/* If sorting by supervisor add the order by condition */
		if ($sortField == 10) {
			$sqlQString .= " ORDER BY Supervisor $sortOrder";
		}

		/* Add the search limit */
		if (isset($limit)) {
			$sqlQString .= " LIMIT $limit";
		}

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$i=0;

		 while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {

	    	$arrayDispList[$i][0] = $line[0];
	    	$arrayDispList[$i][1] = $line[1].' '.$line[3].' '.$line[2];
	    	$arrayDispList[$i][2] = $line[4];
	    	$arrayDispList[$i][3] = $line[5];
	    	$arrayDispList[$i][4] = $line[6];
	    	$arrayDispList[$i][6] = $line[9];
	    	$arrayDispList[$i][5] = $line[10];

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

		/*$arrFieldList[0] = "a.`emp_number`";
		$arrFieldList[1] = "a.`emp_firstname`";
		$arrFieldList[2] = "a.`emp_lastname`";
		$arrFieldList[3] = "a.`emp_middle_name`";*/

		$arrFieldList[0] = "a.`employee_id`";
		$arrFieldList[1] = "a.`emp_firstname`";
		$arrFieldList[2] = "a.`emp_lastname`";
		$arrFieldList[3] = "a.`emp_middle_name`";
		$arrFieldList[4] = "LPAD(a.`emp_number`, ".$this->employeeIdLength.", 0)";
		$arrFieldList[5] = "a.`work_station`";
		$arrFieldList[6] = "c.`jobtit_name`";
		$arrFieldList[7] = "CONCAT(a.`emp_firstname`, ' ', a.`emp_middle_name`, ' ', a.`emp_lastname`)";
		$arrFieldList[8] = "d.`title`";
		$arrFieldList[9] = "e.`estat_name`";

		$arrTables[0] = "`hs_hr_employee` a";
		$arrTables[1] = "`hs_hr_job_title` c";
		$arrTables[2] = "`hs_hr_compstructtree` d";
		$arrTables[3] = "`hs_hr_empstat` e";

		$joinConditions[1] = "a.`job_title_code` = c.`jobtit_code`";
		$joinConditions[2] = "a.`work_station` = d.`id`";
		$joinConditions[3] = "a.`emp_status` = e.`estat_code`";

		$selectConditions = null;

        if (($mode != -1) && !empty($schStr)) {

            $filteredSearch = mysql_real_escape_string($schStr);

            if( $mode == 6 ){

                // Need to join extra tables if searching by job title
                $arrTables[1] = "`hs_hr_job_title` c";
                $joinConditions[1] = "a.`job_title_code` = c.`jobtit_code`";
                $selectConditions[] = "c.`jobtit_name` LIKE '" . $filteredSearch . "%'";
            } else if( $mode == 7 ){

                // search by subdivision
                // Get list of workstations with matches in the title or matches higher in the hierachy
                $subdivisionIds = $this->_getMatchingSubdivisionIds($filteredSearch);

                // Create select condition for employees with workstation set to any of the subdivisions
                if (isset($subdivisionIds) && !empty($subdivisionIds)) {
                    $selectConditions[] = "a.`work_station` IN (" . $subdivisionIds . ") ";
                } else {
                    // no matches. Return 0
                    return 0;
                }

            } else if ($mode == 8) {

                // search by supervisor
                $empNumbers = $this->_getEmpIdsWithMatchingSupervisor($filteredSearch);

                if (isset($empNumbers) && !empty($empNumbers)) {
                    $selectConditions[] = "a.`emp_number` IN (" . $empNumbers . ") ";
                } else {

                    // No subordinates found with with supervisor matching search string.
                    return 0;
                }

            } else {
                $selectConditions[] = "{$arrFieldList[$mode]} LIKE '" . $filteredSearch . "%'";
            }

        }

		$sql_builder = new SQLQBuilder();
		$sqlQString = $sql_builder->countFromMultipleTables($arrTables, $joinConditions, $selectConditions);
		$dbConnection = new DMLFunctions();
		$result = $dbConnection -> executeQuery($sqlQString);

		$resultSet = mysql_fetch_array($result, MYSQL_NUM);
        $count = $resultSet[0];

        return $count;
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

		return str_pad(((int) $this->singleField)+1, $this->employeeIdLength, "0", STR_PAD_LEFT);
		}

	}

	function getEmployeeIdLength() {
		$confObj = new Conf();

		if (isset($confObj->maxEmp)) {
			return strlen($confObj->maxEmp);
		}

		return 6;
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

		return $message2;
	}

	function filterEmpMain($getID) {

		$this->getID = $getID;
		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';
		$arrFieldList[2] = 'EMP_FIRSTNAME';
		$arrFieldList[3] = 'EMP_MIDDLE_NAME';
		$arrFieldList[4] = 'EMP_NICK_NAME';
		$arrFieldList[5] = 'EMPLOYEE_ID';

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

	    	$i++;

	     }

	     if (isset($arrayDispList)) {

			return $arrayDispList;

		} else {

			$arrayDispList = '';
			return $arrayDispList;

		}

	}


	function addEmpMain() {

		$arrRecordsList[0] = "'". $this->getEmpId() . "'";
		$arrRecordsList[1] = "'". $this->getEmpLastName() . "'";
		$arrRecordsList[2] = "'". $this->getEmpFirstName() . "'";
		$arrRecordsList[3] = "'". $this->getEmpNickName() . "'";
		$arrRecordsList[4] = "'". $this->getEmpMiddleName() . "'";
		$arrRecordsList[5] = "'". $this->getEmployeeId() . "'";

		//personal
/*		$arrRecordsList[5] = "'". $this->getEmpsmoker() . "'";
		$arrRecordsList[6] = "'". $this->getEmpEthnicRace() . "'";
		$arrRecordsList[7] = "'". $this->getEmpDOB() . "'";
		$arrRecordsList[8] =($this->getEmpNation()=='0') ? 'null' : "'". $this->getEmpNation() . "'";
		$arrRecordsList[9] = "'". $this->getEmpGender() . "'";
		$arrRecordsList[10] = "'". $this->getEmpMarital() . "'";
		$arrRecordsList[11] = "'". $this->getEmpSSNNo() . "'";
		$arrRecordsList[12] = "'". $this->getEmpSINNo() . "'";
		$arrRecordsList[13] = "'". $this->getEmpOthID() . "'";
		$arrRecordsList[14] = "'". $this->getEmpDriLicNo() . "'";
		$arrRecordsList[15] = "'". $this->empDriLicExpDat . "'";
		$arrRecordsList[16] = "'". $this->getEmpMilitary() . "'";

		//job info
		$arrRecordsList[17] = "'". $this->getEmpStatus() . "'";
		$arrRecordsList[18] = ($this->getEmpJobTitle()=='0') ? 'null' : "'". $this->getEmpJobTitle() . "'";
		$arrRecordsList[19] = "'". $this->getEmpEEOCat() . "'";
		$arrRecordsList[20] = ($this->getEmpLocation()=='0') ? 'null' : "'". $this->getEmpLocation() . "'";
		$arrRecordsList[32] = "'". $this->getEmpJoinedDate() . "'";

		//contact
		$arrRecordsList[21] = "'". $this->getEmpStreet1() . "'";
		$arrRecordsList[22] = "'". $this->getEmpStreet2() . "'";
		$arrRecordsList[23] = "'". $this->getEmpCity() . "'";
		$arrRecordsList[24] = "'". $this->getEmpCountry()  . "'";
		$arrRecordsList[25] = "'". $this->getEmpProvince()  . "'";
		$arrRecordsList[26] = "'". $this->getEmpZipCode() . "'";
		$arrRecordsList[27] = "'". $this->getEmpHomeTelephone() . "'";
		$arrRecordsList[28] = "'". $this->getEmpMobile() . "'";
		$arrRecordsList[29] = "'". $this->getEmpWorkTelephone() . "'";
		$arrRecordsList[30] = "'". $this->getEmpWorkEmail() . "'";
		$arrRecordsList[31] = "'". $this->getEmpOtherEmail() . "'";
*/

		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';
		$arrFieldList[2] = 'EMP_FIRSTNAME';
		$arrFieldList[3] = 'EMP_NICK_NAME';
		$arrFieldList[4] = 'EMP_MIDDLE_NAME';
		$arrFieldList[5] = 'EMPLOYEE_ID';

		//personal
/*		$arrFieldList[5]  = 'EMP_SMOKER';
		$arrFieldList[6]  = 'EMP_ETH_RACE';
		$arrFieldList[7]  = 'EMP_BIRTHDAY';
		$arrFieldList[8]  = 'NATION_CODE';
		$arrFieldList[9]  = 'EMP_GENDER';
		$arrFieldList[10] = 'EMP_MARITAL_STATUS';
		$arrFieldList[11] = 'EMP_SSN_NUM';
		$arrFieldList[12] = 'EMP_SIN_NUM';
		$arrFieldList[13] = 'EMP_OTHER_ID';
		$arrFieldList[14] = 'EMP_DRI_LICE_NUM';
		$arrFieldList[15] = 'EMP_DRI_LICE_EXP_DATE';
		$arrFieldList[16] = 'EMP_MILITARY_SERVICE';

		//job info
		$arrFieldList[17] = 'EMP_STATUS';
		$arrFieldList[18] = 'JOB_TITLE_CODE';
		$arrFieldList[19] = 'EEO_CAT_CODE';
		$arrFieldList[20] = 'WORK_STATION';
		$arrFieldList[32] = 'JOINED_DATE';

		//contact
		$arrFieldList[21] = 'EMP_STREET1';
		$arrFieldList[22] = 'EMP_STREET2';
		$arrFieldList[23] = 'CITY_CODE';
		$arrFieldList[24] = 'COUN_CODE';
		$arrFieldList[25] = 'PROVIN_CODE';
		$arrFieldList[26] = 'EMP_ZIPCODE';
		$arrFieldList[27] = 'EMP_HM_TELEPHONE';
		$arrFieldList[28] = 'EMP_MOBILE';
		$arrFieldList[29] = 'EMP_WORK_TELEPHONE';
		$arrFieldList[30] = 'EMP_WORK_EMAIL';
		$arrFieldList[31] = 'EMP_OTH_EMAIL';
*/

		$tableName = 'HS_HR_EMPLOYEE';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_insert = 'true';
		$sql_builder->arr_insert = $arrRecordsList;
		$sql_builder->arr_insertfield = $arrFieldList;


		$sqlQString = $sql_builder->addNewRecordFeature2();

	//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		 return $message2;
	}

	function updateEmpMain() {

		$arrRecordsList[0] = "'". $this->getEmpId() . "'";
		$arrRecordsList[1] = "'". $this->getEmpLastName() . "'";
		$arrRecordsList[2] = "'". $this->getEmpFirstName() . "'";
		$arrRecordsList[3] = "'". $this->getEmpNickName() . "'";
		$arrRecordsList[4] = "'". $this->getEmpMiddleName() . "'";


		$tableName = 'HS_HR_EMPLOYEE';

		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';
		$arrFieldList[2] = 'EMP_FIRSTNAME';
		$arrFieldList[3] = 'EMP_NICK_NAME';
		$arrFieldList[4] = 'EMP_MIDDLE_NAME';

		if ($this->getEmployeeId() != null) {
			$arrRecordsList[5] = "'". $this->getEmployeeId() . "'";
			$arrFieldList[5] = 'EMPLOYEE_ID';
		}

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

	function filterEmpPers($getID) {

		$this->getID = $getID;
		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';

		$arrFieldList[1]  = 'EMP_SMOKER';
		$arrFieldList[2]  = 'ETHNIC_RACE_CODE';
		$arrFieldList[3]  = 'EMP_BIRTHDAY';
		$arrFieldList[4]  = 'NATION_CODE';
		$arrFieldList[5]  = 'EMP_GENDER';
		$arrFieldList[6]  = 'EMP_MARITAL_STATUS';
		$arrFieldList[7]  = 'EMP_SSN_NUM';
		$arrFieldList[8]  = 'EMP_SIN_NUM';
		$arrFieldList[9]  = 'EMP_OTHER_ID';
		$arrFieldList[10] = 'EMP_DRI_LICE_NUM';
		$arrFieldList[11] = 'EMP_DRI_LICE_EXP_DATE';
		$arrFieldList[12] = 'EMP_MILITARY_SERVICE';

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

		$arrRecordsList[0]  = "'". $this->getEmpId() . "'";
		$arrRecordsList[1]  = "'". $this->getEmpsmoker() . "'";
		$arrRecordsList[2]  = ($this->getEmpEthnicRace()=='0') ? 'null' : "'". $this->getEmpEthnicRace() . "'";
		$arrRecordsList[3]  = "'". $this->getEmpDOB() . "'";
		$arrRecordsList[4]  =($this->getEmpNation()=='0') ? 'null' : "'". $this->getEmpNation() . "'";
		$arrRecordsList[5]  = "'". $this->getEmpGender() . "'";
		$arrRecordsList[6]  = "'". $this->getEmpMarital() . "'";
		$arrRecordsList[7]  = "'". $this->getEmpSSNNo() . "'";
		$arrRecordsList[8]  = "'". $this->getEmpSINNo() . "'";
		$arrRecordsList[9]  = "'". $this->getEmpOthID() . "'";
		$arrRecordsList[10] = "'". $this->getEmpDriLicNo() . "'";
		$arrRecordsList[11] = "'". $this->empDriLicExpDat . "'";
		$arrRecordsList[12] = "'". $this->getEmpMilitary() . "'";

		$tableName = 'HS_HR_EMPLOYEE';

		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1]  = 'EMP_SMOKER';
		$arrFieldList[2]  = 'ETHNIC_RACE_CODE';
		$arrFieldList[3]  = 'EMP_BIRTHDAY';
		$arrFieldList[4]  = 'NATION_CODE';
		$arrFieldList[5]  = 'EMP_GENDER';
		$arrFieldList[6]  = 'EMP_MARITAL_STATUS';
		$arrFieldList[7]  = 'EMP_SSN_NUM';
		$arrFieldList[8]  = 'EMP_SIN_NUM';
		$arrFieldList[9]  = 'EMP_OTHER_ID';
		$arrFieldList[10] = 'EMP_DRI_LICE_NUM';
		$arrFieldList[11] = 'EMP_DRI_LICE_EXP_DATE';
		$arrFieldList[12] = 'EMP_MILITARY_SERVICE';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_update = 'true';
		$sql_builder->arr_update = $arrFieldList;
		$sql_builder->arr_updateRecList = $arrRecordsList;

		$sqlQString = $sql_builder->addUpdateRecord1();
	//$logw = new LogFileWriter();
	//$logw->writeLogDB($sqlQString);
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		return $message2;
	}

	function filterEmpJobInfo($getID) {

		$this->getID = $getID;
		$tableName = 'HS_HR_EMPLOYEE A LEFT JOIN HS_HR_COMPSTRUCTTREE B ON A.WORK_STATION = B.ID';
		$arrFieldList[0] = 'a.EMP_NUMBER';
		$arrFieldList[1] = 'a.EMP_STATUS';
		$arrFieldList[2] = 'a.JOB_TITLE_CODE';
		$arrFieldList[3] = 'a.EEO_CAT_CODE';
		$arrFieldList[4] = 'b.TITLE';
		$arrFieldList[5] = 'a.JOINED_DATE';
		$arrFieldList[6] = 'a.WORK_STATION';

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
		$arrRecordsList[1] = $this->getEmpStatus() != '0' ? "'" . $this->getEmpStatus() . "'" : 'null';
		$arrRecordsList[2] = $this->getEmpJobTitle() != '0' ? "'" . $this->getEmpJobTitle() . "'" : 'null';
		$arrRecordsList[3] = $this->getEmpEEOCat() != '0' ? "'" . $this->getEmpEEOCat() . "'" : 'null';
		$arrRecordsList[4] = $this->getEmpLocation()!= '' ? "'" . $this->getEmpLocation() . "'" : 'null' ;
		$arrRecordsList[5] = "'". $this->getEmpJoinedDate() . "'";

		$tableName = 'HS_HR_EMPLOYEE';

		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_STATUS';
		$arrFieldList[2] = 'JOB_TITLE_CODE';
		$arrFieldList[3] = 'EEO_CAT_CODE';
		$arrFieldList[4] = 'WORK_STATION';
		$arrFieldList[5] = 'JOINED_DATE';

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

	/*function getSalGrdCodes() {

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
*/

	/*function filterEmpJobStat($getID) {

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
		$arrFieldList[7] = 'EMP_LASTNAME';
		$arrFieldList[8] = 'EMP_OTHER_NAMES';

		$arrFieldList[9] = 'WORK_STATION';
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
		$arrFieldList[1] = 'WORK_STATION';
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
*/
	function filterEmpContact($getID) {

		$this->getID = $getID;
		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';

		$arrFieldList[1] = 'EMP_STREET1';
		$arrFieldList[2] = 'EMP_STREET2';
		$arrFieldList[3] = 'CITY_CODE';
		$arrFieldList[4] = 'COUN_CODE';
		$arrFieldList[5] = 'PROVIN_CODE';
		$arrFieldList[6] = 'EMP_ZIPCODE';
		$arrFieldList[7] = 'EMP_HM_TELEPHONE';
		$arrFieldList[8] = 'EMP_MOBILE';
		$arrFieldList[9] = 'EMP_WORK_TELEPHONE';
		$arrFieldList[10] = 'EMP_WORK_EMAIL';
		$arrFieldList[11] = 'EMP_OTH_EMAIL';

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

	function updateEmpContact() {

		$arrRecordsList[0] = "'". $this->getEmpId() . "'";
		$arrRecordsList[1] = "'". $this->getEmpStreet1() . "'";
		$arrRecordsList[2] = "'". $this->getEmpStreet2() . "'";
		$arrRecordsList[3] = "'". $this->getEmpCity() . "'";
		$arrRecordsList[4] = "'". $this->getEmpCountry() . "'";
		$arrRecordsList[5] = "'". $this->getEmpProvince()  . "'";
		$arrRecordsList[6] = "'". $this->getEmpZipCode() . "'";
		$arrRecordsList[7] = "'". $this->getEmpHomeTelephone() . "'";
		$arrRecordsList[8] = "'". $this->getEmpMobile() . "'";
		$arrRecordsList[9] = "'". $this->getEmpWorkTelephone() . "'";
		$arrRecordsList[10] = "'". $this->getEmpWorkEmail() . "'";
		$arrRecordsList[11] = "'". $this->getEmpOtherEmail() . "'";

		$tableName = 'HS_HR_EMPLOYEE';

		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_STREET1';
		$arrFieldList[2] = 'EMP_STREET2';
		$arrFieldList[3] = 'CITY_CODE';
		$arrFieldList[4] = 'COUN_CODE';
		$arrFieldList[5] = 'PROVIN_CODE';
		$arrFieldList[6] = 'EMP_ZIPCODE';
		$arrFieldList[7] = 'EMP_HM_TELEPHONE';
		$arrFieldList[8] = 'EMP_MOBILE';
		$arrFieldList[9] = 'EMP_WORK_TELEPHONE';
		$arrFieldList[10] = 'EMP_WORK_EMAIL';
		$arrFieldList[11] = 'EMP_OTH_EMAIL';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_update = 'true';
		$sql_builder->arr_update = $arrFieldList;
		$sql_builder->arr_updateRecList = $arrRecordsList;

		$sqlQString = $sql_builder->addUpdateRecord1();
	//echo $sqlQString;
	//$log = new LogFileWriter();
	//$log->writeLogDB($sqlQString);
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		return $message2;
	}


/////////////
	/*function filterEmpTempRes($getID) {

		$this->getID = $getID;
		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_TITLE';
		$arrFieldList[2] = 'EMP_CALLING_NAME';
		$arrFieldList[3] = 'EMP_SURNAME';
		$arrFieldList[4] = 'EMP_MAIDEN_NAME';
		$arrFieldList[5] = 'EMP_MIDDLE_INI';
		$arrFieldList[6] = 'EMP_NAMES_BY_INI';
		$arrFieldList[7] = 'EMP_LASTNAME';
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
		$arrFieldList[7] = 'EMP_LASTNAME';
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
*/

	function getBasSalUnAssEmployee($pageNO,$schStr,$mode) {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_EMP_BASICSALARY';
		$sql_builder->field = 'EMP_NUMBER';
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultFilter($pageNO,$schStr,$mode);

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

	function countBasSalUnAssEmployee($schStr,$mode) {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_EMP_BASICSALARY';
		$sql_builder->flg_select = 'true';
		$sql_builder->field = 'EMP_NUMBER';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->countResultFilter($schStr,$mode);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$line = mysql_fetch_array($message2, MYSQL_NUM);

	    	return $line[0];
	}

	function getCashBenUnAssEmployee($pageNO,$schStr,$mode) {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_EMP_CASH_BENEFIT';
		$sql_builder->field = 'EMP_NUMBER';
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultFilter($pageNO,$schStr,$mode);

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

	function countCashBenUnAssEmployee($schStr,$mode) {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_EMP_CASH_BENEFIT';
		$sql_builder->flg_select = 'true';
		$sql_builder->field = 'EMP_NUMBER';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->countResultFilter($schStr,$mode);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$line = mysql_fetch_array($message2, MYSQL_NUM);

	    	return $line[0];
	}

	function getNonCashUnAssEmployee($pageNO,$schStr,$mode) {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_EMP_NONCASH_BENEFIT';
		$sql_builder->field = 'EMP_NUMBER';
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultFilter($pageNO,$schStr,$mode);

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

	function countNonCashUnAssEmployee($schStr,$mode) {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_EMP_NONCASH_BENEFIT';
		$sql_builder->flg_select = 'true';
		$sql_builder->field = 'EMP_NUMBER';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->countResultFilter($schStr,$mode);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$line = mysql_fetch_array($message2, MYSQL_NUM);

	    	return $line[0];
	}

	function getJobSpecUnAssEmployee($pageNO,$schStr,$mode) {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_EMP_JOBSPEC';
		$sql_builder->field = 'EMP_NUMBER';
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultFilter($pageNO,$schStr,$mode);

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

	function countJobSpecUnAssEmployee($schStr,$mode) {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_EMP_JOBSPEC';
		$sql_builder->flg_select = 'true';
		$sql_builder->field = 'EMP_NUMBER';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->countResultFilter($schStr,$mode);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$line = mysql_fetch_array($message2, MYSQL_NUM);

	    	return $line[0];
	}

	function getLangUnAssEmployee($pageNO,$schStr,$mode) {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_EMP_LANGUAGE';
		$sql_builder->field = 'EMP_NUMBER';
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultFilter($pageNO,$schStr,$mode);

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

	function countLangUnAssEmployee($schStr,$mode) {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_EMP_LANGUAGE';
		$sql_builder->flg_select = 'true';
		$sql_builder->field = 'EMP_NUMBER';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->countResultFilter($schStr,$mode);

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$line = mysql_fetch_array($message2, MYSQL_NUM);

	    	return $line[0];
	}

	function getSkillUnAssEmployee($pageNO,$schStr,$mode) {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_EMP_SKILL';
		$sql_builder->field = 'EMP_NUMBER';
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultFilter($pageNO,$schStr,$mode);

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

	function getLicensesUnAssEmployee($pageNO,$schStr,$mode) {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_EMP_LICENSES';
		$sql_builder->field = 'EMP_NUMBER';
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultFilter($pageNO,$schStr,$mode);

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

	function countSkillUnAssEmployee($schStr,$mode) {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_EMP_SKILL';
		$sql_builder->flg_select = 'true';
		$sql_builder->field = 'EMP_NUMBER';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->countResultFilter($schStr,$mode);

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$line = mysql_fetch_array($message2, MYSQL_NUM);

	    	return $line[0];
	}

	function countLicensesUnAssEmployee($schStr,$mode) {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_EMP_LICENSES';
		$sql_builder->flg_select = 'true';
		$sql_builder->field = 'EMP_NUMBER';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->countResultFilter($schStr,$mode);

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$line = mysql_fetch_array($message2, MYSQL_NUM);

	    	return $line[0];
	}

	function getQulUnAssEmployee($pageNO,$schStr,$mode) {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_EMP_QUALIFICATION';
		$sql_builder->field = 'EMP_NUMBER';
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultFilter($pageNO,$schStr,$mode);

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

	function countQulUnAssEmployee($schStr,$mode) {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_EMP_QUALIFICATION';
		$sql_builder->flg_select = 'true';
		$sql_builder->field = 'EMP_NUMBER';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->countResultFilter($schStr,$mode);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$line = mysql_fetch_array($message2, MYSQL_NUM);

	    	return $line[0];
	}

	function getUnAssEmployee($pageNO,$schStr,$mode) {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';
		$arrFieldList[2] = 'EMPLOYEE_ID';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_EMP_CONTRACT_EXTEND';
		$sql_builder->field = 'EMP_NUMBER';
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultFilter($pageNO,$schStr,$mode);

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

	function countUnAssEmployee($schStr,$mode) {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';
		$arrFieldList[2] = 'EMPLOYEE_ID';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_EMP_CONTRACT_EXTEND';
		$sql_builder->flg_select = 'true';
		$sql_builder->field = 'EMP_NUMBER';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->countResultFilter($schStr,$mode);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$line = mysql_fetch_array($message2, MYSQL_NUM);

	    	return $line[0];
	}

	function getUnAssEmployeeExCur($pageNO,$schStr,$mode) {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_EMP_EXTRA_ACTIVITY';
		$sql_builder->field = 'EMP_NUMBER';
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultFilter($pageNO,$schStr,$mode);

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

	function countUnAssEmployeeExCur($schStr,$mode) {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_EMP_EXTRA_ACTIVITY';
		$sql_builder->flg_select = 'true';
		$sql_builder->field = 'EMP_NUMBER';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->countResultFilter($schStr,$mode);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$line = mysql_fetch_array($message2, MYSQL_NUM);

	    	return $line[0];
	}

	function getUnAssEmployeeMemShip($pageNO,$schStr,$mode) {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_EMP_MEMBER_DETAIL';
		$sql_builder->field = 'EMP_NUMBER';
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultFilter($pageNO,$schStr,$mode);

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

	function countUnAssEmployeeMemShip($schStr,$mode) {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_EMP_MEMBER_DETAIL';
		$sql_builder->flg_select = 'true';
		$sql_builder->field = 'EMP_NUMBER';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->countResultFilter($schStr,$mode);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$line = mysql_fetch_array($message2, MYSQL_NUM);

	    	return $line[0];
	}

	function getEmpInfo() {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';
		$arrFieldList[2] = 'EMPLOYEE_ID';
		$arrFieldList[3] = 'EMP_FIRSTNAME';

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

	function countUnAssEmployeeRepTo($schStr,$mode) {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'LPAD(`EMP_NUMBER`, '.$this->employeeIdLength.', 0)';
		$arrFieldList[1] = 'EMP_LASTNAME';
		$arrFieldList[2] = 'EMPLOYEE_ID';

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

	function getUnAssEmployeeRepTo($pageNO,$schStr,$mode, $sortField = 0, $sortOrder = 'ASC') {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'LPAD(`EMP_NUMBER`, '.$this->employeeIdLength.', 0)';
		$arrFieldList[1] = "CONCAT(EMP_FIRSTNAME, ' ', EMP_MIDDLE_NAME, ' ', EMP_LASTNAME)";
		$arrFieldList[2] = 'EMPLOYEE_ID';
		$arrFieldList[3] = 'EMP_STATUS';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$specialSearch = "{$arrFieldList[3]} != 'EST000' OR {$arrFieldList[3]} IS NULL";

		$sqlQString = $sql_builder->passResultSetMessage($pageNO,$schStr,$mode, $sortField, $sortOrder, false, $specialSearch);

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

	function countEmpRepTo($schStr,$mode) {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';
		$arrFieldList[21] = 'EMPLOYEE_ID';

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

	function getListofEmpRepTo($pageNO,$schStr,$mode) {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';
		$arrFieldList[2] = 'EMP_FIRSTNAME';

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

	function countRepEmployee($schStr='',$mode=0) {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';

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

	function getListofRepEmployee($pageNO=0,$schStr='',$mode=0) {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';
		$arrFieldList[2] = 'EMPLOYEE_ID';
		$arrFieldList[3] = 'EMP_FIRSTNAME';

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

	function getUnAssEmployeeWorkExp($pageNO,$schStr,$mode) {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';
		$arrFieldList[2] = 'EMP_FIRSTNAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_EMP_WORK_EXPERIENCE';
		$sql_builder->field = 'EMP_NUMBER';
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->passResultFilter($pageNO,$schStr,$mode);

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

	function countUnAssEmployeeWorkExp($schStr,$mode) {

		$tableName = 'HS_HR_EMPLOYEE';
		$arrFieldList[0] = 'EMP_NUMBER';
		$arrFieldList[1] = 'EMP_LASTNAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->table2_name = 'HS_HR_EMP_WORK_EXPERIENCE';
		$sql_builder->flg_select = 'true';
		$sql_builder->field = 'EMP_NUMBER';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->countResultFilter($schStr,$mode);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$line = mysql_fetch_array($message2, MYSQL_NUM);

	    return $line[0];
	}

	public function fetchEmployeeId ($emp_number) {
		$tableName = '`hs_hr_employee`';
		$arrFieldList[0] = '`employee_id`';

		$sql_builder = new SQLQBuilder();

		$selectConditions[0] = " `emp_number` = '$emp_number'";

		$sqlQString = $sql_builder->simpleSelect($tableName, $arrFieldList, $selectConditions);

		//echo $sqlQString;

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString);

		if ($message2) {
			$line = mysql_fetch_array($message2, MYSQL_NUM);

			return $line[0];
		}

		return false;
	}

    /**
     * Searches for supervisors with name matching the search string and
     * returns a comma separated list of employee numbers of their
     * subordinates.
     *
     * @param searchStr The search string to match supervisor name with
     * @return comma separated list of employee numbers or null if no match.
     */
    private function _getEmpIdsWithMatchingSupervisor($searchStr) {

        $employeeNumbers = null;

        $empRepTo = new EmpRepTo();
        $empNumberArray = $empRepTo->getSubordinatesOfSupervisorWithName($searchStr);

        if (isset($empNumberArray) && count($empNumberArray) > 0) {
            $employeeNumbers = implode(',', $empNumberArray);
        }

        return $employeeNumbers;
    }

    /**
     * Searches for subdivisions in the company structure that have a title that
     * match the given search string and returns all subdivions under those
     * matches as a comma separated string of subdivision ids.
     *
     * @param searchStr The string to match against subdivision IDs
     * @return comma separated string of subdivision ids or null if no match.
     */
    private function _getMatchingSubdivisionIds($searchStr){

        $subdivisionIds = null;

        // Get list of workstations with matches in the title or matches higher in the hierachy
        $compStructObj = new CompStruct();
        $sublist = $compStructObj->getSubdivisionsUnderMatchInHierachy($searchStr);

        // Create select condition for employees with workstation set to any of the
        // subdivisions
        if (isset($sublist) && count($sublist) > 0) {

            $rowNum = 0;
            foreach ($sublist as $subdivision ){
                $subdivIdList[$rowNum] = $subdivision['id'];
                $rowNum++;
            }

            $subdivisionIds = implode(',', $subdivIdList);
        }

        return $subdivisionIds;
    }


}



?>
