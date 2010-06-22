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

set_include_path(get_include_path() . PATH_SEPARATOR . ROOT_PATH . '/lib/common');

require_once ROOT_PATH . '/lib/common/Zend/Mail.php';
require_once ROOT_PATH . '/lib/common/Zend/Mail/Transport/Smtp.php';
require_once ROOT_PATH . '/lib/common/Zend/Mail/Transport/Sendmail.php';

require_once ROOT_PATH . '/lib/confs/Conf.php';
require_once ROOT_PATH . '/lib/confs/sysConf.php';
require_once ROOT_PATH.'/lib/common/CommonFunctions.php';

class EmailConfiguration {

	const EMAILCONFIGURATION_FILE_CONFIG = '/lib/confs/mailConf.php';

	const EMAILCONFIGURATION_TYPE_MAIL = 'mail';
	const EMAILCONFIGURATION_TYPE_SENDMAIL = 'sendmail';
	const EMAILCONFIGURATION_TYPE_SMTP = 'smtp';
	const EMAILCONFIGURATION_SMTP_SECURITY_NONE = 'NONE';
	const EMAILCONFIGURATION_SMTP_SECURITY_TLS = 'TLS';
	const EMAILCONFIGURATION_SMTP_SECURITY_SSL = 'SSL';

	const EMAILCONFIGURATION_SMTP_AUTH_NONE = 'NONE';
	const EMAILCONFIGURATION_SMTP_AUTH_LOGIN = 'LOGIN';

	private $smtpHost;
	private $smtpUser;
	private $smtpPass;
	private $smtpPort;
	private $mailAddress;
	private $mailType;
	private $sendmailPath;
    private $originalSendmailPath;
	private $smtpSecurity;
	private $smtpAuth;
	private $configurationFile;
	private $testEmail;
	private $testEmailType;

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

	public function setSmtpAuth($auth) {
		$this->smtpAuth = $auth;
	}

	public function getSmtpAuth() {
		return $this->smtpAuth;
	}

	public function setSmtpSecurity($security) {
		$this->smtpSecurity = $security;
	}

	public function getSmtpSecurity() {
		return $this->smtpSecurity;
	}

	public function setTestEmail($testEmail) {
		$this->testEmail = $testEmail;
	}

	public function getTestEmail() {
		return $this->testEmail;
	}

	public function setTestEmailType($testEmailType) {
		$this->testEmailType = $testEmailType;
	}

	public function getTestEmailType() {
		return $this->testEmailType;
	}

	public function __construct() {
		$confObj = new Conf();

		if (is_file(ROOT_PATH.self::EMAILCONFIGURATION_FILE_CONFIG)) {
			$this->configurationFile=ROOT_PATH.self::EMAILCONFIGURATION_FILE_CONFIG;
		}

		if (isset($confObj->emailConfiguration) && is_file($confObj->emailConfiguration)) {
			$this->configurationFile=$confObj->emailConfiguration;
		} else if (isset($confObj->emailConfiguration) && is_file(ROOT_PATH.$confObj->emailConfiguration)) {
			$this->configurationFile=ROOT_PATH.$confObj->emailConfiguration;
		}

		if ($this->configurationFile == null) {
			include ROOT_PATH.self::EMAILCONFIGURATION_FILE_CONFIG."-distribution";

			if (isset($confObj->emailConfiguration)) {
				$this->configurationFile=$confObj->emailConfiguration;
			}

			$this->reWriteConf();
		}

		include $this->configurationFile;

        $this->originalSendmailPath = $this->sendmailPath;
	}

	public function reWriteConf() {
        $sysConf = new sysConf();

        $sendMailPath = $this->originalSendmailPath;

        /*
         * Only override sendmail path if allowed.
         */
        if ( CommonFunctions::allowSendmailPathEdit() ) {
            $sendMailPath = $this->getSendmailPath();
        }

		$content = '
<?php
	$this->smtpHost = \''.$this->_safeEscape($this->getSmtpHost()).'\';
	$this->smtpUser = \''.$this->_safeEscape($this->getSmtpUser()).'\';
	$this->smtpPass = \''.$this->_safeEscape($this->getSmtpPass()).'\';
	$this->smtpPort = \''.$this->_safeEscape($this->getSmtpPort()).'\';

	$this->sendmailPath = \''.$this->_safeEscape($sendMailPath).'\';

	$this->mailType = \''.$this->_safeEscape($this->getMailType()).'\';
	$this->mailAddress = \''.$this->_safeEscape($this->getMailAddress()).'\';
	$this->smtpAuth = \''.$this->_safeEscape($this->getSmtpAuth()).'\';
	$this->smtpSecurity = \''.$this->_safeEscape($this->getSmtpSecurity()).'\';
?>';

		return file_put_contents($this->configurationFile, $content);
	}

	/**
	 * Uses to test the SMTP details set in Email Configuration
	 * @param string $testAddress Email address to send test email
	 * @return bool Returns true if no error occurs during transport. False other wise.
	 */

	public function sendTestEmail() {


		if ($this->getTestEmailType() == "smtp") {

            	$auth = $this->getSmtpAuth();

			if ($auth != self::EMAILCONFIGURATION_SMTP_AUTH_NONE){

            $config = array('auth' => 'login',
                            'username' =>$this->getSmtpUser(),
                            'password' =>$this->getSmtpPass(),
                            'port' =>$this->getSmtpPort());
            } else {
            	$config = array('port' =>$this->getSmtpPort());
            }

			$security = $this->getSmtpSecurity();
			
			if ($security != self::EMAILCONFIGURATION_SMTP_SECURITY_NONE) {
				$config['ssl'] = strtolower($security);
			}

			$transport = new Zend_Mail_Transport_Smtp($this->getSmtpHost(), $config);
			$subject = "SMTP Configuration Test Email";
			$message = "This email confirms that SMTP details set in OrangeHRM are correct. You received this email since your email address was entered to test email in configuration screen.";
			$logMessage = date('r')." Sending Test Email Using SMTP to {$this->getTestEmail()} ";

		} elseif ($this->getTestEmailType() == "sendmail") {

			$transport = new Zend_Mail_Transport_Sendmail();
			$subject = "SendMail Configuration Test Email";
			$message = "This email confirms that SendMail details set in OrangeHRM are correct. You received this email since your email address was entered to test email in configuration screen.";
			$logMessage = date('r')." Sending Test Email Using SendMail to {$this->getTestEmail()} ";

		}

		$mail = new Zend_Mail();
		$mail->setFrom($this->getMailAddress(), "OrangeHRM EMail");
		$mail->addTo($this->getTestEmail());
		$mail->setSubject($subject);
		$mail->setBodyText($message);

		$logPath = ROOT_PATH.'/lib/logs/notification_mails.log';

		try {
		    $mail->send($transport);
		    $logMessage .= "Succeeded \r\n";
		    error_log($logMessage, 3, $logPath);
		    return true;
		} catch (Exception $e) {
			$logMessage .= "Failed \r\n Reason: {$e->getMessage()} \r\n";
			error_log($logMessage, 3, $logPath);
			return false;
		}

	}

    /**
     * Escape variable to make it safe to include in mailConf.php
     * Ideally these should go into a configuration file (non executable)
     * @param  $value
     * @return mixed
     */
    protected function _safeEscape($value) {
        $value = str_replace("'", "", $value);
        $value = str_replace("\\", "", $value);
        $value = str_replace("\n", "", $value);
        $value = str_replace("\r", "", $value);
        $value = str_replace("\t", "", $value);
        $value = str_replace("\f", "", $value);

        return $value;
    }
}
?>
