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
require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';
require_once ROOT_PATH . '/lib/dao/SQLQBuilder.php';
require_once ROOT_PATH . '/lib/models/eimadmin/JobTitEmpStat.php';
require_once ROOT_PATH . '/lib/models/eimadmin/EmployStat.php';
require_once ROOT_PATH . '/lib/common/CommonFunctions.php';
require_once ROOT_PATH . '/lib/common/UniqueIDGenerator.php';

class JobTitle {

    const MANAGER_JOB_TITLE_NAME = 'Manager';
    const DIRECTOR_JOB_TITLE_NAME = 'Director';

	const TABLE_NAME = 'HS_HR_JOB_TITLE';
    
    const DB_FIELD_CODE = 'jobtit_code';
    const DB_FIELD_JOBSPEC_ID = 'jobspec_id';
    const DB_FIELD_IS_ACTIVE = 'is_active';
    
    const DELETED_JOB_TITLE = 0;
    const ACTIVE_JOB_TITLE = 1;
    
	var $tableName = self::TABLE_NAME;

	var $jobId;
	var $jobName;
	var $jobDesc;
    var $jobComm;
    var $jobSalGrd;
	var $arrayDispList;
	var $singleField;
    private $jobSpecId;

	function JobTitle() {

	}

	function setJobId($jobId) {

		$this->jobId = $jobId;
	}

	function setJobName($jobName) {
		$this->jobName = $jobName;
	}

	function setJobDesc($jobDesc) {

		$this->jobDesc = $jobDesc;
    }

    function setJobComm($jobComm) {

        $this->jobComm = $jobComm;
    }

    function setJobSalGrd($jobSalGrd) {

    	$this->jobSalGrd = $jobSalGrd;
    }

    public function setJobSpecId($jobSpecId) {
        $this->jobSpecId = $jobSpecId;
    }

	function getJobId() {

		return $this->jobId;
	}

	function getJobName() {
		return $this->jobName;
	}

	function getJobDesc() {

		return $this->jobDesc;
    }

    function getJobComm() {

        return $this->jobComm;
    }

	function getJobSalGrd() {
		return $this->jobSalGrd;
	}
    
    public function getJobSpecId() {
        return $this->jobSpecId;
    }

	function getListofJobTitles($pageNO,$schStr,$mode, $sortField = 0, $sortOrder = 'ASC') {

		$tableName = 'HS_HR_JOB_TITLE';
		$arrFieldList[0] = 'JOBTIT_CODE';
		$arrFieldList[1] = 'JOBTIT_NAME';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;
		$specialSearch = "`".self::DB_FIELD_IS_ACTIVE."` = ".self::ACTIVE_JOB_TITLE;

		$sqlQString = $sql_builder->passResultSetMessage($pageNO,$schStr,$mode, $sortField, $sortOrder, false, $specialSearch);

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

	function countJobTitles($schStr,$mode) {

		$tableName = 'HS_HR_JOB_TITLE';
		$arrFieldList[0] = 'JOBTIT_CODE';
		$arrFieldList[1] = 'JOBTIT_NAME';
		$arrFieldList[2] = 'IS_ACTIVE';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;
		
		if (is_array($schStr)) {
			$schStr[] = 1; // To get only the active job titles    
		}

		$sqlQString = $sql_builder->countResultset($schStr,$mode);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$line = mysql_fetch_array($message2, MYSQL_NUM);

	    return $line[0];
	}

	function delJobTitles($arrList) {

		$updateTable = "`hs_hr_job_title`";
		
		$changeFields[0] = "`".self::DB_FIELD_IS_ACTIVE."`";
		$changeValues[0] = self::DELETED_JOB_TITLE;
		
		$updateConditions[0] = self::DB_FIELD_CODE." IN('".implode("','",$arrList[0])."')";
		
		$sqlBuilder = new SQLQBuilder();
		$query = $sqlBuilder->simpleUpdate($updateTable, $changeFields, $changeValues, $updateConditions);
		
		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($query);
		
		if ($result) {
		    return true;
		}

		return false;

	}

	function addJobTitles() {

		$tableName = 'HS_HR_JOB_TITLE';

		$this->jobId = UniqueIDGenerator::getInstance()->getNextID($tableName, 'JOBTIT_CODE', 'JOB');

		$arrRecordsList[0] = "'". $this->getJobId() . "'";
		$arrRecordsList[1] = "'". $this->getJobName() . "'";
		$arrRecordsList[2] = "'". $this->getJobDesc() . "'";
		$arrRecordsList[3] = "'". $this->getJobComm() . "'";		
		$arrRecordsList[4] = $this->jobSalGrd != '0' ? $this->jobSalGrd : 'null';
        $arrRecordsList[5] = isset($this->jobSpecId) ? $this->jobSpecId : 'null';

        $arrFieldList[0] = 'JOBTIT_CODE';
        $arrFieldList[1] = 'JOBTIT_NAME';
        $arrFieldList[2] = 'JOBTIT_DESC';
        $arrFieldList[3] = 'JOBTIT_COMM';
        $arrFieldList[4] = 'SAL_GRD_CODE';
        $arrFieldList[5] = self::DB_FIELD_JOBSPEC_ID;

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_insert = 'true';
		$sql_builder->arr_insert = $arrRecordsList;
        $sql_builder->arr_insertfield = $arrFieldList;

		$sqlQString = $sql_builder->addNewRecordFeature2();

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection->executeQuery($sqlQString);

		$jobTitleEmpStat = new JobTitEmpStat();

	    $jobTitleEmpStat->setEmpStatId(EmploymentStatus::EMPLOYMENT_STATUS_ID_TERMINATED);
	    $jobTitleEmpStat->setJobTitId($this->getJobId());

		$jobTitleEmpStat->addJobTitEmpStat();

		return $message2;
	}

	function getJobTit() {

		$sql_builder = new SQLQBuilder();
		$tableName = 'HS_HR_JOB_TITLE';
		$arrFieldList[0] = 'JOBTIT_CODE';
		$arrFieldList[1] = 'JOBTIT_NAME';

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;
		$specialSearch = "`".self::DB_FIELD_IS_ACTIVE."` = ".self::ACTIVE_JOB_TITLE;

		$sqlQString = $sql_builder->passResultSetMessage(0, '', -1, 1, 'ASC', false, $specialSearch);

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

	function updateJobTitles() {

		$arrRecordsList[0] = "'". $this->getJobId() . "'";
		$arrRecordsList[1] = "'". $this->getJobName() . "'";
		$arrRecordsList[2] = "'". $this->getJobDesc() . "'";
		$arrRecordsList[3] = "'". $this->getJobComm() . "'";		
		$arrRecordsList[4] = $this->jobSalGrd != '0' ? $this->jobSalGrd : 'null';
        $arrRecordsList[5] = isset($this->jobSpecId) ? $this->jobSpecId : 'null';
        
		$arrFieldList[0] = 'JOBTIT_CODE';
		$arrFieldList[1] = 'JOBTIT_NAME';
		$arrFieldList[2] = 'JOBTIT_DESC';
		$arrFieldList[3] = 'JOBTIT_COMM';
		$arrFieldList[4] = 'SAL_GRD_CODE';
        $arrFieldList[5] = self::DB_FIELD_JOBSPEC_ID;
        
		$tableName = 'HS_HR_JOB_TITLE';

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


	function filterJobTitles($getID) {

		$arrFieldList[0] = 'JOBTIT_CODE';
		$arrFieldList[1] = 'JOBTIT_NAME';
		$arrFieldList[2] = 'JOBTIT_DESC';
		$arrFieldList[3] = 'JOBTIT_COMM';
		$arrFieldList[4] = 'SAL_GRD_CODE';
        $arrFieldList[5] = self::DB_FIELD_JOBSPEC_ID;

		$tableName = 'HS_HR_JOB_TITLE';

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->selectOneRecordFiltered($getID);

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

        public function getJobStatusFromTitle($getID){

            $this->getID = $getID;

	    	$dbConnection = new DMLFunctions();

            if ($this->getID=='0') {                  // if clicked on select

                $selectTable = "`hs_hr_empstat` a";
                $arrFieldList[0] = "a.`estat_code`";
                $arrFieldList[1] = "a.`estat_code`";
                $arrFieldList[2] = "a.`estat_name`";
                $selectConditions=null;
                
            } else {

                $selectTable = "`hs_hr_jobtit_empstat` a,`hs_hr_empstat` b";
                $arrFieldList[0] = "a.`jobtit_code`";
                $arrFieldList[1] = "a.`estat_code`";
                $arrFieldList[2] = "b.`estat_name`";

                // ID should not have quotes, so it's safe to escape without looking at magic quotes setting
                $selectConditions[] = "a.`jobtit_code`='" . mysql_real_escape_string($this->getID) . "'";
                $selectConditions[] = "a.`estat_code`=b.`estat_code`";
            }           

            $sql_builder = new SQLQBuilder();
            $sqlQString = $sql_builder->simpleSelect($selectTable, $arrFieldList, $selectConditions, $arrFieldList[2], 'ASC');

            $result = $dbConnection -> executeQuery($sqlQString); 
						
	    	$i=0;
			$fieldNum=count($arrFieldList);
			
            while ($line = $dbConnection->dbObject->getArray($result)) {
	    		
				for ($c=0;$fieldNum> $c ; $c++){
                            $arrayDispList[$i][$c] = $line[$c];
                }
	    		$i++;
            }

            if (isset($arrayDispList)) {

				return $arrayDispList;

			} else {

				$arrayDispList = '';
				return $arrayDispList;

			}
           
            return $arrayDispList;

        }

}

?>
