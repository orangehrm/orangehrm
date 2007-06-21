<?php
/**
 *
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
 * @copyright 2006 OrangeHRM Inc., http://www.orangehrm.com
 */


class EmailConfiguration {

	const EMAILCONFIGURATION_FILE_CONFIG = '/lib/confs/mailConf.php';

	const EMAILCONFIGURATION_TYPE_MAIL = 'mail';
	const EMAILCONFIGURATION_TYPE_SENDMAIL = 'sendmail';
	const EMAILCONFIGURATION_TYPE_SMTP = 'smtp';

	private $smtpHost;
	private $smtpUser;
	private $smtpPass;
	private $smtpPort;
	private $mailAddress;
	private $mailType;
	private $sendmailPath;

	public function getSmtpHost() {
		return $this->smtpHost;
	}

	public function setSmtpHost($smtphost) {
		$this->smtpHost = $smtphost;
	}

	public function getSmtpUser() {
		return $this->smtpUser;
	}

	public function setSmtpUser($smtpUser) {
		$this->smtpUser = $smtpUser;
	}

	public function getSmtpPass() {
		return $this->smtpPass;
	}

	public function setSmtpPass($smtpPass) {
		$this->smtpPass = $smtpPass;
	}

	public function getSmtpPort() {
		return $this->smtpPort;
	}

	public function setSmtpPort($smtpPort) {
		$this->smtpPort = $smtpPort;
	}

	public function getMailAddress() {
		return $this->mailAddress;
	}

	public function setMailAddress($mailAddress) {
		$this->mailAddress = $mailAddress;
	}

	public function getMailType() {
		return $this->mailType;
	}

	public function setMailType($mailType) {
		$this->mailType = $mailType;
	}

	public function getSendmailPath() {
		return $this->sendmailPath;
	}

	public function setSendmailPath($sendmailPath) {
		$this->sendmailPath = $sendmailPath;
	}

	public function __construct() {
		if (is_file(ROOT_PATH . self::EMAILCONFIGURATION_FILE_CONFIG)) {
			include ROOT_PATH.self::EMAILCONFIGURATION_FILE_CONFIG;
		}
	}

	public function reWriteConf() {
		$content = '
<?php
	$this->smtpHost = \''.$this->getSmtpHost().'\';
	$this->smtpUser = \''.$this->getSmtpUser().'\';
	$this->smtpPass = \''.$this->getSmtpPass().'\';
	$this->smtpPort = \''.$this->getSmtpPort().'\';

	$this->sendmailPath = \''.$this->getSendmailPath().'\';

	$this->mailType = \''.$this->getMailType().'\';
	$this->mailAddress = \''.$this->getMailAddress().'\';
?>';

		return file_put_contents(ROOT_PATH.self::EMAILCONFIGURATION_FILE_CONFIG, $content);
	}

}
?>