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

    private $mailType;
    private $sentAs;
    private $smtpHost;
    private $smtpPort;
    private $smtpUsername;
    private $smtpPassword;
    private $smtpAuthType;
    private $smtpSecurityType;
    private $sendmailPath;
    private $confPath;

    public function getMailType() {
        return $this->mailType;
    }

    public function getSentAs() {
        return $this->sentAs;
    }

    public function getSmtpHost() {
        return $this->smtpHost;
    }

    public function getSmtpPort() {
        return $this->smtpPort;
    }

    public function getSmtpUsername() {
        return $this->smtpUsername;
    }

    public function getSmtpPassword() {
        return $this->smtpPassword;
    }

    public function getSmtpAuthType() {
        return $this->smtpAuthType;
    }

    public function getSmtpSecurityType() {
        return $this->smtpSecurityType;
    }

    public function getSendmailPath() {
        return $this->sendmailPath;
    }

    public function setMailType($mailType) {
        $this->mailType = $mailType;
    }

    public function setSentAs($sentAs) {
        $this->sentAs = $sentAs;
    }

    public function setSmtpHost($smtpHost) {
        $this->smtpHost = $smtpHost;
    }

    public function setSmtpPort($smtpPort) {
        $this->smtpPort = $smtpPort;
    }

    public function setSmtpUsername($smtpUsername) {
        $this->smtpUsername = $smtpUsername;
    }

    public function setSmtpPassword($smtpPassword) {
        $this->smtpPassword = $smtpPassword;
    }

    public function setSmtpAuthType($smtpAuthType) {
        $this->smtpAuthType = $smtpAuthType;
    }

    public function setSmtpSecurityType($smtpSecurityType) {
        $this->smtpSecurityType = $smtpSecurityType;
    }

    public function setSendmailPath($sendmailPath) {
        $this->sendmailPath = $sendmailPath;
    }

    public function __construct() {

        $this->_setConfPath();
        $this->_setConfValues();

    }

    private function _setConfPath() {

        $path = sfConfig::get('sf_root_dir') . '/apps/orangehrm/config/emailConfiguration.yml';

        if (is_writable($path)) {
            $this->confPath = $path;
        } else {
            throw new Exception("Email Configuration is not writable");
        }

    }

    private function _setConfValues() {

        $confData = sfYaml::load($this->confPath);

        $this->mailType = $confData['mailType'];
        $this->sentAs = $confData['sentAs'];
        $this->smtpHost = $confData['smtp']['host'];
        $this->smtpPort = $confData['smtp']['port'];
        $this->smtpUsername = $confData['smtp']['username'];
        $this->smtpPassword = $confData['smtp']['password'];
        $this->smtpAuthType = $confData['smtp']['authType'];
        $this->smtpSecurityType = $confData['smtp']['securityType'];
        $this->sendmailPath = $confData['sendmail']['path'];

    }

    public function save() {

        $confData['mailType'] = $this->mailType;
        $confData['sentAs'] = $this->sentAs;
        $confData['smtp']['host'] = $this->smtpHost;
        $confData['smtp']['port'] = $this->smtpPort;
        $confData['smtp']['username'] = $this->smtpUsername;
        $confData['smtp']['password'] = $this->smtpPassword;
        $confData['smtp']['authType'] = $this->smtpAuthType;
        $confData['smtp']['securityType'] = $this->smtpSecurityType;
        $confData['sendmail']['path'] = $this->sendmailPath;

        file_put_contents($this->confPath, sfYaml::dump($confData));

        // TODO: This should be removed after every module is converted into Symfony
        $this->_saveOldEmailConfiguration();

    }

    private function _saveOldEmailConfiguration() {

		$content = '
<?php
	$this->smtpHost = \''.$this->smtpHost.'\';
	$this->smtpUser = \''.$this->smtpUsername.'\';
	$this->smtpPass = \''.$this->smtpPassword.'\';
	$this->smtpPort = \''.$this->smtpPort.'\';

	$this->sendmailPath = \''.$this->sendmailPath.'\';

	$this->mailType = \''.$this->mailType.'\';
	$this->mailAddress = \''.$this->sentAs.'\';
	$this->smtpAuth = \''.$this->smtpAuthType.'\';
	$this->smtpSecurity = \''.$this->smtpSecurityType.'\';
?>';

        $oldConfigPath = ROOT_PATH . '/lib/confs/mailConf.php';

		return file_put_contents($oldConfigPath, $content);

    }

}

?>
