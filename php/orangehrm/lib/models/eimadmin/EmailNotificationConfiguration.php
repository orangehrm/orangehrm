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
require_once ROOT_PATH . '/lib/models/maintenance/Users.php';

/**
 * Handle mail notification settings
 */
class EmailNotificationConfiguration {

	const EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_REJECTED = -1;
	const EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_CANCELLED = 0;
	const EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_PENDING_APPROVAL = 1;
	const EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_APPROVED = 2;
	const EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_HSP = 3;
	const EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_JOB_APPLIED = 4;
    const EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_SEEK_HIRE_APPROVAL = 5;
    const EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_HIRE_TASKS = 6;
    const EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_HIRE_APPROVED = 7;

	private $userId = null;
	private $notifcationTypeId;
	private $notificationStatus;
	private $email;

	public function setUserId($userId) {
		$this->userId = $userId;
	}

	public function getUserId() {
		return $this->userId;
	}

	public function setNotifcationTypeId($notifcationTypeId) {
		$this->notifcationTypeId = $notifcationTypeId;
	}

	public function getNotifcationTypeId() {
		return $this->notifcationTypeId;
	}

	public function setNotificationStatus($notificationStatus) {
		$this->notificationStatus = $notificationStatus;
	}

	public function getNotificationStatus() {
		return $this->notificationStatus;
	}

	public function setEmail($email) {
		$this->email = $email;
	}

	public function getEmail() {
		return $this->email;
	}

	public function __construct($userId=null) {
		if (isset($userId)) {
			$this->setUserId($userId);
		}
	}

	public function fetchMailNotifications($notificationTypeId = null, $filterUnsubscribed = false) {
		$notificationObjs = $this->fetchNotifcationStatus($notificationTypeId, $filterUnsubscribed);
		$emails = null;

		if (is_array($notificationObjs)) {
			foreach ($notificationObjs as $notificationObj) {
				$emails[] = $notificationObj->getEmail();
			}
		}

		return $emails;
	}


	/**
	 * Fetch all notification status
	 */
	public function fetchNotifcationStatus($notificationTypeId = null, $filterUnsubscribed = false) {
		$sqlQBuilder = new SQLQBuilder();

		$arrFields[0] = '`user_id`';
		$arrFields[1] = '`notification_type_id`';
		$arrFields[2] = '`status`';
        $arrFields[3] = '`email`';

		$arrTable = "`hs_hr_mailnotifications`";

		$userId = $this->getUserId();
		if (isset($userId)) {
			$selectConditions[1] = "`user_id` = '{$this->getUserId()}'";
		}

		if (isset($notificationTypeId)) {
			$selectConditions[2] = "`notification_type_id` = '{$notificationTypeId}'";
		}

		if ($filterUnsubscribed) {
			$selectConditions[] = "`status` = 1";
		}
		
		$query = $sqlQBuilder->simpleSelect($arrTable, $arrFields, $selectConditions, $arrFields[0], 'ASC');

		$dbConnection = new DMLFunctions();

		$result = $dbConnection -> executeQuery($query);

		return $this->_buildObjArr($result);
	}

	public function fetchSelectedMailNotifications($notificationTypeId=null) {
		$notificationObjs = $this->fetchSelectedNotifcationStatus($notificationTypeId);
		$emails = null;

		if (is_array($notificationObjs)) {
			foreach ($notificationObjs as $notificationObj) {
				$emails[] = $notificationObj->getEmail();
			}
		}

		return $emails;
	}
	/*
	 * Fetch only selected notification statuses for admins status=1
	 *
	 * */
	public function fetchSelectedNotifcationStatus($notificationTypeId=null) {
		$sqlQBuilder = new SQLQBuilder();

		$arrFields[0] = '`user_id`';
		$arrFields[1] = '`notification_type_id`';
		$arrFields[2] = '`status`';
        $arrFields[3] = '`email`';

		$arrTable = "`hs_hr_mailnotifications`";

		$userId = $this->getUserId();
		if (isset($userId)) {
			$selectConditions[1] = "`user_id` = '{$this->getUserId()}'";
		}

		if (isset($notificationTypeId)) {
			$selectConditions[2] = "`notification_type_id` = '{$notificationTypeId}'";
			$selectConditions[3] = "`status`='1'";
		}

		$query = $sqlQBuilder->simpleSelect($arrTable, $arrFields, $selectConditions, $arrFields[0], 'ASC');

		$dbConnection = new DMLFunctions();

		$result = $dbConnection -> executeQuery($query);

		return $this->_buildObjArr($result);
	}

	public function updateNotificationStatus() {

		$userObj = new Users();

		$userObj->updateUserEmail($this->getUserId(), $this->getEmail());

		if (!$this->_notificationConfigurationExsist()) {
			return $this->_addNotificationStatus();
		}

		$sqlQBuilder = new SQLQBuilder();

		$arrFields[0] = '`status`';

		$changeValues[0] = $this->getNotificationStatus();

		$arrTable = "`hs_hr_mailnotifications`";

		$updateConditions[1] = "`user_id` = '{$this->getUserId()}'";
		$updateConditions[2] = "`notification_type_id` = '{$this->getNotifcationTypeId()}'";

		$query = $sqlQBuilder->simpleUpdate($arrTable, $arrFields, $changeValues, $updateConditions);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		return $result;
	}

	private function _notificationConfigurationExsist() {
		$result = $this->fetchNotifcationStatus($this->getNotifcationTypeId());

		if (isset($result) && isset($result[0])) {
			return true;
		}

		return false;
	}

	private function _addNotificationStatus() {
		$sqlQBuilder = new SQLQBuilder();

		$arrFields[0] = '`user_id`';
		$arrFields[1] = '`notification_type_id`';
		$arrFields[2] = '`status`';

		$insertValues[0] = "'{$this->getUserId()}'";
		$insertValues[1] = "'{$this->getNotifcationTypeId()}'";
		$insertValues[2] = $this->getNotificationStatus();

		$arrTable = "`hs_hr_mailnotifications`";

		$query = $sqlQBuilder->simpleInsert($arrTable, $insertValues, $arrFields, true);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		return $result;
	}

	private function _buildObjArr($result) {
		if (!isset($result)) {
			return false;
		}

		$objArr = null;

		$userObj = new Users();

		while ($row = mysql_fetch_assoc($result)) {
			$tmpEmailNotificationConf = new EmailNotificationConfiguration($row['user_id']);

			$tmpEmailNotificationConf->setNotifcationTypeId($row['notification_type_id']);
			$tmpEmailNotificationConf->setNotificationStatus($row['status']);
            $tmpEmailNotificationConf->setEmail($row['email']);

			$objArr[] = $tmpEmailNotificationConf;
		}

		return $objArr;
	}
}
?>
