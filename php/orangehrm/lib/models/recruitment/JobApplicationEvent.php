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

/**
 * Class representing a Job Application Event
 */
class JobApplicationEvent {

	const TABLE_NAME = 'hs_hr_job_application_events';

	/** Database fields */
	const DB_FIELD_ID = 'id';
	const DB_FIELD_APPLICATION_ID = 'application_id';
	const DB_FIELD_CREATED_TIME = 'created_time';
	const DB_FIELD_CREATED_BY = 'created_by';
	const DB_FIELD_OWNER = 'owner';
    const DB_FIELD_EVENT_TIME = 'event_time';
	const DB_FIELD_EVENT_TYPE = 'event_type';
	const DB_FIELD_STATUS = 'status';
	const DB_FIELD_NOTES = 'notes';

    private static $dbFields = array(self::DB_FIELD_ID, self::DB_FIELD_APPLICATION_ID, self::DB_FIELD_CREATED_TIME,
        self::DB_FIELD_CREATED_BY, self::DB_FIELD_OWNER, self::DB_FIELD_EVENT_TIME, self::DB_FIELD_EVENT_TYPE, self::DB_FIELD_STATUS,
        self::DB_FIELD_NOTES);

    /** Fields retrieved from other tables */
    const OWNER_NAME = 'owner_name';

    /**
     * Event Types
     */
    const EVENT_REJECT = 0;
    const EVENT_SCHEDULE_FIRST_INTERVIEW = 1;
    const EVENT_SCHEDULE_SECOND_INTERVIEW = 2;
    const EVENT_OFFER_JOB = 3;
    const EVENT_MARK_OFFER_DECLINED = 4;
    const EVENT_SEEK_APPROVAL = 5;
    const EVENT_APPROVE = 6;

    /**
     * Event Status list
     */
    const STATUS_INTERVIEW_SCHEDULED = 0;
    const STATUS_INTERVIEW_FINISHED = 1;

	private $id;
	private $applicationId;
	private $createdTime;
	private $createdBy;
	private $owner;
	private $eventType;
	private $status;
	private $notes;
    private $ownerName;

    /**
     * Creator details, lazily fetched on first call of get method
     */
    private $creatorName;
    private $creatorEmail;

	/**
	 * Constructor
	 *
	 * @param int $id ID can be null for newly created job applications
	 */
	public function __construct($id = null) {
		$this->id = $id;
	}

    /**
     * Retrieves the value of id.
     * @return id
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Sets the value of id.
     * @param id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Retrieves the value of applicationId.
     * @return applicationId
     */
    public function getApplicationId() {
        return $this->applicationId;
    }

    /**
     * Sets the value of applicationId.
     * @param applicationId
     */
    public function setApplicationId($applicationId) {
        $this->applicationId = $applicationId;
    }

    /**
     * Retrieves the value of createdTime.
     * @return createdTime
     */
    public function getCreatedTime() {
        return $this->createdTime;
    }

    /**
     * Sets the value of createdTime.
     * @param createdTime
     */
    public function setCreatedTime($createdTime) {
        $this->createdTime = $createdTime;
    }

    /**
     * Retrieves the value of createdBy.
     * @return createdBy
     */
    public function getCreatedBy() {
        return $this->createdBy;
    }

    /**
     * Sets the value of createdBy.
     * @param createdBy
     */
    public function setCreatedBy($createdBy) {
        $this->createdBy = $createdBy;
    }

    /**
     * Retrieves the value of owner.
     * @return owner
     */
    public function getOwner() {
        return $this->owner;
    }

    /**
     * Sets the value of owner.
     * @param owner
     */
    public function setOwner($owner) {
        $this->owner = $owner;
    }

    /**
     * Retrieves the event time
     * @return String event time
     */
    public function getEventTime() {
        return $this->eventTime;
    }

    /**
     * Sets the value of event time
     * @param String $eventTime Event time
     */
    public function setEventTime($eventTime) {
        $this->eventTime = $eventTime;
    }

    /**
     * Retrieves the value of eventType.
     * @return eventType
     */
    public function getEventType() {
        return $this->eventType;
    }

    /**
     * Sets the value of eventType.
     * @param eventType
     */
    public function setEventType($eventType) {
        $this->eventType = $eventType;
    }

    /**
     * Retrieves the value of status.
     * @return status
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Sets the value of status.
     * @param status
     */
    public function setStatus($status) {
        $this->status = $status;
    }

    /**
     * Retrieves the value of notes.
     * @return notes
     */
    public function getNotes() {
        return $this->notes;
    }

    /**
     * Sets the value of notes.
     * @param notes
     */
    public function setNotes($notes) {
        $this->notes = $notes;
    }

    public function setOwnerName($ownerName) {
        $this->ownerName = $ownerName;
    }

    public function getOwnerName() {
        return $this->ownerName;
    }

    /**
     * Retrieves name of creator. (Logged in user who initiated this event)
     * If the user has a corresponding employee, the name from the employee is fetched.
     * Otherwise the user name from the users table is returned.
     *
     * @return String Creator name
     */
    public function getCreatorName() {
        if (!isset($this->creatorName)) {
            $this->_fetchCreatorDetails();
        }
        return $this->creatorName;
    }

    /**
     * Fetches the creator details.
     */
    private function _fetchCreatorDetails() {

        /* No creator set */
        if (empty($this->createdBy)) {
            return;
        }

        $firstName = '';
        $lastName = '';
        $email = '';

        $users = new Users();
        $userDetails = $users->filterUsers($this->createdBy);
        if (!empty($userDetails)) {

            /* If an employee id is found, this means the user is mapped to
             * an employee. Therefore, get the employee details
             */
            if (!empty($userDetails[0][11])) {

                $firstName = $userDetails[0][10];
                $lastName =  $userDetails[0][12];
                $email = $userDetails[0][13];
            } else {

                /*
                 * Otherwise, just get the user name
                 */
                $firstName = $userDetails[0][1];
            }
        }

        $this->creatorName = trim($firstName . ' ' . $lastName);
        $this->creatorEmail = $email;
    }

    /**
     * Retrieves the email of the creator. (Logged in user who initiated this event)
     * If the user has a corresponding employee, the work email of the employee is fetched.
     * If the employee does not have a work email, an empty string is returned.
     * @return String Creator name
     */
    public function getCreatorEmail() {
        if (!isset($this->creatorEmail)) {
            $this->_fetchCreatorDetails();
        }
        return $this->creatorEmail;
    }

	/**
	 * Save JobApplicationEvent object to database
	 *
	 * If a new JobApplicationEvent, inserts into the database, otherwise, updates
	 * the existing entry.
	 *
	 * @return int Returns the ID of the JobApplicationEvent
	 */
    public function save() {

		if (isset($this->id)) {

			if (!CommonFunctions::isValidId($this->id)) {
			    throw new JobApplicationEventException("Invalid id", JobApplicationEventException::INVALID_PARAMETER);
			}
			return $this->_update();
		} else {
			return $this->_insert();
		}
    }


	/**
	 * Insert new object to database
	 */
	private function _insert() {

		$this->id = UniqueIDGenerator::getInstance()->getNextID(self::TABLE_NAME, self::DB_FIELD_ID);
        if (empty($this->createdTime)) {
            $this->createdTime = date(LocaleUtil::STANDARD_TIMESTAMP_FORMAT);
        }

		$sqlBuilder = new SQLQBuilder();
		$sqlBuilder->table_name = self::TABLE_NAME;
		$sqlBuilder->flg_insert = 'true';
		$sqlBuilder->arr_insert = $this->_getFieldValuesAsArray();
		$sqlBuilder->arr_insertfield = self::$dbFields;

		$sql = $sqlBuilder->addNewRecordFeature2();

		$conn = new DMLFunctions();

		$result = $conn->executeQuery($sql);
		if (!$result || (mysql_affected_rows() != 1)) {
			throw new JobApplicationEventException("Insert failed. ", JobApplicationEventException::DB_ERROR);
		}

		return $this->id;
	}

	/**
	 * Update existing object
	 */
	private function _update() {

		$values = $this->_getFieldValuesAsArray();
		$sqlBuilder = new SQLQBuilder();
		$sqlBuilder->table_name = self::TABLE_NAME;
		$sqlBuilder->flg_update = 'true';
		$sqlBuilder->arr_update = self::$dbFields;
		$sqlBuilder->arr_updateRecList = $this->_getFieldValuesAsArray();

		$sql = $sqlBuilder->addUpdateRecord1(0);

		$conn = new DMLFunctions();
		$result = $conn->executeQuery($sql);

		// Here we don't check mysql_affected_rows because update may be called
		// without any changes.
		if (!$result) {
			throw new JobApplicationEventException("Update failed. SQL=$sql", JobApplicationEventException::DB_ERROR);
		}
		return $this->id;
	}

	/**
	 * Returns the db field values as an array
	 *
	 * @return Array Array containing field values in correct order.
	 */
	private function _getFieldValuesAsArray() {

		$values[0] = $this->id;
		$values[1] = $this->applicationId;
		$values[2] = $this->createdTime;
		$values[3] = isset($this->createdBy) ? $this->createdBy : 'null';
		$values[4] = isset($this->owner) ? $this->owner : 'null';
        $values[5] = isset($this->eventTime) ? $this->eventTime : 'null';
		$values[6] = isset($this->eventType) ? $this->eventType : 'null';
		$values[7] = isset($this->status) ? $this->status : 'null';
		$values[8] = isset($this->notes) ? $this->notes : 'null';

		return $values;
	}

    /**
     * Get events for given Job Application
     *
     * @param int $applicationId Job Application ID
     * @return JobApplicationEvent JobApplicationEvent object
     */
    public static function getEvents($applicationId) {

        if (!CommonFunctions::isValidId($applicationId)) {
            throw new JobApplicationEventException("Invalid id", JobApplicationEventException::INVALID_PARAMETER);
        }

        $conditions[] = self::DB_FIELD_APPLICATION_ID . ' = ' . $applicationId;
        $list = self::_getList($conditions);
        return $list;
    }

    /**
     * Get job application Event with given id
     *
     * @param int $id Job Application Event ID
     * @return JobApplicationEvent JobApplicationEvent object
     */
    public static function getJobApplicationEvent($id) {

        if (!CommonFunctions::isValidId($id)) {
            throw new JobApplicationEventException("Invalid id", JobApplicationEventException::INVALID_PARAMETER);
        }

        $conditions[] = self::DB_FIELD_ID . ' = ' . $id;
        $list = self::_getList($conditions);
        $application = (count($list) == 1) ? $list[0] : null;

        return $application;
    }

    /**
     * Get a list of jobs applications with the given conditions.
     *
     * @param array   $selectCondition Array of select conditions to use.
     * @return array  Array of JobApplicationEvent objects. Returns an empty (length zero) array if none found.
     */
    private static function _getList($selectCondition = null) {

        $fields[0] = 'a.' . self::DB_FIELD_ID;
        $fields[1] = 'a.' . self::DB_FIELD_APPLICATION_ID;
        $fields[2] = 'a.' . self::DB_FIELD_CREATED_TIME;
        $fields[3] = 'a.' . self::DB_FIELD_CREATED_BY;
        $fields[4] = 'a.' . self::DB_FIELD_OWNER;
        $fields[5] = 'a.' . self::DB_FIELD_EVENT_TIME;
        $fields[6] = 'a.' . self::DB_FIELD_EVENT_TYPE;
        $fields[7] = 'a.' . self::DB_FIELD_STATUS;
        $fields[8] = 'a.' . self::DB_FIELD_NOTES;
        $fields[9] = "CONCAT(b.`emp_firstname`, ' ', b.`emp_lastname`) AS " . self::OWNER_NAME;

        $tables[0] = self::TABLE_NAME . ' a';
        $tables[1] = 'hs_hr_employee b';

        $joinConditions[1] = 'a.' . self::DB_FIELD_OWNER . ' = b.emp_number';

        $orderBy = self::DB_FIELD_CREATED_TIME;
        $order = 'ASC';

        $sqlBuilder = new SQLQBuilder();
        $sql = $sqlBuilder->selectFromMultipleTable($fields, $tables, $joinConditions, $selectCondition, null, $orderBy, $order);

        $actList = array();

        $conn = new DMLFunctions();
        $result = $conn->executeQuery($sql);

        while ($result && ($row = mysql_fetch_assoc($result))) {
            $actList[] = self::_createFromRow($row);
        }

        return $actList;
    }


    /**
     * Creates a JobApplicationEvent object from a resultset row
     *
     * @param array $row Resultset row from the database.
     * @return JobApplicationEvent JobApplicationEvent object.
     */
    private static function _createFromRow($row) {

        $event = new JobApplicationEvent($row[self::DB_FIELD_ID]);
        $event->setApplicationId($row[self::DB_FIELD_APPLICATION_ID]);
        $event->setCreatedTime($row[self::DB_FIELD_CREATED_TIME]);
        $event->setCreatedBy($row[self::DB_FIELD_CREATED_BY]);
        $event->setOwner($row[self::DB_FIELD_OWNER]);
        $event->setEventTime($row[self::DB_FIELD_EVENT_TIME]);
        $event->setEventType($row[self::DB_FIELD_EVENT_TYPE]);
        $event->setStatus($row[self::DB_FIELD_STATUS]);
        $event->setNotes($row[self::DB_FIELD_NOTES]);

        if (isset($row[self::OWNER_NAME])) {
            $event->setOwnerName($row[self::OWNER_NAME]);
        }

        return $event;
    }

}

class JobApplicationEventException extends Exception {
	const INVALID_PARAMETER = 0;
	const MISSING_PARAMETERS = 1;
	const DB_ERROR = 2;
}

?>
