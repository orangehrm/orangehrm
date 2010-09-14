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
require_once ROOT_PATH . '/lib/common/CommonFunctions.php';
require_once ROOT_PATH . '/lib/common/LocaleUtil.php';
require_once ROOT_PATH . '/lib/common/UniqueIDGenerator.php';
require_once ROOT_PATH . '/lib/common/SearchObject.php';
require_once ROOT_PATH . '/lib/models/recruitment/JobVacancy.php';
require_once ROOT_PATH . '/lib/models/recruitment/JobApplicationEvent.php';

/**
 * Class representing a Job Application
 */
class JobApplication {

	const TABLE_NAME = 'hs_hr_job_application';

	/** Database fields */
	const DB_FIELD_ID = 'application_id';
	const DB_FIELD_VACANCY_ID = 'vacancy_id';
	const DB_FIELD_FIRSTNAME = 'firstname';
	const DB_FIELD_MIDDLENAME = 'middlename';
	const DB_FIELD_LASTNAME = 'lastname';
	const DB_FIELD_STREET1 = 'street1';
	const DB_FIELD_STREET2 = 'street2';
	const DB_FIELD_CITY = 'city';
	const DB_FIELD_COUNTRY_CODE = 'country_code';
	const DB_FIELD_PROVINCE = 'province';
	const DB_FIELD_ZIP = 'zip';
	const DB_FIELD_PHONE = 'phone';
	const DB_FIELD_MOBILE = 'mobile';
	const DB_FIELD_EMAIL = 'email';
	const DB_FIELD_QUALIFICATIONS = 'qualifications';
    const DB_FIELD_STATUS = 'status';
    const DB_FIELD_APPLIED_DATETIME = 'applied_datetime';
    const DB_FIELD_EMP_NUMBER = 'emp_number';
    const DB_FIELD_RESUME_NAME = 'resume_name';
    const DB_FIELD_RESUME_DATA ='resume_data';

    /**
     * Job application status
     */
    const STATUS_SUBMITTED = 0;
    const STATUS_FIRST_INTERVIEW_SCHEDULED = 1;
    const STATUS_SECOND_INTERVIEW_SCHEDULED = 2;
    const STATUS_JOB_OFFERED = 3;
    const STATUS_OFFER_DECLINED = 4;
    const STATUS_PENDING_APPROVAL = 5;
    const STATUS_HIRED = 6;
    const STATUS_REJECTED = 7;

    /**
     * Actions that can be performed on Job Application
     */
    const ACTION_REJECT = 'Reject';
    const ACTION_SCHEDULE_FIRST_INTERVIEW = 'FirstInterview';
    const ACTION_SCHEDULE_SECOND_INTERVIEW = 'SecondInterview';
    const ACTION_OFFER_JOB = 'OfferJob';
    const ACTION_MARK_OFFER_DECLINED = 'MarkDeclined';
    const ACTION_SEEK_APPROVAL = 'SeekApproval';
    const ACTION_APPROVE = 'Approve';

    /** Fields retrieved from other tables */
    const JOB_TITLE_NAME = 'job_title_name';
    const HIRING_MANAGER_NAME = 'hiring_manager_name';

	private $dbFields = array(self::DB_FIELD_ID, self::DB_FIELD_VACANCY_ID, self::DB_FIELD_FIRSTNAME,
		self::DB_FIELD_MIDDLENAME, self::DB_FIELD_LASTNAME,	self::DB_FIELD_STREET1,	self::DB_FIELD_STREET2,
		self::DB_FIELD_CITY, self::DB_FIELD_COUNTRY_CODE, self::DB_FIELD_PROVINCE, self::DB_FIELD_ZIP,
		self::DB_FIELD_PHONE, self::DB_FIELD_MOBILE, self::DB_FIELD_EMAIL, self::DB_FIELD_QUALIFICATIONS,
        self::DB_FIELD_STATUS, self::DB_FIELD_APPLIED_DATETIME, self::DB_FIELD_EMP_NUMBER,
        self::DB_FIELD_RESUME_NAME, self::DB_FIELD_RESUME_DATA);

	private $id;
	private $vacancyId;
	private $firstName;
	private $middleName;
	private $lastName;
	private $street1;
	private $street2;
	private $city;
	private $province;
	private $country;
	private $zip;
	private $phone;
	private $mobile;
	private $email;
	private $qualifications;
	public $resumeData = array('name'=>'', 'tmpName'=>'', 'extension'=>'', 'size'=>0, 'error'=>''); // 'name' = Original name of the file
    private $status = self::STATUS_SUBMITTED;
    private $appliedDateTime;
    private $empNumber;
    private $resumeName; // Name assigned by OrangeHRM

    private $events;

    /**
     * Attributes retrieved from other objects
     */
    private $hiringManagerName;
    private $jobTitleName;

	/**
	 * Constructor
	 *
	 * @param int $id ID can be null for newly created job applications
	 */
	public function __construct($id = null) {
		$this->id = $id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getId() {
		return $this->id;
	}

	public function setVacancyId($vacancyId) {
		$this->vacancyId = $vacancyId;
	}

	public function getVacancyId() {
		return $this->vacancyId;
	}

	public function setFirstName($firstName) {
		$this->firstName = $firstName;
	}

	public function getFirstName() {
		return $this->firstName;
	}

	public function setMiddleName($middleName) {
		$this->middleName = $middleName;
	}

	public function getMiddleName() {
		return $this->middleName;
	}

	public function setLastName($lastName) {
		$this->lastName = $lastName;
	}

	public function getLastName() {
		return $this->lastName;
	}

	public function setStreet1($street1) {
		$this->street1 = $street1;
	}

	public function getStreet1() {
		return $this->street1;
	}

	public function setStreet2($street2) {
		$this->street2 = $street2;
	}

	public function getStreet2() {
		return $this->street2;
	}

	public function setCity($city) {
		$this->city = $city;
	}

	public function getCity() {
		return $this->city;
	}

	public function setProvince($province) {
		$this->province = $province;
	}

	public function getProvince() {
		return $this->province;
	}

	public function setCountry($country) {
		$this->country = $country;
	}

	public function getCountry() {
		return $this->country;
	}

	public function setZip($zip) {
		$this->zip = $zip;
	}

	public function getZip() {
		return $this->zip;
	}

	public function setPhone($phone) {
		$this->phone = $phone;
	}

	public function getPhone() {
		return $this->phone;
	}

	public function setMobile($mobile) {
		$this->mobile = $mobile;
	}

	public function getMobile() {
		return $this->mobile;
	}

	public function setEmail($email) {
		$this->email = $email;
	}

	public function getEmail() {
		return $this->email;
	}

	public function setQualifications($qualifications) {
		$this->qualifications = $qualifications;
	}

	public function getQualifications() {
	    return $this->qualifications;
	}

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getEvents() {

        if (!isset($this->events) && isset($this->id)) {

            // Get application events
            $events = JobApplicationEvent::getEvents($this->id);
            $this->events = $events;
        }
        return $this->events;
    }

    public function setEvents($events) {
        $this->events = $events;
    }

    /**
     * Retrieves the value of hiringManagerName.
     * @return hiringManagerName
     */
    public function getHiringManagerName() {
        return $this->hiringManagerName;
    }

    /**
     * Sets the value of hiringManagerName.
     * @param hiringManagerName
     */
    public function setHiringManagerName($hiringManagerName) {
        $this->hiringManagerName = $hiringManagerName;
    }

    /**
     * Retrieves the value of jobTitleName.
     * @return jobTitleName
     */
    public function getJobTitleName() {
        return $this->jobTitleName;
    }

    /**
     * Sets the value of jobTitleName.
     * @param jobTitleName
     */
    public function setJobTitleName($jobTitleName) {
        $this->jobTitleName = $jobTitleName;
    }

    /**
     * Get the applied date and time
     */
    public function getAppliedDateTime() {
        return $this->appliedDateTime;
    }

    /**
     * Set the applied date and time
     */
    public function setAppliedDateTime($date) {
        $this->appliedDateTime = $date;
    }

    /**
     * Set the employee number of employee created after hiring
     * @param int $empNumber The employee number
     */
    public function setEmpNumber($empNumber) {
        $this->empNumber = $empNumber;
    }

    /**
     * Get the employee number of employee created after hiring
     * @return int The employee number of the employee created or null
     */
    public function getEmpNumber($empNumber) {
        return $this->empNumber;
    }

    public function setResumeName($resumeName) {
        $this->resumeName = $resumeName;
    }

    public function getResumeName() {
        return $this->resumeName;
    }

    /**
     * Returns the latest event
     * @return JobApplicationEvent The latest event, or null if no events
     */
    public function getLatestEvent() {

        $latestEvent = null;
        $events = $this->getEvents();
        if (!empty($events)) {
            $latestEvent = $events[count($events) - 1];
        }

        return $latestEvent;
    }

    /**
     * Returns event of given type
     * @param $eventType The event type
     * @return JobApplicationEvent The latest event of given type or null if not found
     */
    public function getEventOfType($eventType) {
        $event = null;

        $events = $this->getEvents();
        if (!empty($events)) {

            for($i = count($events) - 1; $i >= 0; $i--) {
                if ($events[$i]->getEventType() == $eventType) {
                    $event = $events[$i];
                    break;
                }
            }
        }

        return $event;
    }

	/**
	 * Save JobApplication object to database
	 *
	 * If a new JobApplication, inserts into the database, otherwise, updates
	 * the existing entry.
	 *
	 * @return int Returns the ID of the JobApplication
	 */
    public function save() {

		if (empty($this->firstName) || empty($this->lastName) || empty($this->email) || empty($this->vacancyId)) {
			throw new JobApplicationException("Attributes not set", JobApplicationException::MISSING_PARAMETERS);
		}
		if (!CommonFunctions::isValidId($this->vacancyId)) {
		    throw new JobApplicationException("Invalid vacancy id", JobApplicationException::INVALID_PARAMETER);
		}

		if (isset($this->id)) {

			if (!CommonFunctions::isValidId($this->id)) {
			    throw new JobApplicationException("Invalid id", JobApplicationException::INVALID_PARAMETER);
			}
			return $this->_update();
		} else {
			return $this->_insert();
		}
    }


    /**
     * Get job application with given id
     *
     * @param int $id Job Application ID
     * @return JobApplication JobApplication object
     */
    public static function getJobApplication($id) {

        if (!CommonFunctions::isValidId($id)) {
            throw new JobApplicationException("Invalid id", JobApplicationException::INVALID_PARAMETER);
        }

        $conditions[] = 'a.' . self::DB_FIELD_ID . ' = ' . $id;
        $list = self::_getList($conditions);
        $application = (count($list) == 1) ? $list[0] : null;

        return $application;
    }

    /**
     * Get list of job applications.
     * If optional emp number is given, only job applications associated with given manager
     * are returned.
     *
     * @param int $managerEmpNum Employee number of manager.
     * @return Array Array of JobApplication objects.
     */
    public static function getList($managerEmpNum = null) {

        if (!empty($managerEmpNum) && !CommonFunctions::isValidId($managerEmpNum)) {
            throw new JobApplicationException("Invalid id", JobApplicationException::INVALID_PARAMETER);
        }

        return self::_getList(null, $managerEmpNum);
    }

	/**
	 * Insert new object to database
	 */
	private function _insert() {

		$this->id = UniqueIDGenerator::getInstance()->getNextID(self::TABLE_NAME, self::DB_FIELD_ID);
        if (empty($this->appliedDateTime)) {
            $this->appliedDateTime = date(LocaleUtil::STANDARD_TIMESTAMP_FORMAT);
        }

		$sql = "INSERT INTO `".self::TABLE_NAME."` (";

		$fields = $this->dbFields;
		$fCount = count($fields);

		for ($i=0; $i<$fCount; $i++) {

			if ($i != ($fCount - 1)) {
			    $sql .= "`".$fields[$i]."`, ";
			} else {
			    $sql .= "`".$fields[$i]."`) ";
			}

		}

		$sql .= "VALUES (";

		$values = $this->_getFieldValuesAsArray(true);
		$vCount = count($values);

		for ($j=0; $j<$vCount; $j++) {

			if ($j != ($vCount - 1)) {
				if ($values[$j] == 'null') {
					$sql .= $values[$j].", ";
				} else {
					$sql .= "'". $values[$j]."', ";
				}
			} else {
				if ($values[$j] == 'null') {
					$sql .= $values[$j].")";
				} else {
					$sql .= "'". $values[$j] . "')";
				}
			}

		}

		$conn = new DMLFunctions();

		$result = $conn->executeQuery($sql);
		if (!$result || (mysql_affected_rows() != 1)) {
			throw new JobApplicationException("Insert failed. ", JobApplicationException::DB_ERROR);
		}

		return $this->id;
	}

	/**
	 * Update existing object
	 */
	private function _update() {

		$fields = $this->dbFields;
		$values = $this->_getFieldValuesAsArray();

		for ($i=0; $i<count($values); $i++) {
		    if (!empty($values[$i]) && $values[$i] !== 'null') {
		        $fieldsArr[] = $fields[$i];
		        $valuesArr[] = $values[$i];
		    }
		}

		$sqlBuilder = new SQLQBuilder();
		$sqlBuilder->table_name = self::TABLE_NAME;
		$sqlBuilder->flg_update = 'true';
		$sqlBuilder->arr_update = $fieldsArr;
		$sqlBuilder->arr_updateRecList = $valuesArr;

		$sql = $sqlBuilder->addUpdateRecord1(0);

		$conn = new DMLFunctions();
		$result = $conn->executeQuery($sql);

		// Here we don't check mysql_affected_rows because update may be called
		// without any changes.
		if (!$result) {
			throw new JobApplicationException("Update failed. SQL=$sql", JobApplicationException::DB_ERROR);
		}
		return $this->id;
	}

    /**
     * Get a list of jobs applications with the given conditions.
     *
     * @param array  $selectCondition Array of select conditions to use.
     * @param String $filterForManagerId Filter by the given manager
     * @return array Array of JobApplication objects. Returns an empty (length zero) array if none found.
     */
    private static function _getList($selectCondition = null, $filterForManagerId = null) {

        $fields[0] = 'a.' . self::DB_FIELD_ID;
        $fields[1] = 'a.' . self::DB_FIELD_VACANCY_ID;
        $fields[2] = 'a.' . self::DB_FIELD_FIRSTNAME;
        $fields[3] = 'a.' . self::DB_FIELD_MIDDLENAME;
        $fields[4] = 'a.' . self::DB_FIELD_LASTNAME;
        $fields[5] = 'a.' . self::DB_FIELD_STREET1;
        $fields[6] = 'a.' . self::DB_FIELD_STREET2;
        $fields[7] = 'a.' . self::DB_FIELD_CITY;
        $fields[8] = 'a.' . self::DB_FIELD_COUNTRY_CODE;
        $fields[9] = 'a.' . self::DB_FIELD_PROVINCE;
        $fields[10] = 'a.' . self::DB_FIELD_ZIP;
        $fields[11] = 'a.' . self::DB_FIELD_PHONE;
        $fields[12] = 'a.' . self::DB_FIELD_MOBILE;
        $fields[13] = 'a.' . self::DB_FIELD_EMAIL;
        $fields[14] = 'a.' . self::DB_FIELD_QUALIFICATIONS;
        $fields[15] = 'a.' . self::DB_FIELD_STATUS;
        $fields[16] = 'a.' . self::DB_FIELD_APPLIED_DATETIME;
        $fields[17] = 'a.' . self::DB_FIELD_EMP_NUMBER;
        $fields[18] = 'c.jobtit_name AS ' . self::JOB_TITLE_NAME;
        $fields[19] = "CONCAT(d.`emp_firstname`, ' ', d.`emp_lastname`) AS " . self::HIRING_MANAGER_NAME;
        $fields[20] = 'a.' . self::DB_FIELD_RESUME_NAME;

        $tables[0] = self::TABLE_NAME . ' a';
        $tables[1] = JobVacancy::TABLE_NAME .' b';
        $tables[2] = 'hs_hr_job_title c';
        $tables[3] = 'hs_hr_employee d';

        $joinConditions[1] = 'a.' . self::DB_FIELD_VACANCY_ID . ' = b.' . JobVacancy::DB_FIELD_VACANCY_ID;
        $joinConditions[2] = 'b.jobtit_code = c.jobtit_code';
        $joinConditions[3] = 'b.' . JobVacancy::DB_FIELD_MANAGER_ID . ' = d.emp_number';

        $groupBy = null;

        if (!empty($filterForManagerId)) {
            $tables[4] = JobApplicationEvent::TABLE_NAME . ' e';
            $joinConditions[4] = 'a.' . self::DB_FIELD_ID . ' = e.' . JobApplicationEvent::DB_FIELD_APPLICATION_ID;
            $selectCondition[] = '((b.' . JobVacancy::DB_FIELD_MANAGER_ID . ' = ' . $filterForManagerId . ') OR ' .
                    '(e.' . JobApplicationEvent::DB_FIELD_OWNER . ' = '.$filterForManagerId.'))' ;
            $groupBy = 'a.' . self::DB_FIELD_ID;
        }

        $sqlBuilder = new SQLQBuilder();
        $sql = $sqlBuilder->selectFromMultipleTable($fields, $tables, $joinConditions, $selectCondition, null, null, null, null, $groupBy);

        $actList = array();

        $conn = new DMLFunctions();
        $result = $conn->executeQuery($sql);

        while ($result && ($row = mysql_fetch_assoc($result))) {
            $actList[] = self::_createFromRow($row);
        }

        return $actList;
    }

	/**
	 * Returns the db field values as an array
	 *
     * @param boolean escape if true, values are escaped
	 * @return Array Array containing field values in correct order.
	 */
	private function _getFieldValuesAsArray($escape = false) {

		$values[0] = $this->id;
		$values[1] = $this->vacancyId;
		$values[2] = $this->firstName;
		$values[3] = $this->middleName;
		$values[4] = $this->lastName;
		$values[5] = $this->street1;
		$values[6] = $this->street2;
		$values[7] = $this->city;
		$values[8] = $this->country;
		$values[9] = $this->province;
		$values[10] = $this->zip;
		$values[11] = $this->phone;
		$values[12] = $this->mobile;
		$values[13] = $this->email;
		$values[14] = $this->qualifications;
        $values[15] = is_null($this->status) ? self::STATUS_SUBMITTED : $this->status;
        $values[16] = is_null($this->appliedDateTime) ? 'null' : $this->appliedDateTime;
        $values[17] = empty($this->empNumber) ? 'null' : $this->empNumber;

        // Escape all values in array.
        if ($escape) {
            $values = array_map(array('CommonFunctions', 'safeEscapeSQL'), $values);
        }

        $values[18] = ($this->resumeData['size'] > 0 || !empty($this->resumeName)) ? $this->_getResumeName($this->id, $this->firstName, $this->lastName) : 'null';
        $values[19] = ($this->resumeData['size'] > 0 || !empty($this->resumeName)) ? $this->_prepareResumeToStore() : 'null';

		return $values;
	}

    /**
     * Creates a JobApplication object from a resultset row
     *
     * @param array $row Resultset row from the database.
     * @return JobApplication JobApplication object.
     */
    private static function _createFromRow($row) {

        $application = new JobApplication($row[self::DB_FIELD_ID]);
        $application->setVacancyId($row[self::DB_FIELD_VACANCY_ID]);
        $application->setFirstName($row[self::DB_FIELD_FIRSTNAME]);
        $application->setMiddleName($row[self::DB_FIELD_MIDDLENAME]);
        $application->setLastName($row[self::DB_FIELD_LASTNAME]);
        $application->setStreet1($row[self::DB_FIELD_STREET1]);
        $application->setStreet2($row[self::DB_FIELD_STREET2]);
        $application->setCity($row[self::DB_FIELD_CITY]);
        $application->setCountry($row[self::DB_FIELD_COUNTRY_CODE]);
        $application->setProvince($row[self::DB_FIELD_PROVINCE]);
        $application->setZip($row[self::DB_FIELD_ZIP]);
        $application->setPhone($row[self::DB_FIELD_PHONE]);
        $application->setMobile($row[self::DB_FIELD_MOBILE]);
        $application->setEmail($row[self::DB_FIELD_EMAIL]);
        $application->setQualifications($row[self::DB_FIELD_QUALIFICATIONS]);
        $application->setStatus($row[self::DB_FIELD_STATUS]);
        $application->setAppliedDateTime($row[self::DB_FIELD_APPLIED_DATETIME]);
        $application->setEmpNumber($row[self::DB_FIELD_EMP_NUMBER]);
        $application->setResumeName($row[self::DB_FIELD_RESUME_NAME]);

        if (isset($row[self::JOB_TITLE_NAME])) {
            $application->setJobTitleName($row[self::JOB_TITLE_NAME]);
        }

        if (isset($row[self::HIRING_MANAGER_NAME])) {
            $application->setHiringManagerName($row[self::HIRING_MANAGER_NAME]);
        }

        return $application;
    }

    /**
     * Checks whether the upload file is compatible
     * @return bool True if compatible, false otherwise, also sets $this->resumeData['error']
     */

	public function isResumeCompatible() {

		$ext = $this->resumeData['extension'];

		if ($this->resumeData['size'] > (1024*1024)) {
			$this->resumeData['error'] = 'size-error';
			return false;
		} elseif ($ext != 'doc' && $ext != 'docx' && $ext != 'odt' && $ext != 'pdf' && $ext != 'rtf' && $ext != 'txt') {
			$this->resumeData['error'] = 'type-error';
			return false;
		} else {
		    return true;
		}

     }

    /**
     * Creates the name of to be stored resume
     * @param integer $appId Application ID
     * @param string $firstName First name of the applicant
     * @param string $lastName Last name of the applicant
     * @return string Name on success and null if any of given parameter is null
     */

    private function _getResumeName($appId, $firstName, $lastName) {
		if (isset($this->resumeName)) {
			$this->resumeName = trim($this->resumeName);
			if (!empty($this->resumeName)) {
				return $this->resumeName;
			}
		}

    	if (!is_null($appId) && !is_null($firstName) && !is_null($lastName)) {
			$name = $appId.'-'.$firstName.'-'.$lastName.'.'.$this->resumeData['extension'];
    	} else {
    	    $name = 'null';
    	}

		return $name;

    }

    /**
     * Prepares the resume to store in the database
     * @return string Prepared string on success, null on no resume to store
     */

	private function _prepareResumeToStore() {

	    if ($this->resumeData['size'] > 0) {

	    	$fileAsString = file_get_contents($this->resumeData['tmpName']);

	    	if (get_magic_quotes_gpc()) {
	    	    stripslashes($fileAsString);
	    	}

	    	return mysql_real_escape_string($fileAsString);

	    } else {

	       return 'null';

	    }

	}

	/**
	 * Updates an existing resume
	 * @return int Job application ID
	 */
	public function updateResume() {

		if (!isset($this->id) ||
			!isset($this->firstName) ||
			!isset($this->lastName) ||
			$this->resumeData['size'] == 0) {

			throw new JobApplicationException("Resume update: Missing values on required fields", JobApplicationException::RESUME_DATA_NOT_SET);

		}

		$resumeName = self::_getResumeName($this->id, $this->firstName, $this->lastName);
		$resumeData = self::_prepareResumeToStore();

		$sql = "UPDATE `".self::TABLE_NAME."` SET `".self::DB_FIELD_RESUME_NAME."` = '$resumeName', `";
		$sql .= self::DB_FIELD_RESUME_DATA."` = '$resumeData' ";
		$sql .= "WHERE `".self::DB_FIELD_ID."` = {$this->id}";

		$conn = new DMLFunctions();
		$result = $conn->executeQuery($sql);

		if (!$result) {
			throw new JobApplicationException("Update failed. SQL=$sql", JobApplicationException::DB_ERROR);
		}

		return $this->id;

	}

	/**
	 * Deletes an existing resume
	 * @return int Job application ID
	 */
	public function deleteResume() {

		if (!isset($this->id)) {
			throw new JobApplicationException("Resume delete: Missing job application ID", JobApplicationException::ID_NOT_SET);
		}

		$sql = "UPDATE `".self::TABLE_NAME."` SET `".self::DB_FIELD_RESUME_NAME."` = NULL, `";
		$sql .= self::DB_FIELD_RESUME_DATA."` = NULL ";
		$sql .= "WHERE `".self::DB_FIELD_ID."` = {$this->id}";

		$conn = new DMLFunctions();
		$result = $conn->executeQuery($sql);

		if (!$result) {
			throw new JobApplicationException("Deletion failed. SQL=$sql", JobApplicationException::DB_ERROR);
		}

		return $this->id;

	}

	/**
	 * Triggers a download of the resume for give application id
	 * @param integer $applicationId Id of the application
	 * @return sting Outputs the resume only if it's availabe
	 */
	public static function downloadResume($applicationId) {

		$selectTable = "`".self::TABLE_NAME."`";

		$selectFields[0] = "`".self::DB_FIELD_RESUME_NAME."`";
		$selectFields[1] = "`".self::DB_FIELD_RESUME_DATA."`";

		$selectConditions[0] = "`".self::DB_FIELD_ID."` = $applicationId";

		$sqlBuilder = new SQLQBuilder();
		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($query);

		$row = $dbConnection->dbObject->getArray($result);

		if (!empty($row[self::DB_FIELD_RESUME_DATA])) {

			$name = $row[self::DB_FIELD_RESUME_NAME];

			$extension = array_pop(explode(".", $name));
            $mimeTypes = array(
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'odt' => 'application/vnd.oasis.opendocument.text',
                'pdf' => 'application/pdf',
                'rtf' => 'application/rtf',
                'txt' => 'text/plain');
            $contentType = isset($mimeTypes[$extension]) ? $mimeTypes[$extension]: null;

			$content = $row[self::DB_FIELD_RESUME_DATA];
            $size = strlen($content);

            @ob_clean();
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private", false);
			header("Content-type: $contentType");
			header("Content-Disposition: attachment; filename=\"".$name."\";");
			header("Content-Transfer-Encoding: binary");
			header("Content-length: $size");
			echo $content;
		}

	}

}

class JobApplicationException extends Exception {
	const INVALID_PARAMETER = 0;
	const MISSING_PARAMETERS = 1;
	const DB_ERROR = 2;
    const INVALID_STATUS = 3;
    const RESUME_DATA_NOT_SET = 4;
    const ID_NOT_SET = 5;
}

