<?php

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

	//class constatnts
	const PROJECT_NOT_DELETED = 0;
	const PROJECT_DELETED = 1;

	const PROJECT_DB_TABLE = 'HS_HR_PROJECT';
	const PROJECT_DB_FIELD_PROJECT_ID = 'PROJECT_ID';
	const PROJECT_DB_FIELD_CUSTOMER_ID = 'CUSTOMER_ID';
	const PROJECT_DB_FIELD_NAME = 'NAME';
	const PROJECT_DB_FIELD_DESCRIPTION = 'DESCRIPTION';
	const PROJECT_DB_FIELD_DELETED = 'DELETED';

	//class attributes
	private $projectID;
	private $customerID;
	private $projectName;
	private $projectDescription;

	public function setProjectID($projectID) {
		$this->projectID = $projectID;
	}

	public function getProjectID() {
		return $this->projectID;
	}

	public function setCustomerID($customerID) {
		$this->customerID = $customerID;
	}

	public function getCustomerID() {
		return $this->customerID;
	}

	public function setProjectName($projectName) {
		$this->projectName = $projectName;
	}

	public function getProjectName() {
		return $this->projectName;
	}

	public function setProjectDescription($projectDescription) {
		$this->projectDescription = $projectDescription;
	}

	public function getProjectDescription() {
		return $this->projectDescription;
	}

	public function addProject() {

	}

	public function editProject() {

	}

	public function deleteProject() {

	}

	public function fetchProject() {

	}

	public function fetchProjects() {

	}
}

?>