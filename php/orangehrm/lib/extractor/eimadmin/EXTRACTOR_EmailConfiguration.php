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
 */

require_once ROOT_PATH . '/lib/models/eimadmin/EmailConfiguration.php';
require_once ROOT_PATH.'/lib/common/CommonFunctions.php';

class EXTRACTOR_EmailConfiguration {

	private $emailConfiguration;

	public function __construct() {
		$this->emailConfiguration = new EmailConfiguration();
	}

	public function parseAddData() {}

	public function parseEditData($postArr) {

			$this->emailConfiguration->setSmtpHost($postArr["txtSmtpHost"]);

			if (isset($postArr["txtSmtpUser"])) {
				$this->emailConfiguration->setSmtpUser($postArr["txtSmtpUser"]);
			}

			if (isset($postArr["txtSmtpPass"])) {
				$this->emailConfiguration->setSmtpPass($postArr["txtSmtpPass"]);
			}

			$this->emailConfiguration->setSmtpPort($postArr["txtSmtpPort"]);
			$this->emailConfiguration->setMailType($postArr["txtMailType"]);
			$this->emailConfiguration->setMailAddress($postArr["txtMailAddress"]);

			if (isset($postArr["chkTestEmail"]) && !empty($postArr["txtTestEmail"])) {
				if ($postArr["txtMailType"] == "smtp") {
					$this->emailConfiguration->setTestEmailType("smtp");
				} elseif ($postArr["txtMailType"] == "sendmail") {
				    $this->emailConfiguration->setTestEmailType("sendmail");
				}
			    $this->emailConfiguration->setTestEmail(trim($postArr["txtTestEmail"]));
			}

			if (isset($postArr["optAuth"])) {
				$this->emailConfiguration->setSmtpAuth($postArr["optAuth"]);
			}

			if (isset($postArr["optSecurity"])) {
				$this->emailConfiguration->setSmtpSecurity($postArr["optSecurity"]);
			}

            if (CommonFunctions::allowSendmailPathEdit() ) {
               
                if (isset($postArr["txtSendmailPath"])) {
                    $this->emailConfiguration->setSendmailPath($postArr["txtSendmailPath"]);
                }
            }

			return $this->emailConfiguration;
	}

	public function parseDeleteData() {}

}
?>
