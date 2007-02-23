<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.

 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

require_once ROOT_PATH . '/lib/models/eimadmin/EmailNotificationConfiguration.php';

class EXTRACTOR_EmailNotificationConfiguration {

	public function __construct() {
	}

	public function parseEditData($postArr) {

		$emailNotificationObjs = null;

		if (isset($postArr['notificationMessageStatus']) && is_array($postArr['notificationMessageStatus'])) {

			for ($i=0; $i<count($postArr['notificationMessageStatus']); $i++) {
				$tmpEmailNotificationObj = new EmailNotificationConfiguration($_SESSION['user']);
				if (!isset($postArr['notificationMessageStatus'][$i])) {
					$postArr['notificationMessageStatus'][$i] = 0;
				}
				$tmpEmailNotificationObj->setUserId($_SESSION['user']);
				$tmpEmailNotificationObj->setEmail($postArr['txtMailAddress']);
				$tmpEmailNotificationObj->setNotifcationTypeId($postArr['notificationMessageId'][$i]);
				$tmpEmailNotificationObj->setNotificationStatus($postArr['notificationMessageStatus'][$i]);

				$emailNotificationObjs[] = $tmpEmailNotificationObj;
			}
		}

		return $emailNotificationObjs;
	}
}
?>
