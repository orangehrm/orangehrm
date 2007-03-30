
<?php

require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';
require_once ROOT_PATH . '/lib/dao/SQLQBuilder.php';
require_once ROOT_PATH . '/lib/confs/Conf.php';
require_once ROOT_PATH . '/lib/common/CommonFunctions.php';



/**
 * Project Class
 *
 * This class was introduced under Time & Attendance module. The HR-Admin wilkl be defining
 * the projects, which would have customers assigned to it.
 *
 */


class Projects {

	//class constatnts
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


	/**
	 * Automatic id genaration
	 */

	private  $singleField;
	private $maxidLength = '4';

	/**
	 *  Table Name
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

	public function setProjectName($projectname){
		$this->projectName  = 	$projectname ;
	}

	public function getCustomerId () {
		return $this->customerID;
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



	/**
	 * Add data to the project
	 *
	 */

public function AddProject () {

		$this->getProjectId ();

		$arrRecord[0] = "'". $this->getProjectId () . "'";
		$arrRecord[1] = "'". $this->getCustomerId () . "'";
		$arrRecord[2] = "'". $this->getProjectName() . "'";
		$arrRecord[3] = "'". $this->getProjectDescription() . "'";
		$arrRecord[4] = self::PROJECT_NOT_DELETED;

		$tableName = self::TABLE_NAME;

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_insert = 'true';
		$sql_builder->arr_insert = $arrRecord;


		$sqlQString = $sql_builder->addNewRecordFeature1();

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		return $message2;
	}

	public function fetchProjects($pageNO=0,$schStr='',$schField=-1, $sortField=0, $sortOrder='ASC'){


		$arrFieldList[0] = "`".self::PROJECT_DB_FIELD_PROJECT_ID."`";
		$arrFieldList[1] = "`".self::PROJECT_DB_FIELD_CUSTOMER_ID."`";
		$arrFieldList[2] = "`".self::PROJECT_DB_FIELD_NAME."`";
		$arrFieldList[3] = "`".self::PROJECT_DB_FIELD_DESCRIPTION."`";
		$arrFieldList[4] = "`".self::PROJECT_DB_FIELD_DELETED."`";

		$tableName = "`".self::TABLE_NAME."`";

		$sql_builder = new SQLQBuilder();

		$arrSelectConditions[0] = "`".self::PROJECT_DB_FIELD_DELETED."`= ".self::PROJECT_NOT_DELETED."";

		if ($schField != -1) {
			$arrSelectConditions[1] = "`".$arrFieldList[$schField]."` LIKE '%".$schStr."%'";
		}

		$limitStr = null;

		if ($pageNO > 0) {
			$sysConfObj = new sysConf();
			$page = ($pageNO-1)*$sysConfObj->itemsPerPage;
			$limit = $sysConfObj->itemsPerPage;
			$limitStr = "$page,$limit";

		}
		$sqlQString = $sql_builder->simpleSelect($tableName, $arrFieldList, $arrSelectConditions, $arrFieldList[0], 'ASC', $limitStr);

		echo $sqlQString;

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection->executeQuery($sqlQString); //Calling the addData() function

		 return   $this->projectObjArr($message2) ;

	}

	 public function projectObjArr($result) {

		$objArr = null;

		echo mysql_num_rows($result);

		while ($row = mysql_fetch_assoc($result)) {

			$tmpcusArr = new Projects();

			$tmpcusArr->setProjectId($row[self::PROJECT_DB_FIELD_PROJECT_ID]);
			$tmpcusArr->setCustomerId($row[self::PROJECT_DB_FIELD_CUSTOMER_ID]);
			$tmpcusArr->setProjectName($row[self::PROJECT_DB_FIELD_NAME]);
			$tmpcusArr->setProjectDescription($row[self::PROJECT_DB_FIELD_DESCRIPTION]);

			$objArr[] = $tmpcusArr;
		}

		return $objArr;
	}
}

?>