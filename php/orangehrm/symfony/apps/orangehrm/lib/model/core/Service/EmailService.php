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

require_once sfConfig::get('sf_root_dir').'/lib/vendor/symfony/lib/vendor/swiftmailer/swift_required.php';

class EmailService extends BaseService {

    const SMTP_SECURITY_NONE = 'none';
    const SMTP_SECURITY_TLS = 'tls';
    const SMTP_SECURITY_SSL = 'ssl';

    const SMTP_AUTH_NONE = 'none';
    const SMTP_AUTH_LOGIN = 'login';

    private $emailConfig;
    private $configSet = false;

    private $messageSubject;
    private $messageFrom;
    private $messageTo;
    private $messageBody;
    private $messageCc;
    private $messageBcc;

    public function setMessageSubject($messageSubject) {
        $this->messageSubject = $messageSubject;
    }
    public function setMessageFrom($messageFrom) {
        $this->messageFrom = $messageFrom;
    }
    public function setMessageTo($messageTo) {
        $this->messageTo = $messageTo;
    }
    public function setMessageBody($messageBody) {
        $this->messageBody = $messageBody;
    }
    public function setMessageCc($messageCc) {
        $this->messageCc = $messageCc;
    }
    public function setMessageBcc($messageBcc) {
        $this->messageBcc = $messageBcc;
    }

    public function __construct() {

        $this->emailConfig = new EmailConfiguration();

        if ($this->emailConfig->getMailType() == 'smtp' ||
            $this->emailConfig->getMailType() == 'sendmail') {
            $this->configSet = true;
        }

    }

    private function _getMailer() {

        $mailer = null;

        if ($this->configSet) {

            switch ($this->emailConfig->getMailType()) {

                case 'smtp':

                    $transport = Swift_SmtpTransport::newInstance(
                                   $this->emailConfig->getSmtpHost(),
                                   $this->emailConfig->getSmtpPort());

                    if ($this->emailConfig->getSmtpAuthType() == self::SMTP_AUTH_LOGIN) {
                        $transport->setUsername($this->emailConfig->getSmtpUsername());
                        $transport->setPassword($this->emailConfig->getSmtpPassword());
                    }

                    if ($this->emailConfig->getSmtpSecurityType() == self::SMTP_SECURITY_SSL ||
                        $this->emailConfig->getSmtpSecurityType() == self::SMTP_SECURITY_TLS) {
                        $transport->setEncryption($this->emailConfig->getSmtpSecurityType());
                    }

                    $mailer = Swift_Mailer::newInstance($transport);

                    break;

                case 'sendmail':

                    $transport = Swift_SendmailTransport::newInstance($this->emailConfig->getSendmailPath());
                    $mailer = Swift_Mailer::newInstance($transport);

                    break;

            }

        }

        return $mailer;

    }

    private function _getMessage() {

        if (empty($this->messageSubject)) {
            throw new Exception("Email subjet is not set");
        }

        if (empty($this->messageFrom)) {
            $this->_validateEmailAddress($this->emailConfig->getSentAs());
            $this->messageFrom = array($this->emailConfig->getSentAs() => 'OrangeHRM');
        }

        if (empty($this->messageTo)) {
            throw new Exception("Email 'to' is not set");
        }

        if (empty($this->messageBody)) {
            throw new Exception("Email body is not set");
        }

        $message = Swift_Message::newInstance();
        $message->setSubject($this->messageSubject);
        $message->setFrom($this->messageFrom);
        $message->setTo($this->messageTo);
        $message->setBody($this->messageBody);

        if (!empty($this->messageCc)) {
            $message->setCc($this->messageCc);
        }

        if (!empty($this->messageBcc)) {
            $message->setBcc($this->messageBcc);
        }

        return $message;

    }

    public function sendEmail() {

        if ($this->configSet) {

            try {

                $mailer = $this->_getMailer();
                $message = $this->_getMessage();

                $result = $mailer->send($message);

                $logMessage = 'Emails was sent to ';
                $logMessage .= implode(', ', $this->messageTo);

                if (!empty($this->messageCc)) {
                    $logMessage .= ' and CCed to ';
                    $logMessage .= implode(', ', $this->messageCc);
                }

                if (!empty($this->messageBcc)) {
                    $logMessage .= ' and BCCed to ';
                    $logMessage .= implode(', ', $this->messageBcc);
                }

                $logMessage .= ' using '.$this->emailConfig->getMailType();

                $this->_logResult('Success', $logMessage);

                return true;

            } catch (Exception $e) {

                $logMessage = 'Sending email failed to ';
                $logMessage .= implode(', ', $this->messageTo);

                if (!empty($this->messageCc)) {
                    $logMessage .= ' and CCing to ';
                    $logMessage .= implode(', ', $this->messageCc);
                }

                if (!empty($this->messageBcc)) {
                    $logMessage .= ' and BCCing to ';
                    $logMessage .= implode(', ', $this->messageBcc);
                }

                $logMessage .= ' using '.$this->emailConfig->getMailType();

                $logMessage .= '. Reason: '.$e->getMessage();


                $this->_logResult('Failure', $logMessage);

                return false;

            }

        } else {

            $this->_logResult('Failure', 'Email configuration is not set.');

            return false;

        }

    }

    public function sendTestEmail($toEmail) {

        $mailType = $this->emailConfig->getMailType();

        if ($mailType == 'smtp') {

            $subject = "SMTP Configuration Test Email";

            $body = "This email confirms that SMTP details set in OrangeHRM are ";
            $body .= "correct. You received this email since your email address ";
            $body .= "was entered to test email in configuration screen.";
            
        } elseif ($mailType == 'sendmail') {

            $subject = "Sendmail Configuration Test Email";

            $body = "This email confirms that Sendmail details set in OrangeHRM ";
            $body .= "are correct. You received this email since your email ";
            $body .= "address was entered to test email in configuration screen.";

        }

        $this->_validateEmailAddress($toEmail);

        $this->messageSubject = $subject;
        $this->messageTo = array($toEmail);
        $this->messageBody = $body;

        return $this->sendEmail();

    }

    private function _validateEmailAddress($emailAddress) {

        if (!preg_match("/^[^@]*@[^@]*\.[^@]*$/", $emailAddress)) {
            throw new Exception("Invalid email address");
        }

    }

    private function _logResult($type = '', $logMessage = '') {

        $logPath = ROOT_PATH . '/lib/logs/notification_mails.log';

        if (file_exists($logPath) && !is_writable($logPath)) {
            throw new Exception("EmailService : Log file is not writable");
        }

        $message = '========== Message Begins ==========';
        $message .= "\r\n\n";
        $message .= 'Time : '.date("F j, Y, g:i a");
        $message .= "\r\n";
        $message .= 'Message Type : '.$type;
        $message .= "\r\n";
        $message .= 'Message : '.$logMessage;
        $message .= "\r\n\n";
        $message .= '========== Message Ends ==========';
        $message .= "\r\n\n";

        file_put_contents($logPath, $message, FILE_APPEND);

    }

}