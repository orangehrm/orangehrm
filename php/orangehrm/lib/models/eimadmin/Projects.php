<?php
/**
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
 *
 */

require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';
require_once ROOT_PATH . '/lib/dao/SQLQBuilder.php';
require_once ROOT_PATH . '/lib/confs/Conf.php';
require_once ROOT_PATH . '/lib/common/CommonFunctions.php';
require_once ROOT_PATH . '/lib/models/eimadmin/Customer.php';

/**
 * Project Class
 *
 * This class was introduced under Time & Attendance module. The HR-Admin wilkl be defining
 * the projects, which would have customers assigned to it.
 *
 */
class Projects {

	/**
	 * class constants
	 */
	const PROJECT_NOT_DELETED = 0;
	const PROJECT_DELETED = 1;

	const PROJECT_DB_TABLE = 'hs_hr_project';
	const PROJECT_DB_FIELD_PROJECT_ID = 'project_id';
	const PROJECT_DB_FIELD_CUSTOMER_ID = 'customer_id';
	const PROJECT_DB_FIELD_NAME = 'name';
	const PROJECT_DB_FIELD_DESCRIPTION = 'description';
	const PROJECT_DB_FIELD_DELETED = 'deleted';

	/**
	 * class attributes
	 *
	 */
	private $projectID;
	private $customerID;
	private $projectName;
	private $projectDescription;
	private $deleted;

	/**
	 * Automatic id genaration
	 *
	 */
	private  $singleField;
	private $maxidLength = '4';

	/**
	 *  Table Name
	 *
	 */
	const TABLE_NAME = 'hs_hr_project';

 	/**
	 *	Setter method followed by getter method for each
	 *	attribute
	 */
	public function setProjectId($projectid) {
			$this->projectID = $projectid;
	}

	public function getProjectId () {
		return $this->projectID;
	}

	public function setCustomerId($customerid) {
			$this->customerID = $customerid;
	}

	public function getCustomerId () {
		return $this->customerID;
	}

	public function setProjectName($projectname){
		$this->projectName  = 	$projectname ;
	}

	public function getProjectName(){
		return $this->projectName;
	}

	public function setProjectDescription ($projectDescription) {
		$this->projectDescription = $projectDescription ;
	}

	public function getProjectDescription () {
		return $this->projectDescription;
	}

	public function setDeleted ($deleted) {
		$this->deleted=$deleted;
	}

	public function getDeleted() {
		return $this->deleted;
	}


	/**
	 * Compute the new Project id
	 */
	public function getNewProjectId() {
		$sql_builder = new SQLQBuilder();

		$selectTable = self::PROJECT_DB_TABLE;
		$selectFields[0] = self::PROJECT_DB_FIELD_PROJECT_ID;
		$selectOrder = "DESC";
		$selectLimit = 1;
		$sortingField = self::PROJECT_DB_FIELD_PROJECT_ID;

		$query = $sql_builder->simpleSelect($selectTable, $selectFields, null, $sortingField, $selectOrder, $selectLimit);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		$row = mysql_fetch_row($result);

		$this->setProjectId($row[0]+1);
	}

	/**
	 * Add new project
	 *
	 * Deleted will be overwritten to NOT_DELETED
	 */
	public function addProject() {

		$this->getNewProjectId();

		$arrRecord[0] = "'".$this->getProjectId()."'";
		$arrRecord[1] = "'".$this->getCustomerId()."'";
		$arrRecord[2] = "'".$this->getProjectName()."'";
		$arrRecord[3] = "'".$this->getProjectDescription()."'";
		$arrRecord[4] = self::PROJECT_NOT_DELETED;

		$tableName = self::TABLE_NAME;

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_insert = 'true';
		$sql_builder->arr_insert = $arrRecord;

		$sqlQString = $sql_builder->addNewRecordFeature1();

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		if ($message2 && (mysql_affected_rows() > 0)) {
			return true;
		}
		return false;
	}

	/**
	 * Wraaper for delete
	 */

 	public function deletewrapperProjects ($arrList) {


		$i=0;
		$array_count = count($arrList,COUNT_RECURSIVE)- 1;
	  	for ($i=0; $i <  $array_count;$i++){

	 		$this->setProjectId( $arrList[0][$i]);
	 		$res=$this->deleteProject();

	 		if (!$res) {
	 			return $res;
	 		}
	 	}

		return $res;
	}

	/**
	 * Mark project deleted
	 */
	public function deleteProject() {
		$this->setDeleted(self::PROJECT_DELETED);

		return $this->updateProject();
	}

	/**
	 * Update project information
	 */
	public function updateProject() {

		$sql_builder = new SQLQBuilder();

		$updateTable = self::TABLE_NAME;

		if ($this->getCustomerId()!= null) {
			$updateFields[] = "`".self::PROJECT_DB_FIELD_CUSTOMER_ID."`";
			$updateValues[] = "'".$this->getCustomerId()."'";
		}

		if ($this->getProjectName() != null) {
			$updateFields[] = "`".self::PROJECT_DB_FIELD_NAME."`";
			$updateValues[] = "'".$this->getProjectName()."'";
		}

		if ($this->getProjectDescription() != null) {
			$updateFields[] = "`".self::PROJECT_DB_FIELD_DESCRIPTION."`";
			$updateValues[] = "'".$this->getProjectDescription()."'";
		}

		if ($this->getDeleted() != null) {
			$updateFields[] = "`".self::PROJECT_DB_FIELD_DELETED."`";
			$updateValues[] = $this->getDeleted();
		}

		$updateConditions[] = "`".self::PROJECT_DB_FIELD_PROJECT_ID."` = {$this->getProjectId()}";

		if (is_array($updateFields)) {
			$updateValues = $sql_builder->quoteCorrect($updateValues);

			$sqlQString = $sql_builder->simpleUpdate($updateTable, $updateFields, $updateValues, $updateConditions);

			$dbConnection = new DMLFunctions();
			$message2 = $dbConnection->executeQuery($sqlQString); //Calling the addData() function

			if ($message2 && (mysql_affected_rows() > 0)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Fetch project information, only one
	 */
	public function fetchProject($projectId) {
		$this->setProjectId($projectId);
		$objArr = $this->fetchProjects();
		if (isset($objArr)) {
			return $objArr[0];
		}
		return null;
	}

	/**
	 * To get the number of projects
	 */


	public function countprojectID($schStr,$schField) {

		$tableName = self::PROJECT_DB_TABLE;
		$arrFieldList[0] = self::PROJECT_DB_FIELD_PROJECT_ID;
		$arrFieldList[1] = self::PROJECT_DB_FIELD_DELETED;
		$arrFieldList[2] = self::PROJECT_DB_FIELD_CUSTOMER_ID;
		$arrFieldList[3] = self::PROJECT_DB_FIELD_NAME;
		$arrFieldList[4] = self::PROJECT_DB_FIELD_DESCRIPTION;

		$schField   = 1;
		$schStr		= 0;

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->countResultset($schStr,$schField);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$line = mysql_fetch_array($message2, MYSQL_NUM);

	    return $line[0];
	}



	public function fetchProjects() {
		$arrFieldList[0] = "`".self::PROJECT_DB_FIELD_PROJECT_ID."`";
		$arrFieldList[1] = "`".self::PROJECT_DB_FIELD_CUSTOMER_ID."`";
		$arrFieldList[2] = "`".self::PROJECT_DB_FIELD_NAME."`";
		$arrFieldList[3] = "`".self::PROJECT_DB_FIELD_DESCRIPTION."`";
		$arrFieldList[4] = "`".self::PROJECT_DB_FIELD_DELETED."`";

		$tableName = "`".self::TABLE_NAME."`";

		$sql_builder = new SQLQBuilder();

		$arrSelectConditions=null;

		if ($this->getProjectId() != null) {
			$arrSelectConditions[] = "`".self::PROJECT_DB_FIELD_PROJECT_ID."`= '".$this->getProjectId()."'";
		}

		if ($this->getCustomerId() != null) {
			$arrSelectConditions[] = "`".self::PROJECT_DB_FIELD_CUSTOMER_ID."`= '".$this->getCustomerId()."'";
		}

		if ($this->getProjectName() != null) {
			$arrSelectConditions[] = "`".self::PROJECT_DB_FIELD_NAME."`= '".$this->getProjectName()."'";
		}

		if ($this->getProjectDescription() != null) {
			$arrSelectConditions[] = "`".self::PROJECT_DB_FIELD_DESCRIPTION."`= '".$this->getProjectDescription()."'";
		}

		if (!is_null($this->getDeleted())) {

			$arrSelectConditions[] = "`".self::PROJECT_DB_FIELD_DELETED."`= ".$this->getDeleted()."";
		}

		$sqlQString = $sql_builder->simpleSelect($tableName, $arrFieldList, $arrSelectConditions, $arrFieldList[0], 'ASC');

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection->executeQuery($sqlQString); //Calling the addData() function

		$objArr = self::projectObjArr($message2);

		return $objArr;
	}

	/**
	 * Fetch all projects with paging
	 */
	public function getListOfProjectsStr($pageNO,$schStr,$schField,$sortField=0, $sortOrder='ASC') {

		$arrFieldList[0] = "a.`".self::PROJECT_DB_FIELD_PROJECT_ID."`";
		$arrFieldList[1] = "a.`".self::PROJECT_DB_FIELD_NAME."`";
		$arrFieldList[2] = "b.`".Customer::CUSTOMER_DB_FIELDS_NAME."`";
		$arrFieldList[3] = "a.`".self::PROJECT_DB_FIELD_DESCRIPTION."`";
		$arrFieldList[4] = "a.`".self::PROJECT_DB_FIELD_DELETED."`";

		$tableNames[0] = "`".Customer::TABLE_NAME."` b ";
		$tableNames[1] = "`".self::PROJECT_DB_TABLE."` a ";

		$joinConditions[1] = "b.`".Customer::CUSTOMER_DB_FIELDS_ID."` = a.`".self::PROJECT_DB_FIELD_CUSTOMER_ID."`";

		$sql_builder = new SQLQBuilder();

		$arrSelectConditions[0] = "a.`".self::PROJECT_DB_FIELD_DELETED."`= ".self::PROJECT_NOT_DELETED."";

		if ($schField != -1) {
			$arrSelectConditions[1] = "".$arrFieldList[$schField]." LIKE '%".$schStr."%'";
		}

		$limitStr = null;

		if ($pageNO > 0) {
			$sysConfObj = new sysConf();
			$page = ($pageNO-1)*$sysConfObj->itemsPerPage;
			$limit = $sysConfObj->itemsPerPage;
			$limitStr = "$page,$limit";
		}

		$sqlQString = $sql_builder->selectFromMultipleTable($arrFieldList, $tableNames, $joinConditions, $arrSelectConditions, null, $arrFieldList[$sortField], $sortOrder, $limitStr);

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection->executeQuery($sqlQString); //Calling the addData() function

		$arrDispArr = null;
		$i=0;
		while ($row = mysql_fetch_row($message2)) {
			$arrDispArr[$i] = $row;
			$i++;
		}

		return $arrDispArr;
	}

	/**
	 * Build the project object array from the given result set
	 *
	 * @param resource $result Result set from the database
	 * @return array   Array of Project objects
	 */
	public static function projectObjArr($result) {

		$objArr = null;

		while ($row = mysql_fetch_assoc($result)) {

			$tmpcusArr = new Projects();

			$tmpcusArr->setProjectId($row[self::PROJECT_DB_FIELD_PROJECT_ID]);
			$tmpcusArr->setCustomerId($row[self::PROJECT_DB_FIELD_CUSTOMER_ID]);
			$tmpcusArr->setProjectName($row[self::PROJECT_DB_FIELD_NAME]);
			$tmpcusArr->setProjectDescription($row[self::PROJECT_DB_FIELD_DESCRIPTION]);
			$tmpcusArr->setDeleted($row[self::PROJECT_DB_FIELD_DELETED]);

			$objArr[] = $tmpcusArr;
		}

		return $objArr;
	}
}

?>