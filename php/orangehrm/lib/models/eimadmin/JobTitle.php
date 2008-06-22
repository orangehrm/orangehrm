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
    
    const DB_FIELD_JOBSPEC_ID = 'jobspec_id';
    
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

		$sqlQString = $sql_builder->passResultSetMessage($pageNO,$schStr,$mode, $sortField, $sortOrder);

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

	function delJobTitles($arrList) {

		$tableName = 'HS_HR_JOB_TITLE';
		$arrFieldList[0] = 'JOBTIT_CODE';

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

	function addJobTitles() {

		$tableName = 'HS_HR_JOB_TITLE';

		$this->jobId = UniqueIDGenerator::getInstance()->getNextID($tableName, 'JOBTIT_CODE', 'JOB');

		$arrRecordsList[0] = "'". $this->getJobId() . "'";
		$arrRecordsList[1] = "'". $this->getJobName() . "'";
		$arrRecordsList[2] = "'". $this->getJobDesc() . "'";
		$arrRecordsList[3] = "'". $this->getJobComm() . "'";
		$arrRecordsList[4] = "'". $this->getJobSalGrd() . "'";
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

	function updateJobTitles() {

		$arrRecordsList[0] = "'". $this->getJobId() . "'";
		$arrRecordsList[1] = "'". $this->getJobName() . "'";
		$arrRecordsList[2] = "'". $this->getJobDesc() . "'";
		$arrRecordsList[3] = "'". $this->getJobComm() . "'";
		$arrRecordsList[4] = "'". $this->getJobSalGrd() . "'";
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

}

?>
